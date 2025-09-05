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
use App\Helpers\SlackNotifier;

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
        if (Auth::check()) {
            $wallet = Auth::user()->wallet ?? Session::get('wallet');
            if ($wallet) {
                return redirect()->to('/holder/' . $wallet);
            }
        }

        $uuid = Session::get('xumm_login_uuid');

        if ($uuid) {
            try {
                $payload = $this->xaman->getPayload($uuid);
                if (empty($payload->response->account)) {
                    return view('xaman.login', [
                        'qr' => $payload->refs->qrPng,
                        'url' => $payload->next->always,
                    ]);
                }
                $wallet = $payload->response->account;
                return redirect()->to('/holder/' . $wallet);
            } catch (\Throwable $e) {
                $logMessage = '[showLoginQr] Old UUID invalid, generating new one: ' . $e->getMessage();
                Log::warning($logMessage);
                SlackNotifier::warning($logMessage);
                Session::forget('xumm_login_uuid');
            }
        }

        $payload = $this->xaman->createLoginPayload();
        Session::put('xumm_login_uuid', $payload->uuid);

        $logMessage = '[showLoginQr] New login payload created: UUID=' . $payload->uuid;
        Log::info($logMessage);
        SlackNotifier::info($logMessage);

        return view('xaman.login', [
            'qr' => $payload->refs->qrPng,
            'url' => $payload->next->always,
        ]);
    }

    public function loginCheck()
    {
        $uuid = Session::get('xumm_login_uuid');
        if (!$uuid) {
            $logMessage = '[loginCheck] Missing xumm_login_uuid in session';
            Log::warning($logMessage);
            SlackNotifier::warning($logMessage);
            return response()->json(['success' => false]);
        }

        $payload = $this->xaman->getPayload($uuid);
        $account = $payload->response->account ?? null;

//        $logMessage = '[loginCheck] Payload account: ' . ($account ?? 'null');
        Log::info($logMessage);
        SlackNotifier::info($logMessage);

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

        if (!$wallet || !$token) {
            $logMessage = '[loginStore] Missing wallet or access_token in request';
            Log::warning($logMessage);
            SlackNotifier::warning($logMessage);
            return response()->json(['success' => false]);
        }

        Session::put('wallet', $wallet);
        Session::put('xumm_token', $token);

        $logMessage = '[loginStore] Login stored for wallet: ' . $wallet;
        Log::info($logMessage);
        SlackNotifier::info($logMessage);

        return response()->json(['success' => true]);
    }

    public function logout(Request $request)
    {
        $logMessage = '[logout] User logging out: ' . (auth()->id() ?? 'none');
        Log::info($logMessage);
        SlackNotifier::info($logMessage);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function handleWebhook(Request $request)
    {
        $logMessage = '[handleWebhook] Webhook request received at: ' . now()->toDateTimeString();
        Log::info($logMessage, [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);
        SlackNotifier::info($logMessage . ' | IP: ' . $request->ip());

        $data = $request->all();
        $uuid = $data['meta']['payload_uuidv4'] ?? null;

        if (!$uuid) {
            $logMessage = '[handleWebhook] Missing UUID in webhook: ' . json_encode($data);
            Log::warning($logMessage);
            SlackNotifier::warning($logMessage);
            return response()->json(['success' => false], 400);
        }

        try {
            $payload = $this->xummPayment->getPayload($uuid);
            $logMessage = '[handleWebhook] Payload fetched for UUID: ' . $uuid;
            Log::info($logMessage, ['payload' => (array) $payload]);
            SlackNotifier::info($logMessage);
        } catch (NotFoundException $e) {
            $logMessage = '[handleWebhook] Payload not found for UUID: ' . $uuid . ', Error: ' . $e->getMessage();
            Log::error($logMessage);
            SlackNotifier::error($logMessage);
            return response()->json(['success' => false, 'message' => 'Payload not found'], 404);
        } catch (\Exception $e) {
            $logMessage = '[handleWebhook] Error fetching payload: ' . $e->getMessage() . ', UUID: ' . $uuid;
            Log::error($logMessage);
            SlackNotifier::error($logMessage);
            return response()->json(['success' => false, 'message' => 'Error fetching payload'], 500);
        }

        $wallet = $payload->response->account ?? null;
        if (!$wallet) {
            $logMessage = '[handleWebhook] Wallet not found in payload response for UUID: ' . $uuid;
            Log::warning($logMessage, ['payload' => (array) $payload]);
            SlackNotifier::warning($logMessage);
            return response()->json(['success' => false], 400);
        }

        // Log the raw request object for debugging
        $requestPayloadData = json_encode((array) $payload->payload->request, JSON_PRETTY_PRINT);
        Log::info("[handleWebhook] Raw payload request data for UUID: {$uuid}: {$requestPayloadData}");
        SlackNotifier::info("[handleWebhook] Raw payload request data for UUID: {$uuid}: ```{$requestPayloadData}```");

        // Check for transaction type, using txType as fallback
        $transactionType = $payload->payload->request->TransactionType ?? $payload->payload->txType ?? null;
        $logMessage = '[handleWebhook] Processing transaction type: ' . ($transactionType ?? 'none') . ', UUID: ' . $uuid;
        Log::info($logMessage);
        SlackNotifier::info($logMessage);

        if ($transactionType === 'SignIn') {
            $userToken = $data['userToken']['user_token'] ?? null;
            if (!$userToken) {
                $logMessage = '[handleWebhook] Missing userToken in login webhook: ' . json_encode($data);
                Log::warning($logMessage);
                SlackNotifier::warning($logMessage);
                return response()->json(['success' => false], 400);
            }

            Session::put('wallet', $wallet);
            $user = User::updateOrCreate(
                ['wallet' => $wallet],
                ['name' => $wallet, 'xumm_token' => $userToken, 'updated_at' => now()]
            );
            Auth::login($user);

            $logMessage = '[handleWebhook] Login successful for wallet: ' . $wallet;
            Log::info($logMessage);
            SlackNotifier::info($logMessage);
        } elseif ($transactionType === 'Payment') {
            $txid = $payload->response->txid ?? null;
            if (!$txid) {
                $logMessage = '[handleWebhook] Missing txid in payment webhook for UUID: ' . $uuid;
                Log::warning($logMessage, ['payload' => (array) $payload]);
                SlackNotifier::warning($logMessage);
                return response()->json(['success' => false], 400);
            }

            Session::forget('xumm_payment_uuid');
            $logMessage = '[handleWebhook] Payment successful for wallet: ' . $wallet . ', txid: ' . $txid;
            Log::info($logMessage);
            SlackNotifier::info($logMessage);
        } elseif (!$transactionType) {
            // Handle non-transaction payloads (e.g., expiration notices)
            $logMessage = '[handleWebhook] Non-transaction payload received for UUID: ' . $uuid . ', Data: ' . json_encode($data, JSON_PRETTY_PRINT);
            Log::info($logMessage);
            SlackNotifier::info($logMessage . "\nFull Payload: ```" . json_encode($payload, JSON_PRETTY_PRINT) . "```");
            return response()->json(['success' => true, 'message' => 'Non-transaction payload processed']);
        } else {
            $logMessage = '[handleWebhook] Unknown transaction type: ' . $transactionType . ', UUID: ' . $uuid;
            Log::warning($logMessage);
            SlackNotifier::warning($logMessage);
            return response()->json(['success' => false, 'message' => 'Unknown transaction type'], 400);
        }

        return response()->json(['success' => true, 'transactionType' => $transactionType]);
    }

    public function loginFinalize(Request $request)
    {
        $wallet = $request->input('wallet');
        if (!$wallet) {
            $logMessage = '[loginFinalize] Missing wallet in login finalize request';
            Log::warning($logMessage);
            SlackNotifier::warning($logMessage);
            return response()->json(['success' => false]);
        }

        $user = User::where('wallet', $wallet)->first();
        if (!$user) {
            $logMessage = '[loginFinalize] User not found for wallet: ' . $wallet;
            Log::warning($logMessage);
            SlackNotifier::warning($logMessage);
            return response()->json(['success' => false]);
        }

        Auth::login($user);
        Session::put('wallet', $wallet);

        $logMessage = '[loginFinalize] Login finalized for wallet: ' . $wallet;
        Log::info($logMessage);
        SlackNotifier::info($logMessage);

        return response()->json(['success' => true]);
    }

    public function handleCallback(Request $request)
    {
        $logMessage = '[handleCallback] Callback received';
        Log::info($logMessage);
        SlackNotifier::info($logMessage);
        return redirect()->route('welcome');
    }
}
