<x-filament::page>
    {{ $this->form }}

    <x-filament::button
        wire:click="simpan"
        color="primary"
        class="mt-4"
    >
        Simpan Transaksi
    </x-filament::button>
</x-filament::page>
