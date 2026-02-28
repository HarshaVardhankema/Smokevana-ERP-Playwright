@extends('layouts.app')

@section('title', 'Visit Tracking')

@section('content')
    <style>
        .stats-card {
            background: #ffffff;
            border-radius: 6px;
            padding: 12px;
            color: #2c3e50;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stats-card.blue {
            border-left: 4px solid #3498db;
        }

        .stats-card.green {
            border-left: 4px solid #27ae60;
        }

        .stats-card.orange {
            border-left: 4px solid #f39c12;
        }

        .stats-card.red {
            border-left: 4px solid #e74c3c;
        }

        .stats-card.purple {
            border-left: 4px solid #9b59b6;
        }

        /* Ensure cards fit properly */
        .stats-card * {
            max-width: 100%;
        }

        .stats-card .stats-icon,
        .stats-card .stats-number,
        .stats-card .stats-label {
            display: block;
            width: 100%;
        }

        .stats-icon {
            font-size: 1.4rem;
            opacity: 0.8;
            margin-bottom: 4px;
        }

        .stats-number {
            font-size: 1.4rem;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }

        .stats-label {
            font-size: 0.75rem;
            opacity: 0.9;
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
        }

        .filter-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .filter-row {
            display: flex;
            align-items: end;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }

        .filter-group .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 14px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            align-items: end;
        }

        /* DataTables Styling */
        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_length label,
        .dataTables_filter label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .dataTables_length select,
        .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 14px;
        }

        .dataTables_info {
            color: #666;
            font-size: 14px;
            padding-top: 8px;
        }

        .pagination {
            display: inline-flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination li {
            margin: 0 2px;
        }

        .pagination li a {
            display: inline-block;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 3px;
            color: #337ab7;
            font-size: 13px;
            line-height: 1.42857143;
        }

        .pagination li a:hover {
            background-color: #eee;
            border-color: #ddd;
        }

        .pagination li.active a {
            background-color: #337ab7;
            color: white;
            border-color: #337ab7;
        }

        .pagination li.disabled a {
            color: #777;
            background-color: #fff;
            border-color: #ddd;
            cursor: not-allowed;
        }

        .pagination li.disabled a:hover {
            background-color: #fff;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .modern-table {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .modern-table thead th {
            background: #f8f9fa;
            color: #2c3e50;
            border: none;
            padding: 12px 10px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        .modern-table tbody td {
            padding: 12px 10px;
            border: none;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: middle;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9ff;
            transition: all 0.3s ease;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
            margin-right: 12px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: inline-block;
        }

        .status-completed {
            background-color: #28a745;
            color: white;
        }

        .status-visited {
            background-color: #28a745;
            color: white;
        }

        .status-scheduled {
            background-color: #007bff;
            color: white;
        }

        .status-progress {
            background-color: #17a2b8;
            color: white;
        }

        .status-in-progress {
            background-color: #17a2b8;
            color: white;
        }

        .status-missing {
            background-color: #ffc107;
            color: #212529;
        }

        .status-missing-proof {
            background-color: #ffc107;
            color: #212529;
        }

        .status-pending {
            background-color: #6c757d;
            color: white;
        }

        .proof-icons i {
            font-size: 1.2rem;
            margin-right: 8px;
            padding: 5px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05);
        }

        .action-btn {
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            margin-right: 5px;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .filter-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .pagination-modern {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
        }

        .pagination-modern .btn {
            border-radius: 8px;
            margin: 0 3px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .pagination-modern .btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }

        /* Modal Improvements */
        #create_track_modal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
            padding: 30px;
        }

        /* Fix input text visibility */
        #create_track_modal .form-control,
        #create_track_modal .form-control:focus {
            color: #000000 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        #create_track_modal .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #create_track_modal select.form-control {
            color: #000000 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
            height: 44px;
            line-height: 1.5;
            font-size: 14px;
            vertical-align: middle;
            padding: 8px 12px;
            opacity: 1 !important;
            font-weight: 400;
        }

        #create_track_modal select.form-control:focus,
        #create_track_modal select.form-control:active,
        #create_track_modal select.form-control:not([value=""]) {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            background-color: #ffffff !important;
            opacity: 1 !important;
        }

        #create_track_modal select.form-control option {
            color: #000000 !important;
            background-color: #ffffff !important;
            padding: 8px 12px;
            -webkit-text-fill-color: #000000 !important;
            font-size: 14px;
            line-height: 1.5;
        }

        #create_track_modal select.form-control option:checked,
        #create_track_modal select.form-control option:hover {
            color: #000000 !important;
            background-color: #e9ecef !important;
            -webkit-text-fill-color: #000000 !important;
        }

        #create_track_modal select.form-control option:selected {
            color: #000000 !important;
            background-color: #007bff !important;
            -webkit-text-fill-color: #ffffff !important;
        }

        /* Force black text on all select elements */
        #create_track_modal select {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 400;
            opacity: 1 !important;
        }

        #create_track_modal select:valid {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
        }

        #create_track_modal option {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
        }

        /* Override any Bootstrap or framework styles */
        #create_track_modal select[name="sales_rep_id"],
        #create_track_modal select[name="lead_id"],
        #create_track_modal select[name="status"],
        #create_track_modal select[name="visit_type"] {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            background-color: #ffffff !important;
            opacity: 1 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
        }

        /* Ensure text is visible in all states */
        #create_track_modal select.form-control[disabled],
        #create_track_modal select.form-control[readonly] {
            color: #6c757d !important;
            background-color: #e9ecef !important;
        }

        #create_track_modal textarea.form-control {
            color: #000000 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
            line-height: 1.5;
            font-size: 14px;
            padding: 8px 12px;
            resize: vertical;
        }

        #create_track_modal textarea.form-control:focus,
        #create_track_modal textarea.form-control:active {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            background-color: #ffffff !important;
        }

        #create_track_modal input.form-control {
            color: #000000 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
            height: 44px;
            line-height: 1.5;
            font-size: 14px;
            vertical-align: middle;
            padding: 8px 12px;
        }

        #create_track_modal input.form-control:focus,
        #create_track_modal input.form-control:active {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            background-color: #ffffff !important;
        }

        #create_track_modal input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
            cursor: pointer;
        }

        #create_track_modal input[type="number"].form-control {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
        }

        /* Ensure placeholder text is visible but lighter */
        #create_track_modal input::placeholder,
        #create_track_modal textarea::placeholder {
            color: #6c757d !important;
            opacity: 1;
        }

        /* Align all form controls properly */
        #create_track_modal .form-control {
            display: block;
            width: 100%;
            box-sizing: border-box;
        }

        /* File Upload Styling */
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload-input {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            border: 2px dashed #667eea;
            border-radius: 8px;
            background-color: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #667eea;
            font-weight: 500;
        }

        .file-upload-label:hover {
            background-color: #667eea;
            color: white;
            border-color: #667eea;
        }

        .file-upload-label i {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        .file-selected-name {
            margin-top: 8px;
            font-size: 0.85rem;
            color: #28a745;
            font-weight: 500;
        }

        /* Scrollbar styling for modal */
        #create_track_modal .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        #create_track_modal .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #create_track_modal .modal-body::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }

        #create_track_modal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* DataTable row styling - keep gray background */
        #visit_tracking_table tbody tr {
            background-color: #f8f9fa;
        }

        #visit_tracking_table tbody tr:hover {
            background-color: #e9ecef !important;
        }

        #visit_tracking_table tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        #visit_tracking_table tbody td {
            padding: 12px 10px !important;
        }

        #visit_tracking_table thead th {
            padding: 12px 10px !important;
        }

        /* Ensure proper spacing in DataTable */
        .dataTables_wrapper {
            padding: 20px 0;
        }

        /* Make table scrollable */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* DataTables scroll body */
        .dataTables_scrollBody {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        /* DataTables scroll head */
        .dataTables_scrollHead {
            overflow: visible !important;
        }

        /* Scrollbar styling */
        .table-responsive::-webkit-scrollbar,
        .dataTables_scrollBody::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track,
        .dataTables_scrollBody::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb,
        .dataTables_scrollBody::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover,
        .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Additional styling for select dropdowns */
        select.form-control {
            appearance: auto;
            -webkit-appearance: auto;
            -moz-appearance: auto;
        }

        /* Ensure all labels in modal are black */
        #create_track_modal label {
            color: #000000 !important;
            display: block;
            margin-bottom: 8px;
        }

        /* Ensure section headers are black */
        #create_track_modal h5 {
            color: #000000 !important;
        }

        /* Form group spacing */
        #create_track_modal .form-group {
            margin-bottom: 20px;
        }

        /* Row spacing */
        #create_track_modal .row {
            margin-left: -15px;
            margin-right: -15px;
        }

        #create_track_modal .row>div {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Ticket Info Styling */
        .ticket-info {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            align-items: center;
            max-width: 250px;
        }

        .ticket-info .badge {
            font-size: 0.7rem;
            padding: 3px 7px;
            border-radius: 10px;
            white-space: nowrap;
            cursor: help;
            display: inline-block;
            line-height: 1.2;
            font-weight: 500;
        }

        .ticket-info .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .ticket-info .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .ticket-info .badge-success {
            background-color: #28a745;
            color: white;
        }

        .ticket-info .badge-secondary {
            background-color: #6c757d;
            color: white;
            font-size: 0.65rem;
        }

        .view-all-tickets {
            cursor: pointer !important;
            transition: all 0.2s ease;
        }

        .view-all-tickets:hover {
            background-color: #5a6268 !important;
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .text-muted {
            color: #6c757d !important;
            font-size: 0.85rem !important;
            font-style: italic;
        }


        /* All Tickets Modal Styling */
        #all_tickets_modal .modal-title,
        #all_tickets_modal .modal-title span {
            color: #000000 !important;
        }

        #all_tickets_modal .table thead th {
            color: #000000 !important;
            background-color: #f8f9fa;
            font-weight: 600;
        }

        #all_tickets_modal .table tbody td {
            color: #000000 !important;
        }
    </style>

    <div class="content">
        <!-- Key Statistics -->




        <!-- Table Section -->
        <div class="row">
            <div class="col-md-12">
                <h4 style="margin: 0; color: #2c3e50; font-weight: 600; margin-bottom: 10px; margin-top: 10px;">
                    Visit & Ticket Tracking
                </h4>
                <div class="table-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; color: #2c3e50; font-weight: 600;">
                            <i class="fa fa-table" style="margin-right: 10px; color: #667eea;"></i>
                            Visit & Ticket Details
                        </h4>
                        <button type="button" class="filter-btn" data-toggle="modal" data-target="#create_track_modal">
                            <i class="fa fa-plus"></i> Create Track
                        </button>
                    </div>

                    <!-- Visit Details Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="visit_tracking_table">
                            <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <tr>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Type</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Reference No</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Store Name</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Address</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Sales Rep</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Date/Time</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Duration</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Description</th>
                                    <th style="border: none; padding: 12px 10px; font-weight: 600;">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Track Modal -->
    <div class="modal fade" id="create_track_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: #37475A; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.15);">
                    <h4 class="modal-title" style="margin: 0; font-weight: 600; color: #ffffff;">
                        <i class="fa fa-plus-circle" style="margin-right: 10px; color: #3498db;"></i>
                        Create Track Entry
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="create_track_form">
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5
                                    style="color: #000000; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f8f9fa;">
                                    <i class="fa fa-info-circle" style="margin-right: 8px; color: #667eea;"></i>
                                    Basic Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">Sales Rep
                                        *</label>
                                    <select class="form-control" name="sales_rep_id" required>
                                        <option value="">Select Sales Rep</option>
                                        @foreach ($salesReps as $rep)
                                            <option value="{{ $rep['id'] }}">{{ $rep['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">Lead *</label>
                                    <select class="form-control" name="lead_id" required>
                                        <option value="">Select Lead</option>
                                        @foreach ($leads as $lead)
                                            <option value="{{ $lead->id }}">{{ $lead->store_name }} -
                                                {{ $lead->reference_no ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">Start Time
                                        *</label>
                                    <input type="datetime-local" class="form-control" name="start_time" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">Duration
                                        (minutes)</label>
                                    <input type="number" class="form-control" name="duration" placeholder="e.g., 30"
                                        min="1">
                                </div>
                            </div>
                        </div>

                        <!-- Status and Proof -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5
                                    style="color: #000000; font-weight: 600; margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #f8f9fa;">
                                    <i class="fa fa-check-circle" style="margin-right: 8px; color: #667eea;"></i>
                                    Status & Proof
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">Status *</label>
                                    <select class="form-control" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="missing_proof">Missing Proof</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">Visit Type</label>
                                    <select class="form-control" name="visit_type">
                                        <option value="initial">Initial Visit</option>
                                        <option value="follow_up">Follow-up Visit</option>
                                        <option value="demo">Product Demo</option>
                                        <option value="meeting">Business Meeting</option>
                                        <option value="support">Support Visit</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Proof Collection -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5
                                    style="color: #000000; font-weight: 600; margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #f8f9fa;">
                                    <i class="fa fa-upload" style="margin-right: 8px; color: #3498db;"></i>
                                    Proof Collection
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">
                                        <i class="fa fa-map-marker text-success" style="margin-right: 5px;"></i>
                                        Location Proof
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="file-upload-input" name="location_proof_file"
                                            id="location_proof_file" accept="image/*,.pdf"
                                            onchange="showFileName(this, 'location_file_name')">
                                        <label for="location_proof_file" class="file-upload-label">
                                            <i class="fa fa-cloud-upload"></i>
                                            <span>Choose File (Image or PDF)</span>
                                        </label>
                                    </div>
                                    <div id="location_file_name" class="file-selected-name"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">
                                        <i class="fa fa-camera text-info" style="margin-right: 5px;"></i>
                                        Photo Proof
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="file-upload-input" name="photo_proof_file"
                                            id="photo_proof_file" accept="image/*" multiple
                                            onchange="showFileName(this, 'photo_file_name')">
                                        <label for="photo_proof_file" class="file-upload-label">
                                            <i class="fa fa-camera"></i>
                                            <span>Choose Photos (Multiple)</span>
                                        </label>
                                    </div>
                                    <div id="photo_file_name" class="file-selected-name"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">
                                        <i class="fa fa-pencil text-warning" style="margin-right: 5px;"></i>
                                        Signature Proof
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="file-upload-input" name="signature_proof_file"
                                            id="signature_proof_file" accept="image/*,.pdf"
                                            onchange="showFileName(this, 'signature_file_name')">
                                        <label for="signature_proof_file" class="file-upload-label">
                                            <i class="fa fa-pencil"></i>
                                            <span>Choose Signature (Image or PDF)</span>
                                        </label>
                                    </div>
                                    <div id="signature_file_name" class="file-selected-name"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">
                                        <i class="fa fa-video-camera text-primary" style="margin-right: 5px;"></i>
                                        Video Proof
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="file-upload-input" name="video_proof_file"
                                            id="video_proof_file" accept="video/*"
                                            onchange="showFileName(this, 'video_file_name')">
                                        <label for="video_proof_file" class="file-upload-label">
                                            <i class="fa fa-video-camera"></i>
                                            <span>Choose Video</span>
                                        </label>
                                    </div>
                                    <div id="video_file_name" class="file-selected-name"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5
                                    style="color: #000000; font-weight: 600; margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #f8f9fa;">
                                    <i class="fa fa-comment" style="margin-right: 8px; color: #667eea;"></i>
                                    Additional Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #000000; margin-bottom: 8px;">Remarks
                                        (Optional)</label>
                                    <textarea class="form-control" name="remarks" rows="4"
                                        placeholder="Enter any additional remarks or notes about the visit..."></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.15); padding: 20px 30px; background: #37475A; color: #ffffff;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"
                        style="border-radius: 4px; padding: 8px 20px; font-weight: 600;">
                        <i class="fa fa-times" style="margin-right: 5px;"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveTrack()"
                        style="border-radius: 4px; padding: 8px 20px;">
                        <i class="fa fa-save" style="margin-right: 5px;"></i>
                        Save Track
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Visit Details Modal -->
    <div class="modal fade" id="view_visit_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #37475A; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.15);">
                    <h4 class="modal-title" style="margin: 0; font-weight: 600;">
                        <i class="fa fa-eye" style="margin-right: 10px; color: #3498db;"></i>
                        Visit Details
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="color: #6c757d;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div id="visit_details_content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.15); padding: 20px 30px; background: #37475A; color: #ffffff;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fa fa-times" style="margin-right: 5px;"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Visited Modal -->
    <div class="modal fade" id="mark_visited_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Mark as Visited</h4>
                </div>
                <div class="modal-body">
                    <form id="mark_visited_form">
                        <input type="hidden" name="lead_id" id="visited_lead_id">
                        <div class="form-group">
                            <label>Visit Date *</label>
                            <input type="datetime-local" class="form-control" name="visit_date" required>
                        </div>
                        <div class="form-group">
                            <label>Visit Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="What happened during the visit?"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Next Follow-up Date</label>
                            <input type="date" class="form-control" name="next_follow_up">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmMarkVisited()">Mark as
                        Visited</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Follow-up Modal -->
    <div class="modal fade" id="schedule_followup_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Schedule Follow-up</h4>
                </div>
                <div class="modal-body">
                    <form id="schedule_followup_form">
                        <input type="hidden" name="lead_id" id="followup_lead_id">
                        <div class="form-group">
                            <label>Follow-up Date *</label>
                            <input type="date" class="form-control" name="followup_date" required>
                        </div>
                        <div class="form-group">
                            <label>Follow-up Time</label>
                            <input type="time" class="form-control" name="followup_time">
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="3"
                                placeholder="What should be discussed in the follow-up?"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmScheduleFollowup()">Schedule
                        Follow-up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- All Tickets Modal -->
    <div class="modal fade" id="all_tickets_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #37475A; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.15);">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="margin-top: -2px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" style="color: #ffffff; font-weight: 600; margin: 0;">
                        <i class="fa fa-ticket" style="margin-right: 8px; color: #667eea;"></i>
                        All Tickets - <span id="tickets_lead_name" style="color: #000000;"></span>
                    </h4>
                </div>
                <div class="modal-body" style="padding: 25px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 20%; color: #000000 !important; font-weight: 600;">Reference No</th>
                                    <th style="width: 15%; color: #000000 !important; font-weight: 600;">Status</th>
                                    <th style="width: 50%; color: #000000 !important; font-weight: 600;">Description</th>
                                    <th style="width: 15%; color: #000000 !important; font-weight: 600;">Created Date</th>
                                </tr>
                            </thead>
                            <tbody id="tickets_table_body">
                                <!-- Tickets will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.15); padding: 20px 25px; background: #37475A; color: #ffffff;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="border-radius: 4px; padding: 8px 20px;">
                        <i class="fa fa-times" style="margin-right: 5px;"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script type="text/javascript">
        var visit_tracking_table;

        // Function to show selected file names
        function showFileName(input, targetId) {
            var target = document.getElementById(targetId);
            if (input.files && input.files.length > 0) {
                if (input.files.length === 1) {
                    target.innerHTML = '<i class="fa fa-check-circle"></i> ' + input.files[0].name;
                } else {
                    target.innerHTML = '<i class="fa fa-check-circle"></i> ' + input.files.length + ' files selected';
                }
            } else {
                target.innerHTML = '';
            }
        }

        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Set current date for visit date
            var now = new Date();
            var datetime = now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0') + 'T' +
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0');
            $('input[name="visit_date"]').val(datetime);

            // Clear file uploads when modal is closed
            $('#create_track_modal').on('hidden.bs.modal', function() {
                $('#create_track_form')[0].reset();
                $('.file-selected-name').html('');
            });

            // Force black text color on select dropdowns
            $('#create_track_modal select').on('change', function() {
                $(this).css({
                    'color': '#000000',
                    '-webkit-text-fill-color': '#000000',
                    'opacity': '1'
                });
            });

            // Ensure dropdowns are visible on modal show
            $('#create_track_modal').on('shown.bs.modal', function() {
                $('#create_track_modal select').css({
                    'color': '#000000',
                    '-webkit-text-fill-color': '#000000',
                    'opacity': '1'
                });
            });

            // Initialize DataTable
            visit_tracking_table = $('#visit_tracking_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollY: '500px',
                scrollCollapse: false,
                ajax: {
                    url: "{{ action([\App\Http\Controllers\LeadController::class, 'visitTracking']) }}",
                    data: function(d) {
                        d.sales_rep_id = $('#sales_rep_filter').val();
                        d.status = $('#status_filter').val();
                        d.date = $('#date_filter').val();
                        d.territory = $('#territory_filter').val();
                    }
                },
                drawCallback: function() {
                    // Attach click event to "view all tickets" badges
                    $('.view-all-tickets').off('click').on('click', function() {
                        var ticketsData = $(this).data('tickets');
                        var leadName = $(this).data('lead-name');
                        showAllTicketsModal(ticketsData, leadName);
                    });
                },
                columns: [{
                        data: 'type',
                        name: 'type',
                        orderable: false
                    },
                    {
                        data: 'reference_no',
                        name: 'reference_no'
                    },
                    {
                        data: 'store_name',
                        name: 'store_name'
                    },
                    {
                        data: 'full_address',
                        name: 'full_address'
                    },
                    {
                        data: 'sales_rep_name',
                        name: 'sales_rep_name'
                    },
                    {
                        data: 'formatted_start_time',
                        name: 'formatted_start_time',
                        orderable: false
                    },
                    {
                        data: 'formatted_duration',
                        name: 'formatted_duration',
                        orderable: false
                    },
                    {
                        data: 'ticket_info',
                        name: 'ticket_info',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    }
                ],
                order: [
                    [5, 'desc']
                ], // Order by date/time descending
                pageLength: 25,
                language: {
                    processing: '<i class="fa fa-spinner fa-spin"></i> Loading...'
                }
            });
        });

        function applyFilters() {
            // Show loading
            toastr.info('Applying filters...');

            // Reload the DataTable with new filter parameters
            visit_tracking_table.ajax.reload(function() {
                toastr.success('Filters applied successfully!');
            });
        }

        function clearFilters() {
            $('#sales_rep_filter').val('');
            $('#status_filter').val('');
            $('#date_filter').val('');
            $('#territory_filter').val('');

            // Reload the DataTable without filters
            visit_tracking_table.ajax.reload();
            toastr.info('Filters cleared');
        }


        function markVisited(leadId) {
            $('#visited_lead_id').val(leadId);
            $('#mark_visited_modal').modal('show');
        }

        function scheduleFollowUp(leadId) {
            $('#followup_lead_id').val(leadId);
            $('#schedule_followup_modal').modal('show');
        }

        function viewLead(leadId) {
            // Redirect to lead details page
            window.location.href = '/leads/' + leadId;
        }

        function viewVisitDetails(visitId) {
            // Show loading
            $('#visit_details_content').html(
                '<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
            $('#view_visit_modal').modal('show');

            // Load visit details via AJAX
            $.ajax({
                method: "GET",
                url: "/leads/visit-details/" + visitId,
                success: function(result) {
                    $('#visit_details_content').html(result);
                },
                error: function(xhr) {
                    $('#visit_details_content').html(
                        '<div class="alert alert-danger">Error loading visit details.</div>');
                }
            });
        }

        function saveTrack() {
            var form = $('#create_track_form')[0];
            var formData = new FormData(form);

            // Validate required fields
            if (!$('select[name="sales_rep_id"]').val()) {
                toastr.error('Please select a Sales Rep');
                return;
            }
            if (!$('select[name="lead_id"]').val()) {
                toastr.error('Please select a Lead');
                return;
            }
            if (!$('input[name="start_time"]').val()) {
                toastr.error('Please select Start Time');
                return;
            }
            if (!$('select[name="status"]').val()) {
                toastr.error('Please select Status');
                return;
            }

            $.ajax({
                method: "POST",
                url: "/leads/store-track",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('button[onclick="saveTrack()"]').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Saving...');
                },
                success: function(result) {
                    if (result.success == true) {
                        $('#create_track_modal').modal('hide');
                        $('#create_track_form')[0].reset();
                        // Clear file name displays
                        $('.file-selected-name').html('');
                        toastr.success('Track entry created successfully!');
                        // Reload DataTable instead of full page reload
                        visit_tracking_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while creating the track entry.');
                },
                complete: function() {
                    $('button[onclick="saveTrack()"]').prop('disabled', false).html(
                        '<i class="fa fa-save"></i> Save Track');
                }
            });
        }

        function confirmMarkVisited() {
            var form = $('#mark_visited_form');
            var data = form.serialize();

            $.ajax({
                method: "POST",
                url: "/leads/mark-visited",
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('#mark_visited_modal').modal('hide');
                        toastr.success(result.msg);
                        // Reload DataTable instead of full page reload
                        visit_tracking_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while marking as visited.');
                }
            });
        }

        function confirmScheduleFollowup() {
            var form = $('#schedule_followup_form');
            var data = form.serialize();

            $.ajax({
                method: "POST",
                url: "/leads/schedule-followup",
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('#schedule_followup_modal').modal('hide');
                        toastr.success(result.msg);
                        // Reload DataTable instead of full page reload
                        visit_tracking_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while scheduling follow-up.');
                }
            });
        }

        function showAllTicketsModal(ticketsData, leadName) {
            // Set lead name in modal title
            $('#tickets_lead_name').text(leadName);

            // Clear previous data
            $('#tickets_table_body').empty();

            // Parse tickets data if it's a string
            if (typeof ticketsData === 'string') {
                try {
                    ticketsData = JSON.parse(ticketsData);
                } catch (e) {
                    console.error('Error parsing tickets data:', e);
                    toastr.error('Error loading tickets data');
                    return;
                }
            }

            // Populate table with tickets
            if (ticketsData && ticketsData.length > 0) {
                ticketsData.forEach(function(ticket) {
                    var statusBadge = '';
                    var statusClass = '';

                    switch (ticket.status) {
                        case 'open':
                            statusClass = 'danger';
                            break;
                        case 'in_progress':
                            statusClass = 'warning';
                            break;
                        case 'closed':
                        case 'resolved':
                            statusClass = 'success';
                            break;
                        default:
                            statusClass = 'default';
                    }

                    statusBadge = '<span class="badge badge-' + statusClass + '">' + ticket.status.replace('_', ' ')
                        .toUpperCase() + '</span>';

                    var row = '<tr>' +
                        '<td style="color: #000000;">' + ticket.reference_no + '</td>' +
                        '<td style="color: #000000;">' + statusBadge + '</td>' +
                        '<td style="color: #000000;">' + ticket.description + '</td>' +
                        '<td style="color: #000000;">' + ticket.created_at + '</td>' +
                        '</tr>';

                    $('#tickets_table_body').append(row);
                });
            } else {
                $('#tickets_table_body').append(
                    '<tr><td colspan="4" class="text-center" style="color: #000000;">No tickets found</td></tr>');
            }

            // Show modal
            $('#all_tickets_modal').modal('show');
        }
    </script>
@endsection
