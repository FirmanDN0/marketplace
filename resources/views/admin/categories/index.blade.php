@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-teal-500/20">
                <i class="fas fa-tags text-lg"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kategori</h1>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl text-sm font-bold transition-all flex items-center gap-2 self-start sm:self-auto shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
            <i class="fas fa-plus text-xs"></i> Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($categories as $cat)
            <div class="px-4 py-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900">
                        @if($cat->parent_id)<span class="text-gray-300 mr-1"><i class="fas fa-angle-right text-xs"></i></span>@endif
                        {{ $cat->name }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $cat->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span>{{ optional($cat->parent)->name ?? 'Root' }}</span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-400">No categories yet.</div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Name</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Parent</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Slug</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Active</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Order</th>
                        <th class="px-5 py-3 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3 font-medium text-gray-900">
                        @if($cat->parent_id)<span class="text-gray-300 mr-1"><i class="fas fa-angle-right text-xs"></i></span>@endif
                        {{ $cat->name }}
                    </td>
                    <td class="px-5 py-3 text-gray-500 hidden md:table-cell">{{ optional($cat->parent)->name ?? 'Root' }}</td>
                    <td class="px-5 py-3 text-gray-500 font-mono text-xs hidden md:table-cell">{{ $cat->slug }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 hidden md:table-cell">{{ $cat->sort_order }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No categories yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection
