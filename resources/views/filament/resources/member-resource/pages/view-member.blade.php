<x-filament::page>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold">Detail Anggota</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <strong>NIA:</strong> {{ $record->nia }}
            </div>
            <div>
                <strong>NIK:</strong> {{ $record->nik }}
            </div>
            <div>
                <strong>Nama:</strong> {{ $record->nama_lengkap }}
            </div>
            <div>
                <strong>Tempat, Tanggal Lahir:</strong> {{ $record->tempat_lahir }}, {{ $record->tanggal_lahir->format('d M Y') }}
            </div>
            <div class="col-span-2">
                <strong>Alamat:</strong> {{ $record->alamat }}
            </div>
            <div>
                <strong>No HP:</strong> {{ $record->no_hp }}
            </div>
            <div>
                <strong>Jenis:</strong> {{ $record->jenis->jenis ?? '-' }}
            </div>
        </div>
    </div>
</x-filament::page>
