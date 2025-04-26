<?php

/**
 * Namespace containing controller classes responsible for handling query builder operations.
 */

namespace Webbycrown\QueryBuilder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Webbycrown\QueryBuilder\Services\ExportService;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateScheduledReportsConroller extends Controller
{

    protected $conn_key;

    /**
     * Constructor to establish database connection.
     */
    public function __construct()
    {
        $this->conn_key = connect_to_main_db();
    }

	public function index(Request $request)
    {   
    	 return view('wc_querybuilder::scheduling-reports.index');
    }

    public function storeScheduledReport(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'report_type'   => 'required',
            'frequency'     => 'required|in:daily,weekly,monthly',
            'time'          => 'required|date_format:H:i',
            'email'         => 'required|email',
            'cc_email'      => 'nullable|string',
            'bcc_email'     => 'nullable|string',
            'subject'       => 'nullable|string|max:255',
            'body'          => 'nullable|string',
            'format'        => 'required|in:pdf,xlsx,csv',
            'record_limit'  => 'nullable|integer|min:1',
            'active'        => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false,'message' => $validator->errors()], 200);
        }

        $database = env('DB_DATABASE');

        DB::connection($this->conn_key)->table('scheduled_reports')->insert([
            'report_type'   => $request->get('report_type'),
            'frequency'     => $request->get('frequency'),
            'time'          => $request->get('time'),
            'email'         => $request->get('email'),
            'cc_email'      => $request->get('cc_email') ?? null,
            'bcc_email'     => $request->get('bcc_email') ?? null,
            'subject'       => $request->get('subject') ?? 'Scheduled Report',
            'body'          => $request->get('body') ?? 'Please find your report attached.',
            'format'        => $request->get('format'),
            'record_limit'  => $request->get('record_limit') ?? 2000,
            'database'      => $database,
            'active'        => $request->get('active') ?? 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json(['message' => 'Scheduled Report Created Successfully']);
    }
}