<?php

namespace App\Services\Session;

use App\Models\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SessionService
{
    public function initSession(): string
    {
        $uuid = Str::uuid();

        Session::insert([
            'session_id' => $uuid,
            'user_id' => Auth::id(),
            'start_session' => date('Y-m-d H:i:s'),
            'end_session' => date('Y-m-d H:i:s'),
        ]);

        return $uuid;
    }
}
