@extends('errors.layout')

@section('title', '503 - Wartungsmodus')

@section('content')
<div class="glass-cyan p-8 text-center">
    <!-- Error Code -->
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4 animate-pulse-glow" style="background: rgba(6, 182, 212, 0.15);">
            <i class="bi bi-gear text-4xl" style="color: #06b6d4;"></i>
        </div>
        <h1 class="text-7xl font-extrabold mb-2">
            <span class="text-gradient-gold">503</span>
        </h1>
        <h2 class="text-2xl font-bold mb-3" style="color: var(--thw-blue);">
            Wartungsmodus
        </h2>
        <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">
            Wir führen gerade Wartungsarbeiten durch, um den THW-Trainer
            für dich noch besser zu machen. Bitte habe einen Moment Geduld.
        </p>
    </div>

    <!-- Loading Indicator -->
    <div class="flex justify-center gap-3 mb-6">
        <span class="w-3 h-3 rounded-full animate-bounce" style="background: var(--gradient-gold-135); animation-delay: 0s;"></span>
        <span class="w-3 h-3 rounded-full animate-bounce" style="background: var(--gradient-gold-135); animation-delay: 0.2s;"></span>
        <span class="w-3 h-3 rounded-full animate-bounce" style="background: var(--gradient-gold-135); animation-delay: 0.4s;"></span>
    </div>

    <!-- Info Box -->
    <div class="alert-glass info mb-6" style="text-align: left;">
        <i class="bi bi-info-circle" style="color: var(--info);"></i>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
            <strong>Info:</strong> Die Wartungsarbeiten dauern in der Regel
            nur wenige Minuten. Danke für deine Geduld!
        </p>
    </div>

    <!-- Action Button -->
    <div class="flex flex-col gap-3">
        <button onclick="window.location.reload()" class="btn-primary">
            Erneut versuchen
        </button>
    </div>
</div>

<style>
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce {
        animation: bounce 1.4s ease-in-out infinite;
    }
</style>
@endsection
