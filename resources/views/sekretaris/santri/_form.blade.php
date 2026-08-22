@php
    $s = $santri ?? null;
@endphp

<div class="space-y-8">
    <div>
        <h2 class="font-display text-base font-semibold text-ink-900">Identitas Utama</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-field label="NIS" name="nis" required>
                <x-input name="nis" :value="$s?->nis" />
            </x-field>
            <x-field label="NISN" name="nisn">
                <x-input name="nisn" :value="$s?->nisn" />
            </x-field>
            <x-field label="Nama Lengkap" name="nama_lengkap" required>
                <x-input name="nama_lengkap" :value="$s?->nama_lengkap" />
            </x-field>
            <x-field label="Nama Panggilan" name="nama_panggilan">
                <x-input name="nama_panggilan" :value="$s?->nama_panggilan" />
            </x-field>
            <x-field label="Jenis Kelamin" name="jenis_kelamin" required>
                <x-select name="jenis_kelamin" :value="$s?->jenis_kelamin" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" />
            </x-field>
            <x-field label="Tempat Lahir" name="tempat_lahir">
                <x-input name="tempat_lahir" :value="$s?->tempat_lahir" />
            </x-field>
            <x-field label="Tanggal Lahir" name="tanggal_lahir">
                <x-input type="date" name="tanggal_lahir" :value="$s?->tanggal_lahir?->format('Y-m-d')" />
            </x-field>
            <x-field label="Foto" name="foto" :hint="$s?->foto_path ? 'Sudah ada foto tersimpan, unggah untuk mengganti.' : 'JPG/PNG, maks 2MB.'">
                <input type="file" name="foto" accept="image/*"
                       class="block w-full rounded-xl border border-ink-900/10 bg-white px-3.5 py-2.5 text-sm text-ink-900 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-forest-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-forest-700" />
            </x-field>
        </div>
    </div>

    <div class="border-t border-ink-900/5 pt-6">
        <h2 class="font-display text-base font-semibold text-ink-900">Alamat</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-field label="Alamat" name="alamat" class="lg:col-span-3">
                <x-textarea name="alamat" :value="$s?->alamat" :rows="2" />
            </x-field>
            <x-field label="Provinsi" name="provinsi">
                <x-input name="provinsi" :value="$s?->provinsi" />
            </x-field>
            <x-field label="Kabupaten/Kota" name="kabupaten_kota">
                <x-input name="kabupaten_kota" :value="$s?->kabupaten_kota" />
            </x-field>
        </div>
    </div>

    <div class="border-t border-ink-900/5 pt-6">
        <h2 class="font-display text-base font-semibold text-ink-900">Orang Tua / Wali</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-field label="Nama Ayah" name="nama_ayah">
                <x-input name="nama_ayah" :value="$s?->nama_ayah" />
            </x-field>
            <x-field label="Pekerjaan Ayah" name="pekerjaan_ayah">
                <x-input name="pekerjaan_ayah" :value="$s?->pekerjaan_ayah" />
            </x-field>
            <div></div>
            <x-field label="Nama Ibu" name="nama_ibu">
                <x-input name="nama_ibu" :value="$s?->nama_ibu" />
            </x-field>
            <x-field label="Pekerjaan Ibu" name="pekerjaan_ibu">
                <x-input name="pekerjaan_ibu" :value="$s?->pekerjaan_ibu" />
            </x-field>
            <div></div>
            <x-field label="Nama Wali" name="nama_wali">
                <x-input name="nama_wali" :value="$s?->nama_wali" />
            </x-field>
            <x-field label="Kontak Wali" name="kontak_wali">
                <x-input name="kontak_wali" :value="$s?->kontak_wali" />
            </x-field>
        </div>
    </div>

    <div class="border-t border-ink-900/5 pt-6">
        <h2 class="font-display text-base font-semibold text-ink-900">Kontak Darurat</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-field label="Nama" name="kontak_darurat_nama">
                <x-input name="kontak_darurat_nama" :value="$s?->kontak_darurat_nama" />
            </x-field>
            <x-field label="Hubungan" name="kontak_darurat_hubungan">
                <x-input name="kontak_darurat_hubungan" :value="$s?->kontak_darurat_hubungan" />
            </x-field>
            <x-field label="Telepon" name="kontak_darurat_telepon">
                <x-input name="kontak_darurat_telepon" :value="$s?->kontak_darurat_telepon" />
            </x-field>
        </div>
    </div>

    <div class="border-t border-ink-900/5 pt-6">
        <div class="flex items-center gap-2">
            <h2 class="font-display text-base font-semibold text-ink-900">Data Sensitif</h2>
            <x-badge color="rose">Hanya Sekretaris</x-badge>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-field label="NIK" name="nik">
                <x-input name="nik" :value="$s?->nik" />
            </x-field>
            <x-field label="Golongan Darah" name="golongan_darah">
                <x-input name="golongan_darah" :value="$s?->golongan_darah" />
            </x-field>
            <div></div>
            <x-field label="Riwayat Kesehatan" name="riwayat_kesehatan">
                <x-textarea name="riwayat_kesehatan" :value="$s?->riwayat_kesehatan" :rows="2" />
            </x-field>
            <x-field label="Alergi" name="alergi">
                <x-textarea name="alergi" :value="$s?->alergi" :rows="2" />
            </x-field>
        </div>
    </div>

    <div class="border-t border-ink-900/5 pt-6">
        <h2 class="font-display text-base font-semibold text-ink-900">Akademik & Status</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-field label="Kelas" name="kelas_id">
                <x-select name="kelas_id" :value="$s?->kelas_id" :options="$kelasList->pluck('nama', 'id')" />
            </x-field>
            <x-field label="Status" name="status" required>
                <x-select name="status" :value="$s?->status ?? 'Aktif'" :placeholder="null"
                          :options="['Aktif' => 'Aktif', 'Lulus' => 'Lulus', 'Boyong' => 'Boyong']" />
            </x-field>
            <div></div>
            <x-field label="Tanggal Masuk" name="tanggal_masuk">
                <x-input type="date" name="tanggal_masuk" :value="$s?->tanggal_masuk?->format('Y-m-d')" />
            </x-field>
            <x-field label="Tanggal Keluar" name="tanggal_keluar">
                <x-input type="date" name="tanggal_keluar" :value="$s?->tanggal_keluar?->format('Y-m-d')" />
            </x-field>
            <div></div>
            <x-field label="Keterangan Keluar" name="keterangan_keluar" class="sm:col-span-2 lg:col-span-3">
                <x-textarea name="keterangan_keluar" :value="$s?->keterangan_keluar" :rows="2" />
            </x-field>
        </div>
    </div>
</div>
