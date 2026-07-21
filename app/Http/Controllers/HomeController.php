<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;

class HomeController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('status', 'active')->get();
        $totalTenants = Tenant::where('is_demo', false)->count();

        return view('landing', compact('plans', 'totalTenants'));
    }
}
