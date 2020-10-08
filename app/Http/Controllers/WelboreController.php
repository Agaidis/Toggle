<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\MineralOwner;
use App\Permit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WelboreController extends Controller
{

    private $apiManager;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('Auth');
        $this->apiManager = new APIManager();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        try {
            $currentUser = Auth::user()->name;
            $userRole = Auth::user()->role;

            $userId = $request->userId;
            $users = User::all();

            if ($request->userId != 0) {
                //In here override the current user if the userId is not 0
                $highPriorityProspects = DB::select('select id, follow_up_date, lease_name, assignee, wellbore_type, owner, owner_address, owner_city, owner_zip, owner_decimal_interest, owner_interest_type, Grantor, GrantorAddress  from mineral_owners WHERE assignee = ' . $request->userId . ' AND wellbore_type != "0" ORDER BY FIELD(wellbore_type, "4", "3", "2", "1" ), wellbore_type DESC');

                $owners = DB::table('mineral_owners')
                    ->where('follow_up_date', '!=', NULL)
                    ->where('assignee', $request->userId)
                    ->where(function ($query) {
                        $query->where('wellbore_type', '=', NULL)
                            ->orWhere('wellbore_type', '=', '0');
                    })->orderBy('follow_up_date', 'ASC')->get();

            } else {
                //In here override the current user if the userId is not 0
                $highPriorityProspects = DB::select('select id, follow_up_date, lease_name, assignee, wellbore_type, owner, owner_address, owner_city, owner_zip, owner_decimal_interest, owner_interest_type, Grantor, GrantorAddress  from mineral_owners WHERE assignee = ' . Auth::user()->id . ' AND wellbore_type != "0" ORDER BY FIELD(wellbore_type, "4", "3", "2", "1" ), wellbore_type DESC');

                $owners = DB::table('mineral_owners')
                    ->where('follow_up_date', '!=', NULL)
                    ->where('assignee', Auth::user()->id)
                    ->where(function ($query) {
                        $query->where('wellbore_type', '=', NULL)
                            ->orWhere('wellbore_type', '=', '0');
                    })->orderBy('follow_up_date', 'ASC')->get();

            }

            return view('wellbore', compact('owners','userRole', 'userId', 'currentUser', 'highPriorityProspects', 'users'));
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }
}
