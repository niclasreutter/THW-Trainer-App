{{-- Interaktive Fragekarte mit echten Fragen. $items = Fragen (section, text, answers, correct, explain) --}}
<div class="d-card d-q" x-data="{
        items: @js($items),
        sections: @js(collect($sections)->pluck('short', 'nr')),
        i: 0, picked: [], checked: false,
        get q() { return this.items[this.i]; },
        get right() { return [...this.picked].sort().join() === [...this.q.correct].sort().join(); },
        toggle(n) { if (this.checked) return; this.picked = this.picked.includes(n) ? this.picked.filter(p => p !== n) : [...this.picked, n]; },
        next() { this.i = (this.i + 1) % this.items.length; this.picked = []; this.checked = false; }
    }">
    <div class="d-q-meta"><span x-text="'Lernabschnitt ' + q.section + ' · ' + sections[q.section]"></span><span class="d-num" x-text="(i + 1) + ' / ' + items.length"></span></div>
    <p class="d-q-text" x-text="q.text" aria-live="polite">{{ $items[0]['text'] ?? '' }}</p>
    <template x-for="(a, n) in q.answers" :key="i + '-' + n">
        <button type="button" class="d-opt" :disabled="checked" :aria-pressed="picked.includes(n).toString()" @click="toggle(n)"
            :class="{ 'is-right': checked && q.correct.includes(n), 'is-wrong': checked && picked.includes(n) && !q.correct.includes(n) }">
            <span class="k" x-text="'ABC'[n]"></span><span x-text="a"></span>
        </button>
    </template>
    <p x-show="checked && q.explain" x-text="q.explain" class="d-small" style="margin:18px 0 0;font-size:15px;line-height:1.5"></p>
    <div class="d-q-foot">
        <span class="d-small" aria-live="polite">
            <span x-show="!checked">Eine oder mehrere Antworten.</span>
            <span x-show="checked" :style="{ color: right ? 'var(--ok)' : 'var(--bad)', fontWeight: 600 }" x-text="right ? 'Richtig.' : 'Richtig wäre ' + q.correct.map(c => 'ABC'[c]).join(' und ') + '.'"></span>
        </span>
        <button type="button" class="d-pill" x-show="!checked" :disabled="!picked.length" :style="{ opacity: picked.length ? 1 : .35 }" @click="checked = true">Prüfen</button>
        <button type="button" class="d-pill d-pill--ghost" x-show="checked" @click="next()">Nächste Frage</button>
    </div>
</div>
