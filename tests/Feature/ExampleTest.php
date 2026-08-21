<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * FIX: test bawaan Laravel meng-assert status 200 di "/", padahal routes/web.php
     * aplikasi ini men-redirect "/" ke halaman login (lihat Route::get('/', fn () =>
     * redirect()->route('login'))). Assert 200 akan selalu gagal; diganti jadi
     * assertRedirect ke route('login').
     */
    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    // Catatan: test untuk halaman login (GET route('login')) sengaja belum
    // ditambahkan karena view 'auth.login' belum dibuat (lihat pembahasan
    // sebelumnya soal views yang ditunda). Tambahkan setelah view tersedia.
}