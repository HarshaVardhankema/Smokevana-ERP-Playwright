@extends('layouts.app')
@section('title', __('lang_v1.payment_accounts'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* Amazon theme banner - matching standard project styles */
.admin-amazon-page .content-header .account-header-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.admin-amazon-page .content-header .account-header-title {
    font-size: 22px !important;
    font-weight: 700;
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.admin-amazon-page .content-header .account-header-title i {
    color: #ff9900;
    font-size: 22px;
}
.admin-amazon-page .content-header .account-header-subtitle {
    font-size: 13px !important;
    color: rgba(249, 250, 251, 0.88);
    margin: 0;
}

/* Alert banner - Amazon style */
.admin-amazon-page .alert-danger {
    background: #fff8e7;
    border: 1px solid #ffb84d;
    border-left: 4px solid #ff9900;
    border-radius: 8px;
    color: #b45309;
    padding: 16px 20px;
}
.admin-amazon-page .alert-danger a {
    color: #ff9900;
    font-weight: 600;
    text-decoration: underline;
}
.admin-amazon-page .alert-danger a:hover {
    color: #e47911;
}

/* Tabs card – Amazon style with orange selection */
.admin-amazon-page .nav-tabs-custom {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #D5D9D9;
}
.admin-amazon-page .nav-tabs-custom > .nav-tabs {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border: none;
    margin: 0;
    padding: 14px 16px 0;
    position: relative;
}
.admin-amazon-page .nav-tabs-custom > .nav-tabs::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #ff9900;
}
.admin-amazon-page .nav-tabs-custom > .nav-tabs > li > a {
    color: rgba(255,255,255,0.9) !important;
    border: none !important;
    border-radius: 8px 8px 0 0;
    padding: 10px 18px;
    font-weight: 500;
    transition: background 0.2s ease, color 0.2s ease;
}
.admin-amazon-page .nav-tabs-custom > .nav-tabs > li > a:hover {
    color: #fff !important;
    background: rgba(255,255,255,0.12) !important;
}
/* Active tab: Amazon orange */
.admin-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a,
.admin-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a:hover,
.admin-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a:focus {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    color: #fff !important;
    border: none !important;
    box-shadow: 0 -2px 8px rgba(255,153,0,0.25);
}
.admin-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a i {
    color: #fff !important;
}
.admin-amazon-page .nav-tabs-custom > .tab-content {
    background: #f7f8f8;
    padding: 1.25rem 1.5rem;
    border: none;
}

/* Add Button Alignment and Styling */
.admin-amazon-page .col-md-8[style*="flex"],
.admin-amazon-page .col-md-12[style*="flex"] {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
}

.admin-amazon-page button[class*="tw-dw-btn-primary"][class*="btn-modal"],
.admin-amazon-page .btn-modal.tw-dw-btn-primary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    border-radius: 6px !important;
    padding: 8px 16px !important;
    background: #ff9900 !important;
    color: #ffffff !important;
    border: 1px solid #e47911 !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3) !important;
    transition: all 0.2s ease !important;
}

.admin-amazon-page button[class*="tw-dw-btn-primary"][class*="btn-modal"]:hover,
.admin-amazon-page .btn-modal.tw-dw-btn-primary:hover {
    background: #e47911 !important;
    box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4) !important;
    transform: translateY(-1px);
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="account-header-content">
            <h1 class="account-header-title">
                <i class="fas fa-wallet"></i>
                @lang('lang_v1.payment_accounts')
            </h1>
            <p class="account-header-subtitle">@lang('account.manage_your_account')</p>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        @if (!empty($not_linked_payments))
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <ul>
                            @if (!empty($not_linked_payments))
                                <li>{!! __('account.payments_not_linked_with_account', ['payments' => $not_linked_payments]) !!} <a
                                        href="{{ action([\App\Http\Controllers\AccountReportsController::class, 'paymentAccountReport']) }}">@lang('account.view_details')</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        @can('account.access')
            <div class="row">
                @component('components.widget', ['class' => 'box-primary'])
                    <div class="col-sm-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#other_accounts" data-toggle="tab">
                                        <i class="fa fa-book"></i> <strong>@lang('account.accounts')</strong>
                                    </a>
                                </li>
                                {{--
                    <li>
                        <a href="#capital_accounts" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>
                            @lang('account.capital_accounts') </strong>
                        </a>
                    </li>
                    --}}
                                <li>
                                    <a href="#account_types" data-toggle="tab">
                                        <i class="fa fa-list"></i> <strong>
                                            @lang('lang_v1.account_types') </strong>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="other_accounts">
                                    <div class="row">
                                        <div class="col-md-12">
                                            {{-- @component('components.widget') --}}
                                            <div class="col-md-4">
                                                {!! Form::select(
                                                    'account_status',
                                                    ['active' => __('business.is_active'), 'closed' => __('account.closed')],
                                                    null,
                                                    ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_status'],
                                                ) !!}
                                            </div>
                                            <div class="col-md-8" style="display: flex; align-items: center; justify-content: flex-end;">
                                                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white btn-modal"
                                                        data-container=".account_model"
                                                        data-href="{{ action([\App\Http\Controllers\AccountController::class, 'create']) }}"
                                                        style="display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; padding: 8px 16px;">
                                                        <i class="fa fa-plus"></i> @lang('messages.add')
                                                    </button>
                                            </div>
                                            {{-- @endcomponent --}}
                                        </div>
                                        <div class="col-sm-12">
                                            <br>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="other_account_table">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('lang_v1.name')</th>
                                                            <th>@lang('lang_v1.account_type')</th>
                                                            <th>@lang('lang_v1.account_sub_type')</th>
                                                            <th>@lang('account.account_number')</th>
                                                            <th>@lang('brand.note')</th>
                                                            <th>@lang('lang_v1.balance')</th>
                                                            <th>@lang('lang_v1.account_details')</th>
                                                            <th>@lang('lang_v1.added_by')</th>
                                                            <th>@lang('messages.action')</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr class="bg-gray font-17 footer-total text-center">
                                                            <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                                            <td class="footer_total_balance"></td>
                                                            <td colspan="3"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--
                    <div class="tab-pane" id="capital_accounts">
                        <table class="table table-bordered table-striped" id="capital_account_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'lang_v1.name' )</th>
                                    <th>@lang('account.account_number')</th>
                                    <th>@lang( 'brand.note' )</th>
                                    <th>@lang('lang_v1.balance')</th>
                                    <th>@lang( 'messages.action' )</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    --}}
                                <div class="tab-pane" id="account_types">
                                    <div class="row">
                                        <div class="col-md-12" style="display: flex; align-items: center; justify-content: flex-end;">
                                            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm btn-modal"
                                                data-href="{{ action([\App\Http\Controllers\AccountTypeController::class, 'create']) }}"
                                                data-container="#account_type_modal"
                                                style="display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; padding: 8px 16px;">
                                                <i class="fa fa-plus"></i> @lang('messages.add')</button>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-striped table-bordered" id="account_types_table"
                                                style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('lang_v1.name')</th>
                                                        <th>@lang('messages.action')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($account_types as $account_type)
                                                        <tr class="account_type_{{ $account_type->id }}">
                                                            <th>{{ $account_type->name }}</th>
                                                            <td>

                                                                {!! Form::open([
                                                                    'url' => action([\App\Http\Controllers\AccountTypeController::class, 'destroy'], $account_type->id),
                                                                    'method' => 'delete',
                                                                ]) !!}
                                                                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-outline tw-dw-btn-xs btn-modal"
                                                                    data-href="{{ action([\App\Http\Controllers\AccountTypeController::class, 'edit'], $account_type->id) }}"
                                                                    data-container="#account_type_modal">
                                                                    <i class="fa fa-edit"></i> @lang('messages.edit')</button>

                                                                <button type="button"
                                                                    class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error delete_account_type">
                                                                    <i class="fa fa-trash"></i> @lang('messages.delete')</button>
                                                                {!! Form::close() !!}
                                                            </td>
                                                        </tr>
                                                        @foreach ($account_type->sub_types as $sub_type)
                                                            <tr>
                                                                <td>&nbsp;&nbsp;-- {{ $sub_type->name }}</td>
                                                                <td>


                                                                    {!! Form::open([
                                                                        'url' => action([\App\Http\Controllers\AccountTypeController::class, 'destroy'], $sub_type->id),
                                                                        'method' => 'delete',
                                                                    ]) !!}
                                                                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary btn-modal"
                                                                        data-href="{{ action([\App\Http\Controllers\AccountTypeController::class, 'edit'], $sub_type->id) }}"
                                                                        data-container="#account_type_modal">
                                                                        <i class="fa fa-edit"></i> @lang('messages.edit')</button>
                                                                    <button type="button"
                                                                        class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error delete_account_type">
                                                                        <i class="fa fa-trash"></i> @lang('messages.delete')</button>
                                                                    {!! Form::close() !!}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
        @endcan

        <div class="modal fade account_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"
            id="account_type_modal">
        </div>
    </section>
    </div>
    <!-- /.content -->

