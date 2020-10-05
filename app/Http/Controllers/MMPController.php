<?php

namespace App\Http\Controllers;

use App\Console\Commands\DetermineProduction;
use App\ErrorLog;
use App\GeneralSetting;
use App\LegalLease;
use App\MineralOwner;
use App\Permit;
use App\PermitNote;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MMPController extends Controller
{

    private $apiManager;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->apiManager = new APIManager();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {


        try {
            $users = User::all();
            $currentUser = Auth::user()->name;
            $userRole = Auth::user()->role;


            if ($userRole === 'regular') {
                $eaglePermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'eagleford')->get();
                $wtxPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'wtx')->get();
                $etxPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'etx')->get();
                $nmPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'nm')->get();
                $laPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('is_producing', 1)->where('interest_area', 'la')->get();

                $nonProducingEaglePermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'eagleford')->where('is_producing', 0)->get();
                $nonProducingWTXPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'wtx')->where('is_producing', 0)->get();
                $nonProducingETXPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'etx')->where('is_producing', 0)->get();
                $nonProducingNMPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'nm')->where('is_producing', 0)->get();
                $nonProducingLAPermits = DB::table('permits')->where('is_stored', 0)->where('assignee', Auth::user()->id)->where('interest_area', 'la')->where('is_producing', 0)->get();

                return view('userMMP', compact('userRole', 'eaglePermits', 'wtxPermits', 'nmPermits', 'users', 'currentUser', 'nonProducingEaglePermits', 'nonProducingWTXPermits', 'nonProducingNMPermits', 'etxPermits', 'nonProducingETXPermits', 'laPermits', 'nonProducingLAPermits'));
            } else {
                $nonProducingEaglePermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'eagleford')->where('is_producing', 0)->groupBy('lease_name', 'reported_operator')->get();
                $nonProducingWTXPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'wtx')->where('is_producing', 0)->groupBy('lease_name', 'reported_operator')->get();
                $nonProducingETXPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'etx')->where('is_producing', 0)->groupBy('lease_name', 'reported_operator')->get();
                $nonProducingNMPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'nm')->where('is_producing', 0)->groupBy('lease_name', 'reported_operator')->get();
                $nonProducingLAPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'la')->where('is_producing', 0)->groupBy('lease_name', 'reported_operator')->get();

                $eaglePermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'eagleford')->where('is_producing', 1)->groupBy('abstract', 'lease_name', 'survey')->get();
                $wtxPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'wtx')->where('is_producing', 1)->groupBy('abstract', 'lease_name', 'survey')->get();
                $etxPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'etx')->where('is_producing', 1)->groupBy('abstract', 'lease_name', 'survey')->get();
                $nmPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'nm')->where('is_producing', 1)->where('assignee', Auth::user()->id)->get();
                $laPermits = DB::table('permits')->where('is_stored', 0)->where('interest_area', 'la')->where('is_producing', 1)->where('assignee', Auth::user()->id)->get();

                return view('dashboard', compact('userRole', 'eaglePermits', 'wtxPermits', 'nmPermits', 'users', 'currentUser', 'nonProducingEaglePermits', 'nonProducingWTXPermits', 'nonProducingNMPermits', 'etxPermits', 'nonProducingETXPermits', 'laPermits', 'nonProducingLAPermits'));
            }

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function getPermitDetails(Request $request) {

        try {
            $permit = Permit::where('permit_id', $request->permitId)->first();

            $allRelatedPermits = Permit::where('lease_name', $permit->lease_name)->where('SurfaceLatitudeWGS84', '!=', null)->get();



            if ($request->isNonProducing) {
                $leaseData = LegalLease::select('LeaseId','Grantor', 'Range', 'Section', 'Township', 'Geometry', 'permit_stitch_id')->get();
                $leaseDescription = '';
                foreach ($leaseData as $lease) {
                    if ($lease->Geometry != '' || $lease->Geometry != null) {
                        $lease->Geometry = str_replace(['POINT (', ')', ' '], ['{"lng":', '}', ',"lat":'], $lease->Geometry);
                    }
                }

                $objData = new \stdClass;
                $objData->permit = $permit;
                $objData->allRelatedPermits = $allRelatedPermits;
                $objData->leaseDescription = $leaseDescription;
                $objData->leaseGeo = $leaseData;
            } else {
                $leaseGeo = LegalLease::where('LeaseId', $permit->stitch_lease_id)->value('Geometry');
                $leaseGeo = str_replace(['POINT (', ')', ' '], ['{"lng":', '}', ',"lat":'], $leaseGeo);
                $leaseDescription = MineralOwner::where('lease_name', $request->reportedOperator)->first();

                $objData = new \stdClass;
                $objData->permit = $permit;
                $objData->leaseDescription = $leaseDescription;
                $objData->leaseGeo = $leaseGeo;
            }
        } catch ( \Exception $e)  {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            $objData = false;
        }
        return response()->json($objData);
    }

    public function stitchLeaseToPermit(Request $request) {
        try {
            if ($request->isChecked) {
                LegalLease::where('LeaseId', $request->leaseId)
                    ->update(['permit_stitch_id' => $request->permitId]);
            } else {
                LegalLease::where('LeaseId', $request->leaseId)
                    ->update(['permit_stitch_id' => '']);
            }
            return 'success';

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function getNotes(Request $request) {
        try {
           $leaseName = Permit::where('permit_id', $request->permitId)->value('lease_name');

            return PermitNote::where('lease_name', $leaseName)->orderBy('id', 'DESC')->get();
        } catch( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateNotes(Request $request) {
        try {
            $permitInfo = Permit::where('permit_id', $request->permitId)->first();

            $userName = Auth()->user()->name;
            $date = date('d/m/Y h:m:s', strtotime('-5 hours'));

            $newPermitNote = new permitNote();

            $newPermitNote->permit_id = $request->permitId;
            $newPermitNote->lease_name = $permitInfo->lease_name;
            $newPermitNote->notes = '<div class="permit_note" id="permit_'.$newPermitNote->id.'_'. $request->permitId.'"><p style="font-size:14px; margin-bottom:0;"> '.$userName . ' | '. $date . '<span class="fas fa-trash delete_permit_note" id="delete_permit_note_'.$newPermitNote->id.'_'.$request->permitId.'" style="display:none; cursor:pointer; color:red; float:right;margin-right:5%;"></span></p>' . $request->notes .'<hr></div>';

            $newPermitNote->save();

            PermitNote::where('id', $newPermitNote->id)
                ->update(['notes' => '<div class="permit_note" id="permit_'.$newPermitNote->id.'_'. $request->permitId.'"><p style="font-size:14px; margin-bottom:0;">'.$userName . ' | '. $date . '<span class="fas fa-trash delete_permit_note" id="delete_permit_note_'.$newPermitNote->id.'_'.$request->permitId.'" style="display: none; cursor:pointer; color:red; float:right;margin-right:3%;"></span></p>' . $request->notes .'<hr></div>']);

            $updatedPermitNote = PermitNote::where('lease_name', $permitInfo->lease_name)->orderBy('id', 'DESC')->get();

            return $updatedPermitNote;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function deleteNote(Request $request) {
        try {
            $permitNote = PermitNote::where('id', $request->id)->first();

            PermitNote::destroy($request->id);

            $updatedPermitNotes = PermitNote::where('permit_id', $permitNote->permit_id)->orderBy('id', 'DESC')->get();

            return $updatedPermitNotes;
        } catch( Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateAssignee(Request $request) {
        try {
            $doesLeaseExist = Permit::where('permit_id', $request->permitId)->get();

            if ($doesLeaseExist->isEmpty()) {
                $newLease = new Permit();

                $newLease->permit_id = $request->permitId;
                $newLease->assignee = $request->assigneeId;
                $newLease->notes = '';

                $newLease->save();

                return 'success';
            } else {
                Permit::where('permit_id', $request->permitId)
                    ->update(['assignee' => $request->assigneeId]);

                return 'success';
            }
        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateStatus(Request $request) {
        try {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $_POST['permitId'] . ' Status #: ' . $_POST['status'];

            $errorMsg->save();
            Permit::where('permit_id', $_POST['permitId'])
                    ->update(['toggle_status' => $_POST['status']]);

                return $_POST['status'];

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function storePermit()
    {
        try {
            Permit::where('lease_name', $_GET['leaseName'])->update(['is_stored' => 1]);

            return 'success';
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();

            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            return 'error';
        }
    }

    public function updatePrices(Request $request) {
        try {

            GeneralSetting::where('name', 'oil')
                ->update(['value' => $request->oilPrice]);

            GeneralSetting::where('name', 'gas')
                ->update(['value' => $request->gasPrice]);

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }
}