<x-filament::widget>
    <x-filament::card>

        <h2 class="text-lg font-bold mb-3">
            Jatuh Tempo Bulan Ini : {{ $this->total }} Bilyet
            {{-- alternatif: {{ $total ?? $records->count() }} --}}
        </h2>

        @if(!empty($records) && $records->count())
            <table class="w-full text-sm p-2">
                <thead>
                    <tr class="border-b">
                        <th class="py-1 text-center">Kode Bilyet</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Jatuh Tempo</th>
                        <th class="text-center" colspan=2>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $r)
                        <tr>
                            <td class="py-1 text-center">{{ $r->kode_bilyet }}</td>
                            <td class="text-left">{{ $r->nama_lengkap }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($r->tanggal_masuk)->addMonths($r->jangka_waktu)->format('d M Y') }}</td>
                            <td>Rp. </td>
                            <td align="right">{{ number_format($r->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                    @php
                        $total = $records->sum('nominal');
                    @endphp
                <tfoot class="text-center border-t">
                    <tr>
                        <td colspan="3">Total</td>
                        <td>Rp. </td>
                        <td align="right">{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="text-gray-500">Tidak ada yang jatuh tempo hari ini.</div>
        @endif

    </x-filament::card>
</x-filament::widget>
