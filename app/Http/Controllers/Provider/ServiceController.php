<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('provider_id', auth()->id())
            ->with(['category', 'packages'])
            ->withTrashed()
            ->latest()
            ->paginate(15);

        return view('provider.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::active()->with('children')->roots()->get();
        return view('provider.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:50',
            'tags'        => 'nullable|string',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpg,jpeg,png|max:3072',
            // basic
            'basic_name'      => 'required|string|max:100',
            'basic_desc'      => 'required|string',
            'basic_price'     => 'required|numeric|min:1',
            'basic_days'      => 'required|integer|min:1',
            'basic_revisions' => 'required|integer|min:-1',
            'basic_features'  => 'nullable|string',
            // standard
            'standard_name'      => 'nullable|string|max:100',
            'standard_desc'      => 'nullable|string',
            'standard_price'     => 'nullable|numeric|min:1',
            'standard_days'      => 'nullable|integer|min:1',
            'standard_revisions' => 'nullable|integer|min:-1',
            'standard_features'  => 'nullable|string',
            // premium
            'premium_name'      => 'nullable|string|max:100',
            'premium_desc'      => 'nullable|string',
            'premium_price'     => 'nullable|numeric|min:1',
            'premium_days'      => 'nullable|integer|min:1',
            'premium_revisions' => 'nullable|integer|min:-1',
            'premium_features'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $request) {
            $service = Service::create([
                'provider_id' => auth()->id(),
                'category_id' => $data['category_id'],
                'title'       => $data['title'],
                'slug'        => Str::slug($data['title']) . '-' . uniqid(),
                'description' => $data['description'],
                'tags'        => $data['tags'] ? array_filter(array_map('trim', explode(',', $data['tags']))) : null,
                'status'      => 'draft',
            ]);

            $this->savePackage($service, 'basic', $data);

            if (!empty($data['standard_name'])) {
                $this->savePackage($service, 'standard', $data);
            }

            if (!empty($data['premium_name'])) {
                $this->savePackage($service, 'premium', $data);
            }

            if ($request->hasFile('images')) {
                $isFirst = true;
                foreach ($request->file('images') as $i => $img) {
                    $path = $img->store('services', 'public');
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'image_path' => $path,
                        'is_cover'   => $isFirst,
                        'sort_order' => $i,
                    ]);
                    $isFirst = false;
                }
            }
        });

        return redirect()->route('provider.services.index')->with('success', 'Service created. Awaiting review.');
    }

    public function edit(Service $service)
    {
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::active()->with('children')->roots()->get();
        $packages   = $service->packages()->get()->keyBy('package_type');
        $service->load('images');

        return view('provider.services.edit', compact('service', 'categories', 'packages'));
    }

    public function update(Request $request, Service $service)
    {
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:50',
            'tags'        => 'nullable|string',
            'basic_name'      => 'required|string|max:100',
            'basic_desc'      => 'required|string',
            'basic_price'     => 'required|numeric|min:1',
            'basic_days'      => 'required|integer|min:1',
            'basic_revisions' => 'required|integer|min:-1',
            'basic_features'  => 'nullable|string',
            'standard_name'      => 'nullable|string|max:100',
            'standard_desc'      => 'nullable|string',
            'standard_price'     => 'nullable|numeric|min:1',
            'standard_days'      => 'nullable|integer|min:1',
            'standard_revisions' => 'nullable|integer|min:-1',
            'standard_features'  => 'nullable|string',
            'premium_name'      => 'nullable|string|max:100',
            'premium_desc'      => 'nullable|string',
            'premium_price'     => 'nullable|numeric|min:1',
            'premium_days'      => 'nullable|integer|min:1',
            'premium_revisions' => 'nullable|integer|min:-1',
            'premium_features'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $service) {
            $service->update([
                'category_id' => $data['category_id'],
                'title'       => $data['title'],
                'description' => $data['description'],
                'tags'        => $data['tags'] ? array_filter(array_map('trim', explode(',', $data['tags']))) : null,
                'status'      => 'draft', // re-review on edit
            ]);

            $this->savePackage($service, 'basic', $data);

            if (!empty($data['standard_name'])) {
                $this->savePackage($service, 'standard', $data);
            } else {
                $service->packages()->where('package_type', 'standard')->delete();
            }

            if (!empty($data['premium_name'])) {
                $this->savePackage($service, 'premium', $data);
            } else {
                $service->packages()->where('package_type', 'premium')->delete();
            }
        });

        return redirect()->route('provider.services.index')->with('success', 'Service updated.');
    }

    public function toggleStatus(Service $service)
    {
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $newStatus = $service->status === 'active' ? 'paused' : 'active';
        $service->update(['status' => $newStatus]);

        return back()->with('success', "Service {$newStatus}.");
    }

    public function destroy(Service $service)
    {
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $service->update(['status' => 'deleted']);
        $service->delete();

        return redirect()->route('provider.services.index')->with('success', 'Service deleted.');
    }

    private function savePackage(Service $service, string $type, array $data): void
    {
        $prefix   = $type . '_';
        $features = !empty($data[$prefix . 'features'])
            ? array_filter(array_map('trim', explode(',', $data[$prefix . 'features'])))
            : null;

        $service->packages()->updateOrCreate(
            ['package_type' => $type],
            [
                'name'          => $data[$prefix . 'name'],
                'description'   => $data[$prefix . 'desc'],
                'price'         => $data[$prefix . 'price'],
                'delivery_days' => $data[$prefix . 'days'],
                'revisions'     => $data[$prefix . 'revisions'],
                'features'      => $features,
                'is_active'     => true,
            ]
        );
    }
}
