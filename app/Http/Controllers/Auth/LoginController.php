<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            return $this->redirectUserByRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        $plans = SubscriptionPlan::where('status', 'active')->get();
        return view('auth.register', compact('plans'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

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
