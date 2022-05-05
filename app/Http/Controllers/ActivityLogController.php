<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActivityLog;
use DB;

class ActivityLogController extends Controller
{
    //
    public function index() {
        return view('activity_log',['logs'=> DB::table('activity_logs')->join('activity_type', 'activity_type.id', '=', 'activity_logs.type')->get()]);
    }
    
    // get the highest count of characters linked with particular username 
    public function getTopUser()
    {
        return getTopUser(); 
    }
}
