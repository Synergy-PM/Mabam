<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;  


class UserActivityController extends Controller
{
     public function index()
    {
        $userActivities = UserActivity::orderBy('created_at', 'desc')->get();
        return view('admin.user.activity_show', compact('userActivities'));
    }
}
