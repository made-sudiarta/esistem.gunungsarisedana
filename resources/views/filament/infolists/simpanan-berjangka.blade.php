@php
    $simpananBerjangkas = collect($getState() ?? []);
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="w-full min-w-[750px] text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-4 py-3 text-left">Kode Bilyet</th>
                <th class="px-4 py-3 text-left">Tanggal Masuk</th>
                <th class="px-4 py-3 text-right">Nominal</th>
                <th class="px-4 py-3 text-center">Jangka Waktu</th>
                <th class="px-4 py-3 text-left">Jatuh Tempo</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($simpananBerjangkas as $simpanan)
                @php
                    $tanggalMasuk = $simpanan->tanggal_masuk
                        ? \Illuminate\Support\Carbon::parse($simpanan->tanggal_masuk)
                        : null;

                    // Asumsi jangka_waktu disimpan dalam satuan bulan.
                    $jatuhTempo = $tanggalMasuk
                        ? $tanggalMasuk->copy()->addMonths((int) $simpanan->jangka_waktu)
                        : null;
                @endphp
                <tr>
                    <td class="px-4 py-3 font-medium">
                        {{ $simpanan->kode_bilyet ?: '-' }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $tanggalMasuk?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        Rp {{ number_format((float) $simpanan->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        {{ $simpanan->jangka_waktu }} bulan
                    </td>
                    <td class="px-4 py-3">
                        {{ $jatuhTempo?->format('d/m/Y') ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        Member ini belum memiliki simpanan berjangka.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>