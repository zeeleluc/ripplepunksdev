<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\XummService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Xrpl\XummSdkPhp\Exception\Http\NotFoundException;

class XamanController extends Controller
{
    protected XummService $xaman;

    public function __construct(XummService $xumm)
    {
        $this->xaman = $xumm;
    }

    public function showLoginQr()
    {
        $payload = $this->xaman->createLoginPayload();

        // Store UUID in session or DB to verify login later
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

        // Defensive checks, just in case
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

        // You should store this with your User model/session
        Session::put('wallet', $wallet);
        Session::put('xumm_token', $token);

        return response()->json(['success' => true]);
    }

    public function logout()
    {
        Session::forget(['wallet', 'xumm_token', 'xumm_login_uuid']);
        return redirect('/');
    }

    public function handleWebhook(Request $request)
    {
        Log::info('webhook', $request->all());

        $data = $request->all();
        $uuid = $data['meta']['payload_uuidv4'] ?? null;
        $userToken = $data['userToken']['user_token'] ?? null;

        if (!$uuid || !$userToken) {
            Log::warning('Missing UUID or userToken in webhook');
            return response()->json(['success' => false], 400);
        }

        try {
            $payload = $this->xaman->getPayload($uuid);
        } catch (NotFoundException $e) {
            Log::error('Payload not found for UUID: ' . $uuid);
            return response()->json(['success' => false, 'message' => 'Payload not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching payload: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching payload'], 500);
        }

        $wallet = $payload->response->account ?? null;
        Session::put('wallet', $wallet);

        if (!$wallet) {
            Log::warning('Wallet not found in payload response for UUID: ' . $uuid);
            return response()->json(['success' => false], 400);
        }

        // Create or update user
        $user = User::updateOrCreate(
            ['wallet' => $wallet],
            ['name' => $wallet, 'xumm_token' => $userToken]
        );

        // Log in user
        Auth::login($user);

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
        Log::info('callback');
        return redirect()->route('welcome'); // Or any page after login
    }
}
