@php $u = $user ?? null; @endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Nama" name="name" required>
        <x-input name="name" :value="$u?->name" />
    </x-field>
    <x-field label="Username" name="username" required>
        <x-input name="username" :value="$u?->username" />
    </x-field>
    <x-field label="Email" name="email">
        <x-input type="email" name="email" :value="$u?->email" />
    </x-field>
    <x-field label="Role" name="role" required>
        <x-select name="role" :value="$u?->role" :placeholder="null" :options="$roles" />
    </x-field>
    <x-field label="Password" name="password" :required="! $u" :hint="$u ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 8 karakter.'">
        <x-input type="password" name="password" />
    </x-field>
    <x-field label="Konfirmasi Password" name="password_confirmation" :required="! $u">
        <x-input type="password" name="password_confirmation" />
    </x-field>
    <x-field label="Pengurus Terkait" name="pengurus_id">
        <x-select name="pengurus_id" :value="$u?->pengurus_id" :options="$pengurusList->pluck('nama', 'id')" />
    </x-field>
    <x-field label="Status" name="is_active">
        <x-select name="is_active" :value="$u ? (int) $u->is_active : 1" :placeholder="null" :options="[1 => 'Aktif', 0 => 'Nonaktif']" />
    </x-field>
</div>
