<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm">
                    <b>Simpanan Berjangka</b>
                </p>

                <h2 class="text-3xl font-bold text-primary-600">
                    {{ $total }} Bilyet
                </h2>

                <p class="text-sm text-gray-600 mt-1">
                    Jatuh tempo bulan ini
                </p>
            </div>

            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-primary-100 text-primary-600">
                <x-heroicon-o-clock class="w-7 h-7" />
            </div>
        </div>

        @if ($records->count())
            <div class="mt-4 border-t pt-3 space-y-2">
                @foreach ($records->take(10) as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">
                            {{ $item->nama_lengkap }}
                        </span>
                        <span class="text-gray-500">
                            {{ \Carbon\Carbon::parse($item->tanggal_masuk)
                                ->addMonths($item->jangka_waktu)
                                ->translatedFormat('d M Y') }}
                        </span>
                    </div>
                @endforeach

                @if ($records->count() > 10)
                    <p class="text-xs text-gray-400 mt-1">
                        +{{ $records->count() - 5 }} lainnya
                    </p>
                @endif
            </div>
        @endif
    </x-filament::card>
</x-filament::widget>
