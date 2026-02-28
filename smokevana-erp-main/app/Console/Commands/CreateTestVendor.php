<?php

namespace App\Console\Commands;

use App\Models\WpVendor;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestVendor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:create-test 
                            {--email=vendor@test.com : Email for the vendor account}
                            {--password=vendor123 : Password for the vendor account}
                            {--name=Test Vendor : Name of the vendor}
                            {--business_id=1 : Business ID for the vendor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test vendor account for Vendor Portal testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');
        $businessId = $this->option('business_id');

        $this->info('Creating test vendor account...');
        $this->newLine();

        // Check if user with this email already exists
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            // Check if already a vendor
            $existingVendor = WpVendor::where('user_id', $existingUser->id)->first();
            
            if ($existingVendor) {
                $this->warn('A vendor account with this email already exists!');
                $this->newLine();
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Vendor ID', $existingVendor->id],
                        ['Name', $existingVendor->display_name],
                        ['Email', $email],
                        ['Password', '(unchanged - use existing or reset it)'],
                        ['Status', $existingVendor->status],
                        ['Vendor Type', $existingVendor->vendor_type],
                    ]
                );
                $this->newLine();
                $this->info('Login URL: ' . url('/vendorlogin'));
                
                // Offer to reset password
                if ($this->confirm('Would you like to reset the password?')) {
                    $existingUser->update(['password' => Hash::make($password)]);
                    $this->info("Password has been reset to: {$password}");
                }
                
                return Command::SUCCESS;
            }
            
            // User exists but not a vendor - convert to vendor
            $this->info('User exists. Converting to vendor account...');
            $user = $existingUser;
            $user->update(['password' => Hash::make($password)]);
        } else {
            // Create new user
            $this->info('Creating new user...');
            
            $user = User::create([
                'surname' => $name,
                'first_name' => 'Test',
                'last_name' => 'Vendor',
                'username' => 'vendor_test_' . time(),
                'email' => $email,
                'password' => Hash::make($password),
                'business_id' => $businessId,
                'user_type' => 'vendor',
                'status' => 'active',
            ]);
            
            $this->info('User created successfully!');
        }

        // Check if vendor record exists for this user
        $vendor = WpVendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            // Create vendor record
            $this->info('Creating vendor record...');
            
            $vendor = WpVendor::create([
                'name' => $name,
                'display_name' => $name,
                'email' => $email,
                'vendor_type' => WpVendor::TYPE_ERP_DROPSHIP,
                'status' => WpVendor::STATUS_ACTIVE,
                'business_id' => $businessId,
                'user_id' => $user->id,
                'company_name' => $name . ' Company',
                'phone' => '555-0100',
                'address' => '123 Test Street, Test City, TC 12345',
                'commission_type' => WpVendor::COMMISSION_PERCENTAGE,
                'commission_value' => 10.00,
                'payment_terms' => 'monthly',
            ]);
            
            $this->info('Vendor record created!');
        } else {
            // Update vendor to be active and correct type
            $vendor->update([
                'vendor_type' => WpVendor::TYPE_ERP_DROPSHIP,
                'status' => WpVendor::STATUS_ACTIVE,
            ]);
            $this->info('Existing vendor record updated!');
        }

        // Assign vendor role
        $this->info('Assigning vendor permissions...');
        $this->assignVendorRole($user, $businessId);

        $this->newLine();
        $this->info('✅ Test vendor account created successfully!');
        $this->newLine();
        
        $this->table(
            ['Field', 'Value'],
            [
                ['Vendor ID', $vendor->id],
                ['User ID', $user->id],
                ['Name', $vendor->display_name],
                ['Email', $email],
                ['Password', $password],
                ['Status', $vendor->status],
                ['Vendor Type', $vendor->vendor_type],
                ['Business ID', $businessId],
            ]
        );

        $this->newLine();
        $this->info('===========================================');
        $this->info('VENDOR PORTAL ACCESS');
        $this->info('===========================================');
        $this->newLine();
        $this->line('Login URL: ' . url('/vendorlogin'));
        $this->line('Email:     ' . $email);
        $this->line('Password:  ' . $password);
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Assign vendor role with dropship permissions
     */
    protected function assignVendorRole($user, $businessId)
    {
        $roleName = "Dropship Vendor#{$businessId}";
        
        // Check if role exists
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)
            ->where('guard_name', 'web')
            ->first();
        
        if (!$role) {
            // Create the role with vendor permissions
            $role = \Spatie\Permission\Models\Role::create([
                'name' => $roleName,
                'guard_name' => 'web',
                'business_id' => $businessId
            ]);
        }
        
        // Ensure permissions exist and assign them to role
        $vendorPermissions = [
            'dropship.vendor.access_portal',
            'dropship.vendor.view_products',
            'dropship.vendor.manage_stock',
            'dropship.vendor.view_orders',
            'dropship.vendor.fulfill_orders',
            'dropship.vendor.view_earnings',
        ];
        
        foreach ($vendorPermissions as $permissionName) {
            // Create permission if it doesn't exist
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            
            // Assign permission to role if not already assigned
            if (!$role->hasPermissionTo($permissionName)) {
                $role->givePermissionTo($permissionName);
            }
        }
        
        // Assign role to user
        if (!$user->hasRole($roleName)) {
            $user->assignRole($roleName);
        }
    }
}
