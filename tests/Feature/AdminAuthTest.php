<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login page is accessible.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Admin Panel Login');
    }

    /**
     * Test admin user can login and access admin dashboard.
     */
    public function test_admin_can_login_and_access_admin(): void
    {
        $admin = User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'password' => 'SecurePassword123!',
            'is_admin' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'SecurePassword123!',
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertAuthenticatedAs($admin);

        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Manajemen Artikel');
    }

    /**
     * Test non-admin user cannot access admin panel.
     */
    public function test_non_admin_cannot_access_admin_panel(): void
    {
        $regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => 'Password123!',
            'is_admin' => false,
        ]);

        $loginResponse = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'Password123!',
        ]);

        $loginResponse->assertSessionHasErrors('email');
        $this->assertGuest();

        // Also test direct access when logged in as regular user
        $this->actingAs($regularUser);
        $adminResponse = $this->get('/admin');
        $adminResponse->assertRedirect('/login');
    }

    /**
     * Test guest is redirected to login when accessing admin routes.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        $createResponse = $this->get('/admin/articles/create');
        $createResponse->assertRedirect('/login');
    }

    /**
     * Test admin can log out.
     */
    public function test_admin_can_logout(): void
    {
        $admin = User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'password' => 'SecurePassword123!',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/logout');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
