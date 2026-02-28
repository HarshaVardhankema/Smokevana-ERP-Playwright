@extends('layouts.app')
@section('title', __('user.roles'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    /* Ensure parent container allows overflow for full-width banner */
    .admin-amazon-page {
        overflow-x: visible !important;
    }
    
    #scrollable-container {
        overflow-x: visible !important;
    }
    
    /* Top banner for Roles page – Amazon style - Full Width */
    .admin-amazon-page .content-header.roles-page-container {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        position: relative !important;
        margin: 0 0 20px !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    .admin-amazon-page .content-header.roles-page-container::before {
        display: none !important;
    }

    .roles-header-banner {
        background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
        border-radius: 0;
        padding: 22px 28px;
        margin: 0 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        color: #f9fafb;
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .roles-header-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff9900, #e47911);
        z-index: 1;
    }

    .roles-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
        position: relative;
        z-index: 2;
    }

    .roles-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }

    .roles-header-title i {
        font-size: 22px;
        color: #ff9900;
    }

    .roles-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }

    /* Amazon-style Edit/Delete action buttons – Roles table */
    #roles_table td:last-child {
        vertical-align: middle;
    }
    
    /* Edit Button - Amazon Orange, Square Shape */
    #roles_table td:last-child a.edit_role_button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        min-height: 32px !important;
        padding: 6px 14px !important;
        margin: 0 2px !important;
        background: #FF9900 !important;
        color: #FFFFFF !important;
        border: 1px solid #e47911 !important;
        border-radius: 6px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3) !important;
        outline: none !important;
        transition: all 0.2s ease !important;
    }
    #roles_table td:last-child a.edit_role_button:hover {
        background: #ffac33 !important;
        border-color: #ff9900 !important;
        color: #FFFFFF !important;
        box-shadow: 0 3px 8px rgba(255, 153, 0, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    #roles_table td:last-child a.edit_role_button i,
    #roles_table td:last-child a.edit_role_button .glyphicon {
        color: #FFFFFF !important;
        font-size: 13px !important;
    }
    
    /* Delete Button - Dark Navy, Square Shape */
    #roles_table td:last-child button.delete_role_button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        min-height: 32px !important;
        padding: 6px 14px !important;
        margin: 0 2px !important;
        background: #232F3E !important;
        color: #FFFFFF !important;
        border: 1px solid #37475A !important;
        border-radius: 6px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(35, 47, 62, 0.3) !important;
        outline: none !important;
        transition: all 0.2s ease !important;
    }
    #roles_table td:last-child button.delete_role_button:hover {
        background: #37475A !important;
        border-color: #ff9900 !important;
        color: #FFFFFF !important;
        box-shadow: 0 3px 8px rgba(35, 47, 62, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    #roles_table td:last-child button.delete_role_button i,
    #roles_table td:last-child button.delete_role_button .glyphicon {
        color: #FFFFFF !important;
        font-size: 13px !important;
    }
    
    /* Add Button Alignment and Styling */
    .box-tools {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
    }
    
    #dynamic_button {
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
    
    #dynamic_button:hover {
        background: #e47911 !important;
        box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4) !important;
        transform: translateY(-1px);
    }
    
    #dynamic_button svg {
        width: 18px;
        height: 18px;
    }
</style>
@endsection

@section('content')
<div class="admin-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header roles-page-container">
    <div class="roles-header-banner">
        <div class="roles-header-content">
            <h1 class="roles-header-title">
                <i class="fas fa-user-shield"></i>
                @lang('user.roles')
            </h1>
            <p class="roles-header-subtitle">
                @lang('user.manage_roles')
            </p>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'user.all_roles' )])
        @can('roles.create')
            @slot('tool')
                <div class="box-tools" style="display: flex; align-items: center; justify-content: flex-end;">
                    <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-dw-btn-primary"
                    href="{{action([\App\Http\Controllers\RoleController::class, 'create'])}}"
                    style="display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; padding: 8px 16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                </div>
            @endslot
        @endcan
        @can('roles.view')
            <table class="table table-bordered table-striped" id="roles_table">
                <thead>
                    <tr>
                        <th>@lang( 'user.roles' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        @endcan
    @endcomponent

</section>
</div>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var roles_table = $('#roles_table').DataTable({
                    processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    serverSide: true,
                    fixedHeader:false,
                    ajax: '/roles',
                    buttons:[],
                    columnDefs: [ {
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    } ]
                });
        $(document).on('click', 'button.delete_role_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_role,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                roles_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
