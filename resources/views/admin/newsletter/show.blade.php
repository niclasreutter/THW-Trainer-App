@extends('layouts.app')

@section('title', 'Newsletter Details - Admin')
@section('description', 'Newsletter-Details anzeigen')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Newsletter <span>Details</span></h1>
        <p class="page-subtitle">{{ $newsletter->subject }}</p>
    </header>

    <div class="stats-row">
        <div class="stat-pill">
            <span>Empfänger</span>
            <strong>{{ $newsletter->recipients_count }}</strong>
        </div>
        <div class="stat-pill">
            <span>Gesendet von</span>
            <strong>{{ $newsletter->sender->name ?? 'Unbekannt' }}</strong>
        </div>
        <div class="stat-pill">
            <span>Datum</span>
            <strong>{{ $newsletter->sent_at?->format('d.m.Y H:i') ?? '-' }}</strong>
        </div>
    </div>

    <div class="bento-grid">
        <div class="glass-gold bento-main" style="padding: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
                Inhalt
            </h2>
            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255, 255, 255, 0.1);">
                <div id="newsletter-content" style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                    {!! $newsletter->content !!}
                </div>
            </div>
        </div>

        <div class="glass-tl bento-side" style="padding: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem;">
                Aktionen
            </h2>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('admin.newsletter.create') }}" class="btn-primary" style="text-align: center;">
                    Neuer Newsletter
                </a>
            </div>
        </div>
    </div>
</div>

<style>
#newsletter-content .info-card {
    background-color: #eff6ff;
    border: 2px solid #3b82f6;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}
#newsletter-content .warning-card {
    background-color: #fef3c7;
    border: 2px solid #f59e0b;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}
#newsletter-content .success-card {
    background-color: #f0fdf4;
    border: 2px solid #22c55e;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}
#newsletter-content .error-card {
    background-color: #fef2f2;
    border: 2px solid #ef4444;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}
#newsletter-content .glow-button {
    display: inline-block;
    background: linear-gradient(to right, #2563eb, #1d4ed8);
    color: white;
    padding: 15px 30px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
}
#newsletter-content .stat-box {
    background-color: #f3f4f6;
    padding: 15px;
    border-radius: 8px;
    margin: 10px 0;
}
#newsletter-content .stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #2563eb;
}
#newsletter-content .stat-label {
    font-size: 14px;
    color: #6b7280;
}
</style>
@endsection
