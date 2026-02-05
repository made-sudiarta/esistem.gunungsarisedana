<x-filament::widget>
    <x-filament::card class="space-y-4">

        {{-- ================= STATUS ================= --}}
        @if ($todayStatus === 'not_absen')
            <p class="text-sm text-center">
                Silahkan melakukan Absensi Masuk
            </p>

            <x-filament::button
                wire:click="absenMasuk"
                color="success"
                class="w-full font-semibold"
            >
                ABSEN MASUK KERJA
            </x-filament::button>

        @elseif ($todayStatus === 'masuk_done')
            <p class="text-sm text-center">
                Selamat bekerja dan sukses hari ini
            </p>

            <x-filament::button
                wire:click="$set('showKeluarModal', true)"
                color="danger"
                class="w-full font-semibold"
            >
                ABSEN PULANG KERJA
            </x-filament::button>

        @else
            <p class="text-center font-medium">
                ✅ Terima kasih, selamat beristirahat
            </p>
        @endif

        {{-- ================= MODAL (WAJIB DI LUAR IF) ================= --}}
        <x-filament::modal
            wire:model="showKeluarModal"
            width="md"
        >
            <x-slot name="heading">
                Formulir Absensi Keluar
            </x-slot>

            <x-slot name="description">
                Masukkan setoran dan penarikan hari ini
            </x-slot>

            <div class="space-y-4">
                <x-filament::input
                    type="number"
                    prefix="Rp"
                    label="Setoran Hari Ini"
                    wire:model.defer="jumlah_setoran"
                />

                <x-filament::input
                    type="number"
                    prefix="Rp"
                    label="Penarikan Hari Ini"
                    wire:model.defer="penarikan"
                />
            </div>

            <x-slot name="footer">
                <x-filament::button
                    wire:click="absenKeluar"
                    color="primary"
                >
                    Simpan
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

    </x-filament::card>
</x-filament::widget>
