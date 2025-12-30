<x-filament::widget>
    <x-filament::card
        class="
            bg-white dark:bg-slate-900
            border border-slate-200 dark:border-slate-800
            rounded-2xl shadow-sm
        "
    >
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">

            {{-- LEFT : DATE --}}
            <div class="flex items-start">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Hari ini
                    </p>

                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white leading-tight">
                        {{ now()->translatedFormat('l') }}
                    </h2>

                    <p class="text-base text-slate-600 dark:text-slate-300">
                        {{ now()->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            {{-- RIGHT : TIME --}}
            <div wire:poll.1s class="flex items-center">
                

                <div class="text-right">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Waktu (WITA)
                    </p>

                    <span class="text-xl font-mono font-semibold text-slate-900 dark:text-white tracking-wide leading-none">
                        {{ $this->time }}
                    </span>
                </div>
            </div>

        </div>
    </x-filament::card>
</x-filament::widget>
