@extends('layouts.app')
@section('title', 'Create Service')
@section('content')

<div class="max-w-4xl">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('provider.services.index') }}" class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create New Service</h1>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $e)
                <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('provider.services.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Service Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Service Information</h3>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Service Title</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required
                           placeholder="e.g. I will design a professional logo for your brand"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700">
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat)
                            <optgroup label="{{ $cat->name }}">
                                @if(!$cat->children->count())
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endif
                                @foreach($cat->children as $sub)
                                    <option value="{{ $sub->id }}" {{ old('category_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-gray-400">(min 50 characters)</span></label>
                    <textarea id="description" name="description" rows="6" required
                              placeholder="Describe your service in detail..."
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition resize-none">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-1.5">Tags <span class="text-gray-400">(comma separated)</span></label>
                        <input id="tags" type="text" name="tags" value="{{ old('tags') }}"
                               placeholder="logo, design, branding"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-1.5">Gallery Images <span class="text-gray-400">(max 5)</span></label>
                        <input id="images" type="file" name="images[]" multiple accept="image/*"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 file:font-medium file:text-sm hover:file:bg-blue-100 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- Packages --}}
        @foreach(['basic' => ['label' => 'Basic', 'required' => true], 'standard' => ['label' => 'Standard', 'required' => false], 'premium' => ['label' => 'Premium', 'required' => false]] as $type => $meta)
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $meta['label'] }} Package</h3>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $meta['required'] ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $meta['required'] ? 'Required' : 'Optional' }}
                </span>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Package Name</label>
                        <input type="text" name="{{ $type }}_name" value="{{ old("{$type}_name") }}"
                               {{ $meta['required'] ? 'required' : '' }} placeholder="e.g. Basic"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Price (Rp)</label>
                        <input type="number" name="{{ $type }}_price" value="{{ old("{$type}_price") }}"
                               {{ $meta['required'] ? 'required' : '' }} min="1000" step="1000" placeholder="0"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery Days</label>
                        <input type="number" name="{{ $type }}_days" value="{{ old("{$type}_days") }}"
                               {{ $meta['required'] ? 'required' : '' }} min="1" placeholder="3"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Revisions <span class="text-gray-400">(-1 = unlimited)</span></label>
                        <input type="number" name="{{ $type }}_revisions"
                               value="{{ old("{$type}_revisions", $type === 'premium' ? -1 : ($type === 'standard' ? 2 : 1)) }}"
                               {{ $meta['required'] ? 'required' : '' }} min="-1"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Short Description</label>
                    <input type="text" name="{{ $type }}_desc" value="{{ old("{$type}_desc") }}"
                           {{ $meta['required'] ? 'required' : '' }} placeholder="What's included in this package"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Features <span class="text-gray-400">(comma separated)</span></label>
                    <input type="text" name="{{ $type }}_features" value="{{ old("{$type}_features") }}"
                           placeholder="Feature 1, Feature 2"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                </div>
            </div>
        </div>
        @endforeach

        <div class="flex items-center gap-4">
            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50">
                Create Service
            </button>
            <a href="{{ route('provider.services.index') }}" class="px-6 py-3 bg-gray-100 text-gray-600 font-medium rounded-xl hover:bg-gray-200 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
