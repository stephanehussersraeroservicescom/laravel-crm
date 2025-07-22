<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimplifiedImportService;

class ImportProductData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {type} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import product data from CSV files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }
        
        $importService = new SimplifiedImportService();
        
        switch ($type) {
            case 'roots':
                $this->info('Importing product roots...');
                $results = $importService->importProductRoots($file);
                break;
                
            case 'series':
                $this->info('Importing product series...');
                $results = $importService->importProductSeries($file);
                break;
                
            case 'prices':
                $this->info('Importing price lists...');
                $results = $importService->importPriceLists($file);
                break;
                
            default:
                $this->error("Invalid type. Use: roots, series, or prices");
                return 1;
        }
        
        // Display results
        if (isset($results['success'])) {
            $this->info("Successfully imported: {$results['success']} records");
        }
        if (isset($results['updated'])) {
            $this->info("Updated: {$results['updated']} records");
        }
        if (!empty($results['errors'])) {
            $this->error("Errors encountered:");
            foreach ($results['errors'] as $error) {
                $this->error("  - {$error}");
            }
        }
        
        return 0;
    }
}