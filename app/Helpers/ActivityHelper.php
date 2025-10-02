<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\UserActivity;

if (!function_exists('logUserActivity')) {
    function logUserActivity($module, $description, $recordId = null, $type = null)
    {
        UserActivity::create([
            'user_id'    => Auth::id(),
            'module'     => $module,
            'description'=> $description,
            'record_id'  => $recordId,
            'type'       => $type,
            'ip_address' => Request::ip(),
        ]);
    }
}
