@extends('layouts.app')

@section('title', __('lang_v1.view_user'))

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <h3>Commission Agent</h3>
            </div>
            <div class="col-md-4 col-xs-12 mt-15 pull-right">
                {!! Form::select('user_id', $users, $user->id, ['class' => 'form-control select2', 'id' => 'user_id']) !!}
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        @php
                            if (isset($user->media->display_url)) {
                                $img_src = $user->media->display_url;
                            } else {
                                $img_src = 'https://ui-avatars.com/api/?name=' . $user->first_name;
                            }
                        @endphp

                        <img class="profile-user-img img-responsive img-circle" src="{{ $img_src }}"
                            alt="User profile picture">

                        <h3 class="profile-username text-center">
                            {{ $user->user_full_name }}
                        </h3>

                        <p class="text-muted text-center" title="@lang('user.role')">
                            {{ $user->role_name }}
                        </p>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>@lang('business.username')</b>
                                <a class="pull-right">{{ $user->username }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang('business.email')</b>
                                <a class="pull-right">{{ $user->email }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{ __('lang_v1.status_for_user') }}</b>
                                @if ($user->status == 'active')
                                    <span class="label label-success pull-right">
                                        @lang('business.is_active')
                                    </span>
                                @else
                                    <span class="label label-danger pull-right">
                                        @lang('lang_v1.inactive')
                                    </span>
                                @endif
                            </li>
                        </ul>

                        @can('user.update')
                            <button type="button"
                                data-href="{{ action([\App\Http\Controllers\SalesCommissionAgentController::class, 'edit'], [$user->id]) }}"
                                data-container=".commission_agent_modal"
                                class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline btn-modal tw-dw-btn-primary">
                                <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")
                            </button>
                        @endcan
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active">
                            <a href="#user_info_tab" data-toggle="tab" aria-expanded="true" style="white-space: nowrap;">
                                <i class="fas fa-user" aria-hidden="true"></i> @lang('lang_v1.user_info')
                            </a>
                        </li>
                        <li>
                            <a href="#payouts_tab" data-toggle="tab" aria-expanded="true" style="white-space: nowrap;">
                                <i class="fas fa-money-bill" aria-hidden="true"></i> Payouts
                            </a>
                        </li>
                        <li>
                            <a href="#payments_tab" data-toggle="tab" aria-expanded="true" style="white-space: nowrap;">
                                <i class="fas fa-money-bill-alt" aria-hidden="true"></i> Payments
                            </a>
                        </li>
                        <li>
                            <a href="#settings_tab" data-toggle="tab" aria-expanded="true" style="white-space: nowrap;">
                                <i class="fas fa-cog" aria-hidden="true"></i> Settings
                            </a>
                        </li>
                        <li>
                            <a href="#documents_and_notes_tab" data-toggle="tab" aria-expanded="true" style="white-space: nowrap;">
                                <i class="fas fa-paperclip" aria-hidden="true"></i> Documents
                            </a>
                        </li>
                        <li>
                            <a href="#activities_tab" data-toggle="tab" aria-expanded="true" style="white-space: nowrap;">
                                <i class="fas fa-pen-square" aria-hidden="true"></i> @lang('lang_v1.activities')
                            </a>
                        </li>
                        <li>
                            <a href="#visit_history_tab" data-toggle="tab" aria-expanded="true" style="white-space: nowrap;">
                                <i class="fas fa-map-marked-alt" aria-hidden="true"></i> Visit History
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="user_info_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Commission Overview Card -->
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">
                                                <i class="fa fa-calculator"></i> Commission Overview
                                            </h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="info-box bg-green">
                                                        <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                                                        <div class="info-box-content">
                                                            <p>Commission Rate</p>
                                                            <span class="info-box-number">{{ $user->percentage_value }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-box bg-blue">
                                                        <span class="info-box-icon"><i
                                                                class="fas fa-dollar-sign"></i></span>
                                                        <div class="info-box-content">
                                                            <p>Total Sales</p>
                                                            <span
                                                                class="info-box-number">${{ number_format($total_sells, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-box bg-yellow">
                                                        <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
                                                        <div class="info-box-content">
                                                            <p>Commission Earned</p>
                                                            <span
                                                                class="info-box-number">{{ number_format($total_paid ?? 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-box bg-red">
                                                        <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                                                        <div class="info-box-content">
                                                            <p>This Month</p>
                                                            <span
                                                                class="info-box-number">${{ number_format($monthly_commission ?? 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-box bg-purple">
                                                        <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                                                        <div class="info-box-content">
                                                            <p>Max Discount Percentage (%)</p>
                                                            <span
                                                                class="info-box-number">${{ number_format($user->max_discount_percent ?? 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @include('sales_commission_agent.partial.show_details')
                        </div>

                        <div class="tab-pane" id="payouts_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('sales_commission_agent.partial.payouts')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="payments_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('sales_commission_agent.partial.payments')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="settings_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('sales_commission_agent.partial.commission_settings')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="documents_and_notes_tab">
                            <!-- model id like project_id, user_id -->
                            <input type="hidden" name="notable_id" id="notable_id" value="{{ $user->id }}">
                            <!-- model name like App\User -->
                            <input type="hidden" name="notable_type" id="notable_type" value="App\User">
                            <div class="document_note_body"></div>
                        </div>

                        <div class="tab-pane" id="activities_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('activity_log.activities')
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="visit_history_tab">
                            @include('sales_commission_agent.partial.visit_stats')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade commission_agent_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>
@endsection
@section('javascript')
    {{-- @include('documents_and_notes.document_and_note_js') --}}

    <script type="text/javascript">
        $(document).ready(function () {
            console.log('show.blade.php');

            // User selection change handler
            $('#user_id').change(function () {
                if ($(this).val()) {
                    window.location = "{{ url('/sales-commission-agents') }}/" + $(this).val();
                }
            });

            // Commission settings form submission
            $('#commission_settings_form').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ action([\App\Http\Controllers\SalesCommissionAgentController::class, "updateCommissionSettings"], [$user->id]) }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            location.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred while updating commission settings.');
                    }
                });
            });

            // Bonus settings form submission
            $('#bonus_settings_form').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ action([\App\Http\Controllers\SalesCommissionAgentController::class, "updateBonusSettings"], [$user->id]) }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            location.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred while updating bonus settings.');
                    }
                });
            });

            // Process payout button
            $('#process_payout_btn').on('click', function () {
                $('#process_payout_modal').modal('show');
            });

            // Confirm payout
            $('#confirm_payout_btn').on('click', function () {
               var $amount = $('#payout_amount');
                var val = parseFloat($amount.val());
                var maxVal = parseFloat($amount.attr('max')) || 0;

                if (isNaN(val) || val <= 0) {
                    toastr.error('Enter a valid amount greater than 0.');
                    return;
                }

                if (maxVal && val > maxVal + 0.000001) {
                    // Clamp and show message
                    var clamped = maxVal.toFixed(2);
                    $amount.val(clamped);
                    toastr.warning('Only $' + clamped + ' is available to payout. Amount adjusted.');
                    return;
                }
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\SalesCommissionAgentController::class, "processPayout"], [$user->id]) }}',
                    method: 'POST',
                    data: $('#process_payout_form').serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            $('#process_payout_modal').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred while processing payout.');
                    }
                });
            });

            $(document).on('click', '.view_payment_modal', function (e) {
                e.preventDefault();
                var payoutId = $(this).data('id');
                var container = $('.payment_modal');

                $.ajax({
                    url: '/payments/' + payoutId,
                    dataType: 'html',
                    success: function (result) {
                        $(container)
                            .html(result)
                            .modal('show');
                        __currency_convert_recursively(container);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading payment details:', error);
                        toastr.error('Error loading payment details');
                    }
                });
            });
            $(document).on('click', '.view_payment', function () {
                var url = $(this).data('href');
                var container = $('.view_modal');
                $.ajax({
                    method: 'GET',
                    url: url,
                    dataType: 'html',
                    success: function (result) {
                        $(container)
                            .html(result)
                            .modal('show');
                        __currency_convert_recursively(container);
                    },
                });
            });
        });
    </script>
@endsection