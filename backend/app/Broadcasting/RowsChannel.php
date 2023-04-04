<?php

namespace App\Broadcasting;

use App\Models\User;

class RowsChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    //public function join(User $user, string $message): array|bool
    public function join(string $message): array|bool|string
    {
        $date = date('Y-m-d H:i:s');
        return "datetime: $date, message: $message";
    }
}
