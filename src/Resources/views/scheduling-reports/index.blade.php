@extends('wc_querybuilder::layout')

@section('css')
{{-- Include custom query builder CSS --}}
@include('wc_querybuilder::css.style');
@endsection
@section('content')
<div class="container">
	<div class="row">
		 <div class="col-12">
		 	 <div class="card">

		 	 	{{-- Card header with title and buttons --}}
		 	 	<div class="card-header">

		 	 		<div class="d-flex justify-content-between">
		 	 			<h2>Scheduled Report</h2>
		 	 			<div>
		 	 				<a href="{{ route( 'queries.index' ) }}" class="btn btn-secondary">{{ __('querybuilder::messages.back_button') }}</a>
		 	 				<button type="button" class="btn btn-primary btn-saveSchedule">Save Schedule</button>
		 	 			</div>
		 	 		</div>

		 	 	</div> {{-- end card header --}}

		 	 	 {{-- Card body --}}
                <div class="card-body">
                    <form id="scheduledReportForm">
                        
                        <div class="row">
                        {{-- Report Type --}}
                        <div class="col-md-4 mb-3">
                            <label for="report_type" class="form-label">{{ __('Report Type') }}</label>
                            <select name="report_type" id="report_type" class="form-select">
                                <option value="query_lists">{{ __('Query Lists') }}</option>
                                <option value="log_lists">{{ __('Log Lists') }}</option>
                            </select>
                            <span class="text-danger error" id="report_type-error" ></span>
                        </div>

                        {{-- Frequency --}}
                        <div class="col-md-4 mb-3">
                            <label for="frequency" class="form-label">{{ __('Frequency') }}</label>
                            <select name="frequency" id="frequency" class="form-select">
                                <option value="daily">{{ __('Daily') }}</option>
                                <option value="weekly">{{ __('Weekly (Monday)') }}</option>
                                <option value="monthly">{{ __('Monthly (1st)') }}</option>
                            </select>
                            <span class="text-danger error" id="frequency-error" ></span>
                        </div>

                        {{-- Delivery Time --}}
                        <div class="col-md-4 mb-3">
                            <label for="time" class="form-label">{{ __('Delivery Time') }}</label>
                            <input type="time" name="time" id="time" class="form-control" value="">
                            <span class="text-danger error" id="time-error" ></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">To (Email)</label>
                            <input type="text" name="email" class="form-control" placeholder="recipient@example.com">
                            <span class="text-danger error" id="email-error" ></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cc_email" class="form-label">CC (optional)</label>
                            <input type="text" name="cc_email" class="form-control" placeholder="cc@example.com">
                            <span class="text-danger error" id="cc_email-error" ></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="bcc_email" class="form-label">BCC (optional)</label>
                            <input type="text" name="bcc_email" class="form-control" placeholder="bcc@example.com">
                            <span class="text-danger error" id="bcc_email-error" ></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="subject" class="form-label">Email Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Monthly Report Summary">
                            <span class="text-danger error" id="subject-error" ></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="body" class="form-label">Email Body</label>
                            <textarea name="body" class="form-control" rows="4" placeholder="Please find the attached report."></textarea>
                            <span class="text-danger error" id="body-error" ></span>
                        </div>

                        {{-- File Format --}}
                        <div class="col-md-4 mb-3">
                            <label for="format" class="form-label">{{ __('File Format') }}</label>
                            <select name="format" id="format" class="form-select">
                                <option value="pdf">PDF</option>
                                <option value="xlsx">XLSX</option>
                                <option value="csv">CSV</option>
                            </select>
                            <span class="text-danger error" id="format-error" ></span>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="record_limit" class="form-label">Record Limit</label>
                            <input type="number" min="1" name="record_limit" class="form-control" placeholder="e.g., 1000">
                            <span class="text-danger error" id="record_limit-error" ></span>
                        </div>

                        {{-- Active --}}
                        <div class="col-md-4 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="active" id="active" value="1">
                            <label for="active" class="form-check-label">{{ __('Active') }}</label>
                        </div>
                    </div>

                    </form>
                </div>
		 	 </div>
		 </div>
	</div>
</div>
@section('scripts')
@include('wc_querybuilder::scripts.scheduling-reports-scripts');
@endsection