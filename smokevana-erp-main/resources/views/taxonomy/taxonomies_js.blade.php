<script type="text/javascript">
    $(document).ready( function() {

        function getTaxonomiesIndexPage () {
            var data = {category_type : $('#category_type').val()};
            $.ajax({
                method: "GET",
                dataType: "html",
                url: '/taxonomies-ajax-index-page',
                data: data,
                async: false,
                success: function(result){
                    $('.taxonomy_body').html(result);
                }
            });
        }

        function initializeTaxonomyDataTable() {
            //Category table
            if ($('#category_table').length) {
                var category_type = $('#category_type').val();
                category_table = $('#category_table').DataTable({
                    processing: true,
                    
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    serverSide: true,
                    fixedHeader:false,
                    order: [[1,'asc']],
                    scrollY: "70vh",
                scrollX: true,
                scrollCollapse: false,
                
                    ajax: '/taxonomies?type=' + category_type,
                    columns: [
                        { data: 'logo', name: 'logo', orderable: false, searchable: false},
                        { data: 'name', name: 'name' },
                        @if($cat_code_enabled)
                            { data: 'short_code', name: 'short_code' },
                        @endif
                        { data: 'slug', name: 'slug' },
                        { data: 'visibility', name: 'visibility'},
                        { data: 'description', name: 'description' },
                        { data: 'parent_cat', name: 'parent_cat', orderable: false, searchable: false},
                        { data: 'location', name: 'location', orderable: false, searchable: false},
                        { data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                });
            }
        }

        @if(empty(request()->get('type')))
            getTaxonomiesIndexPage();
        @endif

        initializeTaxonomyDataTable();
    });
    $(document).on('submit', 'form#category_add_form', function(e) {
        e.preventDefault();
        var form = $(this);
        // var data = form.serialize();

        var formData = new FormData(form[0]);
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                __disable_submit_button(form.find('button[type="submit"]'));
            },
            success: function(result) {
                if (result.success === true) {
                    $('div.category_modal').modal('hide');
                    toastr.success(result.msg);
                    if(typeof category_table !== 'undefined') {
                        category_table.ajax.reload();
                    }

                    var evt = new CustomEvent("categoryAdded", {detail: result.data});
                    window.dispatchEvent(evt);

                    //event can be listened as
                    //window.addEventListener("categoryAdded", function(evt) {}
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
   
    // Open edit modal when clicking the Edit button in the action column
    // (buttons are rendered with class table-action-btn-edit in TaxonomyController)
    $(document).on('click', 'button.table-action-btn-edit', function() {
        $('div.category_modal').load($(this).data('href'), function() {
            $(this).modal('show');
            $('form#category_edit_form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'), 
                    dataType: 'json', 
                    data: formData, 
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) {
                        __disable_submit_button(form.find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.success == true) {
                            $('div.category_modal').modal('hide');
                            toastr.success(result.msg);
                            if (typeof category_table !== 'undefined') {
                                category_table.ajax.reload();
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error("An error occurred. Please try again.");
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    });

    // Delete category when clicking the Delete button in the action column
    // (buttons are rendered with class table-action-btn-delete)
    $(document).on('click', 'button.table-action-btn-delete', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            if (typeof category_table !== 'undefined') {
                                category_table.ajax.reload();
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    // Fallback: direct submit of edit form (if loaded without the click handler above)
    $(document).on('submit', 'form#category_edit_form', function(e) {
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success === true) {
                    $('div.category_modal').modal('hide');
                    toastr.success(result.msg);
                    category_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
</script>