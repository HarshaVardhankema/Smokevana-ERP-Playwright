<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SubscriptionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'subscription.view',
            'subscription.create',
            'subscription.update',
            'subscription.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Subscription permissions created successfully.');
    }
}
