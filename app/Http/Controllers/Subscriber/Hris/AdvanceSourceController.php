<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\AdvanceSource;
use App\Models\Tenant;
use Illuminate\Http\Request;

class AdvanceSourceController extends Controller
{
    public function index()
    {
        $sources = AdvanceSource::orderBy('id', 'desc')->paginate(15);
        return view('subscriber.hris.advance-sources.index', compact('sources'));
    }

    public function create()
    {
        return view('subscriber.hris.advance-sources.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30',
            'is_active' => 'boolean',
        ]);
        $validated['tenant_id'] = $tenant->id;
        $validated['is_active'] = $request->boolean('is_active', true);
        AdvanceSource::create($validated);
        return redirect()->route('subscriber.hris.advance-sources.index')->with('success', 'Advance source created.');
    }

    public function edit(AdvanceSource $advanceSource)
    {
        return view('subscriber.hris.advance-sources.edit', ['source' => $advanceSource]);
    }

    public function update(Request $request, AdvanceSource $advanceSource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $advanceSource->update($validated);
        return redirect()->route('subscriber.hris.advance-sources.index')->with('success', 'Advance source updated.');
    }

    public function destroy(AdvanceSource $advanceSource)
    {
        $advanceSource->delete();
        return redirect()->route('subscriber.hris.advance-sources.index')->with('success', 'Advance source deleted.');
    }
}
