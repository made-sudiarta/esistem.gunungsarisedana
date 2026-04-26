<x-filament::widget>
    <x-filament::card>
        {{-- ================= STATUS ================= --}}
        @if ($todayStatus === 'not_absen')
            <p class="text-sm text-center">
                Silahkan melakukan Absensi Masuk
            </p>

            <x-filament::button
                wire:click="absenMasuk"
                color="primary"
                class="w-full mt-3"
            >
                Absen Masuk
            </x-filament::button>

        @elseif ($todayStatus === 'masuk_done')
            <p class="text-sm text-center mb-3">
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
    </x-filament::card>

    {{-- ================= MODAL MANUAL ================= --}}
    @if ($showKeluarModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/50 px-4"
            wire:key="modal-absen-keluar"
        >
            <div
                class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900"
                wire:click.stop
            >
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                        Formulir Absensi Keluar
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Masukkan setoran dan penarikan hari ini.
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Setoran Hari Ini
                        </label>

                        <div class="flex rounded-lg border border-gray-300 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <span class="inline-flex items-center px-3 text-sm text-gray-500 dark:text-gray-400">
                                Rp
                            </span>

                            <input
                                type="number"
                                wire:model.defer="jumlah_setoran"
                                placeholder="0"
                                min="0"
                                class="block w-full border-0 bg-transparent px-3 py-2 text-sm text-gray-950 outline-none focus:ring-0 dark:text-white"
                            >
                        </div>

                        @error('jumlah_setoran')
                            <p class="mt-1 text-sm text-danger-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Penarikan Hari Ini
                        </label>

                        <div class="flex rounded-lg border border-gray-300 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <span class="inline-flex items-center px-3 text-sm text-gray-500 dark:text-gray-400">
                                Rp
                            </span>

                            <input
                                type="number"
                                wire:model.defer="penarikan"
                                placeholder="0"
                                min="0"
                                class="block w-full border-0 bg-transparent px-3 py-2 text-sm text-gray-950 outline-none focus:ring-0 dark:text-white"
                            >
                        </div>

                        @error('penarikan')
                            <p class="mt-1 text-sm text-danger-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-filament::button
                        color="gray"
                        wire:click="$set('showKeluarModal', false)"
                    >
                        Batal
                    </x-filament::button>

                    <x-filament::button
                        color="primary"
                        wire:click="absenKeluar"
                    >
                        Simpan
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</x-filament::widget>