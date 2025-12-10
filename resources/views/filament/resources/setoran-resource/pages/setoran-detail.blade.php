<x-filament::page>

    <div class="border p-4 rounded-xl shadow mb-4">
        <h2 class="text-md font-semibold">Total Setoran</h2>
        <p class="text-xl text-gray-700 dark:text-white text-right font-bold">Rp. {{ number_format($total, 0, ',', '.') }}</p>
    </div>
    <!-- <div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-4"> -->


<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4">


        {{-- Setoran Tabungan --}}
        <!-- <a href="#" class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(230, 126, 34)"> -->
        <a href="{{ \App\Filament\Resources\SetoranSukarelaResource::getUrl('create', ['setoran_id' => $this->record->id]) }}"
            class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(230, 126, 34)">
            <x-heroicon-o-banknotes class="w-20 h-20 mr-4" style="margin-right:10px;" />
            <span class="text-xl font-bold">
                Simpanan Sukarela
                <p class="text-sm font-semibold">Rp. {{ number_format($jumlahSukarela, 0, ',', '.') }}</p>
            </span>
        </a>
        



        <!-- {{-- Setoran Beasiswa --}}
        <a href="#" class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(93, 109, 126)">
            <x-heroicon-o-academic-cap class="w-20 h-20" style="margin-right:10px;" />
            <span class="text-xl font-bold">Simpanan Beasiswa
                <p class="text-sm font-semibold">Rp. {{ number_format($jumlahBeasiswa, 0, ',', '.') }}</p>
            </span>
        </a> -->


        {{-- Setoran Kredit Harian --}}
        <a href="#" class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(93, 109, 126)">
            <x-heroicon-o-credit-card class="w-20 h-20" style="margin-right:10px;" />
            <span class="text-xl font-bold">Kredit Harian
                <p class="text-sm font-semibold">Rp. {{ number_format($jumlahAcrh, 0, ',', '.') }}</p>
            </span>
        </a>
        
        <!-- {{-- Setoran Anggota Pokok --}}
        <a href="#" class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(93, 109, 126)">
            <x-heroicon-o-user-group class="w-20 h-20" style="margin-right:10px;" />
            <span class="text-xl font-bold">Simpanan Pokok
                <p class="text-sm font-semibold">Rp. {{ number_format($jumlahPokok, 0, ',', '.') }}</p>
            </span>
        </a>

        {{-- Setoran Anggota Penyerta --}}
        <a href="#" class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(230, 126, 34)">
            <x-heroicon-o-hand-raised class="w-20 h-20" style="margin-right:10px;" />
            <span class="text-xl font-bold">Simpanan Penyerta
                <p class="text-sm font-semibold">Rp. {{ number_format($jumlahPenyerta, 0, ',', '.') }}</p>
            </span>
        </a>

        {{-- Setoran Anggota Wajib --}}
        <a href="#" class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(93, 109, 126)">
            <x-heroicon-o-identification class="w-20 h-20" style="margin-right:10px;" />
            <span class="text-xl font-bold">Simpanan Wajib
                <p class="text-sm font-semibold">Rp. {{ number_format($jumlahWajib, 0, ',', '.') }}</p>
            </span>
        </a> -->

        {{-- PROSES --}}
        <a href="#" class="text-white p-6 rounded-xl transition flex items-center shadow" style="background-color:rgb(0, 150, 80)">
            <x-heroicon-m-arrow-up-circle class="w-20 h-20" style="margin-right:10px;" />
            <span class="text-xl font-bold">Posting Setoran
                <!-- <p class="text-sm font-semibold">Rp. {{ number_format($jumlahAcrh, 0, ',', '.') }}</p> -->
            </span>
        </a>

    </div>
    <div class="space-y-6">
        <!-- <div class="border rounded-xl p-4 bg-white shadow">
            <h2 class="text-lg font-semibold">Informasi Setoran</h2>
            <p><strong>Tanggal Transaksi:</strong> {{ \Carbon\Carbon::parse($record->tanggal_trx)->format('d-m-Y') }}</p>

            <p><strong>Kolektor:</strong> {{ $record->group->group }}</p>
            <p><strong>Keterangan:</strong> {{ $record->keterangan }}</p>
            <p><strong>Total Setoran:</strong> Rp. {{ number_format($total, 0, ',', '.') }}</p>
        </div> -->

        <div class="border rounded-xl p-4 shadow">
            <h2 class="text-lg font-semibold mb-2">Detail Setoran Sukarela</h2>

            @livewire(\App\Livewire\Filament\Resources\SetoranResource\Pages\Components\SetoranSukarelaTable::class, ['setoran' => $record])
        </div>
    </div>

</x-filament::page>
