<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GatewayConfig;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        $securityConfig = GatewayConfig::find(1);

        $captchaData = $this->generateCaptcha();

        return view('auth.login', compact('securityConfig', 'captchaData'));
    }

    private function generateCaptcha(): array
    {
        $a = rand(1, 20);
        $b = rand(1, 20);
        $op = ['+', '-'][array_rand(['+', '-'])];
        $result = $op === '+' ? $a + $b : $a - $b;

        session()->put('captcha_result', $result);

        return [
            'question' => "{$a} {$op} {$b} = ?",
        ];
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];

        $config = GatewayConfig::find(1);

        if ($config && $config->captcha_enabled) {
            $rules['captcha_answer'] = 'required|integer';
        }

        $credentials = $request->validate($rules);

        if ($config && $config->captcha_enabled) {
            $expected = $request->session()->get('captcha_result');
            if ((int) $request->input('captcha_answer') !== $expected) {
                return back()->withErrors(['captcha' => 'Incorrect math answer. Please try again.'])
                    ->onlyInput('email');
            }
        }

        if ($config && $config->honeypot_enabled) {
            if ($request->input('hp_name') !== '' || $request->input('hp_time') === '') {
                return back()->withErrors(['email' => 'Invalid request.'])->onlyInput('email');
            }

            $hpTime = (int) $request->input('hp_time');
            if (time() - $hpTime < 3) {
                return back()->withErrors(['email' => 'Please wait a moment before submitting.'])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            $request->session()->forget('two_factor_verified');

            if ($config && $config->two_factor_enabled) {
                return redirect()->route('two-factor.challenge');
            }

            return $this->redirectUserByRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        $plans = SubscriptionPlan::where('status', 'active')->get();
        $securityConfig = GatewayConfig::find(1);

        $captchaData = $this->generateCaptcha();

        return view('auth.register', compact('plans', 'securityConfig', 'captchaData'));
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ];

        $config = GatewayConfig::find(1);

        if ($config && $config->captcha_enabled) {
            $rules['captcha_answer'] = 'required|integer';
        }

        $validated = $request->validate($rules);

        if ($config && $config->captcha_enabled) {
            $expected = $request->session()->get('captcha_result');
            if ((int) $request->input('captcha_answer') !== $expected) {
                return back()->withErrors(['captcha' => 'Incorrect math answer. Please try again.'])
                    ->onlyInput('name', 'email');
            }
        }

        if ($config && $config->honeypot_enabled) {
            if ($request->input('hp_name') !== '' || $request->input('hp_time') === '') {
                return back()->withErrors(['name' => 'Invalid request.'])->onlyInput('name', 'email');
            }

            $hpTime = (int) $request->input('hp_time');
            if (time() - $hpTime < 3) {
                return back()->withErrors(['name' => 'Please wait a moment before submitting.'])->onlyInput('name', 'email');
            }
        }

        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole('Subscriber');

        $tenant = Tenant::create([
            'name' => $validated['name'] . ' Organization',
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'tenant_token' => strtoupper(Str::random(16)),
            'status' => 'active',
            'expires_at' => now()->addMonth(),
            'max_devices' => $plan->max_devices,
        ]);

        $user->update(['tenant_id' => $tenant->id]);

        Auth::login($user);

        return redirect()->route('subscriber.dashboard')
            ->with('success', 'Account registered successfully! Welcome to your ZKTeco ADMS SaaS Portal.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have logged out successfully.');
    }

    protected function redirectUserByRole(User $user)
    {
        if ($user->hasRole('System Admin')) {
            return redirect()->route('admin.system.dashboard');
        }

        if ($user->hasRole('Business Admin')) {
            return redirect()->route('admin.business.dashboard');
        }

        if ($user->hasRole('Subscriber')) {
            return redirect()->route('subscriber.dashboard');
        }

        if ($user->hasRole('Demo User')) {
            return redirect()->route('demo.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }
}
