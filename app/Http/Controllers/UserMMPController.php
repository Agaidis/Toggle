<?php

namespace App\Http\Controllers;

use App\Permit;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserMMPController extends Controller
{

    public function index() {
        try {
            $eaglePermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'eagleford')->get();
            $wtxPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'wtx')->get();
            $nmPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'nm')->get();

            $nonProducingEaglePermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'eagleford')->where('is_producing', 0)->get();
            $nonProducingWTXPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'wtx')->where('is_producing', 0)->get();
            $nonProducingNMPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'nm')->where('is_producing', 0)->get();

            $userRole = Auth::user()->role;
            $users = User::all();

            return view('userMMP', compact('eaglePermits', 'wtxPermits', 'nmPermits', 'nonProducingEaglePermits', 'nonProducingNMPermits', 'nonProducingWTXPermits', 'users', 'userRole'));
        } catch( \Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getCode());
            Log::info($e->getLine());
            mail('andrew.gaidis@gmail.com', 'Toggle Update Assignee Error', $e->getMessage());
            return view('dashboard');
        }
    }

    public function justus() {
        try {
            $eaglePermits = DB::table('permits')->where('is_stored', 0)->where('assignee', 68)->where('is_producing', 1)->where('interest_area', 'eagleford')->get();
            $wtxPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', 68)->where('is_producing', 1)->where('interest_area', 'wtx')->get();
            $nmPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', 68)->where('is_producing', 1)->where('interest_area', 'nm')->get();

            $nonProducingEaglePermits = DB::table('permits')->where('is_stored', 0)->where('assignee', 68)->where('interest_area', 'eagleford')->where('is_producing', 0)->get();
            $nonProducingWTXPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', 68)->where('interest_area', 'wtx')->where('is_producing', 0)->get();
            $nonProducingNMPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', 68)->where('interest_area', 'nm')->where('is_producing', 0)->get();
            $userRole = Auth::user()->role;
            $users = User::all();

            return view('userMMP', compact('eaglePermits', 'wtxPermits', 'nmPermits', 'nonProducingEaglePermits', 'nonProducingNMPermits', 'nonProducingWTXPermits', 'users', 'userRole'));
        } catch( \Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getCode());
            Log::info($e->getLine());
            mail('andrew.gaidis@gmail.com', 'Toggle Update Assignee Error', $e->getMessage());
            return view('dashboard');
        }
    }
}
