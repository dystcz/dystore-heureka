<?php

namespace Modules\DystoreHeureka\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GenerateHeurekaFeed implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->queue = 'long-running';
    }

    /**
     * Generate sitemap.
     */
    public function handle(): void
    {
        $name = 'heureka';

        $feed = Config::get("feed.feeds.{$name}");

        $response = Http::timeout(60 * 2)->get(url($feed['url']));

        Storage::disk('public')->put('heureka-feed.xml', $response->body());
    }
}
