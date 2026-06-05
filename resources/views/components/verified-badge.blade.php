@props(['user', 'size' => 'text-xs'])

@if($user && $user->role === 'provider' && optional($user->profile)->is_verified_provider)
    <i class="fas fa-check-circle text-blue-500 {{ $size }} ml-1" title="Verified Provider"></i>
@endif
