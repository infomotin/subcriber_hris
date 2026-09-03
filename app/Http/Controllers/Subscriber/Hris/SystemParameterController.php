<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\SystemParameter;
use Illuminate\Http\Request;

class SystemParameterController extends Controller
{
    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'No tenant found.');
        }

        $query = SystemParameter::where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $parameters = $query->orderBy('key_name')->paginate(15)->withQueryString();

        return view('subscriber.hris.system-parameters.index', compact('parameters'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'No tenant found.');
        }

        $validated = $request->validate([
            'key_name' => 'required|string|max:100',
            'value' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['key_name'] = strtoupper(trim($validated['key_name']));

        $exists = SystemParameter::where('tenant_id', $tenant->id)
            ->where('key_name', $validated['key_name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['key_name' => 'A parameter with this key already exists.'])->withInput();
        }

        SystemParameter::create($validated);

        return redirect()->route('subscriber.hris.system-parameters.index')
            ->with('success', 'Parameter created successfully.');
    }

    public function update(Request $request, SystemParameter $parameter)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant || $parameter->tenant_id !== $tenant->id) {
            return redirect()->route('subscriber.hris.system-parameters.index')
                ->with('error', 'Parameter not found.');
        }

        $validated = $request->validate([
            'key_name' => 'required|string|max:100',
            'value' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['key_name'] = strtoupper(trim($validated['key_name']));

        $exists = SystemParameter::where('tenant_id', $tenant->id)
            ->where('key_name', $validated['key_name'])
            ->where('id', '!=', $parameter->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['key_name' => 'A parameter with this key already exists.'])->withInput();
        }

        $parameter->update($validated);

        return redirect()->route('subscriber.hris.system-parameters.index')
            ->with('success', 'Parameter updated successfully.');
    }

    public function destroy(SystemParameter $parameter)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant || $parameter->tenant_id !== $tenant->id) {
            return redirect()->route('subscriber.hris.system-parameters.index')
                ->with('error', 'Parameter not found.');
        }

        $parameter->delete();

        return redirect()->route('subscriber.hris.system-parameters.index')
            ->with('success', 'Parameter deleted.');
    }
}
