<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    private string $guard = 'web';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->createPermissions();
        $this->createRoles();
        $this->assignUsersToRoles();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function createPermissions(): void
    {
        $models = ['Product', 'Category', 'Supplier', 'StockIn', 'StockOut', 'StockMutation', 'User', 'Role'];
        $methods = [
            'ViewAny', 'View', 'Create', 'Update', 'Delete',
            'DeleteAny', 'Restore', 'ForceDelete', 'ForceDeleteAny',
            'RestoreAny', 'Replicate', 'Reorder',
        ];

        foreach ($models as $model) {
            foreach ($methods as $method) {
                Permission::findOrCreate("{$method}:{$model}", $this->guard);
            }
        }

        Permission::findOrCreate('View:Settings', $this->guard);

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
            Permission::findOrCreate("View:{$widget}", $this->guard);
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
            'ViewAny:Product', 'View:Product', 'Update:Product',
            'ViewAny:Category', 'View:Category',
            'ViewAny:Supplier', 'View:Supplier',
            'ViewAny:StockIn', 'View:StockIn', 'Create:StockIn',
            'ViewAny:StockOut', 'View:StockOut', 'Create:StockOut',
            'ViewAny:StockMutation', 'View:StockMutation',
            'View:Settings',
            'View:Dashboard',
            'View:StockReports',
            'View:StockReportsTableWidget',
            'View:InventoryOverviewStats',
            'View:TransactionStats',
            'View:StockMovementChart',
            'View:StockByCategoryChart',
            'View:TopOutboundProductsChart',
            'View:LowStockProductsTable',
            'View:RecentStockMutationsTable',
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
            ['email' => 'kasir@mail.com'],
            [
                'name' => 'Staff Kasir',
                'password' => Hash::make('kasir1234'),
            ]
        );
        $kasirUser->assignRole('kasir');
    }
}
