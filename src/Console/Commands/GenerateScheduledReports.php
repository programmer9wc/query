<?php

namespace Webbycrown\QueryBuilder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Str;

class GenerateScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate';

    /**
     * The console command description.
     *
     * @var string
     */
   
    protected $description = 'Generate and send scheduled reports';

    protected $conn_key;

    /**
     * Constructor to establish database connection.
     */
    public function __construct()
    {
        $this->conn_key = connect_to_main_db();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->format('H:i');
        $database = env('DB_DATABASE');
        $reports = DB::connection($this->conn_key)->table('scheduled_reports')->where('database',$database)->where('active', 1)
            ->where('time', $now)
            ->get();

        foreach ($reports as $report) {
            try {
                $this->info("Processing report ID: {$report->id}");

                $data = $this->getReportData($report->report_type, $report->record_limit);
                $file = $this->generateFile($data, $report->format);

                $this->sendEmail($report, $file);

                $this->info("Sent to: {$report->email}");
            } catch (\Exception $e) {
                \Log::error("Report {$report->id} failed: " . $e->getMessage());
            }
        }
    }

    private function getReportData($type, $limit = 100)
    {   
         $database = env('DB_DATABASE');
        // 🔧 Replace with actual data logic based on $type and $parameters
        if( $type == 'query_lists' ){
            $query =  DB::connection($this->conn_key)->table('query_forms')->where('database',$database);
        }else{

            $query = DB::connection($this->conn_key)->table('scheduled_reports')->where('database',$database);
        }

        $querys =  $query->take($limit)->toArray();
    }

    private function generateFile($data, $format = 'csv')
    {
        $filename = 'report_' . Str::random(8) . '.' . $format;
        $path = storage_path("app/reports/$filename");

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        switch ($format) {
            case 'csv':
                $handle = fopen($path, 'w');
                fputcsv($handle, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
                $mime = 'text/csv';
                break;

            case 'pdf':
                $html = view('emails.report_pdf', compact('data'))->render();
                \PDF::loadHTML($html)->save($path);
                $mime = 'application/pdf';
                break;

            case 'xlsx':
                \Excel::store(new \App\Exports\ArrayExport($data), "reports/$filename");
                $path = storage_path("app/reports/$filename");
                $mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;

            default:
                throw new \Exception("Unsupported format: $format");
        }

        return ['path' => $path, 'filename' => $filename, 'mime' => $mime];
    }

    private function sendEmail($report, $file)
    {
            Mail::send([], [], function ($message) use ($report, $file) {
            $message->to($report->email);

            if (!empty($report->cc_email)) {
                $message->cc(explode(',', $report->cc_email));
            }

            if (!empty($report->bcc_email)) {
                $message->bcc(explode(',', $report->bcc_email));
            }

            $message->subject($report->subject ?? 'Scheduled Report');
            $message->setBody($report->body ?? 'Please find your scheduled report attached.', 'text/html');

            $message->attach($file['path'], [
                'as' => $file['filename'],
                'mime' => $file['mime'],
            ]);
        });
    }
}
