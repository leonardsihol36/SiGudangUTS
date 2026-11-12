<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\MutasiStok;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiGudangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure seeder is run
        $this->artisan('db:seed');
    }

    /* =========================================================================
       1. AUTHENTICATION TESTS
       ========================================================================= */

    public function test_login_berhasil_dengan_kredensial_valid(): void
    {
        $response = $this->post('/login', [
            'username' => '25K10036',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $response = $this->post('/login', [
            'username' => '25K10036',
            'password' => 'passwordsalah123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_password_tersimpan_dalam_bentuk_hash_bcrypt(): void
    {
        $user = User::where('username', '25K10036')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotEquals('password123', $user->password);
    }

    public function test_logout_membersihkan_session(): void
    {
        $user = User::where('username', '25K10036')->first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_akun_nonaktif_ditolak_login(): void
    {
        // Set staf036 to inactive temporarily
        $user = User::where('username', 'staf036')->first();
        $user->is_active = false;
        $user->save();

        $response = $this->post('/login', [
            'username' => 'staf036',
            'password' => 'password123',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();

        // Restore active
        $user->is_active = true;
        $user->save();
    }

    /* =========================================================================
       2. AUTHORIZATION & RBAC TESTS
       ========================================================================= */

    public function test_manajer_bisa_mengakses_manajemen_pengguna(): void
    {
        $manajer = User::where('username', '25K10036')->first();

        $response = $this->actingAs($manajer)->get('/pengguna');
        $response->assertStatus(200);
    }

    public function test_staf_gudang_ditolak_mengakses_manajemen_pengguna(): void
    {
        $staf = User::where('username', 'staf036')->first();

        $response = $this->actingAs($staf)->get('/pengguna');
        $response->assertStatus(403);
    }

    public function test_auditor_ditolak_mengakses_tambah_barang(): void
    {
        $auditor = User::where('username', 'auditor036')->first();

        $response = $this->actingAs($auditor)->get('/barang-baru/create');
        $response->assertStatus(403);
    }

    /* =========================================================================
       3. DATABASE & MUTASI STOK TESTS
       ========================================================================= */

    public function test_mutasi_masuk_menambah_stok_secara_konsisten(): void
    {
        $staf = User::where('username', 'staf036')->first();
        $barang = Barang::first();
        $stokAwal = $barang->stok;
        $jumlahMasuk = 15;

        $response = $this->actingAs($staf)->post('/mutasi', [
            'barang_id' => $barang->id,
            'tipe_mutasi' => 'masuk',
            'jumlah' => $jumlahMasuk,
            'tanggal_mutasi' => now()->format('Y-m-d H:i:s'),
            'keterangan' => 'Uji Mutasi Masuk Otomatis',
        ]);

        $response->assertRedirect('/mutasi');

        $barang->refresh();
        $this->assertEquals($stokAwal + $jumlahMasuk, $barang->stok);

        // Verify mutasi record created
        $this->assertDatabaseHas('t036_mutasi_stok', [
            'barang_id' => $barang->id,
            'tipe_mutasi' => 'masuk',
            'jumlah' => $jumlahMasuk,
            'stok_sebelum' => $stokAwal,
            'stok_sesudah' => $stokAwal + $jumlahMasuk,
        ]);
    }

    public function test_barang_keluar_ditolak_jika_melebihi_stok(): void
    {
        $staf = User::where('username', 'staf036')->first();
        $barang = Barang::first();
        $stokAwal = $barang->stok;
        $jumlahKeluarBerlebih = $stokAwal + 999;

        $response = $this->actingAs($staf)->from('/mutasi-baru/create')->post('/mutasi', [
            'barang_id' => $barang->id,
            'tipe_mutasi' => 'keluar',
            'jumlah' => $jumlahKeluarBerlebih,
            'tanggal_mutasi' => now()->format('Y-m-d H:i:s'),
            'keterangan' => 'Uji Penolakan Stok Negatif',
        ]);

        $response->assertSessionHas('error');

        $barang->refresh();
        $this->assertEquals($stokAwal, $barang->stok); // Stok tidak berubah
    }

    /* =========================================================================
       4. UPLOAD FOTO TESTS
       ========================================================================= */

    public function test_upload_foto_profil_jpg_png_berhasil(): void
    {
        $manajer = User::where('username', '25K10036')->first();
        $file = UploadedFile::fake()->image('avatar_test.png', 200, 200);

        $response = $this->actingAs($manajer)->post('/profil/foto', [
            'profile_photo' => $file,
        ]);

        $response->assertSessionHas('success');
        $manajer->refresh();
        $this->assertNotNull($manajer->profile_photo);
        $this->assertFileExists(public_path($manajer->profile_photo));

        // Cleanup test file
        if (file_exists(public_path($manajer->profile_photo)) && !str_contains($manajer->profile_photo, 'leonard-25k10036.png')) {
            @unlink(public_path($manajer->profile_photo));
        }
    }

    public function test_upload_foto_profil_pdf_ditolak(): void
    {
        $manajer = User::where('username', '25K10036')->first();
        $file = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');

        $response = $this->actingAs($manajer)->post('/profil/foto', [
            'profile_photo' => $file,
        ]);

        $response->assertSessionHasErrors('profile_photo');
    }

    /* =========================================================================
       5. REST API TESTS
       ========================================================================= */

    public function test_api_login_berhasil_menghasilkan_bearer_token(): void
    {
        $response = $this->postJson('/api/login', [
            'username' => '25K10036',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'username', 'name', 'role'],
                ],
            ]);
    }

    public function test_api_identitas_mengembalikan_data_yang_benar(): void
    {
        $manajer = User::where('username', '25K10036')->first();
        $token = $manajer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/identitas');

        $response->assertStatus(200)
            ->assertJson([
                'nim' => '25K10036',
                'nama' => 'Leonard Sihol Pardede',
                'aplikasi' => 'SiGudang – 036',
            ])
            ->assertJsonStructure(['nim', 'nama', 'foto', 'aplikasi']);
    }

    public function test_api_tanpa_token_menghasilkan_401(): void
    {
        $response = $this->getJson('/api/barang');
        $response->assertStatus(401);
    }
}
