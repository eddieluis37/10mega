<?php
1. Instalar y publicar el paquete
En tu proyecto Laravel, instala el paquete:

bash
Copiar
Editar
composer require maatwebsite/excel
Publica la configuración (opcional, para ajustar formatos, rutas, etc.):

bash
Copiar
Editar
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
2. Definir relaciones en los modelos
Asegúrate de que tus modelos estén relacionados:

php
Copiar
Editar
// app/Models/User.php
class User extends Authenticatable
{
    // ...
    public function stores()
    {
        return $this->belongsToMany(Store::class);
    }
}
php
Copiar
Editar
// app/Models/Store.php
class Store extends Model
{
    // ...
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centrocosto_id');
    }
}
3. Crear la clase de Exportación
Genera un export con Artisan:

bash
Copiar
Editar
php artisan make:export UsersStoresExport
Luego edita app/Exports/UsersStoresExport.php así:

php
Copiar
Editar
<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    ShouldAutoSize
};

class UsersStoresExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Construye una colección de filas para el Excel.
     */
    public function collection(): Collection
    {
        $rows = collect();

        // Traemos todos los usuarios con sus bodegas y centros de costo
        $users = User::with(['stores.centroCosto'])->get();

        foreach ($users as $user) {
            if ($user->stores->isEmpty()) {
                // Si el usuario no tiene bodegas, igual mostramos la fila con nulos
                $rows->push([
                    'user_name'             => $user->name,
                    'user_email'            => $user->email,
                    'user_profile'          => $user->profile,
                    'user_password'         => $user->password,
                    'store_name'            => null,
                    'store_description'     => null,
                    'centrocosto_name'      => null,
                ]);
            } else {
                foreach ($user->stores as $store) {
                    $rows->push([
                        'user_name'             => $user->name,
                        'user_email'            => $user->email,
                        'user_profile'          => $user->profile,
                        'user_password'         => $user->password,
                        'store_name'            => $store->name,
                        'store_description'     => $store->description,
                        'centrocosto_name'      => optional($store->centroCosto)->name,
                    ]);
                }
            }
        }

        return $rows;
    }

    /**
     * Cabeceras de las columnas en el Excel.
     */
    public function headings(): array
    {
        return [
            'Usuario',
            'Email',
            'Perfil',
            'Contraseña (hash)',
            'Bodega',
            'Descripción Bodega',
            'Centro de Costo',
        ];
    }
}
4. Crear ruta y método en el controlador
En tu routes/web.php añade:

php
Copiar
Editar
use App\Http\Controllers\UserController;

Route::get('export-users', [UserController::class, 'export'])->name('users.export');
Y en app/Http/Controllers/UserController.php:

php
Copiar
Editar
<?php

namespace App\Http\Controllers;

use App\Exports\UsersStoresExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function export()
    {
        return Excel::download(new UsersStoresExport, 'usuarios_bodegas.xlsx');
    }
}


http://10mega.test/export-users
Se descargará automáticamente el archivo usuarios_bodegas.xlsx 

6. (Opcional) Ajustes adicionales
implementa interfaces como WithStyles, WithColumnFormatting o eventos WithEvents en tu clase UsersStoresExport.

Para grandes volúmenes de datos, considera usar FromQuery y la interfaz ShouldQueue para exportaciones en segundo plano.

Con estos pasos tendrás un reporte .xlsx que incluye el nombre de usuario, email, perfil, contraseña (hash)