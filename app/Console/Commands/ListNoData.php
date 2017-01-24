<?php

namespace App\Console\Commands;

use App\Data;
use App\Value;
use App\Profile;
use Illuminate\Console\Command;

class ListNoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:nodata {--a|all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all profiles that have no-data data displayed.';

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
        $this->info('List profiles that have no-data displayed');

        $profiles = Profile::all();
        $data = Data::all();

        $table = [];

        $profiles->each(function ($profile, $key) use ($data, &$table) {
            $data->each(function ($d, $k) use ($profile, $data, &$table) {
                if ($profile->lastValue($d)->isNoData()) 
                {
                    $count = Value::countLastNoData($profile->values($d)->get());
                    if ($this->option('all'))
                    {
                        $table[] = [$profile->stn_code, $d->code, $count, ''];
                    }
                    if ($count < config('constants.max_number_no_data_to_hide_data') 
                        && $count > config('constants.max_substituted_values'))
                    {
                        $table[] = [$profile->stn_code, $d->code, $count, '✔'];
                    }
                }
            });
        });

        $this->table(['profile', 'data', 'num', 'no-data'], $table);
    }
}
