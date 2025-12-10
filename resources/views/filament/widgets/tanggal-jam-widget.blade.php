<x-filament::widget>
    <x-filament::card class="bg-blue-600 text-white p-6 rounded-xl shadow-lg flex flex-col items-center justify-center">
        <!-- <x-heroicon-o-clock class="w-8 h-8 mb-2 text-center"/> -->
        <div class="text-2xl font-bold text-center" wire:poll.1000ms>
            {{ now()->setTimezone('Asia/Shanghai')->format('l, d M Y H:i:s') }}
        </div>
    </x-filament::card>
</x-filament::widget>
