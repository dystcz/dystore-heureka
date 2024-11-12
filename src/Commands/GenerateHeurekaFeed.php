<?php

namespace Modules\DystoreHeureka\Commands;

use Illuminate\Console\Command;
use Modules\DystoreHeureka\Jobs\GenerateHeurekaFeed as GenerateFeed;

class GenerateHeurekaFeed extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'feeds:heureka:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Heureka feed.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        GenerateFeed::dispatch();
    }
}
