@extends('layouts.app')

@section('title', $ortsverband->name . ' - Lernpools')

@push('styles')
<style>
    .lernpool-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .lernpool-card:hover {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .lernpool-info {
        flex: 1;
        margin-right: 1rem;
    }

    .lernpool-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .lernpool-description {
        font-size: 0.95rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }

    .lernpool-meta {
        display: flex;
        gap: 1.5rem;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .lernpool-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-small {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0, 51, 127, 0.3);
    }

    .btn-outline {
        background: white;
        border: 1px solid #d1d5db;
        color: #1f2937;
    }

    .btn-outline:hover {
        background: #f9fafb;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        transition: width 0.3s ease;
    }

    .section-header {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        background: #f9fafb;
        border-radius: 1rem;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $ortsverband->name }}</h1>
        <p class="text-gray-600">Verfügbare Lernpools</p>
    </div>

    <!-- Tags-Filter -->
    @if($allTags->isNotEmpty())
        <div style="margin-bottom: 1.5rem; padding: 1rem; background: white; border-radius: 1rem; border: 1px solid #e5e7eb;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <span style="font-weight: 600; color: #1f2937; white-space: nowrap;">🏷️ Filter:</span>
                <a href="{{ route('ortsverband.lernpools.list', $ortsverband) }}"
                   class="btn-small {{ !$selectedTag ? 'btn-primary' : 'btn-outline' }}">
                    Alle
                </a>
                @foreach($allTags as $tag)
                    <a href="{{ route('ortsverband.lernpools.list', ['ortsverband' => $ortsverband, 'tag' => $tag]) }}"
                       class="btn-small {{ $selectedTag === $tag ? 'btn-primary' : 'btn-outline' }}">
                        {{ $tag }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Lernpools Liste -->
    @if($lernpools->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Keine Lernpools verfügbar</h3>
            <p>Der Ortsverband hat noch keine Lernpools erstellt.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($lernpools as $lernpool)
                <div class="lernpool-card">
                    <div class="lernpool-info">
                        <h2 class="lernpool-title">{{ $lernpool->name }}</h2>

                        @if($lernpool->tags && count($lernpool->tags) > 0)
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
                                @foreach($lernpool->tags as $tag)
                                    <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                                        🏷️ {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($lernpool->description)
                            <p class="lernpool-description">{{ $lernpool->description }}</p>
                        @endif
                        <div class="lernpool-meta">
                            <span>📝 {{ $lernpool->getQuestionCount() }} Fragen</span>
                            <span>👥 {{ $lernpool->getEnrollmentCount() }} eingeschrieben</span>
                        </div>

                        @if(in_array($lernpool->id, $enrolledIds))
                            <div class="mt-3">
                                @php
                                    $enrollment = auth()->user()->lernpoolEnrollments()->where('lernpool_id', $lernpool->id)->first();
                                    $progress = $enrollment ? $enrollment->getProgress() : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $progress }}% abgeschlossen</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="lernpool-actions">
                        @if(in_array($lernpool->id, $enrolledIds))
                            <a href="{{ route('ortsverband.lernpools.practice', [$ortsverband, $lernpool]) }}" class="btn-small btn-primary">
                                📖 Weitermachen
                            </a>
                        @else
                            <form action="{{ route('ortsverband.lernpools.enroll', [$ortsverband, $lernpool]) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-small btn-primary">
                                    ✍️ Einschreiben
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Back Link -->
    <div class="mt-8 pt-4 border-t border-gray-200">
        <a href="{{ route('ortsverband.show', $ortsverband) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Zurück zum Ortsverband
        </a>
    </div>
</div>
@endsection
