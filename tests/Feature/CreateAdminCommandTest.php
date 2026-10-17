<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test command fails when password is not supplied and mode is non-interactive.
     */
    public function test_command_fails_without_password_in_no_interaction(): void
    {
        config([
            'admin.demo.email' => 'admin@example.com',
            'admin.demo.password' => '',
        ]);

        $this->artisan('admin:create', ['--no-interaction' => true])
            ->expectsOutputToContain('ADMIN_PASSWORD atau opsi --password belum diisi')
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test command creates admin user successfully with arguments.
     */
    public function test_command_creates_admin_user_successfully(): void
    {
        $this->artisan('admin:create', [
            '--email' => 'newadmin@example.com',
            '--name' => 'Lead Admin',
            '--password' => 'SuperSecret123!',
        ])->expectsOutputToContain('berhasil dibuat dengan status admin aktif')
            ->assertExitCode(0);

        $user = User::where('email', 'newadmin@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);
        $this->assertEquals('Lead Admin', $user->name);
        $this->assertTrue(Hash::check('SuperSecret123!', $user->password));
    }

    /**
     * Test command updates existing user idempotently without duplicating.
     */
    public function test_command_updates_existing_user_without_duplication(): void
    {
        $existing = User::create([
            'name' => 'Old Name',
            'email' => 'existing@example.com',
            'password' => 'OldPassword123!',
            'is_admin' => false,
        ]);

        $this->artisan('admin:create', [
            '--email' => 'existing@example.com',
            '--name' => 'Updated Name',
            '--password' => 'NewPassword123!',
        ])->expectsOutputToContain('berhasil diperbarui')
            ->assertExitCode(0);

        $this->assertDatabaseCount('users', 1);

        $fresh = $existing->fresh();
        $this->assertEquals('Updated Name', $fresh->name);
        $this->assertTrue($fresh->is_admin);
        $this->assertTrue(Hash::check('NewPassword123!', $fresh->password));
    }
}
