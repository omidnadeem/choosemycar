<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repository\DealerAndVehicleRepository;

class FeedProcessVehiclesAndDealersCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChooseMyCar:feed:list-dealers-with-cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the number of cars for each dealer (Available, Processing and Sold)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $dealers = app(DealerAndVehicleRepository::class)->getDealerSummary()->toArray();
        $headers = ['Name', 'Available', 'Sold', 'Reserved', 'Total'];
        $this->table($headers, $dealers);
    }
}
