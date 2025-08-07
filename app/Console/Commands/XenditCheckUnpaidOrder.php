<?php
namespace App\Console\Commands;

use App\Services\XenditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class XenditCheckUnpaidOrder extends Command
{
    protected $signature = 'xendit:check-unpaid-order';
    protected $description = 'Check unpaid invoices and update expired ones';

    public function handle()
    {
        $this->info('Starting to check unpaid orders...');
        
        try {
            $result = XenditService::checkUnpaidOrders();
            
            $this->info('Successfully checked unpaid orders.');
            Log::info('Xendit check unpaid orders completed successfully');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to check unpaid orders: ' . $e->getMessage());
            Log::error('Xendit check unpaid orders failed: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}