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
                $user = $request->userId;
            } else {
                $user = Auth::user()->id;
            }

                //In here override the current user if the userId is not 0
                $highPriorityProspects = DB::select('select id, follow_up_date, lease_name, assignee, wellbore_type, owner, owner_address, owner_city, owner_zip, owner_decimal_interest, owner_interest_type, Grantor, GrantorAddress  from mineral_owners WHERE assignee = ' . $user . ' AND wellbore_type != "0"  ORDER BY FIELD(wellbore_type, "4", "3", "2", "1" ), wellbore_type DESC');

                $owners = DB::table('mineral_owners')
                    ->where('follow_up_date', '!=', NULL)
                    ->where('mineral_owners.assignee', $user)
                    ->join('permits', function ($join) {
                        $join->on('mineral_owners.lease_name', '=', 'permits.lease_name')
                            ->orOn('mineral_owners.lease_name', '=', 'quran.selected_lease_name');
                    })
               //     ->join('permits', 'mineral_owners.lease_name', '=', 'permits.lease_name')
                    ->select('mineral_owners.id', 'mineral_owners.follow_up_date', 'mineral_owners.lease_name', 'mineral_owners.assignee', 'mineral_owners.wellbore_type', 'owner', 'owner_address', 'owner_city', 'owner_zip', 'owner_decimal_interest', 'owner_interest_type', 'Grantor', 'GrantorAddress')
                    ->where(function ($query) {
                        $query->where('wellbore_type', '=', NULL)
                            ->orWhere('wellbore_type', '=', '0');

                    })->orderBy('follow_up_date', 'ASC')->groupBy('mineral_owners.owner')->get();

            return view('wellbore', compact('owners','userRole', 'userId', 'currentUser', 'highPriorityProspects', 'users'));
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }
}
