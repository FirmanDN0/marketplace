<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['provider', 'category'])->withTrashed();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $services = $query->latest()->paginate(20)->withQueryString();
        return view('admin.services.index', compact('services'));
    }

    public function show(Service $service)
    {
        $service->load(['provider.profile', 'category', 'packages', 'images', 'reviews']);
        return view('admin.services.show', compact('service'));
    }

    public function updateStatus(Request $request, Service $service)
    {
        $data = $request->validate([
            'status'           => 'required|in:draft,active,paused,rejected,deleted',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $service->update($data);

        \App\Services\NotificationService::send(
            $service->provider_id,
            'service_status_update',
            'Service Status Updated',
            "Your service \"{$service->title}\" status has been changed to {$data['status']}.",
            ['service_id' => $service->id],
            route('provider.services.show', $service->id)
        );

        return back()->with('success', 'Service status updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }
}
