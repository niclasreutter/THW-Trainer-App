{{-- Geteilte Bild-Lightbox für Practice-Partials.
     Aktivierung: beliebiges Element mit [data-lightbox-src="<url>"]
     (optional data-lightbox-alt, data-lightbox-caption).
     Funktionen: Klick, Escape-Taste, Klick außerhalb des Bildes schließt. --}}

<div id="imgLightbox" class="ilb" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Bild-Vollansicht">
    <div class="ilb-backdrop" data-ilb-close></div>
    <button type="button" class="ilb-close" data-ilb-close aria-label="Schließen">
        <i class="bi bi-x-lg"></i>
    </button>
    <figure class="ilb-stage">
        <img id="ilbImage" src="" alt="">
        <figcaption id="ilbCaption" class="ilb-caption"></figcaption>
    </figure>
</div>

<style>
    .ilb {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        animation: ilb-fade 0.15s ease-out;
    }
    .ilb[hidden] { display: none; }

    .ilb-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(5, 10, 20, 0.88);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        cursor: zoom-out;
    }

    .ilb-stage {
        position: relative;
        z-index: 1;
        margin: 0;
        max-width: min(1100px, 100%);
        max-height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        pointer-events: none;
    }
    .ilb-stage img {
        display: block;
        max-width: 100%;
        max-height: calc(100vh - 6rem);
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 0.75rem;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.6);
        pointer-events: auto;
    }
    .ilb-caption {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.8125rem;
        font-family: var(--font-mono, 'IBM Plex Mono', monospace);
        text-align: center;
        max-width: 60ch;
        pointer-events: auto;
    }
    .ilb-caption:empty { display: none; }

    .ilb-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 2;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
    }
    .ilb-close:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: scale(1.05);
    }

    /* Zoom-Hint für anklickbare Bilder (wird von Partials genutzt) */
    .img-zoom-trigger { cursor: zoom-in; }
    .img-zoom-btn {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        z-index: 3;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.35);
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        color: #fff;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
        transition: background 0.15s ease, transform 0.15s ease;
    }
    .img-zoom-btn:hover {
        background: rgba(0, 0, 0, 0.75);
        transform: scale(1.08);
    }

    @keyframes ilb-fade {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    body.ilb-open { overflow: hidden; }
</style>

<script>
(function () {
    if (window._imgLightboxInit) return;
    window._imgLightboxInit = true;

    const lightbox = document.getElementById('imgLightbox');
    const img = document.getElementById('ilbImage');
    const caption = document.getElementById('ilbCaption');
    if (!lightbox || !img) return;

    function open(src, alt, cap) {
        if (!src) return;
        img.src = src;
        img.alt = alt || '';
        caption.textContent = cap || '';
        lightbox.hidden = false;
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('ilb-open');
    }

    function close() {
        lightbox.hidden = true;
        lightbox.setAttribute('aria-hidden', 'true');
        img.src = '';
        document.body.classList.remove('ilb-open');
    }

    // Capture-Phase: feuert vor inline-onclick auf dem Button,
    // damit wir die Lightbox auch dann öffnen, wenn das Event-Bubbling
    // anschließend per stopPropagation() unterbrochen wird (z.B. in Labels).
    document.addEventListener('click', function (ev) {
        const trigger = ev.target.closest('[data-lightbox-src]');
        if (trigger) {
            ev.preventDefault();
            ev.stopPropagation();
            open(
                trigger.getAttribute('data-lightbox-src'),
                trigger.getAttribute('data-lightbox-alt') || '',
                trigger.getAttribute('data-lightbox-caption') || ''
            );
            return;
        }
        if (ev.target.closest('[data-ilb-close]')) {
            close();
        }
    }, true);

    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape' && !lightbox.hidden) {
            close();
        }
    });
})();
</script>
