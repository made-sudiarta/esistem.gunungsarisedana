<x-filament::widget>
    <x-filament::card>

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold">
                    Bunga Simpanan Berjangka
                </h2>
                <p class="text-sm text-gray-500">
                    Hari ini terdapat
                    <span class="font-semibold text-primary-600">
                        {{ $this->total }}
                    </span>
                    bilyet
                </p>
            </div>

            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-600">
                <x-heroicon-o-banknotes class="w-6 h-6" />
            </div>
        </div>

        {{-- CONTENT --}}
        @if($records && $records->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2 text-center">Bilyet</th>
                            <th class="text-left">Nama</th>
                            <th class="text-center">Jatuh Tempo</th>
                            <th class="text-right">Bunga</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach($records as $r)
                            @php
                                $bunga = $r->nominal * (($r->bunga_persen / 100) / 12);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 text-center font-medium">
                                    {{ $r->kode_bilyet }}
                                </td>
                                <td>
                                    {{ $r->nama_lengkap }}
                                </td>
                                <td class="text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($r->tanggal_masuk)
                                        ->addMonths($r->jangka_waktu)
                                        ->translatedFormat('d M Y') }}
                                </td>
                                <td class="text-right font-semibold text-primary-600">
                                    Rp {{ number_format($bunga, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    @php
                        $totalBunga = $records->sum(fn ($r) =>
                            $r->nominal * (($r->bunga_persen / 100) / 12)
                        );
                    @endphp

                    <tfoot class="border-t font-bold bg-gray-50">
                        <tr>
                            <td colspan="3" class="py-2 text-right">
                                Total Bunga
                            </td>
                            <td class="text-right text-primary-700">
                                Rp {{ number_format($totalBunga, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="flex items-center gap-2 text-gray-500">
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-500" />
                Tidak ada simpanan berjangka jatuh tempo hari ini.
            </div>
        @endif

    </x-filament::card>
</x-filament::widget>
