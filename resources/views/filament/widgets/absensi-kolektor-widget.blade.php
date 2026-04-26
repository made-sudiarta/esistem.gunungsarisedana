<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex w-full flex-col gap-4">
            <div class="flex w-full">
                <h2 class="text-lg font-bold tracking-tight text-center md:text-left">
                    Absen Kerja
                </h2>
            </div>

            @php
                $absensi = $this->getAbsensiHariIni();
            @endphp

            <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <div 
                    class="rounded-xl border border-gray-200 p-4 dark:border-gray-700"
                    x-data="{
                        time: '',
                        updateTime() {
                            const now = new Date();

                            this.time = now.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: false,
                            });
                        }
                    }"
                    x-init="
                        updateTime();
                        setInterval(() => updateTime(), 1000);
                    "
                >
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Tanggal
                            </div>

                            <div class="mt-1 font-semibold">
                                {{ today()->format('d M Y') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-right text-gray-500 dark:text-gray-400">
                                Jam Sekarang
                            </div>

                            <div class="mt-1 text-right font-semibold" x-text="time">
                                --:--:--
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Jam Masuk
                    </div>

                    <div class="mt-1 font-semibold">
                        {{ $absensi?->jam_masuk ?? '-' }}
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Jam Keluar
                    </div>

                    <div class="mt-1 font-semibold">
                        {{ $absensi?->jam_keluar ?? '-' }}
                    </div>
                </div> -->
            </div>

           <div class="grid grid-cols-1 gap-3 w-full md:grid-cols-2 absensi-action-buttons">
                <div>
                    {{ $this->absenMasukAction }}
                </div>
                <div>
                    {{ $this->absenKeluarAction }}
                </div>
            </div>
            <style>
                .absensi-action-buttons,
                .absensi-action-buttons > div,
                .absensi-action-buttons div,
                .absensi-action-buttons button,
                .absensi-action-buttons a,
                .absensi-action-buttons .fi-ac,
                .absensi-action-buttons .fi-ac-action,
                .absensi-action-buttons .fi-btn {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .absensi-action-buttons .fi-btn {
                    display: flex !important;
                    justify-content: center !important;
                    align-items: center !important;
                    padding-top: 1rem !important;
                    padding-bottom: 1rem !important;
                    font-size: 1rem !important;
                }

                .absensi-action-buttons .fi-btn::before,
                .absensi-action-buttons .fi-btn::after,
                .absensi-action-buttons button::before,
                .absensi-action-buttons button::after {
                    width: 100% !important;
                }
            </style>
        </div>

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>