@endsection

@section('javascript')
    <script>
        $(document).ready(function() {

            $(document).on('click', 'button.close_account', function() {
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = $(this).data('url');

                        $.ajax({
                            method: "get",
                            url: url,
                            dataType: "json",
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    capital_account_table.ajax.reload();
                                    other_account_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }

                            }
                        });
                    }
                });
            });

            $(document).on('submit', 'form#edit_payment_account_form', function(e) {
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.account_model').modal('hide');
                            toastr.success(result.msg);
                            capital_account_table.ajax.reload();
                            other_account_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('submit', 'form#payment_account_form', function(e) {
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    method: "post",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.account_model').modal('hide');
                            toastr.success(result.msg);
                            capital_account_table.ajax.reload();
                            other_account_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            // capital_account_table
            capital_account_table = $('#capital_account_table').DataTable({
                processing: true,
                language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                fixedHeader:false,
                ajax: '/account/account?account_type=capital',
                columnDefs: [{
                    "targets": 5,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'account_number',
                        name: 'account_number'
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#capital_account_table'));
                }
            });
            // capital_account_table
            other_account_table = $('#other_account_table').DataTable({
                processing: true,
                language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                fixedHeader:false,
                ajax: {
                    url: '/account/account?account_type=other',
                    data: function(d) {
                        d.account_status = $('#account_status').val();
                    }
                },
                columnDefs: [{
                    "targets": [6, 8],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'name',
                        name: 'accounts.name'
                    },
                    {
                        data: 'parent_account_type_name',
                        name: 'pat.name'
                    },
                    {
                        data: 'account_type_name',
                        name: 'ats.name'
                    },
                    {
                        data: 'account_number',
                        name: 'accounts.account_number'
                    },
                    {
                        data: 'note',
                        name: 'accounts.note'
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        searchable: false
                    },
                    {
                        data: 'account_details',
                        name: 'account_details'
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#other_account_table'));
                },
                "footerCallback": function(row, data, start, end, display) {
                    var footer_total_balance = 0;
                    for (var r in data) {
                        footer_total_balance += $(data[r].balance).data('orig-value') ? parseFloat($(
                            data[r].balance).data('orig-value')) : 0;
                    }

                    $('.footer_total_balance').html(__currency_trans_from_en(footer_total_balance));
                }
            });

        });

        $('#account_status').change(function() {
            other_account_table.ajax.reload();
        });

        $(document).on('submit', 'form#deposit_form', function(e) {
            e.preventDefault();
            var data = $(this).serialize();

            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('div.view_modal').modal('hide');
                        toastr.success(result.msg);
                        capital_account_table.ajax.reload();
                        other_account_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $('.account_model').on('shown.bs.modal', function(e) {
            $('.account_model .select2').select2({
                dropdownParent: $(this)
            })
        });

        $(document).on('click', 'button.delete_account_type', function() {
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $(this).closest('form').submit();
                }
            });
        })

        $(document).on('click', 'button.activate_account', function() {
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willActivate) => {
                if (willActivate) {
                    var url = $(this).data('url');
                    $.ajax({
                        method: "get",
                        url: url,
                        dataType: "json",
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                capital_account_table.ajax.reload();
                                other_account_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });
    </script>
@endsection
