@extends('layouts.app')
@section('title', 'Create User')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">Create User</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">User Details</h3></div>
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required placeholder="Min 8 characters"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                        <select id="role" name="role" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="provider" {{ old('role') === 'provider' ? 'selected' : '' }}>Provider</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select id="status" name="status" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                            <option value="banned">Banned</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">Create User</button>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
