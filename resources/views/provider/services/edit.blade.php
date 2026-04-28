@extends('layouts.app')
@section('title', 'Edit Service')
@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Service</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

<form method="POST" action="{{ route('provider.services.update', $service->id) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    @php
        $basic    = $packages->get('basic');
        $standard = $packages->get('standard');
        $premium  = $packages->get('premium');
    @endphp

    {{-- Service Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Service Information</h3></div>
        <div class="p-5 space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Service Title</label>
                <input id="title" type="text" name="title" value="{{ old('title', $service->title) }}" required
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                <select id="category_id" name="category_id" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select a category…</option>
                    @foreach($categories as $cat)
                        <optgroup label="{{ $cat->name }}">
                            @if(!$cat->children->count())
                                <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endif
                            @foreach($cat->children as $sub)
                                <option value="{{ $sub->id }}" {{ old('category_id', $service->category_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="6" required
                          class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('description', $service->description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-1.5">Tags <span class="text-gray-400 font-normal">(comma separated)</span></label>
                    <input id="tags" type="text" name="tags" value="{{ old('tags', is_array($service->tags) ? implode(', ', $service->tags) : $service->tags) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-1.5">Add Gallery Images <span class="text-gray-400 font-normal">(max 5 total)</span></label>
                    <input id="images" type="file" name="images[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                </div>
            </div>
        </div>
    </div>

    {{-- Basic Package --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Basic Package</h3>
            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">Required</span>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Package Name</label>
                    <input type="text" name="basic_name" required value="{{ old('basic_name', optional($basic)->name) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Price (Rp)</label>
                    <input type="number" name="basic_price" required min="1000" step="1000" value="{{ old('basic_price', optional($basic)->price) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery Days</label>
                    <input type="number" name="basic_days" required min="1" value="{{ old('basic_days', optional($basic)->delivery_days) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Revisions <span class="text-gray-400 font-normal">(-1 = unlimited)</span></label>
                    <input type="number" name="basic_revisions" required min="-1" value="{{ old('basic_revisions', optional($basic)->revisions) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Short Description</label>
                <input type="text" name="basic_desc" required value="{{ old('basic_desc', optional($basic)->description) }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Features <span class="text-gray-400 font-normal">(comma separated)</span></label>
                <input type="text" name="basic_features" value="{{ old('basic_features', optional($basic)->features ? implode(', ', optional($basic)->features) : '') }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    {{-- Standard Package --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Standard Package</h3>
            <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2.5 py-1 rounded-full">Optional</span>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Package Name</label>
                    <input type="text" name="standard_name" value="{{ old('standard_name', optional($standard)->name) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Price (Rp)</label>
                    <input type="number" name="standard_price" min="1000" step="1000" value="{{ old('standard_price', optional($standard)->price) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery Days</label>
                    <input type="number" name="standard_days" min="1" value="{{ old('standard_days', optional($standard)->delivery_days) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Revisions</label>
                    <input type="number" name="standard_revisions" min="-1" value="{{ old('standard_revisions', optional($standard)->revisions) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Short Description</label>
                <input type="text" name="standard_desc" value="{{ old('standard_desc', optional($standard)->description) }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Features <span class="text-gray-400 font-normal">(comma separated)</span></label>
                <input type="text" name="standard_features" value="{{ old('standard_features', $standard ? implode(', ', $standard->features ?? []) : '') }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    {{-- Premium Package --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Premium Package</h3>
            <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2.5 py-1 rounded-full">Optional</span>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Package Name</label>
                    <input type="text" name="premium_name" value="{{ old('premium_name', optional($premium)->name) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Price (Rp)</label>
                    <input type="number" name="premium_price" min="1000" step="1000" value="{{ old('premium_price', optional($premium)->price) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery Days</label>
                    <input type="number" name="premium_days" min="1" value="{{ old('premium_days', optional($premium)->delivery_days) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Revisions</label>
                    <input type="number" name="premium_revisions" min="-1" value="{{ old('premium_revisions', optional($premium)->revisions) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Short Description</label>
                <input type="text" name="premium_desc" value="{{ old('premium_desc', optional($premium)->description) }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Features <span class="text-gray-400 font-normal">(comma separated)</span></label>
                <input type="text" name="premium_features" value="{{ old('premium_features', $premium ? implode(', ', $premium->features ?? []) : '') }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">Update Service</button>
        <a href="{{ route('provider.services.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition">Cancel</a>
    </div>
</form>
</div>
@endsection
