    <x-filament::widget>
        <x-filament::card>

            <h2 class="text-lg font-bold mb-3">
                Bunga Simpanan Berjangka Hari Ini : {{ $this->total }} Bilyet
                {{-- alternatif: {{ $total ?? $records->count() }} --}}
            </h2>

            @if(!empty($records) && $records->count())
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-1 text-center">Kode Bilyet</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Jatuh Tempo</th>
                            <th class="text-center" colspan=2>Bunga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $r)
                        @php
                            $bunga = $r->nominal*(($r->bunga_persen/100)/12);
                        @endphp
                            <tr>
                                <td class="py-1 text-center">{{ $r->kode_bilyet }}</td>
                                <td class="text-left">{{ $r->nama_lengkap }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($r->tanggal_masuk)->addMonths($r->jangka_waktu)->format('d M Y') }}</td>
                                <td class="text-left">Rp. </td>
                                <td class="text-right">{{ number_format($bunga, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                        @php
                            $totalBunga = $records->sum(function($r) {
                                return $r->nominal * (($r->bunga_persen / 100) / 12);
                            });
                        @endphp
                    <tfoot class="text-center border-t">
                        <tr>
                            <td colspan="3">Total</td>
                            <td>Rp. </td>
                            <td align="right">{{ number_format($totalBunga, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="text-gray-500">Tidak ada yang jatuh tempo hari ini.</div>
            @endif

        </x-filament::card>
    </x-filament::widget>
