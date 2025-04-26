<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.btn-saveSchedule').click(function() {
            $('#scheduledReportForm').submit();
        });

        $('#scheduledReportForm').submit(function(e) {

            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();

            toastr.clear();

                $.ajax({
                    url: `{{route('api.reports.save')}}`,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if ( response.result ) {
                            toastr.success( response.message );
                            window.location.href = '{{ route( 'queries.index' ) }}';

                        } else if( !response.status ) {
                            var resp = {
                                message: "Validation failed", 
                                errors: {
                                    message : response.message
                                }
                            };
                            var errors = response.message;
                            $.each(errors, function(field, messages) {
                                if(['report_type', 'frequency', 'time','email', 'cc_email', 'bcc_email', 'subject','body','format','record_limit'].includes(field)){
                                    $('#' + field + '-error').text(messages[0]);
                                }


                            });
                            toastr.error(resp.message, 'Error');
                        }else{
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function (error) {
                        toastr.error( error.responseJSON.message );
                    }
                });
        });

    });
</script>