<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ClientStoredEvent;

class ClientStoredListener
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
    public function handle(ClientStoredEvent $event): void
    {
        //
    }
}
