/*
 * Öffentliche Seiten (thw-trainer.de) – Bewegung und Scroll-Sequenzen.
 * Alle Inhalte stehen serverseitig im HTML; dieses Skript ergänzt nur Bewegung.
 * Jede Funktion startet nur, wenn ihr Element auf der Seite existiert.
 */

const reduceMotion = () => matchMedia('(prefers-reduced-motion: reduce)').matches;
const mmss = (secs) => String(Math.floor(secs / 60)).padStart(2, '0') + ':' + String(secs % 60).padStart(2, '0');

// Scroll-Handler nur, solange das Element im Bild ist; pro Frame höchstens ein Update
function onScrollWhileVisible(el, update) {
    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => { ticking = false; update(); });
    };
    new IntersectionObserver(([e]) => {
        if (e.isIntersecting) { addEventListener('scroll', onScroll, { passive: true }); update(); }
        else removeEventListener('scroll', onScroll);
    }).observe(el);
    addEventListener('resize', onScroll, { passive: true });
}

// Navigation wird über blauen Flächen ([data-nav-dark]) zum blauen Material
function navOverDarkSurfaces() {
    const nav = document.querySelector('.d-nav');
    const dark = document.querySelectorAll('[data-nav-dark]');
    if (!nav || !dark.length) return;
    const hits = new Set();
    let io;
    const watch = () => {
        io?.disconnect();
        io = new IntersectionObserver((entries) => {
            entries.forEach((e) => (e.isIntersecting ? hits.add(e.target) : hits.delete(e.target)));
            nav.classList.toggle('is-dark', hits.size > 0);
        }, { rootMargin: `0px 0px -${Math.max(0, innerHeight - 52)}px 0px` });
        dark.forEach((d) => io.observe(d));
    };
    watch();
    addEventListener('resize', watch, { passive: true });
}

// Einmaliges Einblenden beim Sichtbarwerden (.d-reveal, [data-anim])
function revealOnce() {
    const els = document.querySelectorAll('.d-reveal, [data-anim]');
    if (!('IntersectionObserver' in window)) { els.forEach((e) => e.classList.add('is-in')); return; }
    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
    }), { rootMargin: '0px 0px -12% 0px' });
    els.forEach((e) => io.observe(e));
}

/* ----------------------------------------------------------------
   Morph-Engine: 40 Punkte wechseln per Transform-Übergang zwischen Layouts.
   Markup: .d-story > .d-morph (.d-mh, .d-field > .d-dot × 40, .d-mtag[data-k]) + .d-beat[data-s]
   ---------------------------------------------------------------- */
function morph(story, { layouts, onState = () => {} }) {
    const field = story.querySelector('.d-field');
    const dots = [...field.querySelectorAll('.d-dot')];
    const tags = [...field.querySelectorAll('.d-mtag')];
    const heads = [...story.querySelectorAll('.d-mh')];
    const beats = [...story.querySelectorAll('.d-beat')];
    const mq = matchMedia('(max-width: 860px)');
    let state = 0;

    function place() {
        const W = field.clientWidth, H = field.clientHeight;
        const d = Math.max(7, Math.min(12, Math.min(W, H) / 26));
        field.style.setProperty('--d', d + 'px');
        const g = { W, H, d, mobile: mq.matches };
        const L = layouts[state](g);
        dots.forEach((dot, i) => {
            const p = L.dots[i] ?? L.dots[L.dots.length - 1];
            dot.style.transform = `translate(${p.x}px, ${p.y}px) scale(${p.s ?? 1})`;
            dot.dataset.c = p.c;
        });
        // Beschriftungen des aktuellen Zustands einblenden; andere blenden am alten Ort aus
        tags.forEach((t) => {
            const p = L.tags?.[t.dataset.k];
            t.classList.toggle('is-on', !!p);
            if (p) { t.style.left = p.x + 'px'; t.style.top = p.y + 'px'; }
        });
        onState(state, g);
    }

    function setState(s) {
        if (s === state) return;
        state = s;
        field.dataset.s = s;
        heads.forEach((h, k) => h.classList.toggle('is-on', k === s));
        beats.forEach((b, k) => b.classList.toggle('is-on', k === s));
        place();
    }

    // Erste Platzierung ohne Übergang
    dots.forEach((d) => { d.style.transition = 'none'; });
    place();
    requestAnimationFrame(() => requestAnimationFrame(() => dots.forEach((d) => { d.style.transition = ''; })));

    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
        if (e.isIntersecting) setState(+e.target.dataset.s);
    }), { rootMargin: '-58% 0px -36% 0px' });
    beats.forEach((b) => io.observe(b));
    addEventListener('resize', place, { passive: true });
    const sync = () => story.classList.toggle('is-mobile', mq.matches);
    mq.addEventListener('change', sync);
    sync();
}

