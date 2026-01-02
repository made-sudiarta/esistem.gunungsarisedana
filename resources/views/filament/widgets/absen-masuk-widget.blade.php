<div x-data="{ showKeluarModal: @entangle('showKeluarModal') }">

    {{-- STATUS HARI INI --}}
    @if($todayStatus === 'not_absen')
        <div class="rounded-xl shadow-md p-6 flex flex-col items-center space-y-3" style="background-color:#fff !important; border-radius:15px; border:1px solid #f0ececff; padding-left:30px; padding-right:30px;">
            <!-- <div class="text-lg font-bold text-green-800">Absensi Masuk</div> -->
            <p class="text-center text-sm">Silahkan melakukan Absensi Masuk</p>

            <x-filament::button wire:click="absenMasuk"
                                color="success"
                                class="mt-6 w-full py-2 text-md"
                                style="font-weight:bold; padding-top:9px; padding-bottom:9px;"
                                wire:loading.attr="disabled">
                ABSEN MASUK KERJA
            </x-filament::button>
        </div>

    @elseif($todayStatus === 'masuk_done')
        <div class="ounded-xl shadow-md p-6 flex flex-col items-center space-y-3"  style="background-color:#fff !important; border-radius:15px; border:1px solid #f0ececff; padding-left:30px; padding-right:30px;">
            <!-- <div class="text-lg font-bold text-yellow-800">Absensi Keluar</div> -->
            <p class="text-center text-sm">Selamat Bekerja dan Sukses</p>

            <x-filament::button @click="showKeluarModal = true"
                                color="warning"
                                class="mt-6 w-full py-2 text-md"
                                style="font-weight:bold; padding-top:9px; padding-bottom:9px;">
                ABSEN PULANG KERJA
            </x-filament::button>

            {{-- MODAL --}}
            <div x-show="showKeluarModal" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center">
                
                {{-- BACKDROP --}}
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300"
                    @click="showKeluarModal = false"></div>

                {{-- MODAL BOX --}}
                <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 z-10 transform transition-transform duration-300"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    style="padding:30px;">

                    <h2 class="text-xl font-bold mb-2 text-gray-800 flex items-center gap-2">Formulir Absensi Keluar</h2>
                    <p class="text-left text-sm mb-5">
                        Silahkan masukkan setoran dan penarikan anda hari ini untuk absen keluar
                    </p>
                    <hr><br>
                    <div class="space-y-4" style="">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Setoran Hari ini</label>
                            <x-filament::input type="number"
                                            wire:model.defer="jumlah_setoran"
                                            prefix="Rp"
                                            placeholder="Masukkan jumlah setoran" style="background-color:#f9f9f9; border-radius:8px; border:1px solid #f0ececff; margin-bottom:30px;"/>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2  ">Penarikan Hari ini</label>
                            <x-filament::input type="number"
                                            wire:model.defer="penarikan"
                                            prefix="Rp"
                                            placeholder="Masukkan jumlah penarikan" style="background-color:#f9f9f9; border-radius:8px; border:1px solid #f0ececff; margin-bottom:30px;"/>
                        </div>
                    </div>
                    <hr>
                    <div class="flex justify-end gap-3 mt-6">
                        <x-filament::button wire:click="absenKeluar"
                                            color="primary"
                                            wire:loading.attr="disabled"
                                            class="px-6 py-2 text-lg">
                            Simpan
                        </x-filament::button>

                        <x-filament::button @click="showKeluarModal = false"
                                            color="gray"
                                            outlined
                                            class="px-6 py-2 text-lg">
                            Batal
                        </x-filament::button>
                    </div>
                </div>
            </div>


        </div>

    @else
        <div class="bg-white h-64 flex items-center justify-center rounded-xl shadow-md p-6 text-center font-bold text-lg" style="border:1px solid #f0ececff; height:100%;">
            ✅ Terima kasih dan selamat beristirahat
        </div>
    @endif

</div>
