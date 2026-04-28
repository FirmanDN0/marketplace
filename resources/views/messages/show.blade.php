@extends('layouts.app')
@section('title', 'Conversation')
@section('content')
<div class="min-h-screen py-8">
<div class="max-w-4xl mx-auto px-4 sm:px-6">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        @if(optional($other)->avatar)
            <img src="{{ Storage::url($other->avatar) }}" alt="{{ $other->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
        @else
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0">
                {{ strtoupper(substr(optional($other)->name ?? '?', 0, 1)) }}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h1 class="text-lg font-bold text-gray-900">{{ optional($other)->name }}</h1>
            @if($conversation->service)
                <p class="text-sm text-gray-500">About: <a href="{{ route('services.show', $conversation->service->slug) }}" class="text-blue-600 hover:underline">{{ $conversation->service->title }}</a></p>
            @endif
        </div>
    </div>

    {{-- Chat Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="height: 65vh;">
        {{-- Messages Area --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-4" id="msg-area">
            @forelse($messages as $msg)
                @php $mine = $msg->sender_id === auth()->id(); @endphp
                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[75%]">
                        @if(!$mine)
                            <div class="text-xs text-gray-400 mb-1 ml-1">{{ optional($msg->sender)->name }}</div>
                        @endif
                        <div class="{{ $mine ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md' : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-md' }} px-4 py-3">
                            <div class="text-sm whitespace-pre-wrap break-words">{{ $msg->message_text }}</div>
                            @if($msg->attachment_path)
                                <div class="mt-2 pt-2 border-t {{ $mine ? 'border-blue-500' : 'border-gray-200' }}">
                                    <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank"
                                       class="{{ $mine ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-700' }} text-sm inline-flex items-center gap-1">
                                        <i class="fas fa-paperclip"></i> {{ $msg->attachment_name }}
                                    </a>
                                </div>
                            @endif
                            <div class="text-xs {{ $mine ? 'text-blue-200' : 'text-gray-400' }} mt-1 text-right">{{ $msg->created_at->format('d M H:i') }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-comments text-4xl mb-3"></i>
                    <p>No messages yet. Say hello!</p>
                </div>
            @endforelse
        </div>

        {{-- Send Form --}}
        <div class="border-t border-gray-100 p-4 bg-gray-50" x-data="{ fileName: '' }">
            <form method="POST" action="{{ route('messages.send', $conversation->id) }}" enctype="multipart/form-data" class="flex flex-col gap-2">
                @csrf
                <div x-show="fileName" style="display: none;" class="text-xs text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg w-max flex items-center gap-2">
                    <i class="fas fa-file-alt"></i> <span x-text="fileName" class="font-medium"></span>
                    <button type="button" @click="fileName = ''; $refs.fileInput.value = ''" class="text-red-500 hover:text-red-700 ml-2" title="Remove file"><i class="fas fa-times"></i></button>
                </div>
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <textarea name="message_text" rows="2" 
                                  @keydown.enter.prevent="$event.shiftKey ? $el.value += '\n' : $el.closest('form').submit()"
                                  placeholder="Type a message (Enter to send, Shift+Enter for new line)&#8230;"
                                  class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        @error('message_text')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="flex items-center gap-2 pb-1">
                        <label title="Attach file" class="cursor-pointer text-gray-400 hover:text-blue-600 transition p-2">
                            <i class="fas fa-paperclip text-lg"></i>
                            <input type="file" name="attachment" class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                        </label>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
<script>document.getElementById('msg-area').scrollTop=document.getElementById('msg-area').scrollHeight</script>
@endsection