const grid = (cols, rows, W, H, gap, n = 40) => Array.from({ length: n }, (_, i) => ({
    x: W / 2 - ((cols - 1) * gap) / 2 + (i % cols) * gap,
    y: H / 2 - ((rows - 1) * gap) / 2 + Math.floor(i / cols) * gap,
}));

const ring = (W, H, R, color) => Array.from({ length: 40 }, (_, i) => {
    const a = (-90 + i * 9) * Math.PI / 180;
    return { x: W / 2 + R * Math.cos(a), y: H / 2 + R * Math.sin(a), c: color(i) };
});

// 40 Punkte nach größtem Rest auf die Lernabschnitte verteilen
function allocate(counts) {
    const total = counts.reduce((a, b) => a + b, 0) || 1;
    const share = counts.map((c) => (c / total) * 40);
    const alloc = share.map(Math.floor);
    share.map((v, k) => [v - Math.floor(v), k])
        .sort((a, b) => b[0] - a[0])
        .slice(0, 40 - alloc.reduce((a, b) => a + b, 0))
        .forEach(([, k]) => { alloc[k]++; });
    return alloc;
}

// Säulen je Lernabschnitt (Höhe = Anteil am Katalog)
const columns = (alloc) => ({ W, H }) => {
    const x0 = W * 0.08, colW = (W * 0.84) / alloc.length, base = H * 0.8;
    const gy = Math.min(colW * 0.9, (H * 0.68) / Math.max(...alloc, 1));
    const dots = [], tags = {};
    alloc.forEach((n, k) => {
        const x = x0 + (k + 0.5) * colW;
        for (let j = 0; j < n; j++) dots.push({ x, y: base - j * gy, c: k === 0 ? 'on' : 'due' });
        tags['n' + (k + 1)] = { x, y: base + 22 };
    });
    return { dots, tags };
};

const json = (el, key, fallback) => {
    try { return JSON.parse(el.dataset[key]); } catch { return fallback; }
};

