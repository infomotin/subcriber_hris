<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GatewayConfig;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    public function showChallenge()
    {
        $config = GatewayConfig::find(1);
        if (!$config || !$config->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        if (session('two_factor_verified')) {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();

        $hasValidOtp = OtpCode::where('user_id', $user->id)
            ->where('type', 'two_factor')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->exists();

        if (!$hasValidOtp) {
            $this->sendOtp($user);
        }

        return view('auth.two-factor-challenge');
    }

    public function resendOtp(Request $request)
    {
        $user = Auth::user();

        OtpCode::where('user_id', $user->id)
            ->where('type', 'two_factor')
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $this->sendOtp($user);

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = Auth::user();

        $otp = OtpCode::where('user_id', $user->id)
            ->where('type', 'two_factor')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'No valid OTP found. Please request a new one.']);
        }

        if ($request->code !== $otp->code) {
            return back()->withErrors(['code' => 'Invalid OTP code. Please try again.']);
        }

        $otp->markAsUsed();

        $request->session()->put('two_factor_verified', true);

        $redirectTo = $request->session()->get('url.intended', route('dashboard'));

        return redirect()->to($redirectTo)->with('success', 'Two-factor authentication verified successfully.');
    }

    private function sendOtp($user): void
    {
        $code = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'two_factor',
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::raw("Your OTP code is: {$code}\n\nThis code expires in 5 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Two-Factor Authentication OTP');
        });
    }
}
