@extends('layouts.app')

@section('title', $ortsverband->name . ' · Neuer Lernpool')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .dash-container > .space-y-4 > * {
        animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }
    .dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    .tag-pill-suggestion {
        background: rgba(91,154,255,0.1);
        color: #5b9aff;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.7rem;
        font-weight: 600;
        border: 1px solid rgba(91,154,255,0.2);
        cursor: pointer;
        transition: all 150ms;
        font-family: 'IBM Plex Mono', monospace;
    }
    .tag-pill-suggestion:hover {
        background: rgba(91,154,255,0.2);
        border-color: rgba(91,154,255,0.4);
        transform: translateY(-1px);
    }
    html.light-mode .tag-pill-suggestion {
        background: rgba(0,51,127,0.06);
        color: #00337F;
        border-color: rgba(0,51,127,0.15);
    }
    html.light-mode .tag-pill-suggestion:hover {
        background: rgba(0,51,127,0.12);
        border-color: rgba(0,51,127,0.3);
    }

    .form-error-glass {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .form-hint-glass {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    .form-label-glass {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }

    .form-label-glass .required-mark {
        color: #ef4444;
    }

    .form-checkbox-glass {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.85rem;
    }

    .form-checkbox-glass input {
        width: 1.25rem;
        height: 1.25rem;
        accent-color: #5b9aff;
    }

</style>
@endpush

@section('content')
<div class="dash-container">
    <!-- Header -->
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">LERNPOOL</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Neuer Lernpool</h1>
        <p class="text-sm" style="color: var(--text-muted);">{{ $ortsverband->name }}</p>
    </div>

    <!-- Content -->
    <div class="space-y-4">
        <div class="glass p-4" style="border-radius:0.75rem;">
            <form action="{{ route('ortsverband.lernpools.store', $ortsverband) }}" method="POST">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label for="name" class="form-label-glass">
                        Name <span class="required-mark">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="input-glass @error('name') border-red-500 @enderror"
                           placeholder="z.B. Grundlagen Erste Hilfe" required>
                    @error('name')
                        <p class="form-error-glass">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="description" class="form-label-glass">
                        Beschreibung <span class="required-mark">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5"
                              class="textarea-glass @error('description') border-red-500 @enderror"
                              placeholder="Beschreibung des Lernpools..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error-glass">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="tags" class="form-label-glass">
                        Schlagwörter / Tags
                    </label>
                    @if($existingTags->isNotEmpty())
                        <div style="margin-bottom: 0.75rem;">
                            <p class="form-hint-glass" style="margin-bottom: 0.5rem;">
                                Vorhandene Tags (zum Hinzufügen anklicken):
                            </p>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @foreach($existingTags as $tag)
                                    <button type="button" onclick="addTag('{{ $tag }}')" class="tag-pill-suggestion">
                                        + {{ $tag }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                           class="input-glass @error('tags') border-red-500 @enderror"
                           placeholder="z.B. ZTR, B FGr, N FGr (mit Komma trennen)">
                    <p class="form-hint-glass">
                        Mehrere Tags mit Komma trennen (z.B. "ZTR, B FGr, N FGr")
                    </p>
                    @error('tags')
                        <p class="form-error-glass">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    function addTag(tag) {
                        const input = document.getElementById('tags');
                        const currentValue = input.value.trim();

                        const existingTags = currentValue.split(',').map(t => t.trim());
                        if (existingTags.includes(tag)) {
                            return;
                        }

                        if (currentValue === '') {
                            input.value = tag;
                        } else {
                            input.value = currentValue + ', ' + tag;
                        }

                        input.focus();
                    }
                </script>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-checkbox-glass">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>Sofort aktivieren</span>
                    </label>
                </div>

                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary btn-sm">
                        Lernpool erstellen
                    </button>
                    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}"
                       class="btn-ghost btn-sm">
                        Abbrechen
                    </a>
                </div>
            </form>
        </div>

        <!-- Back Link -->
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" style="font-size:0.8125rem;color:var(--text-muted);text-decoration:none;font-weight:600;transition:color 150ms;">Zurück zu Lernpools</a>
        </div>
    </div>
</div>
@endsection
