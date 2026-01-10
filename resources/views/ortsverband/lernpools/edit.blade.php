@if(request()->header('X-Requested-With') === 'XMLHttpRequest')
    <!-- Modal Format -->
    <div class="modal-header">
        <h2>{{ $lernpool->name }} bearbeiten</h2>
        <button class="modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">✕</button>
    </div>
    <form action="{{ route('ortsverband.lernpools.update', [$ortsverband, $lernpool]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="form-group">
                <label for="name" class="form-label">
                    Name <span class="required">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $lernpool->name) }}" 
                       class="form-input @error('name') error @enderror" required>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">
                    Beschreibung <span class="required">*</span>
                </label>
                <textarea name="description" id="description" rows="5" class="form-textarea @error('description') error @enderror" required>{{ old('description', $lernpool->description) }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $lernpool->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Aktiv</label>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
                Abbrechen
            </button>
            <button type="submit" class="btn btn-primary">
                ✓ Speichern
            </button>
        </div>
    </form>
@else
    <!-- Standard Seite -->
@extends('layouts.app')

@section('title', $ortsverband->name . ' · ' . $lernpool->name . ' bearbeiten')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" style="color: #2563eb; text-decoration: none;">← Zurück</a>
    <h1 style="font-size: 2.25rem; font-weight: 800; color: #00337F; margin: 1rem 0;">{{ $lernpool->name }} bearbeiten</h1>
    <div style="background: white; padding: 2rem; border-radius: 1.25rem; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);">
        <form action="{{ route('ortsverband.lernpools.update', [$ortsverband, $lernpool]) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #00337F; margin-bottom: 0.5rem;">Name</label>
                <input type="text" name="name" value="{{ old('name', $lernpool->name) }}" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.75rem;" required>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #00337F; margin-bottom: 0.5rem;">Beschreibung</label>
                <textarea name="description" rows="5" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.75rem;" required>{{ old('description', $lernpool->description) }}</textarea>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $lernpool->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="font-weight: 600; color: #00337F;">Aktiv</label>
            </div>
            <button type="submit" style="background: linear-gradient(135deg, #2563eb, #1e40af); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.75rem; cursor: pointer; font-weight: 600;">✓ Speichern</button>
            <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" style="background: #f3f4f6; color: #00337F; padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; margin-left: 0.5rem;">Abbrechen</a>
        </form>
    </div>
</div>
@endsection
@endif
