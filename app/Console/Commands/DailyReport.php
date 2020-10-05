<?php

namespace App\Console\Commands;

use App\ErrorLog;
use App\OwnerPhoneNumber;
use App\Permit;
use App\WellRollUp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Mailgun\Mailgun;


class DailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:DailyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a daily email of the new leases, phone numbers and wells.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $lastDay = date('Y-m-d H:i:s',strtotime('-24 hours'));

            $permits = Permit::where('created_at', '>=', $lastDay)->get();
            $ownerPhoneNumbers = OwnerPhoneNumber::where('created_at', '>=', $lastDay)->get();
            $wells = WellRollUp::where('created_at', '>=', $lastDay)->get();

            $leaseTable = '<table><tbody>';
            $phoneNumbersTable = '<table><tbody>';
            $wellsTable = '<table><tbody>';

            foreach ($permits as $permit) {
                $leaseTable .= '<tr><th width="50%">Entity</th><th>Details</th></tr>';
                $leaseTable .= '<tr><td width="50%">Permit Id</td><td>' . $permit->permit_id . '</td></tr>';
                $leaseTable .= '<tr><td width="50%">County</td><td>' . $permit->county_parish . '</td></tr>';
                $leaseTable .= '<tr><td width="50%">Lease Name</td><td>' . $permit->lease_name . '</td></tr>';
                $leaseTable .= '<tr><td width="50%">County</td><td>' . $permit->permit_type . '</td></tr>';
                $leaseTable .= '<tr><td width="50%">Lease Name</td><td>' . $permit->permit_status . '</td></tr>';
            }

            foreach ($ownerPhoneNumbers as $ownerPhoneNumber) {
                $phoneNumbersTable .= '<tr><th width="50%">Entity</th><th>Details</th></tr>';
                $phoneNumbersTable .= '<tr><td width="50%">Owner Name</td><td>' . $ownerPhoneNumber->owner_name . '</td></tr>';
                $phoneNumbersTable .= '<tr><td width="50%">Phone Description</td><td>' . $ownerPhoneNumber->phone_desc . '</td></tr>';
                $phoneNumbersTable .= '<tr><td width="50%">Phone Number</td><td>' . $ownerPhoneNumber->phone_number . '</td></tr>';
            }

            foreach ($wells as $well) {
                $wellsTable .= '<tr><th width="50%">Entity</th><th>Details</th></tr>';
                $wellsTable .= '<tr><td width="50%">Well Id</td><td>' . $well->uid . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">County</td><td>' . $well->CountyParish . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">Lease Name</td><td>' . $well->LeaseName . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">Operator Company Name</td><td>' . $well->OperatorCompanyName . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">Reported Operator</td><td>' . $well->ReportedOperator . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">Well Name</td><td>' . $well->WellName . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">Well Number</td><td>' . $well->WellNumber . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">Well Status</td><td>' . $well->WellStatus . '</td></tr>';
                $wellsTable .= '<tr><td width="50%">Drill Type</td><td>' . $well->DrillType . '</td></tr>';

            }

            $leaseTable .= '</tbody></table>';
            $phoneNumbersTable .= '</tbody></table>';
            $wellsTable .= '</tbody></table>';

            $subject = 'Toggle Daily Report';
            $message = '<html><body>';
            $message .= '<style>
            h1{
 padding: 5px;
            }
            th, td {
                padding: 15px;
                text-align: center;
            }
            th {
                text-align: center;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            table, th, td {
                border: 1px solid #000000;
            }
            tr:nth-child(even) {background-color: #dedede}</style>';
            $message .= '<h1>New Leases</h1>';
            $message .= $leaseTable;
            $message .= '<h1>New Phone Numbers</h1>';
            $message .= $phoneNumbersTable;
            $message .= '<h1>New Wells</h1>';
            $message .= $wellsTable;
            $message .= '</body></html>';


            // First, instantiate the SDK with your API credentials
            $mg = Mailgun::create(env('MAIL_API_KEY')); // For US servers

            $mg->messages()->send('sandboxd2bb4a70ddf345fb86cab99733a22be7.mailgun.org', [
                'from'    => 'LTE Toggle <service@toggle.com>',
                'to'      => 'william@lexathonenergy.com',
                'subject' => $subject,
                'text'    => 'Text Report',
                'html'    => $message
            ]);

            $mg->messages()->send('sandboxd2bb4a70ddf345fb86cab99733a22be7.mailgun.org', [
                'from'    => 'LTE Toggle <service@toggle.com>',
                'to'      => 'andrewg@lexathonenergy.com',
                'subject' => $subject,
                'text'    => 'Text Report',
                'html'    => $message
            ]);

            $mg->messages()->send('sandboxd2bb4a70ddf345fb86cab99733a22be7.mailgun.org', [
                'from'    => 'LTE Toggle <service@toggle.com>',
                'to'      => 'audrey.huntsberger@gmail.com',
                'subject' => $subject,
                'text'    => 'Text Report',
                'html'    => $message
            ]);



        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }
}
