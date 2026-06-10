<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    private string $guard = 'web';

    public function run(): void
    {
        Artisan::call('cache:forget spatie.permission.cache');

        $this->createPermissions();
        $this->createRoles();
        $this->assignUsersToRoles();

        Artisan::call('cache:forget spatie.permission.cache');
    }

    protected function createPermissions(): void
    {
        $models = ['Product', 'Category', 'Supplier', 'StockIn', 'StockOut', 'StockMutation', 'User', 'Role'];
        $methods = [
            'viewAny', 'view', 'create', 'update', 'delete',
            'deleteAny', 'restore', 'forceDelete', 'forceDeleteAny',
            'restoreAny', 'replicate', 'reorder',
        ];

        foreach ($models as $model) {
            foreach ($methods as $method) {
                Permission::findOrCreate("{$method}:{$model}", $this->guard);
            }
        }

        Permission::findOrCreate('view:Settings', $this->guard);

        $widgets = [
            'InventoryOverviewStats',
            'TransactionStats',
            'StockMovementChart',
            'StockByCategoryChart',
            'TopOutboundProductsChart',
            'LowStockProductsTable',
            'RecentStockMutationsTable',
        ];

        foreach ($widgets as $widget) {
            Permission::findOrCreate("view:{$widget}", $this->guard);
        }
    }

    protected function createRoles(): void
    {
        $allPermissions = Permission::all();

        $superAdmin = Role::findOrCreate('super_admin', $this->guard);
        $superAdmin->syncPermissions($allPermissions);

        Role::findOrCreate('panel_user', $this->guard);

        $admin = Role::findOrCreate('admin', $this->guard);
        $admin->syncPermissions($allPermissions);

        $kasirPermissions = Permission::whereIn('name', [
            'viewAny:Product', 'view:Product',
            'viewAny:Category', 'view:Category',
            'viewAny:Supplier', 'view:Supplier',
            'viewAny:StockIn', 'view:StockIn', 'create:StockIn',
            'viewAny:StockOut', 'view:StockOut', 'create:StockOut',
            'viewAny:StockMutation', 'view:StockMutation',
            'view:Settings',
            'view:InventoryOverviewStats',
            'view:TransactionStats',
            'view:StockMovementChart',
            'view:StockByCategoryChart',
            'view:TopOutboundProductsChart',
            'view:LowStockProductsTable',
            'view:RecentStockMutationsTable',
        ])->get();

        $kasir = Role::findOrCreate('kasir', $this->guard);
        $kasir->syncPermissions($kasirPermissions);
    }

    protected function assignUsersToRoles(): void
    {
        $superAdmin = User::where('email', 'admin@mail.com')->first();
        if ($superAdmin) {
            $superAdmin->assignRole('super_admin');
        }

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@inventory.web'],
            [
                'name' => 'Staff Admin',
                'password' => Hash::make('admin1234'),
            ]
        );
        $adminUser->assignRole('admin');

        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@inventory.web'],
            [
                'name' => 'Staff Kasir',
                'password' => Hash::make('kasir1234'),
            ]
        );
        $kasirUser->assignRole('kasir');
    }
}
