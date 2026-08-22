@php $a = $asrama ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Nama Asrama" name="nama" required>
        <x-input name="nama" :value="$a?->nama" />
    </x-field>
    <x-field label="Kapasitas" name="kapasitas" required>
        <x-input type="number" name="kapasitas" :value="$a?->kapasitas" min="1" />
    </x-field>
    <x-field label="Penanggung Jawab" name="penanggung_jawab_id" class="sm:col-span-2">
        <x-select name="penanggung_jawab_id" :value="$a?->penanggung_jawab_id" :options="$pengurusList->pluck('nama', 'id')" />
    </x-field>
</div>
