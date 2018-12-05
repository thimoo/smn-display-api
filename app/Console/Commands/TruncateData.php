<?php

namespace App\Console\Commands;

use App\Value;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'truncate data values.';

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
     * @return mixed
     */
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
  		  DB::table('values')->truncate();

        $this->info(Value::count());
  		  DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  		return true;
    }
}
