<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketVisibilityService;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return app(TicketVisibilityService::class)->userCanView($user, $ticket);
    }
}
