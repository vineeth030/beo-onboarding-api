<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Services\DesignationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncDesignationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'designations:sync {userIdCode} {sessionToken}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync designations from external BEO System API to local database';

    public function __construct(private DesignationService $designationService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userIdCode = (int) $this->argument('userIdCode');
        $sessionToken = (string) $this->argument('sessionToken');

        $this->info('Starting designation sync...');

        $result = $this->designationService->syncDesignations($userIdCode, $sessionToken);

        if (! $result['success']) {
            $this->error('Sync failed: '.$result['message']);

            return Command::FAILURE;
        }

        $designations = $result['designations'];

        if (empty($designations)) {
            $this->warn('No designations found in external API.');

            return Command::SUCCESS;
        }

        $createdCount = 0;
        $updatedCount = 0;
        $failedCount = 0;

        DB::transaction(function () use ($designations, &$createdCount, &$updatedCount, &$failedCount) {
            foreach ($designations as $designation) {
                try {
                    $wasRecentlyCreated = false;

                    $model = Designation::updateOrCreate(
                        ['id' => $designation['dId']],
                        ['name' => $designation['designation']]
                    );

                    if ($model->wasRecentlyCreated) {
                        $createdCount++;
                    } else {
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $this->error("Failed to sync designation ID {$designation['dId']}: {$e->getMessage()}");
                }
            }
        });

        $this->newLine();
        $this->info('Sync completed successfully!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Fetched', count($designations)],
                ['Created', $createdCount],
                ['Updated', $updatedCount],
                ['Failed', $failedCount],
            ]
        );

        return Command::SUCCESS;
    }
}
