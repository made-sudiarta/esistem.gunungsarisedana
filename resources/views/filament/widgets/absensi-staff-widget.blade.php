<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex w-full flex-col gap-4">
            <div class="flex w-full justify-center md:justify-start">
                <h2 class="text-lg font-bold tracking-tight text-center md:text-left">
                    Absen Kerja
                </h2>
            </div>

            @php
                $absensi = $this->getAbsensiHariIni();
            @endphp

            <div class="grid grid-cols-1 gap-3">
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
                            <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                Jam Sekarang
                            </div>

                            <div class="mt-1 text-right font-semibold" x-text="time">
                                --:--:--
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div 
                class="grid grid-cols-1 gap-3 w-full md:grid-cols-2 absensi-action-buttons"
                x-data="{
                    ambilLokasi(callback) {
                        if (!navigator.geolocation) {
                            alert('Browser Anda tidak mendukung fitur lokasi.');
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            callback,
                            function (error) {
                                console.log(error);

                                alert('Gagal mengambil lokasi. Pastikan izin lokasi aktif dan website menggunakan HTTPS.');
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 15000,
                                maximumAge: 0,
                            }
                        );
                    },

                    absenMasuk() {
                        this.ambilLokasi(function (position) {
                            Livewire.dispatch('staff-absen-masuk-dengan-lokasi', {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                            });
                        });
                    },

                    absenKeluar() {
                        this.ambilLokasi(function (position) {
                            Livewire.dispatch('staff-absen-keluar-dengan-lokasi', {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                            });
                        });
                    }
                }"
            >
                <div>
                    <button
                        type="button"
                        x-on:click="absenMasuk()"
                        @if (filled($absensi?->jam_masuk)) disabled @endif
                        class="w-full rounded-lg bg-primary-600 px-4 py-4 text-base font-semibold text-white shadow-sm hover:bg-primary-500 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Absen Masuk
                    </button>
                </div>

                <div>
                    <button
                        type="button"
                        x-on:click="absenKeluar()"
                        @if (blank($absensi?->jam_masuk) || filled($absensi?->jam_keluar)) disabled @endif
                        style="background-color: #f59e0b;"
                        class="w-full rounded-lg px-4 py-4 text-base font-semibold text-white shadow-sm hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Absen Keluar
                    </button>
                </div>
            </div>

            <style>
                .absensi-action-buttons,
                .absensi-action-buttons > div,
                .absensi-action-buttons div,
                .absensi-action-buttons button,
                .absensi-action-buttons a {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .absensi-action-buttons button {
                    display: flex !important;
                    justify-content: center !important;
                    align-items: center !important;
                    padding-top: 1rem !important;
                    padding-bottom: 1rem !important;
                    font-size: 1rem !important;
                }

                .absensi-action-buttons button::before,
                .absensi-action-buttons button::after {
                    width: 100% !important;
                }
            </style>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>