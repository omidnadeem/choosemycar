<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repository\FileRepository;
use App\Repository\DealerAndVehicleRepository;

class FeedListVehiclesAndDealersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChooseMyCar:feed:process {--dealers=} {--cars=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing the vehicles and dealers files (XML or JSON format)';
    protected $fileRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileRepository = new FileRepository();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        try {

            if (!$this->option('dealers')) return $this->error('Dealers file is missing', 'Usage: --dealers=file.{json|xml}');
            if (!$this->option('cars')) return $this->error('Vehicles file is missing', 'Usage: --cars=file.{json|xml}');

            $this->line('');
            $this->info('Processing the dealers and vehicles');
            $this->line('');

            $dealerFile = $this->option('dealers');
            $carFile = $this->option('cars');
            $dealerFileContent = $this->fileRepository->readFile($dealerFile)->getFileContent();
            $carFileContent = $this->fileRepository->readFile($carFile)->getFileContent();


            $this->info('Total dealers: ' . count($dealerFileContent));
            $this->info('Total vehicles:' . count($carFileContent));
            $this->line('');

            $totalRecords = intval(count($dealerFileContent) + count($carFileContent));
            $bar = $this->output->createProgressBar($totalRecords);
            $bar->start();

            if (app(DealerAndVehicleRepository::class)->process($dealerFileContent, $carFileContent)) {

                $this->line('');
                $bar->advance($totalRecords);
                $this->info('Process completed!');
                $this->line('');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
