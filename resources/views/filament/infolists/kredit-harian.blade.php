@php
    $kreditHarians = collect($getState() ?? []);
@endphp

<div class="space-y-4">
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full min-w-[800px] text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left">No. Pokok</th>
                    <th class="px-4 py-3 text-left">Tanggal Pengajuan</th>
                    <th class="px-4 py-3 text-right">Plafon</th>
                    <th class="px-4 py-3 text-right">Sisa Pinjaman</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($kreditHarians as $kredit)
                    @php
                        $status = $kredit->status;
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            {{ $kredit->no_pokok ?: '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $kredit->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            Rp {{ number_format((float) $kredit->plafond, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            Rp {{ number_format((float) $kredit->sisa_pokok, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $status === 'lunas',
                                'bg-yellow-100 text-yellow-700' => $status === 'aktif',
                                'bg-red-100 text-red-700' => $status === 'jatuh tempo',
                            ])>
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Member ini belum memiliki pinjaman harian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>