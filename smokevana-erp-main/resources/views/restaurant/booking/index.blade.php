@extends('layouts.app')
@section('title', __('restaurant.bookings'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* Booking subtitle */
.admin-amazon-page .booking-header-subtitle {
    font-size: 13px;
    color: rgba(255,255,255,0.88);
    margin: 4px 0 0 0;
}
.admin-amazon-page .content-header h1 i {
    color: #ff9900;
    margin-right: 6px;
}

/* Amazon Cards */
.admin-amazon-page .box.box-primary {
    background: #ffffff !important;
    border: 1px solid #d5d9d9 !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08) !important;
    overflow: hidden;
    border-top: none !important;
}

.admin-amazon-page .box.box-primary > .box-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    border-bottom: 3px solid #ff9900 !important;
    padding: 14px 20px !important;
    border-radius: 0 !important;
}

.admin-amazon-page .box.box-primary > .box-header .box-title {
    color: #ffffff !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.admin-amazon-page .box.box-primary > .box-header .box-title i {
    color: #ff9900 !important;
    margin-right: 8px;
}

.admin-amazon-page .box.box-primary > .box-body {
    padding: 18px 20px !important;
}

/* Business Location Filter Card */
.admin-amazon-page .booking-filter-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    padding: 16px 20px;
    margin-bottom: 16px;
}

.admin-amazon-page .booking-filter-card .select2-container {
    width: 50% !important;
}

.admin-amazon-page .booking-filter-card .select2-container--default .select2-selection--single {
    border: 1px solid #d5d9d9;
    border-radius: 6px;
    height: 38px;
}

.admin-amazon-page .booking-filter-card .select2-container--default .select2-selection--single:focus,
.admin-amazon-page .booking-filter-card .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

/* Today's Bookings Table */
.admin-amazon-page #todays_bookings_table thead th {
    background: #232f3e !important;
    color: #ffffff !important;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 11px 14px;
    border: none !important;
}

.admin-amazon-page #todays_bookings_table tbody td {
    font-size: 13px;
    color: #0f1111;
    padding: 10px 14px;
    border-bottom: 1px solid #e7e7e7 !important;
    border-left: none !important;
    border-right: none !important;
    vertical-align: middle;
}

.admin-amazon-page #todays_bookings_table tbody tr:hover td {
    background: #fef8f0 !important;
}

.admin-amazon-page .dataTables_info {
    font-size: 12px;
    color: #565959;
    padding: 10px 0 !important;
}

.admin-amazon-page .dataTables_paginate .paginate_button {
    border-radius: 4px !important;
    font-size: 12px !important;
    margin: 0 2px !important;
}

.admin-amazon-page .dataTables_paginate .paginate_button.current {
    background: #ff9900 !important;
    color: #ffffff !important;
    border-color: #e47911 !important;
}

.admin-amazon-page .dataTables_paginate .paginate_button:hover {
    background: #232f3e !important;
    color: #ffffff !important;
    border-color: #232f3e !important;
}

/* Calendar view buttons - orange selection */
.admin-amazon-page .fc-button-group button.fc-button-active,
.admin-amazon-page .fc-button-group button.fc-state-active,
.admin-amazon-page .fc-button.fc-button-active,
.admin-amazon-page .fc-button.fc-state-active {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: #fff !important;
    box-shadow: 0 2px 4px rgba(255,153,0,0.3) !important;
}
.admin-amazon-page .fc-button-group button:hover,
.admin-amazon-page .fc-button:hover {
    border-color: #ff9900 !important;
    background: rgba(255,153,0,0.1) !important;
}
.admin-amazon-page .fc-button-group button,
.admin-amazon-page .fc-button {
    border-color: #D5D9D9 !important;
    color: #0f1111 !important;
    border-radius: 4px !important;
    font-weight: 600;
}
.admin-amazon-page .fc-today-button {
    background: linear-gradient(to bottom, #37475a 0%, #232f3e 100%) !important;
    border-color: #232f3e !important;
    color: #fff !important;
}
.admin-amazon-page .fc-today-button:hover {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: #fff !important;
}

/* Calendar Header */
.admin-amazon-page .fc-toolbar h2 {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0f1111 !important;
}

/* Calendar Day Headers */
.admin-amazon-page .fc-day-header {
    background: #37475a !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    padding: 10px 0 !important;
}

/* Calendar Today Cell */
.admin-amazon-page .fc-today {
    background: #fff8e7 !important;
}

/* Calendar Grid */
.admin-amazon-page .fc-widget-content,
.admin-amazon-page .fc-widget-header {
    border-color: #e7e7e7 !important;
}

/* Add New Booking Button */
.admin-amazon-page #add_new_booking_btn {
    background: #ff9900 !important;
    color: #ffffff !important;
    border: 1px solid #e47911 !important;
    border-radius: 6px !important;
    padding: 8px 18px !important;
    font-weight: 600;
    font-size: 13px;
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3);
    transition: all 0.2s ease;
}

