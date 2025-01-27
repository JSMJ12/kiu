<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_role_is_assigned()
    {
        // Ejecutar los seeders manualmente
        Artisan::call('db:seed');

        // Verificar que el rol 'Administrador' existe, si no, crearlo
        $role = Role::firstOrCreate(['name' => 'Administrador']);

        // Crear un usuario con el rol de administrador
        $user = User::factory()->withAdminRole()->create();

        // Asegurarse de que el usuario tenga el rol de 'Administrador'
        $this->assertTrue($user->hasRole('Administrador'));

        // Verificar si el usuario está autenticado (si lo necesitas)
        $response = $this->actingAs($user)->get('/dashboard/admin');

        
        $response->assertStatus(200);
        
    }
}
