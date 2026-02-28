<section class="content">
    <h3 class=" tw-font-bold tw-text-black">Location Tax Type</h3>
    @component('components.widget', ['class' => 'box-primary', 'title' => "All Location Tax Types"])
    @slot('tool')
    <div class="box-tools">
        <button class="amazon-add-btn btn-modal" data-href="{{ route('locationtaxtype.create') }}"
            data-container=".location_tax_type_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')
        </button>
    </div>
    @endslot

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="location_tax_types_table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>

<div class="modal fade location_tax_type_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>


@section('javascript')
    <script>
        $(document).ready(function () {
            var table = $('#location_tax_types_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/locationtaxtype',
                    type: 'GET',
                    error: function (xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                drawCallback: function (settings) {
                    console.log('DataTables draw callback - records:', settings.json.recordsTotal);
                }
            });

            // Delete functionality
            $(document).on('click', '.delete-location-tax-type', function (e) {
                e.preventDefault();
                var href = $(this).data('href');
                console.log();

                swal({
                    title: "Are you sure?",
                    text: "Do you really want to change address?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then(function (willchange) {
                    if (willchange) {
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (result) {
                                if (result.success) {
                                    swal("Deleted!", "Location tax type has been deleted.", "success");
                                    table.ajax.reload();
                                } else {
                                    swal("Error!", "Something went wrong.", "error");
                                }
                            },
                            error: function () {
                                swal("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });

            // Handle form submission via AJAX for both add and edit forms
            $(document).on('submit', '#location_tax_type_add_form, #location_tax_type_edit_form', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var method = form.attr('method');
                var data = form.serialize();
                var isEdit = form.attr('id') === 'location_tax_type_edit_form';

                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('.location_tax_type_modal').modal('hide');
                            var message = isEdit ? "Location tax type has been updated successfully." : "Location tax type has been created successfully.";
                            swal("Success!", message, "success");
                            table.ajax.reload();
                            if (!isEdit) {
                                form[0].reset();
                            }
                        } else {
                            swal("Error!", response.message || "Something went wrong.", "error");
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                var input = form.find('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').remove();
                                input.after('<span class="invalid-feedback"><strong>' + value[0] + '</strong></span>');
                            });
                        } else {
                            swal("Error!", "Something went wrong.", "error");
                        }
                    }
                });
            });

            // Clear validation errors when modal is hidden
            $(document).on('hidden.bs.modal', '.location_tax_type_modal', function () {
                var forms = $('#location_tax_type_add_form, #location_tax_type_edit_form');
                forms.each(function () {
                    var form = $(this);
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                    if (form.attr('id') === 'location_tax_type_add_form') {
                        form[0].reset();
                    }
                });
            });
        });

        $(document).on('change', '#location_filter, #brand_filter, #tax_type_filter', function() {
            console.log('Filter changed: ', $(this).attr('id'), $(this).val());
            tax_rates_table.ajax.reload();
        });
        
        // Alternative event binding for select2
        $(document).on('select2:select', '#location_filter, #brand_filter, #tax_type_filter', function() {
            console.log('Select2 changed: ', $(this).attr('id'), $(this).val());
            tax_rates_table.ajax.reload();
        });

    </script>
@endsection