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

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            $headers .= "From: andrewg@lexathonenergy.com\r\n";

            $permits = Permit::where('created_at', '>=', $lastDay)->get();
            $ownerPhoneNumbers = OwnerPhoneNumber::where('created_at', '>=', $lastDay)->get();
            $wells = WellRollUp::where('created_at', '>=', $lastDay)->get();

            $leaseTable = '<table><thead>
                                    <tr>
                                        <th class="text-center">Permit Id</th>
                                        <th class="text-center">County</th>
                                        <th class="text-center">Lease Name</th>
                                        <th class="text-center">Permit Status</th>
                                    </tr>
                                    </thead><tbody>';
            $phoneNumbersTable = '<table><thead>
                                    <tr>
                                        <th class="text-center">Owner Name</th>
                                        <th class="text-center">Phone Description</th>
                                        <th class="text-center">Phone Number</th>
                                    </tr>
                                    </thead><tbody>';
            $wellsTable = '<table><thead>
                                    <tr>
                                        <th class="text-center">Well Id</th>
                                        <th class="text-center">County</th>
                                        <th class="text-center">Lease Name</th>
                                        <th class="text-center">Operator Company Name</th>
                                        <th class="text-center">Reported Operator</th>
                                        <th class="text-center">Well Name</th>
                                        <th class="text-center">Well Number</th>
                                        <th class="text-center">Well Status</th>
                                        <th class="text-center">Drill Type</th>
                                    </tr>
                                    </thead><tbody>';

            foreach ($permits as $permit) {
                $leaseTable .= '<tr>';
                $leaseTable .= '<td>' . $permit->permit_id . '</td>';
                $leaseTable .= '<td>' . $permit->county_parish . '</td>';
                $leaseTable .= '<td>' . $permit->lease_name . '</td';
                $leaseTable .= '<td>' . $permit->permit_status . '</td></tr>';
            }

            foreach ($ownerPhoneNumbers as $ownerPhoneNumber) {
                $phoneNumbersTable .= '<tr><td>' . $ownerPhoneNumber->owner_name . '</td>';
                $phoneNumbersTable .= '<td>' . $ownerPhoneNumber->phone_desc . '</td>';
                $phoneNumbersTable .= '<td>' . $ownerPhoneNumber->phone_number . '</td></tr>';
            }

            foreach ($wells as $well) {
                $wellsTable .= '<tr><td>' . $well->API14 . '</td>';
                $wellsTable .= '<td>' . $well->CountyParish . '</td>';
                $wellsTable .= '<td>' . $well->LeaseName . '</td>';
                $wellsTable .= '<td>' . $well->OperatorCompanyName . '</td>';
                $wellsTable .= '<td>' . $well->ReportedOperator . '</td>';
                $wellsTable .= '<td>' . $well->WellName . '</td>';
                $wellsTable .= '<td>' . $well->WellNumber . '</td>';
                $wellsTable .= '<td>' . $well->WellStatus . '</td>';
                $wellsTable .= '<td>' . $well->DrillType . '</td></tr>';

            }

            $leaseTable .= '</tbody></table>';
            $phoneNumbersTable .= '</tbody></table>';
            $wellsTable .= '</tbody></table>';

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

            $subject = 'Toggle Daily Report';


            mail('andrewg@lexathonenergy.com', $subject, $message, $headers);

           // william@lexathonenergy.com  audrey.huntsberger@gmail.com

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }
}
