{{-- Skeleton Loader Component
     Usage: @include('components.skeleton-loader', ['type' => 'dashboard'])
     Types: dashboard, practice-menu, cards
--}}

@props(['type' => 'dashboard'])

<div class="skeleton-wrapper" id="skeletonLoader" style="animation: fadeIn 0.2s ease-out;">
    @if($type === 'dashboard')
        {{-- Dashboard Skeleton --}}
        <div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
            <div class="skeleton skeleton-text-lg" style="width: 250px; margin-bottom: 0.5rem;"></div>
            <div class="skeleton skeleton-text-sm" style="width: 180px; margin-bottom: 2rem;"></div>

            <div style="display: flex; gap: 0.75rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <div class="skeleton skeleton-pill"></div>
                <div class="skeleton skeleton-pill"></div>
                <div class="skeleton skeleton-pill"></div>
                <div class="skeleton skeleton-pill"></div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                <div class="skeleton skeleton-card" style="min-height: 280px; grid-row: span 2;"></div>
                <div class="skeleton skeleton-card"></div>
                <div class="skeleton skeleton-card"></div>
            </div>
        </div>

    @elseif($type === 'practice-menu')
        {{-- Practice Menu Skeleton --}}
        <div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
            <div class="skeleton skeleton-text-lg" style="width: 200px; margin-bottom: 2rem;"></div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem;">
                @for($i = 0; $i < 6; $i++)
                    <div class="skeleton skeleton-card" style="min-height: 150px;"></div>
                @endfor
            </div>
        </div>

    @elseif($type === 'cards')
        {{-- Generic Cards Skeleton --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem;">
            @for($i = 0; $i < 4; $i++)
                <div class="skeleton skeleton-card"></div>
            @endfor
        </div>
    @endif
</div>

<script>
    // Auto-hide skeleton when page is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const skeleton = document.getElementById('skeletonLoader');
        if (skeleton) {
            skeleton.style.display = 'none';
        }
    });
</script>