.admin-amazon-page #add_new_booking_btn:hover {
    background: #e47911 !important;
    box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4);
    transform: translateY(-1px);
}

/* Status legend - Amazon style */
.admin-amazon-page .external-event {
    border-radius: 6px;
    padding: 8px 12px;
    margin-bottom: 8px;
    font-weight: 600;
    border: 1px solid rgba(0,0,0,0.1);
}
.admin-amazon-page .external-event.bg-yellow {
    background: #fff8e7 !important;
    border-color: #ffb84d !important;
    color: #b45309 !important;
}
.admin-amazon-page .external-event.bg-light-blue {
    background: #eff6ff !important;
    border-color: #93c5fd !important;
    color: #1e40af !important;
}
.admin-amazon-page .external-event.bg-green {
    background: #d1fae5 !important;
    border-color: #10b981 !important;
    color: #065f46 !important;
}
.admin-amazon-page .external-event.bg-red {
    background: #fee2e2 !important;
    border-color: #dc2626 !important;
    color: #991b1b !important;
}

/* Help block text */
.admin-amazon-page .help-block {
    color: #565959;
    font-size: 12px;
    line-height: 1.5;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fas fa-calendar-alt"></i> @lang('restaurant.bookings')</h1>
    <p class="booking-header-subtitle">Manage restaurant reservations and table bookings</p>
</section>

<!-- Main content -->
<section class="content">
    @if(count($business_locations) > 1)
    <div class="booking-filter-card">
        <select id="business_location_id" class="select2" style="width:50%">
            <option value="">@lang('purchase.business_location')</option>
            @foreach( $business_locations as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-list"></i> @lang('restaurant.todays_bookings')</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered table-condensed" id="todays_bookings_table">
                        <thead>
                        <tr>
                            <th>@lang('contact.customer')</th>
                            <th>@lang('restaurant.booking_starts')</th>
                            <th>@lang('restaurant.booking_ends')</th>
                            <th>@lang('restaurant.table')</th>
                            <th>@lang('messages.location')</th>
                            <th>@lang('restaurant.service_staff')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-10">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-calendar"></i> @lang('restaurant.bookings')</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="button" class="btn btn-primary tw-dw-btn-primary" id="add_new_booking_btn"><i class="fa fa-plus"></i> @lang('restaurant.add_booking')</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-info-circle"></i> @lang('lang_v1.status')</h3>
                </div>
                <div class="box-body">
                    <!-- the events -->
                    <div class="external-event bg-yellow text-center" style="position: relative;">
                        <small>@lang('lang_v1.waiting')</small>
                    </div>
                    <div class="external-event bg-light-blue text-center" style="position: relative;">
                        <small>@lang('restaurant.booked')</small>
                    </div>
                    <div class="external-event bg-green text-center" style="position: relative;">
                        <small>@lang('restaurant.completed')</small>
                    </div>
                    <div class="external-event bg-red text-center" style="position: relative;">
                        <small>@lang('restaurant.cancelled')</small>
                    </div>
                    <small>
                    <p class="help-block">
                        <i>@lang('restaurant.click_on_any_booking_to_view_or_change_status')<br><br>
                        @lang('restaurant.double_click_on_any_day_to_add_new_booking')
                        </i>
                    </p>
                    </small>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
@include('restaurant.booking.create')
</section>
</div>
<!-- /.content -->

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    @include('contact.create', ['quick_add' => true])
</div>

@endsection

@section('javascript')
    
    <script type="text/javascript">
        $(document).ready(function(){
            clickCount = 0;
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay,listWeek'
                },
                eventLimit: 2,
                events: '/bookings',
                eventRender: function (event, element) {
                    var title_html = event.customer_name;
                    if(event.table){
                        title_html += '<br>' + event.table;
                    }
                    // title_html += '<br>' + event.start_time + ' - ' + event.end_time;

                    element.find('.fc-title').html(title_html);
                    element.attr('data-href', event.url);
                    element.attr('data-container', '.view_modal');
                    element.addClass('btn-modal');
                },
                dayClick:function( date, jsEvent, view ) {
                    clickCount ++;
                    if( clickCount == 2 ){
                       $('#add_booking_modal').modal('show');
                       $('form#add_booking_form #start_time').data("DateTimePicker").date(date).ignoreReadonly(true);
                       $('form#add_booking_form #end_time').data("DateTimePicker").date(date).ignoreReadonly(true);
                    }
                    var clickTimer = setInterval(function(){
                        clickCount = 0;
                        clearInterval(clickTimer);
                    }, 500);
                }
            });

            //If location is set then show tables.

            $('#add_booking_modal').on('shown.bs.modal', function (e) {
                getLocationTables($('select#booking_location_id').val());
                $(this).find('select').each( function(){
                    if(!($(this).hasClass('select2'))){
                        $(this).select2({
                            dropdownParent: $('#add_booking_modal')
                        });
                    }
                });
                booking_form_validator = $('form#add_booking_form').validate({
                    submitHandler: function(form) {
                        var data = $(form).serialize();

                        $.ajax({
                            method: "POST",
                            url: $(form).attr("action"),
                            dataType: "json",
                            data: data,
                            beforeSend: function(xhr) {
                                __disable_submit_button($(form).find('button[type="submit"]'));
                            },
                            success: function(result){
                                if(result.success == true){
                                    if(result.send_notification){
                                        $( "div.view_modal" ).load( result.notification_url,function(){
                                            $(this).modal('show');
                                        });
                                    }

                                    $('div#add_booking_modal').modal('hide');
                                    toastr.success(result.msg);
                                    reload_calendar();
                                    todays_bookings_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                                $(form).find('button[type="submit"]').attr('disabled', false);
                            }
                        });
                    }
                });
            });
            $('#add_booking_modal').on('hidden.bs.modal', function (e) {
                booking_form_validator.destroy();
                reset_booking_form();
            });

            $('form#add_booking_form #start_time').datetimepicker({
                format: moment_date_format + ' ' +moment_time_format,
                minDate: moment(),
                ignoreReadonly: true
            });
            
            $('form#add_booking_form #end_time').datetimepicker({
                format: moment_date_format + ' ' +moment_time_format,
                minDate: moment(),
                ignoreReadonly: true,
            });

            $('.view_modal').on('shown.bs.modal', function (e) {
                $('form#edit_booking_form').validate({
                    submitHandler: function(form) {
                        var data = $(form).serialize();

                        $.ajax({
                            method: "PUT",
                            url: $(form).attr("action"),
                            dataType: "json",
                            data: data,
                            beforeSend: function(xhr) {
                                __disable_submit_button($(form).find('button[type="submit"]'));
                            },
                            success: function(result){
                                if(result.success == true){
                                    $('div.view_modal').modal('hide');
                                    toastr.success(result.msg);
                                    reload_calendar();
                                    todays_bookings_table.ajax.reload();
                                    $(form).find('button[type="submit"]').attr('disabled', false);
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            todays_bookings_table = $('#todays_bookings_table').DataTable({
                            processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                            serverSide: true,
                            fixedHeader:false,
                            "ordering": false,
                            'searching': false,
                            "pageLength": 10,
                            dom:'frtip',
                            "ajax": {
                                "url": "/bookings/get-todays-bookings",
                                "data": function ( d ) {
                                    d.location_id = $('#business_location_id').val();
                                }
                            },
                            columns: [
                                {data: 'customer'},
                                {data: 'booking_start', name: 'booking_start'},
                                {data: 'booking_end', name: 'booking_end'},
                                {data: 'table'},
                                {data: 'location'},
                                {data: 'waiter'},
                            ]
                        });
            $('button#add_new_booking_btn').click( function(){
                $('div#add_booking_modal').modal('show');
            });

        });
        $(document).on('change', 'select#booking_location_id', function(){
            getLocationTables($(this).val());
        });

        $(document).on('change', 'select#business_location_id', function(){
            reload_calendar();
            todays_bookings_table.ajax.reload();
        });

        $(document).on('click', 'button#delete_booking', function(){
            swal({
              title: LANG.sure,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                $('div.view_modal').modal('hide');
                                toastr.success(result.msg);
                                reload_calendar();
                                todays_bookings_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

        function getLocationTables(location_id){
            $.ajax({
                method: "GET",
                url: '/modules/data/get-pos-details',
                data: {'location_id': location_id},
                dataType: "html",
                success: function(result){
                    $('div#restaurant_module_span').html(result);
                }
            });
        }

        function reset_booking_form(){
            $('select#booking_location_id').val('').change();
            // $('select#booking_customer_id').val('').change();
            $('select#correspondent').val('').change();
            $('#booking_note, #start_time, #end_time').val('');
        }

        function reload_calendar(){
            var location_id = '';
            if($('select#business_location_id').val()){
                location_id = $('select#business_location_id').val();
            }

            var events_source = {
                url: '/bookings',
                type: 'get',
                data: {
                    'location_id': location_id
                }
            }
            $('#calendar').fullCalendar( 'removeEventSource', events_source);
            $('#calendar').fullCalendar( 'addEventSource', events_source);         
            $('#calendar').fullCalendar( 'refetchEvents' );
        }

        $(document).on('click', '.add_new_customer', function() {
            $('.contact_modal')
                .find('select#contact_type')
                .val('customer')
                .closest('div.contact_type_div')
                .addClass('hide');
            $('.contact_modal').modal('show');
        });
        $('form#quick_add_contact')
            .submit(function(e) {
                e.preventDefault();
            })
            .validate({
                rules: {
                    contact_id: {
                        remote: {
                            url: '/contacts/check-contacts-id',
                            type: 'post',
                            data: {
                                contact_id: function() {
                                    return $('#contact_id').val();
                                },
                                hidden_id: function() {
                                    if ($('#hidden_id').length) {
                                        return $('#hidden_id').val();
                                    } else {
                                        return '';
                                    }
                                },
                            },
                        },
                    },
                },
                messages: {
                    contact_id: {
                        remote: LANG.contact_id_already_exists,
                    },
                },
                submitHandler: function(form) {
                    var data = $(form).serialize();
                    $.ajax({
                        method: 'POST',
                        url: $(form).attr('action'),
                        dataType: 'json',
                        data: data,
                        beforeSend: function(xhr) {
                            __disable_submit_button($(form).find('button[type="submit"]'));
                        },
                        success: function(result) {
                            if (result.success == true) {
                                $('select#booking_customer_id').append(
                                    $('<option>', { value: result.data.id, text: result.data.name })
                                );
                                $('select#booking_customer_id')
                                    .val(result.data.id)
                                    .trigger('change');
                                    $('div.contact_modal').modal('hide');
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                },
            });
        $('.contact_modal').on('hidden.bs.modal', function() {
            $('form#quick_add_contact')
                .find('button[type="submit"]')
                .removeAttr('disabled');
            $('form#quick_add_contact')[0].reset();
        });

    </script>
@endsection
