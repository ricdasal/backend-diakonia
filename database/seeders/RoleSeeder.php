<?php

namespace Database\Seeders;

use App\Models\Clasificacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create(['name' => 'Administrador']);
        $role2 = Role::create(['name' => 'Usuario General']);
        $role3 = Role::create(['name' => 'Usuario Invitado']);

        # Auth Route
        Permission::create(['name' => 'DataUsers.AllUsers'])->syncRoles([$role1]);

        # ReadData Route
        Permission::create(['name' => 'readData.readData'])->syncRoles([$role1, $role2]);
        Permission::create(['name' => 'AllData.AllData'])->syncRoles([$role1]);
        Permission::create(['name' => 'AllInstituciones.AllInstituciones'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'DataInstituciones.DataInstituciones'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'DataInstituciones.DataInstitucionesId'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'DataInstitucionesDirecciones.DataInstitucionesDirecciones'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'caracterizacion.obtenerCaracterizaciones'])->syncRoles([$role1]);
        Permission::create(['name' => 'sectores.obtenerSectores'])->syncRoles([$role1]);
        Permission::create(['name' => 'actividades.obtenerActividades'])->syncRoles([$role1]);
        Permission::create(['name' => 'ingresarInstitucion.registrarInstitucion'])->syncRoles([$role1]);

        # Clasificacion
        Clasificacion::create(["nombre_clasificacion" => "Salud"]);
        Clasificacion::create(["nombre_clasificacion" => "Rehabilitacion Social"]);
        Clasificacion::create(["nombre_clasificacion" => "Exclusión Social"]);
        Clasificacion::create(["nombre_clasificacion" => "Inseguridad Alimentaria"]);
        Clasificacion::create(["nombre_clasificacion" => "Situación De Calle"]);
        Clasificacion::create(["nombre_clasificacion" => "Albergues"]);
        Clasificacion::create(["nombre_clasificacion" => "Discapacidad"]);
    }
}
