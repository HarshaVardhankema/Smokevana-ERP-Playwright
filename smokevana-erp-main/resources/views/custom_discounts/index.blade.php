@extends('layouts.app')
@section('title', __('Discounts'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.offer-management-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.offer-management-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
}
.offer-management-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.offer-management-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.offer-management-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.offer-management-page .content-header h1 small {
    display: block; font-size: 13px !important; font-weight: 500 !important;
    color: #b8c4ce !important; margin-top: 4px;
}
.offer-management-page #dynamic_button.gradiantDiv {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; border-radius: 8px; padding: 8px 18px; margin: 0 !important;
    text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
}
.offer-management-page #dynamic_button.gradiantDiv svg { stroke: #0f1111; }
.offer-management-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
}
.offer-management-page #custom_discount_table thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important; padding: 12px 14px !important;
}
.offer-management-page #custom_discount_table tbody td {
    padding: 12px 14px; color: #0f1111; border-color: #e5e7eb;
}
.offer-management-page #custom_discount_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.offer-management-page #custom_discount_table tbody tr:hover td { background: #fff8e7 !important; }
.offer-management-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.offer-management-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.offer-management-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important; color: #0f1111 !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page offer-management-page">
    <section class="content-header">
        <div>
            <h1>
                <i class="fa fa-tag page-header-icon"></i>
                Offer Management System
                <small>Configure your promotional offers with flexible settings</small>
            </h1>
        </div>
        <div class="box-tools">
            <a id="dynamic_button" class="gradiantDiv" href="{{ action([\App\Http\Controllers\CustomDiscountController::class, 'create']) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                CREATE A NEW
            </a>
        </div>
    </section>
    <section class="content">
        @component('components.widget', ['class' => 'box-primary'])
        <!-- Dashboard Cards -->
        <!-- Dashboard Cards -->
        <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
            <!-- Active Offers -->
            <div
                style="background: white; border-radius: 8px; padding: 15px; box-shadow: 2px 2px 6px 4px rgba(0,0,0,0.1); flex: 1; min-width: 150px; text-align: center;" hidden>
                <div style="display: flex; align-items: center; gap: 10px; justify-content: space-between">
                    <div>
                        <p
                            style="height: 36px; align-self: stretch; color: #000; font-family: 'Amazon Ember', sans-serif; font-size: 14px; font-style: normal; font-weight: 500; line-height: 36px; margin: 5px 0 0;">
                            Active Offers</p>
                        <h3 style="font-size: 24px; font-weight: bold; color: #1f2937; margin: 0;">12</h3>

                    </div>
                    <div style="background-color: #4E79F2; padding: 8px; border-radius: 10px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="33" height="34" viewBox="0 0 33 34" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.08333 14.1667C6.15314 14.1667 5.23205 13.9834 4.37266 13.6275C3.51327 13.2715 2.73241 12.7498 2.07466 12.092C1.41691 11.4343 0.895157 10.6534 0.539186 9.79401C0.183216 8.93462 0 8.01353 0 7.08333C0 6.15314 0.183216 5.23205 0.539186 4.37266C0.895157 3.51327 1.41691 2.73241 2.07466 2.07466C2.73241 1.41691 3.51327 0.895157 4.37266 0.539186C5.23205 0.183216 6.15314 -1.3861e-08 7.08333 0C8.96195 2.79936e-08 10.7636 0.746278 12.092 2.07466C13.4204 3.40304 14.1667 5.20472 14.1667 7.08333C14.1667 8.96195 13.4204 10.7636 12.092 12.092C10.7636 13.4204 8.96195 14.1667 7.08333 14.1667ZM4.16878 31.5577C3.81253 31.9017 3.33539 32.0921 2.84013 32.0878C2.34487 32.0835 1.87111 31.8849 1.5209 31.5347C1.17068 31.1844 0.97203 30.7107 0.967726 30.2154C0.963423 29.7202 1.15381 29.243 1.49789 28.8868L27.9423 2.44233C28.1166 2.26193 28.325 2.11803 28.5555 2.01903C28.7859 1.92004 29.0338 1.86793 29.2846 1.86575C29.5354 1.86357 29.7841 1.91136 30.0162 2.00634C30.2484 2.10131 30.4593 2.24157 30.6366 2.41892C30.814 2.59627 30.9542 2.80717 31.0492 3.03931C31.1442 3.27145 31.192 3.52017 31.1898 3.77098C31.1876 4.02179 31.1355 4.26964 31.0365 4.5001C30.9375 4.73055 30.7936 4.93898 30.6132 5.11322L4.16878 31.5577ZM17.9444 26.9167C17.9444 28.7953 18.6907 30.597 20.0191 31.9253C21.3475 33.2537 23.1492 34 25.0278 34C26.9064 34 28.7081 33.2537 30.0365 31.9253C31.3648 30.597 32.1111 28.7953 32.1111 26.9167C32.1111 25.038 31.3648 23.2364 30.0365 21.908C28.7081 20.5796 26.9064 19.8333 25.0278 19.8333C23.1492 19.8333 21.3475 20.5796 20.0191 21.908C18.6907 23.2364 17.9444 25.038 17.9444 26.9167ZM7.08333 10.3889C7.96002 10.3889 8.8008 10.0406 9.42071 9.42071C10.0406 8.8008 10.3889 7.96002 10.3889 7.08333C10.3889 6.20665 10.0406 5.36586 9.42071 4.74595C8.8008 4.12604 7.96002 3.77778 7.08333 3.77778C6.20665 3.77778 5.36586 4.12604 4.74595 4.74595C4.12604 5.36586 3.77778 6.20665 3.77778 7.08333C3.77778 7.96002 4.12604 8.8008 4.74595 9.42071C5.36586 10.0406 6.20665 10.3889 7.08333 10.3889ZM28.3333 26.9167C28.3333 27.7934 27.9851 28.6341 27.3652 29.254C26.7452 29.874 25.9045 30.2222 25.0278 30.2222C24.1511 30.2222 23.3103 29.874 22.6904 29.254C22.0705 28.6341 21.7222 27.7934 21.7222 26.9167C21.7222 26.04 22.0705 25.1992 22.6904 24.5793C23.3103 23.9594 24.1511 23.6111 25.0278 23.6111C25.9045 23.6111 26.7452 23.9594 27.3652 24.5793C27.9851 25.1992 28.3333 26.04 28.3333 26.9167Z"
                                fill="white" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Coupon Redemptions -->
            <div
                style="background: white; border-radius: 8px; padding: 15px; box-shadow: 2px 2px 6px 4px rgba(0,0,0,0.1); flex: 1; min-width: 150px; text-align: center;" hidden>
                <div style="display: flex; align-items: center; gap: 10px; justify-content: space-between">
                    <div>
                        <p
                            style="height: 36px; align-self: stretch; color: #000; font-family: 'Amazon Ember', sans-serif; font-size: 14px; font-style: normal; font-weight: 500; line-height: 36px; margin: 5px 0 0;">
                            Coupon Redemptions</p>

                        <h3 style="font-size: 24px; font-weight: bold; color: #1f2937; margin: 0;">1,247</h3>

                    </div>
                    <div style="background-color: #34C759; padding: 8px; border-radius: 10px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="33" height="34" viewBox="0 0 57 57" fill="none">
                            <rect width="57" height="57" rx="8" fill="#4BC462" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M11 17.5652L12.5652 16H45.4348L47 17.5652V25.9782H45.4348C44.0301 25.9782 42.8912 27.117 42.8912 28.5217C42.8912 29.9265 44.0301 31.0653 45.4348 31.0653H47V39.4783L45.4348 41.0435H12.5652L11 39.4783V31.0653H12.5652C13.9699 31.0653 15.1087 29.9265 15.1087 28.5217C15.1087 27.117 13.9699 25.9782 12.5652 25.9782H11V17.5652ZM14.1304 19.1304V23.0665C16.5028 23.7459 18.2391 25.931 18.2391 28.5217C18.2391 31.1125 16.5028 33.2975 14.1304 33.977V37.913H21.9565V19.1304H14.1304ZM25.087 19.1304V37.913H43.8696V33.977C41.4971 33.2975 39.7608 31.1125 39.7608 28.5217C39.7608 25.931 41.4971 23.7459 43.8696 23.0665V19.1304H25.087Z"
                                fill="white" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Customer Engagement -->
            <div
                style="background: white; border-radius: 8px; padding: 15px; box-shadow: 2px 2px 6px 4px rgba(0,0,0,0.1); flex: 1; min-width: 150px; text-align: center;" hidden>
                <div style="display: flex; align-items: center; gap: 10px; justify-content: space-between">
                    <div>
                        <p
                            style="height: 36px; align-self: stretch; color: #000; font-family: 'Amazon Ember', sans-serif; font-size: 14px; font-style: normal; font-weight: 500; line-height: 36px; margin: 5px 0 0;">
                            Customer Engagement</p>

                        <h3 style="font-size: 24px; font-weight: bold; color: #1f2937; margin: 0;">68%</h3>
                    </div>
                    <div style="background-color: #A855F7; padding: 8px; border-radius: 10px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none">
                            <path
                                d="M15.5 14.207C17.6401 14.207 19.375 12.4721 19.375 10.332C19.375 8.19193 17.6401 6.45703 15.5 6.45703C13.3599 6.45703 11.625 8.19193 11.625 10.332C11.625 12.4721 13.3599 14.207 15.5 14.207Z"
                                stroke="white" stroke-width="2.58333" stroke-linecap="round" />
                            <path
                                d="M19.7211 10.3346C20.0637 9.74129 20.6279 9.30832 21.2898 9.131C21.9515 8.95367 22.6566 9.0465 23.25 9.38908C23.8434 9.73165 24.2764 10.2959 24.4537 10.9577C24.6309 11.6195 24.5382 12.3246 24.1956 12.918C23.853 13.5114 23.2888 13.9443 22.6269 14.1217C21.9652 14.2989 21.2601 14.2062 20.6667 13.8636C20.0733 13.5209 19.6403 12.9567 19.463 12.2949C19.2858 11.6331 19.3785 10.928 19.7211 10.3346Z"
                                stroke="white" stroke-width="2.58333" />
                            <path
                                d="M6.80314 10.3346C7.14572 9.74129 7.70996 9.30832 8.37175 9.131C9.03355 8.95367 9.73869 9.0465 10.332 9.38908C10.9254 9.73165 11.3584 10.2959 11.5357 10.9577C11.713 11.6195 11.6202 12.3246 11.2776 12.918C10.935 13.5114 10.3708 13.9443 9.70899 14.1217C9.04719 14.2989 8.34206 14.2062 7.74871 13.8636C7.15535 13.5209 6.72239 12.9567 6.54507 12.2949C6.36773 11.6331 6.46057 10.928 6.80314 10.3346Z"
                                stroke="white" stroke-width="2.58333" />
                            <path
                                d="M21.8051 23.2513L20.5389 23.5063L20.7476 24.543H21.8051V23.2513ZM19.0915 19.0024L18.3103 17.9738L16.8164 19.1085L18.4094 20.0992L19.0915 19.0024ZM25.6615 21.9596H21.8051V24.543H25.6615V21.9596ZM25.5459 22.2676C25.5368 22.242 25.5284 22.1934 25.5392 22.1345C25.5494 22.0786 25.5724 22.0362 25.5942 22.0091C25.6359 21.9573 25.6703 21.9596 25.6615 21.9596V24.543C27.1417 24.543 28.599 23.1472 27.9807 21.4041L25.5459 22.2676ZM21.958 19.3763C24.0784 19.3763 25.0615 20.9019 25.5459 22.2676L27.9807 21.4041C27.3799 19.7106 25.7665 16.793 21.958 16.793V19.3763ZM19.8729 20.031C20.3695 19.6538 21.0306 19.3763 21.958 19.3763V16.793C20.4572 16.793 19.2491 17.2608 18.3103 17.9738L19.8729 20.031ZM18.4094 20.0992C19.7516 20.9341 20.3139 22.389 20.5389 23.5063L23.0713 22.9963C22.7898 21.5985 22.0063 19.2942 19.7738 17.9056L18.4094 20.0992Z"
                                fill="white" />
                            <path
                                d="M11.9099 19.0024L12.592 20.0992L14.185 19.1085L12.6911 17.9738L11.9099 19.0024ZM9.1963 23.2513V24.543H10.2538L10.4625 23.5063L9.1963 23.2513ZM9.04344 19.3763C9.97089 19.3763 10.632 19.6538 11.1286 20.031L12.6911 17.9738C11.7524 17.2607 10.5442 16.793 9.04344 16.793V19.3763ZM5.45553 22.2676C5.93997 20.9019 6.92306 19.3763 9.04344 19.3763V16.793C5.23495 16.793 3.62151 19.7105 3.02082 21.4041L5.45553 22.2676ZM5.33994 21.9596C5.33118 21.9596 5.36559 21.9573 5.40721 22.0091C5.42904 22.0362 5.4521 22.0786 5.46226 22.1345C5.47301 22.1934 5.46462 22.242 5.45553 22.2676L3.02082 21.4041C2.40251 23.1472 3.85968 24.543 5.33994 24.543V21.9596ZM9.1963 21.9596H5.33994V24.543H9.1963V21.9596ZM10.4625 23.5063C10.6876 22.389 11.2498 20.9341 12.592 20.0992L11.2277 17.9056C8.99508 19.294 8.2116 21.5984 7.93007 22.9963L10.4625 23.5063Z"
                                fill="white" />
                            <path
                                d="M15.499 18.0859C20.1122 18.0859 21.4302 21.3812 21.8067 23.2641C21.9466 23.9635 21.379 24.5443 20.6656 24.5443H10.3323C9.61895 24.5443 9.05136 23.9635 9.19124 23.2641C9.56778 21.3812 10.8857 18.0859 15.499 18.0859Z"
                                stroke="white" stroke-width="2.58333" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Clicks -->
            <div
                style="background: white; border-radius: 8px; padding: 15px; box-shadow: 2px 2px 6px 4px rgba(0,0,0,0.1); flex: 1; min-width: 150px; text-align: center;" hidden>
                <div style="display: flex; align-items: center; gap: 10px; justify-content: space-between">
                    <div>
                        <p
                            style="height: 36px; align-self: stretch; color: #000; font-family: 'Amazon Ember', sans-serif; font-size: 14px; font-style: normal; font-weight: 500; line-height: 36px; margin: 5px 0 0;">
                            Total Clicks</p>

                        <h3 style="font-size: 24px; font-weight: bold; color: #1f2937; margin: 0;">2000</h3>
                    </div>
                    <div style="background-color: #F97316; padding: 8px; border-radius: 10px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <path
                                d="M25.6043 30.9175H14.6843C13.8887 30.9175 13.1256 30.6014 12.563 30.0388C12.0004 29.4762 11.6843 28.7131 11.6843 27.9175C11.6843 27.1218 12.0004 26.3587 12.563 25.7961C13.1256 25.2335 13.8887 24.9175 14.6843 24.9175H17.4893L7.49933 14.7924C6.97345 14.2797 6.65396 13.5917 6.60144 12.8591C6.54891 12.1265 6.76698 11.4 7.21433 10.8174C7.48261 10.4938 7.81985 10.2343 8.20136 10.0578C8.58287 9.88134 8.99901 9.79238 9.41933 9.79745C9.79526 9.78977 10.1688 9.85899 10.517 10.0009C10.8652 10.1427 11.1808 10.3542 11.4443 10.6224L17.5343 16.7125L23.6543 12.1224C24.2069 11.7077 24.8907 11.5067 25.5798 11.5563C26.2689 11.6059 26.9169 11.9029 27.4043 12.3925C30.2379 15.232 32.3695 18.6939 33.6293 22.5025L33.7193 22.7874"
                                stroke="white" stroke-width="2.865" stroke-miterlimit="10" />
                            <path
                                d="M15.9448 15.1799C16.3345 14.2551 16.5237 13.2582 16.4998 12.2549C16.4849 10.8376 16.0503 9.45642 15.2508 8.28599C14.4513 7.11555 13.3228 6.20834 12.008 5.67897C10.6931 5.1496 9.25083 5.02183 7.86338 5.31179C6.47593 5.60176 5.20555 6.29645 4.21275 7.30811C3.21995 8.31976 2.54927 9.60297 2.28545 10.9956C2.02163 12.3883 2.17651 13.8279 2.73051 15.1326C3.28451 16.4372 4.21279 17.5484 5.39805 18.3258C6.58332 19.1031 7.97239 19.5117 9.38977 19.4999C10.1812 19.5022 10.9674 19.3703 11.7148 19.1099"
                                stroke="white" stroke-width="2.865" stroke-miterlimit="10" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive" style="">
            <table class="table  table-striped" id="custom_discount_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>WEB DISCOUNT RULE</th>
                        <th> TYPE</th>
                        <th>APPLICABILITY</th>
                        <th>VALIDITY</th>
                        @if($is_super_admin)
                            <th>LOCATION</th>
                            <th>BRAND</th>
                        @endif
                        @if($is_b2c)
                            <th>BRAND</th>
                        @endif
                        <th>STATUS</th>
                        <th>USED</th>
                        <th>PRIORITY</th>
                        <th>LAST UPDATED</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody></tbody> <!-- Empty tbody to prevent initial rendering -->
            </table>
        </div>
        @endcomponent
    </section>

    <div class="modal fade custom_discount_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    {{-- Include the show modal (empty by default, will be replaced by AJAX) --}}
    <div id="show-discount-modal-container"></div>

    <!-- Priority Change Modal -->
    <div class="modal fade" id="priorityModal" tabindex="-1" role="dialog" aria-labelledby="priorityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="priorityModalLabel">Change Priority</h5>

                </div>
                <div class="modal-body">
                    <input type="number" id="priorityInput" class="form-control" min="0">
                    <input type="hidden" id="priorityDiscountId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="savePriorityBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            var custom_discount_table = $('#custom_discount_table').DataTable({
                processing: true,
                serverSide: true,
                language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
                ajax: '{{ route("custom-discounts.index") }}',
                columns: [
                    { data: 'offerName' },
                    { data: 'type', render: function (data) { return `<div style="display: flex; justify-content: center; align-items: center; flex-shrink: 0; border-radius: 7px; background: #DDE9FD; color: #1f2937; padding: 4px 8px;">${data}</div>`; } },
                    { data: 'applicability' },
                    { data: 'validity' },
                    @if($is_super_admin)
                        { data: 'location' },
                        { data: 'brand' },
                    @endif
                    @if($is_b2c)
                        { data: 'brand' },
                    @endif
                    { data: 'status', orderable: false, searchable: false },
                    { data: 'used' },
                    { data: 'priority', orderable: false, searchable: false },
                    {
                        data: 'updated_at',
                        render: function (data, type, row) {
                            if (type === 'sort' || type === 'type') {
                                return data.timestamp;
                            }
                            return data.display;
                        }
                    },
                    { data: 'action', orderable: false, searchable: false }
                ],
                order: [[7, 'desc']]
            });

            // SweetAlert for delete
            $(document).on('click', '.delete-discount-btn', function (e) {
                e.preventDefault();
                var url = $(this).data('delete-url');
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function (response) {
                                swal('Deleted!', 'Your file has been deleted.', 'success');
                                custom_discount_table.ajax.reload();
                            }
                        });
                    }
                });

            });
            $(document).on('click', '.duplicate-discount-btn', function (e) {
                e.preventDefault();
                var url = $(this).data('duplicate-url');
                swal({
                    title: 'Are you sure?',
                    text: "You want to duplicate this discount?",
                    icon: 'warning',
                    buttons: true,
                    dangerMode: false,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            success: function (response) {
                                swal('Duplicated!', 'Your file has been duplicated.', 'success');
                                custom_discount_table.ajax.reload();
                            }
                        });
                    }
                });
            });
            $(document).on('click', '.change-status-btn', function (e) {
                e.preventDefault();
                var url = $(this).data('url');
                swal({
                    title: 'Are you sure?',
                    text: "You want to change the status of this discount?",
                    icon: 'warning',
                    buttons: true,
                    dangerMode: false,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            success: function (response) {
                                swal('Status changed!', 'Your file has been changed.', 'success');
                                custom_discount_table.ajax.reload();
                            }
                        });
                    }
                });
            });

        });

        $(document).on('click', '.change-priority-btn', function () {
            var id = $(this).data('id');
            var priority = $(this).data('priority');
            $('#priorityInput').val(priority);
            $('#priorityDiscountId').val(id);
            $('#priorityModal').modal('show');
        });

        $('#savePriorityBtn').on('click', function () {
            var id = $('#priorityDiscountId').val();
            var priority = $('#priorityInput').val();
            $.ajax({
                url: '/custom-discounts/priority-change/' + id + '/' + priority,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#priorityModal').modal('hide');
                    if (typeof $('.dataTable').DataTable === 'function') {
                        $('.dataTable').DataTable().ajax.reload(null, false);
                    } else {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    alert('Error updating priority');
                }
            });
        });
    </script>
@endsection