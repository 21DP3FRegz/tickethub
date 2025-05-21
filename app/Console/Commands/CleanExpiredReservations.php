<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class CleanExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired reservations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Reservation::where('reserved_until', '<', now())->delete();
        $this->info("Deleted $count expired reservations.");

        return Command::SUCCESS;
    }
}
