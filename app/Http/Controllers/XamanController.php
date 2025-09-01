<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\XummService;
use App\Services\XummPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Xrpl\XummSdkPhp\Exception\Http\NotFoundException;

class XamanController extends Controller
{
    protected XummService $xaman;
    protected XummPayment $xummPayment;

    public function __construct(XummService $xumm, XummPayment $xummPayment)
    {
        $this->xaman = $xumm;
        $this->xummPayment = $xummPayment;
    }

    public function showLoginQr()
    {
        // If already authenticated, redirect to holder page
        if (Auth::check()) {
            $wallet = Auth::user()->wallet ?? Session::get('wallet');
            if ($wallet) {
                return redirect()->to('/holder/' . $wallet);
            }
        }

        // Check if we already have a valid UUID in the session
        $uuid = Session::get('xumm_login_uuid');

        if ($uuid) {
            try {
                $payload = $this->xaman->getPayload($uuid);

                // If not logged in yet, still show QR
                if (empty($payload->response->account)) {
                    return view('xaman.login', [
                        'qr' => $payload->refs->qrPng,
                        'url' => $payload->next->always,
                    ]);
                }

                // If already logged in via payload
                $wallet = $payload->response->account;
                return redirect()->to('/holder/' . $wallet);
            } catch (\Throwable $e) {
                Log::warning('Old UUID invalid, generating new one: ' . $e->getMessage());
                Session::forget('xumm_login_uuid');
            }
        }

        // No valid UUID -> new payload
        $payload = $this->xaman->createLoginPayload();

        // Store UUID in session
        Session::put('xumm_login_uuid', $payload->uuid);

        return view('xaman.login', [
            'qr' => $payload->refs->qrPng,
            'url' => $payload->next->always,
        ]);
    }

    public function loginCheck()
    {
        $uuid = Session::get('xumm_login_uuid');
        if (!$uuid) {
            return response()->json(['success' => false]);
        }

        $payload = $this->xaman->getPayload($uuid);

        // Defensive checks
        $account = $payload->response->account ?? null;

        Log::info('Payload account: ' . ($account ?? 'null'));

        if (empty($account)) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'wallet' => $account,
        ]);
    }

    public function loginStore(Request $request)
    {
        $wallet = $request->input('wallet');
        $token = $request->input('access_token');

        // Store in session
        Session::put('wallet', $wallet);
        Session::put('xumm_token', $token);

        return response()->json(['success' => true]);
    }

    public function logout(Request $request)
    {
        Log::info('User logging out: ' . auth()->id());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function handleWebhook(Request $request)
    {
        Log::info('Webhook request received at: ' . now()->toDateTimeString(), [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        $data = $request->all();
        $uuid = $data['meta']['payload_uuidv4'] ?? null;

        if (!$uuid) {
            Log::warning('Missing UUID in webhook', ['data' => $data]);
            return response()->json(['success' => false], 400);
        }

        try {
            $payload = $this->xummPayment->getPayload($uuid);
            Log::info('Payload fetched for UUID: ' . $uuid, ['payload' => (array) $payload]);
        } catch (NotFoundException $e) {
            Log::error('Payload not found for UUID: ' . $uuid, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Payload not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching payload: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json(['success' => false, 'message' => 'Error fetching payload'], 500);
        }

        $wallet = $payload->response->account ?? null;
        if (!$wallet) {
            Log::warning('Wallet not found in payload response for UUID: ' . $uuid, ['payload' => (array) $payload]);
            return response()->json(['success' => false], 400);
        }

        // Determine transaction type
        $transactionType = $payload->payload->request->TransactionType ?? null;
        Log::info('Processing transaction type: ' . ($transactionType ?? 'null'), ['uuid' => $uuid]);

        if ($transactionType === 'SignIn') {
            // Handle login
            $userToken = $data['userToken']['user_token'] ?? null;
            if (!$userToken) {
                Log::warning('Missing userToken in login webhook', ['data' => $data]);
                return response()->json(['success' => false], 400);
            }

            Session::put('wallet', $wallet);

            // Create or update user
            $user = User::updateOrCreate(
                ['wallet' => $wallet],
                ['name' => $wallet, 'xumm_token' => $userToken]
            );

            // Log in user
            Auth::login($user);

            Log::info('Login successful for wallet: ' . $wallet);
        } elseif ($transactionType === 'Payment') {
            // Handle payment
            $txid = $payload->response->txid ?? null;
            if (!$txid) {
                Log::warning('Missing txid in payment webhook for UUID: ' . $uuid, ['payload' => (array) $payload]);
                return response()->json(['success' => false], 400);
            }

            // Clear payment UUID from session
            Session::forget('xumm_payment_uuid');

            // Optionally store transaction details in a database
            // Example: Transaction::create(['uuid' => $uuid, 'txid' => $txid, 'wallet' => $wallet]);

            Log::info('Payment successful for wallet: ' . $wallet . ', txid: ' . $txid);
        } else {
            Log::warning('Unknown transaction type: ' . ($transactionType ?? 'null'), ['uuid' => $uuid]);
            return response()->json(['success' => false, 'message' => 'Unknown transaction type'], 400);
        }

        return response()->json(['success' => true]);
    }

    public function loginFinalize(Request $request)
    {
        $wallet = $request->input('wallet');
        if (!$wallet) {
            return response()->json(['success' => false]);
        }

        $user = User::where('wallet', $wallet)->first();
        if (!$user) {
            return response()->json(['success' => false]);
        }

        Auth::login($user);
        Session::put('wallet', $wallet);

        return response()->json(['success' => true]);
    }

    public function handleCallback(Request $request)
    {
        Log::info('Callback received');
        return redirect()->route('welcome');
    }
}