const morphLayouts = {
    // Startseite: Lernraster → Wiederholungskreis → Prüfungsbogen → 5 Simulationen
    home(story) {
        const field = story.querySelector('.d-field');
        const cycle = field.querySelector('.d-cycle'), orbit = field.querySelector('.d-orbit');
        const timer = story.querySelector('.d-timer');
        const ringR = (g) => Math.min(g.W, g.H) * (g.mobile ? 0.32 : 0.36);
        let tick = null, secs = 702;
        return {
            layouts: [
                ({ W, H }) => ({ dots: grid(10, 4, W, H, Math.min(W / 11, H / 5.2)).map((p, i) => ({ ...p, c: i < 30 ? 'on' : 'off' })) }),
                (g) => {
                    const R = ringR(g), rr = R + (g.mobile ? 20 : 34);
                    const at = (deg) => ({ x: g.W / 2 + rr * Math.cos(deg * Math.PI / 180), y: g.H / 2 + rr * Math.sin(deg * Math.PI / 180) });
                    return {
                        dots: ring(g.W, g.H, R, (i) => ((i <= 5 || i >= 34) ? 'due' : 'off')).map((p) => ({ ...p, s: 0.8 })),
                        tags: { heute: at(-90), tag1: at(0), tag3: at(90), selten: at(180) },
                    };
                },
                ({ W, H }) => ({ dots: grid(8, 5, W, H, Math.min(W / 9, H / 6.2)).map((p, i) => ({ ...p, s: 1.25, c: [4, 17].includes(i) ? 'bad' : i < 26 ? 'on' : i === 26 ? 'cur' : 'off' })) }),
                ({ W, H, d }) => {
                    const gap = Math.min(W / 5.6, H / 2.2);
                    return { dots: Array.from({ length: 40 }, (_, i) => ({ x: W / 2 + (Math.floor(i / 8) - 2) * gap, y: H / 2, s: (gap * 0.62) / d, c: 'on' })) };
                },
            ],
            onState(s, g) {
                const R = ringR(g), C = R * 0.72;
                Object.assign(cycle.style, { left: g.W / 2 - C + 'px', top: g.H / 2 - C + 'px', width: C * 2 + 'px', height: C * 2 + 'px' });
                Object.assign(orbit.style, { left: g.W / 2 + 'px', top: g.H / 2 + 'px' });
                orbit.style.setProperty('--r', R + 'px');
                clearInterval(tick);
                if (s === 2 && timer && !reduceMotion()) {
                    tick = setInterval(() => { secs = secs > 0 ? secs - 1 : 702; timer.textContent = mmss(secs); }, 1000);
                }
            },
        };
    },

    // THW Theorie: Katalog → Lernabschnitte nach Anteil → Lernstufen
    catalog(story) {
        const alloc = allocate(json(story, 'counts', []));
        return {
            layouts: [
                ({ W, H }) => ({ dots: grid(10, 4, W, H, Math.min(W / 11, H / 5.2)).map((p) => ({ ...p, c: 'on' })), tags: { legend: { x: W / 2, y: H - 10 } } }),
                columns(alloc),
                ({ W, H }) => {
                    const sizes = [12, 10, 18], cx = [0.2, 0.5, 0.8].map((f) => W * f), base = H * 0.76;
                    const g = Math.min(W / 16, H / 9), dots = [];
                    sizes.forEach((n, k) => {
                        for (let j = 0; j < n; j++) dots.push({ x: cx[k] + ((j % 3) - 1) * g, y: base - Math.floor(j / 3) * g, c: ['off', 'due', 'on'][k] });
                    });
                    return { dots, tags: { s1: { x: cx[0], y: base + 28 }, s2: { x: cx[1], y: base + 28 }, s3: { x: cx[2], y: base + 28 } } };
                },
            ],
        };
    },

    // THW Grundausbildung: du im Ortsverband → Dienstabende → Theorie → Haken
    overview() {
        return {
            layouts: [
                ({ W, H }) => {
                    const c = Math.min(W, H) * 0.046, golden = 137.508 * Math.PI / 180;
                    const dots = Array.from({ length: 40 }, (_, i) => {
                        if (i === 0) return { x: W / 2, y: H / 2, s: 1.9, c: 'on' };
                        const r = c * Math.sqrt(i + 1.5), a = i * golden;
                        return { x: W / 2 + r * Math.cos(a), y: H / 2 + r * Math.sin(a), c: 'off' };
                    });
                    return { dots, tags: { you: { x: W / 2, y: H / 2 - 30 }, ov: { x: W / 2, y: H - 10 } } };
                },
                ({ W, H }) => {
                    const gap = Math.min(W / 11, H / 5.6);
                    const dots = grid(10, 4, W, H, gap).map((p, i) => ({ ...p, c: i % 10 < 6 ? 'on' : 'off' }));
                    const tags = {};
                    for (let k = 0; k < 10; k++) tags['n' + (k + 1)] = { x: dots[k].x, y: H / 2 + 1.5 * gap + 26 };
                    return { dots, tags };
                },
                ({ W, H }) => {
                    const gx = Math.min((W * 0.84) / 19, 26), gy = gx * 1.2;
                    const dots = Array.from({ length: 40 }, (_, i) => ({ x: W / 2 + ((i % 20) - 9.5) * gx, y: H / 2 + (Math.floor(i / 20) - 0.5) * gy, s: 1.1, c: i % 20 < 15 ? 'on' : 'off' }));
                    return { dots, tags: { bar: { x: W / 2, y: H / 2 + gy + 28 } } };
                },
                ({ W, H }) => {
                    const S = Math.min(W, H) * 0.8, cx = W / 2, cy = H / 2 - S * 0.04;
                    const P = [[cx - 0.32 * S, cy], [cx - 0.1 * S, cy + 0.22 * S], [cx + 0.34 * S, cy - 0.24 * S]];
                    const l1 = Math.hypot(P[1][0] - P[0][0], P[1][1] - P[0][1]), l2 = Math.hypot(P[2][0] - P[1][0], P[2][1] - P[1][1]);
                    const dots = Array.from({ length: 40 }, (_, i) => {
                        const t = (i / 39) * (l1 + l2);
                        const [a, b, f] = t <= l1 ? [P[0], P[1], t / l1] : [P[1], P[2], (t - l1) / l2];
                        return { x: a[0] + (b[0] - a[0]) * f, y: a[1] + (b[1] - a[1]) * f, s: 1.3, c: 'on' };
                    });
                    return { dots, tags: { pass: { x: cx, y: cy + 0.38 * S } } };
                },
            ],
        };
    },

    // Statistik: Personenraster → Wachstumskurve → Erfolgsring → Säulen je Lernabschnitt
    stats(story) {
        const users = json(story, 'users', []);
        const alloc = allocate(json(story, 'counts', []));
        const filled = +story.dataset.filled || 0;
        const lo = Math.min(...users), hi = Math.max(...users);
        return {
            layouts: [
                ({ W, H }) => ({ dots: grid(8, 5, W, H, Math.min(W / 9, H / 6.2)).map((p) => ({ ...p, c: 'on' })), tags: { legend: { x: W / 2, y: H - 10 } } }),
                ({ W, H }) => {
                    const mx = W * 0.08, top = H * 0.16, bot = H * 0.8, span = hi - lo || 1;
                    const pts = Array.from({ length: 40 }, (_, i) => {
                        const v = users.length ? users[Math.round((i * (users.length - 1)) / 39)] : 0;
                        return { x: mx + (i * (W - 2 * mx)) / 39, y: bot - ((v - lo) / span) * (bot - top), s: i === 39 ? 1.8 : 0.75, c: 'on' };
                    });
                    return { dots: pts, tags: { start: { x: mx + 64, y: bot + 26 }, end: { x: W - mx - 44, y: pts[39].y - 26 } } };
                },
                ({ W, H }) => ({ dots: ring(W, H, Math.min(W, H) * 0.36, (i) => (i < filled ? 'on' : 'off')), tags: { center: { x: W / 2, y: H / 2 } } }),
                columns(alloc),
            ],
        };
    },
};

