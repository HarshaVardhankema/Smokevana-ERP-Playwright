<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerDataMigration extends Command
{
    protected $signature = 'customer:migrate 
                            {action=preview : Action to perform: preview, run, rollback}
                            {--file= : Path to Excel file}
                            {--backup= : Path to backup file for rollback}';

    protected $description = 'Migrate customer data from Excel sheet with rollback capability';

    private $backupDir;
    private $businessId = 1;

    public function __construct()
    {
        parent::__construct();
        $this->backupDir = storage_path('app/customer_backups');
    }

    public function handle()
    {
        $action = $this->argument('action');

        // Ensure backup directory exists
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        switch ($action) {
            case 'preview':
                return $this->preview();
            case 'run':
                return $this->run_migration();
            case 'rollback':
                return $this->rollback();
            default:
                $this->error("Unknown action: $action. Use: preview, run, or rollback");
                return 1;
        }
    }

    private function normalizeNameForMatch($name)
    {
        // Normalize name for comparison: lowercase, remove extra spaces, standardize & vs and
        $name = strtolower(trim($name));
        $name = preg_replace('/\s+/', ' ', $name); // Multiple spaces to single
        $name = str_replace(' & ', '&', $name); // "A & N" -> "A&N"
        $name = str_replace(' and ', '&', $name); // "A and N" -> "A&N"
        return $name;
    }

    private function preview()
    {
        $this->info("=== CUSTOMER DATA MIGRATION PREVIEW ===\n");

        $filePath = $this->option('file') ?: base_path('Go Hunter Distro - Merged.xlsx');
        
        if (!file_exists($filePath)) {
            $this->error("Excel file not found: $filePath");
            return 1;
        }

        $excelData = $this->readExcel($filePath);
        $currentCustomers = $this->getCurrentCustomers();

        $this->info("📊 Excel File: $filePath");
        $this->info("📋 Customers in Excel: " . count($excelData));
        $this->info("📋 Customers in ERP: " . count($currentCustomers) . "\n");

        // Build normalized name maps
        $excelNormalized = [];
        foreach ($excelData as $e) {
            $excelNormalized[$this->normalizeNameForMatch($e['name'])] = $e['name'];
        }
        
        $erpNormalized = [];
        foreach ($currentCustomers as $c) {
            $erpNormalized[$this->normalizeNameForMatch($c['name'])] = $c['name'];
        }

        $toUpdate = [];
        $toCreate = [];
        $toDelete = [];

        // Find matches and creates
        foreach ($excelNormalized as $norm => $original) {
            if (isset($erpNormalized[$norm])) {
                $toUpdate[] = $original;
            } else {
                $toCreate[] = $original;
            }
        }

        // Find deletes
        foreach ($erpNormalized as $norm => $original) {
            if ($original === 'Walk-In Customer') continue;
            if (!isset($excelNormalized[$norm])) {
                $toDelete[] = $original;
            }
        }

        $this->info("📝 Actions Summary:");
        $this->table(
            ['Action', 'Count'],
            [
                ['UPDATE existing customers', count($toUpdate)],
                ['CREATE new customers', count($toCreate)],
                ['DELETE customers not in sheet', count($toDelete)],
            ]
        );

        if (count($toDelete) > 0) {
            $this->warn("\n⚠️  Customers to be DELETED:");
            foreach (array_slice($toDelete, 0, 20) as $name) {
                $this->line("   - $name");
            }
            if (count($toDelete) > 20) {
                $this->line("   ... and " . (count($toDelete) - 20) . " more");
            }
        }

        if (count($toCreate) > 0) {
            $this->info("\n➕ Customers to be CREATED:");
            foreach (array_slice($toCreate, 0, 10) as $name) {
                $this->line("   + $name");
            }
            if (count($toCreate) > 10) {
                $this->line("   ... and " . (count($toCreate) - 10) . " more");
            }
        }

        $this->info("\n🔐 Login Credentials Logic:");
        $this->line("   • Username: Email if available, else auto-generated Customer ID");
        $this->line("   • Password: From sheet if provided, else auto-generated");

        $this->newLine();
        $this->info("To execute this migration, run:");
        $this->line("   php artisan customer:migrate run --file=\"$filePath\"");
        
        $this->newLine();
        $this->warn("⚠️  A backup will be created automatically before migration.");
        $this->warn("   To rollback: php artisan customer:migrate rollback --backup=<backup_file>");

        return 0;
    }

    private function run_migration()
    {
        $this->info("=== CUSTOMER DATA MIGRATION ===\n");

        $filePath = $this->option('file') ?: base_path('Go Hunter Distro - Merged.xlsx');
        
        if (!file_exists($filePath)) {
            $this->error("Excel file not found: $filePath");
            return 1;
        }

        // Create backup first
        $backupFile = $this->createBackup();
        $this->info("✅ Backup created: $backupFile\n");

        $excelData = $this->readExcel($filePath);
        $currentCustomers = $this->getCurrentCustomers();

        // Index current customers by normalized name
        $customersByName = [];
        foreach ($currentCustomers as $c) {
            $customersByName[$this->normalizeNameForMatch($c['name'])] = $c;
        }

        // Index Excel data by normalized name
        $excelByName = [];
        foreach ($excelData as $e) {
            $excelByName[$this->normalizeNameForMatch($e['name'])] = $e;
        }

        $stats = ['updated' => 0, 'created' => 0, 'deleted' => 0, 'errors' => 0];

        DB::beginTransaction();

        try {
            // Process each customer from Excel
            $bar = $this->output->createProgressBar(count($excelData));
            $bar->start();

            foreach ($excelData as $data) {
                $nameKey = $this->normalizeNameForMatch($data['name']);
                
                if (isset($customersByName[$nameKey])) {
                    // UPDATE existing customer
                    $this->updateCustomer($customersByName[$nameKey], $data);
                    $stats['updated']++;
                } else {
                    // CREATE new customer
                    $this->createCustomer($data);
                    $stats['created']++;
                }
                
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            // DELETE customers not in Excel (except Walk-In Customer)
            $this->info("Processing deletions...");
            foreach ($currentCustomers as $c) {
                if ($c['name'] === 'Walk-In Customer') continue;
                
                $nameKey = $this->normalizeNameForMatch($c['name']);
                if (!isset($excelByName[$nameKey])) {
                    // Soft delete - mark as inactive instead of hard delete
                    DB::table('contacts')
                        ->where('id', $c['id'])
                        ->update([
                            'contact_status' => 'inactive',
                            'deleted_at' => now(),
                            'updated_at' => now(),
                        ]);
                    $stats['deleted']++;
                }
            }

            DB::commit();

            $this->info("\n✅ Migration completed successfully!\n");
            $this->table(
                ['Action', 'Count'],
                [
                    ['Updated', $stats['updated']],
                    ['Created', $stats['created']],
                    ['Deleted (soft)', $stats['deleted']],
                ]
            );

            $this->newLine();
            $this->info("📁 Backup file: $backupFile");
            $this->info("To rollback: php artisan customer:migrate rollback --backup=\"$backupFile\"");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n❌ Migration failed: " . $e->getMessage());
            $this->error("Database changes have been rolled back.");
            $this->info("Backup is available at: $backupFile");
            return 1;
        }
    }

    private function rollback()
    {
        $this->info("=== CUSTOMER DATA ROLLBACK ===\n");

        $backupFile = $this->option('backup');
        
        if (!$backupFile) {
            // List available backups
            $files = glob($this->backupDir . '/customer_backup_*.json');
            if (empty($files)) {
                $this->error("No backup files found in: $this->backupDir");
                return 1;
            }

            $this->info("Available backup files:");
            foreach ($files as $f) {
                $this->line("   - " . basename($f));
            }
            $this->newLine();
            $this->info("Usage: php artisan customer:migrate rollback --backup=\"<backup_file_path>\"");
            return 0;
        }

        // If just filename provided, add directory
        if (!file_exists($backupFile) && file_exists($this->backupDir . '/' . $backupFile)) {
            $backupFile = $this->backupDir . '/' . $backupFile;
        }

        if (!file_exists($backupFile)) {
            $this->error("Backup file not found: $backupFile");
            return 1;
        }

        $backupData = json_decode(file_get_contents($backupFile), true);

        if (!$backupData || !isset($backupData['customers'])) {
            $this->error("Invalid backup file format.");
            return 1;
        }

        $this->info("📁 Backup file: $backupFile");
        $this->info("📅 Backup date: " . $backupData['timestamp']);
        $this->info("📋 Customers in backup: " . count($backupData['customers']));
        $this->newLine();

        if (!$this->confirm('Are you sure you want to rollback? This will restore all customers to the backup state.')) {
            $this->info("Rollback cancelled.");
            return 0;
        }

        DB::beginTransaction();

        try {
            // Delete all current customers except Walk-In
            DB::table('contacts')
                ->where('type', 'customer')
                ->where('name', '!=', 'Walk-In Customer')
                ->delete();

            $this->info("Cleared existing customers...");

            // Restore from backup
            $bar = $this->output->createProgressBar(count($backupData['customers']));
            $bar->start();

            foreach ($backupData['customers'] as $customer) {
                // Remove id to let database auto-increment
                $originalId = $customer['id'];
                unset($customer['id']);
                
                // Handle null values
                foreach ($customer as $key => $value) {
                    if ($value === '') {
                        $customer[$key] = null;
                    }
                }

                DB::table('contacts')->insert($customer);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            DB::commit();

            $this->info("✅ Rollback completed successfully!");
            $this->info("📋 Restored " . count($backupData['customers']) . " customers.");

            // Clear cache
            \Artisan::call('cache:clear');
            $this->info("🔄 Cache cleared.");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n❌ Rollback failed: " . $e->getMessage());
            $this->error("Database changes have been rolled back.");
            return 1;
        }
    }

    private function createBackup()
    {
        $timestamp = date('Y-m-d_His');
        $filename = "customer_backup_{$timestamp}.json";
        $filepath = $this->backupDir . '/' . $filename;

        $customers = DB::table('contacts')
            ->where('type', 'customer')
            ->get()
            ->map(fn($c) => (array) $c)
            ->toArray();

        $backup = [
            'timestamp' => $timestamp,
            'created_at' => now()->toISOString(),
            'total_customers' => count($customers),
            'customers' => $customers,
        ];

        file_put_contents($filepath, json_encode($backup, JSON_PRETTY_PRINT));

        return $filepath;
    }

    private function readExcel($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = [];

        $rowCount = $sheet->getHighestRow();

        for ($row = 2; $row <= $rowCount; $row++) {
            $name = trim($sheet->getCell('A' . $row)->getValue() ?? '');
            
            if (empty($name)) continue;

            $data[] = [
                'name' => $name,
                'amount' => floatval($sheet->getCell('B' . $row)->getValue() ?? 0),
                'email' => trim($sheet->getCell('C' . $row)->getValue() ?? ''),
                'mobile' => trim($sheet->getCell('D' . $row)->getValue() ?? ''),
                'address' => trim($sheet->getCell('E' . $row)->getValue() ?? ''),
                'password' => trim($sheet->getCell('F' . $row)->getValue() ?? ''),
            ];
        }

        return $data;
    }

    private function getCurrentCustomers()
    {
        return DB::table('contacts')
            ->where('type', 'customer')
            ->where('business_id', $this->businessId)
            ->get()
            ->map(fn($c) => (array) $c)
            ->toArray();
    }

    private function updateCustomer($existing, $data)
    {
        $username = !empty($data['email']) ? $data['email'] : $existing['contact_id'];
        
        // Hash password if provided in sheet, otherwise keep existing or generate
        $password = $existing['password']; // Keep existing by default
        if (!empty($data['password'])) {
            $password = Hash::make($data['password']);
        } elseif (empty($existing['password'])) {
            $password = Hash::make(Str::random(8));
        }

        DB::table('contacts')
            ->where('id', $existing['id'])
            ->update([
                'name' => $data['name'],
                'email' => $data['email'] ?: null,
                'mobile' => $data['mobile'] ?: null,
                'address_line_1' => $data['address'] ?: null,
                'balance' => $data['amount'],
                'customer_u_name' => $username,
                'password' => $password,
                'contact_status' => 'active',
                'deleted_at' => null,
                'updated_at' => now(),
            ]);
    }

    private function createCustomer($data)
    {
        // Generate new contact_id
        $lastId = DB::table('contacts')
            ->where('business_id', $this->businessId)
            ->where('contact_id', 'like', 'CO%')
            ->orderBy('contact_id', 'desc')
            ->value('contact_id');

        $nextNum = 1;
        if ($lastId) {
            $nextNum = intval(substr($lastId, 2)) + 1;
        }
        $newContactId = 'CO' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        $username = !empty($data['email']) ? $data['email'] : $newContactId;
        $password = !empty($data['password']) ? Hash::make($data['password']) : Hash::make(Str::random(8));

        DB::table('contacts')->insert([
            'business_id' => $this->businessId,
            'type' => 'customer',
            'name' => $data['name'],
            'contact_id' => $newContactId,
            'email' => $data['email'] ?: null,
            'mobile' => $data['mobile'] ?: null,
            'address_line_1' => $data['address'] ?: null,
            'balance' => $data['amount'],
            'customer_u_name' => $username,
            'password' => $password,
            'contact_status' => 'active',
            'created_by' => 1,
            'credit_limit' => 0,
            'is_default' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
