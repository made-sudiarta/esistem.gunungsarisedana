@php
    $riwayat = $getState();
    $paginator = $this->getRiwayatSimpananPaginated();
@endphp

<div class="space-y-4">
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full min-w-[760px] text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-3 py-3">No.</th>
                    <th class="px-3 py-3">Tanggal</th>
                    <th class="px-3 py-3 text-right">Pokok</th>
                    <th class="px-3 py-3 text-right">Penyerta</th>
                    <th class="px-3 py-3 text-right">Wajib</th>
                    <th class="px-3 py-3 text-right">Saldo</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($paginator as $trx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-3 py-3">{{ $trx['no'] }}</td>

                        <td class="px-3 py-3 whitespace-nowrap">
                            {{ $trx['tanggal']
                                ? \Illuminate\Support\Carbon::parse($trx['tanggal'])->format('d/m/Y')
                                : '-' }}
                        </td>

                        @foreach (['pokok', 'penyerta', 'wajib'] as $kolom)
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                @if ($trx[$kolom] != 0)
                                    Rp {{ number_format($trx[$kolom], 0, ',', '.') }}
                                @else
                                    —
                                @endif
                            </td>
                        @endforeach

                        <td class="px-3 py-3 text-right whitespace-nowrap font-semibold">
                            Rp {{ number_format($trx['saldo'], 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                            Belum ada transaksi simpanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-500">
            Menampilkan {{ $paginator->firstItem() ?? 0 }}
            sampai {{ $paginator->lastItem() ?? 0 }}
            dari {{ $paginator->total() }} transaksi
        </div>

        {{ $paginator->links() }}
    </div>
</div>