function initMorphs() {
    document.querySelectorAll('.d-story[data-morph]').forEach((story) => {
        const make = morphLayouts[story.dataset.morph];
        if (make) morph(story, make(story));
    });
}

/* ----------------------------------------------------------------
   Startseite: gepinnte Frage-Sequenz (Scroll-Fortschritt → Phase → Zustand)
   ---------------------------------------------------------------- */
function questionSequence() {
    const quiz = document.querySelector('.d-quiz');
    if (!quiz) return;
    const card = quiz.querySelector('.d-qc');
    const bar = quiz.querySelector('.d-qc-bar i');
    const faces = [...quiz.querySelectorAll('.d-face')];
    const caps = [...quiz.querySelectorAll('.d-cap')];
    const ptr = quiz.querySelector('.d-pointer');
    // Richtige Antworten je Frage kommen aus dem Markup (.is-correct)
    const correct = faces.map((f) => [...f.querySelectorAll('.d-qopt')].flatMap((o, n) => (o.classList.contains('is-correct') ? [n] : [])));
    const picks = (f, upTo) => correct[f].slice(0, upTo);
    // f = Frage, a = Antworten sichtbar, p = gewählt, r = aufgelöst, c = Untertitel, s = richtig in Folge
    const P = [
        { f: 0, a: false, p: [], r: false, c: 0, s: 0 },
        { f: 0, a: true, p: [], r: false, c: 1, s: 0 },
        { f: 0, a: true, p: picks(0, 1), r: false, c: 2, s: 0 },
        { f: 0, a: true, p: picks(0, 3), r: false, c: 2, s: 0 },
        { f: 0, a: true, p: picks(0, 3), r: true, c: 3, s: 1 },
        { f: 1, a: true, p: [], r: false, c: 4, s: 2 },
        { f: 1, a: true, p: picks(1, 3), r: false, c: 4, s: 2 },
        { f: 1, a: true, p: picks(1, 3), r: true, c: 5, s: 3 },
    ];
    let cur = -1;

    function render(i) {
        const st = P[i];
        faces.forEach((face, k) => {
            const on = k === st.f;
            face.classList.toggle('is-off', !on);
            face.classList.toggle('is-next', k > st.f);
            face.toggleAttribute('aria-hidden', !on);
            if (!on) return;
            face.classList.toggle('show-answers', st.a);
            face.classList.toggle('is-resolved', st.r);
            face.querySelectorAll('.d-qopt').forEach((o, n) => o.classList.toggle('is-picked', st.p.includes(n)));
            face.querySelectorAll('.d-mastery i').forEach((d, n) => d.classList.toggle('is-on', n < st.s));
            face.querySelector('.d-mastery').classList.toggle('is-done', st.s >= 3);
        });
        caps.forEach((c, k) => c.classList.toggle('is-on', k === st.c));
        // Zeiger fährt zur zuletzt gewählten Antwort und „tippt“
        if (st.p.length && !st.r) {
            const opt = faces[st.f].querySelectorAll('.d-qopt')[st.p[st.p.length - 1]];
            const o = opt.getBoundingClientRect(), c = card.getBoundingClientRect();
            ptr.style.transform = `translate(${o.right - c.left - 36}px, ${o.top - c.top + o.height / 2}px)`;
            ptr.classList.add('is-on');
            ptr.classList.remove('is-tap');
            void ptr.offsetWidth;
            ptr.classList.add('is-tap');
        } else {
            ptr.classList.remove('is-on');
        }
    }

    onScrollWhileVisible(quiz, () => {
        const r = quiz.getBoundingClientRect();
        const prog = Math.min(1, Math.max(0, -r.top / (r.height - innerHeight)));
        bar.style.transform = `scaleX(${prog})`;
        quiz.classList.toggle('is-live', r.top < innerHeight * 0.55);
        const i = Math.min(P.length - 1, Math.floor(prog * P.length));
        if (i !== cur) { cur = i; render(i); }
    });
}

