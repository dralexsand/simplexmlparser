<?php

namespace App\Listeners;

use App\Events\RowChangeEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RowChangeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RowChangeEvent $event): void
    {
        Storage::put('parser/logevents.json', json_encode($event));
        Log::info('LOG_EVENT:handle', ['RowChangeListener']);
    }
}
