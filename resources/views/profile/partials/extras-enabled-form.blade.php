<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Zusatz-Fragen') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Zusatz-Fragen sind abwechslungsreiche Übungsformate (Zuordnung, Bilder) außerhalb der offiziellen THW-Prüfungsfragen. Sie erscheinen zusätzlich im Übungsmodus und beeinflussen nicht deine Prüfungsfreischaltung.
        </p>
    </header>

    <form method="post" action="{{ route('profile.extras-enabled') }}" class="mt-6 space-y-6">
        @csrf

        <div class="flex items-center gap-3">
            {{-- Hidden default ensures "0" is submitted when toggle is off --}}
            <input type="hidden" name="extras_enabled" value="0">

            <label for="extras_enabled" class="relative inline-flex items-center cursor-pointer">
                <input
                    id="extras_enabled"
                    name="extras_enabled"
                    type="checkbox"
                    value="1"
                    class="sr-only peer"
                    {{ old('extras_enabled', $user->extras_enabled) ? 'checked' : '' }}
                >
                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer
                            peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-offset-2
                            peer-focus:ring-[#00337f] dark:peer-focus:ring-offset-gray-800
                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                            peer-checked:after:translate-x-full peer-checked:after:border-white
                            peer-checked:bg-[#00337f]">
                </div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Zusatz-Fragen aktivieren') }}
                </span>
            </label>
        </div>

        <x-input-error class="mt-2" :messages="$errors->get('extras_enabled')" />

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Speichern') }}</x-primary-button>

            @if (session('status') === 'extras-enabled-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Einstellung gespeichert.') }}</p>
            @endif
        </div>
    </form>
</section>