/* ----------------------------------------------------------------
   THW Theorieprüfung: Simulation im Zeitraffer (Scroll-Fortschritt = beantwortete Fragen)
   ---------------------------------------------------------------- */
function examSimulation() {
    const sim = document.querySelector('.d-sim');
    if (!sim) return;
    const cells = [...sim.querySelectorAll('.d-sheet-grid i')];
    const total = cells.length, passCount = +sim.dataset.pass || Math.ceil(total * 0.8);
    const wrong = [4, 17, 29, 35].filter((w) => w < total);
    const meter = sim.querySelector('.d-meter i'), time = sim.querySelector('.d-sim-time');
    const rightEl = sim.querySelector('.d-sim-right'), nEl = sim.querySelector('.d-sim-n');
    const caps = [...sim.querySelectorAll('.d-scap')];
    const pin = sim.querySelector('.d-sim-pin');
    const startSecs = (+sim.dataset.minutes || 30) * 60;
    let last = -1;

    onScrollWhileVisible(sim, () => {
        const r = sim.getBoundingClientRect();
        const top = parseFloat(getComputedStyle(pin).top) || 0;
        const p = Math.min(1, Math.max(0, (top - r.top) / (r.height - pin.offsetHeight)));
        time.textContent = mmss(Math.round(startSecs - p * (startSecs - 372)));
        const n = Math.min(total, Math.floor(p * (total + 1)));
        if (n === last) return;
        last = n;
        cells.forEach((c, i) => {
            c.classList.toggle('is-ok', i < n && !wrong.includes(i));
            c.classList.toggle('is-bad', i < n && wrong.includes(i));
            c.classList.toggle('is-cur', i === n && n < total);
        });
        const right = n - wrong.filter((w) => w < n).length;
        rightEl.textContent = right;
        nEl.textContent = Math.min(n + 1, total);
        meter.style.transform = `scaleX(${right / total})`;
        sim.classList.toggle('is-pass', right >= passCount);
        sim.classList.toggle('is-done', n >= total);
        const cap = n >= total ? 3 : right >= passCount ? 2 : n >= 14 ? 1 : 0;
        caps.forEach((c, k) => c.classList.toggle('is-on', k === cap));
    });
}

navOverDarkSurfaces();
revealOnce();
initMorphs();
questionSequence();
examSimulation();
