@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Push <span>Benachrichtigungen</span></h1>
        <p class="page-subtitle">Sende Push-Nachrichten an alle oder einzelne Ortsverbande</p>
    </header>

    @if(session('success'))
        <div class="glass-success p-4 rounded-xl mb-6">
            <p class="text-white text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bento-grid">
        {{-- Send Form --}}
        <div class="glass-gold bento-main">
            <h2 class="text-lg font-bold text-white mb-4">Nachricht senden</h2>

            <form method="POST" action="{{ route('admin.push.send') }}" x-data="{ targetType: 'all' }">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm text-white/70 mb-1">Titel</label>
                    <input type="text" name="title" required maxlength="100"
                           class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/40 focus:outline-none focus:border-amber-400/60"
                           placeholder="Benachrichtigungstitel" value="{{ old('title') }}">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-white/70 mb-1">Nachricht</label>
                    <textarea name="message" required maxlength="500" rows="3"
                              class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/40 focus:outline-none focus:border-amber-400/60 resize-none"
                              placeholder="Nachrichtentext">{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-white/70 mb-1">Empfaenger</label>
                    <select name="target_type" x-model="targetType"
                            class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400/60">
                        <option value="all">Alle Nutzer</option>
                        <option value="ortsverband">Ortsverband</option>
                    </select>
                </div>

                <div class="mb-6" x-show="targetType === 'ortsverband'" x-cloak>
                    <label class="block text-sm text-white/70 mb-1">Ortsverband</label>
                    <select name="target_id"
                            class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400/60">
                        <option value="">Bitte waehlen...</option>
                        @foreach($ortsverbande as $ov)
                            <option value="{{ $ov->id }}">{{ $ov->name }}</option>
                        @endforeach
                    </select>
                    @error('target_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary w-full"
                        onclick="return confirm('Push-Benachrichtigung wirklich senden?')">
                    Senden
                </button>
            </form>
        </div>

        {{-- History --}}
        <div class="glass bento-side">
            <h2 class="text-lg font-bold text-white mb-4">Verlauf</h2>

            @forelse($messages as $msg)
                <div class="border-b border-white/10 pb-3 mb-3 last:border-0">
                    <p class="text-white text-sm font-semibold">{{ $msg->title }}</p>
                    <p class="text-white/60 text-xs mt-1">{{ Str::limit($msg->message, 80) }}</p>
                    <div class="flex justify-between mt-2 text-xs text-white/40">
                        <span>{{ $msg->target_type === 'all' ? 'Alle' : $msg->ortsverband?->name }}</span>
                        <span>{{ $msg->recipients_count }} Empf.</span>
                    </div>
                    <p class="text-xs text-white/30 mt-1">{{ $msg->created_at->format('d.m.Y H:i') }} — {{ $msg->admin?->name }}</p>
                </div>
            @empty
                <p class="text-white/40 text-sm">Noch keine Nachrichten gesendet.</p>
            @endforelse

            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
