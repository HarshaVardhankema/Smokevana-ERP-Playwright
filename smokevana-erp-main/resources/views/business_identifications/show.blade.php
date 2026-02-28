@extends('layouts.app')
@section('title', 'View Business Identification')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Business Identification Details</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                
                <!-- Business Identification Section -->
                <h4 class="tw-font-bold tw-mb-4"><i class="fa fa-building"></i> Business Identification</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>ID:</strong>
                        <p>{{ $identification->id }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $color = $statusColors[$identification->status] ?? 'default';
                            @endphp
                            <span class="label label-{{ $color }}">{{ ucfirst($identification->status) }}</span>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Legal Business Name:</strong>
                        <p>{{ $identification->legal_business_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>DBA:</strong>
                        <p>{{ $identification->dba ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>FEIN / Tax ID:</strong>
                        <p>{{ $identification->fein_tax_id ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Customer/Contact:</strong>
                        <p>{{ $identification->contact ? $identification->contact->name : 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Business Type:</strong>
                        <p>
                            @if($identification->business_types && is_array($identification->business_types))
                                @foreach($identification->business_types as $type)
                                    <span class="label label-info">{{ ucfirst($type) }}</span> 
                                @endforeach
                                @if($identification->business_type_other)
                                    <br><small>Other: {{ $identification->business_type_other }}</small>
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <!-- Primary Contact Information -->
                <h4 class="tw-font-bold tw-mb-4 tw-mt-4"><i class="fa fa-user"></i> Primary Contact Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Contact Name:</strong>
                        <p>{{ $identification->primary_contact_name ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Title:</strong>
                        <p>{{ $identification->primary_contact_title ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Phone:</strong>
                        <p>{{ $identification->primary_contact_phone ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p>{{ $identification->primary_contact_email ?: 'N/A' }}</p>
                    </div>
                </div>

                <hr>

                <!-- Address Information -->
                <h4 class="tw-font-bold tw-mb-4 tw-mt-4"><i class="fa fa-map-marker"></i> Address Information</h4>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Business Address:</strong>
                        <p>{{ $identification->business_address ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Ship-From Address:</strong>
                        <p>{{ $identification->ship_from_address ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Ship-To Address:</strong>
                        <p>{{ $identification->ship_to_address ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Website / Marketplace Storefronts:</strong>
                        <p>{{ $identification->website_marketplaces ?: 'N/A' }}</p>
                    </div>
                </div>

                <hr>

                <!-- License and Permit Information -->
                <h4 class="tw-font-bold tw-mb-4 tw-mt-4"><i class="fa fa-certificate"></i> License and Permit Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Resale Certificate/Permit #:</strong>
                        <p>{{ $identification->resale_certificate_number ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Issuing State:</strong>
                        <p>{{ $identification->resale_certificate_state ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>State/Local Licenses:</strong>
                        @if($identification->state_licenses && is_array($identification->state_licenses) && count($identification->state_licenses) > 0)
                            <div class="table-responsive" style="margin-top: 10px;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Number</th>
                                            <th>Expiry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($identification->state_licenses as $license)
                                            @if(isset($license['type']) || isset($license['number']) || isset($license['expiry']))
                                                <tr>
                                                    <td>{{ $license['type'] ?? 'N/A' }}</td>
                                                    <td>{{ $license['number'] ?? 'N/A' }}</td>
                                                    <td>{{ $license['expiry'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>N/A</p>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Age-Gating Information -->
                <h4 class="tw-font-bold tw-mb-4 tw-mt-4"><i class="fa fa-shield"></i> Age-Gating Method</h4>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Methods:</strong>
                        <p>
                            @if($identification->age_gating_methods && is_array($identification->age_gating_methods))
                                @foreach($identification->age_gating_methods as $method)
                                    <span class="label label-primary">{{ str_replace('_', ' ', ucfirst($method)) }}</span> 
                                @endforeach
                                @if($identification->age_gating_other)
                                    <br><small>Other: {{ $identification->age_gating_other }}</small>
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <!-- Acknowledgments -->
                <h4 class="tw-font-bold tw-mb-4 tw-mt-4"><i class="fa fa-check-square"></i> Acknowledgments</h4>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Prohibited Jurisdictions Acknowledged:</strong>
                        <p>
                            @if($identification->prohibited_jurisdictions_acknowledged)
                                <span class="label label-success"><i class="fa fa-check"></i> Yes</span>
                            @else
                                <span class="label label-danger"><i class="fa fa-times"></i> No</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <!-- Attachments -->
                <h4 class="tw-font-bold tw-mb-4 tw-mt-4"><i class="fa fa-paperclip"></i> Attachments</h4>
                <div class="row">
                    <div class="col-md-12">
                        @if($identification->attachments && is_array($identification->attachments) && count($identification->attachments) > 0)
                            <div class="row">
                                @foreach($identification->attachments as $attachment)
                                    @php
                                        $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                        $filename = basename($attachment);
                                        $isPdf = strtolower($ext) === 'pdf';
                                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                                    @endphp
                                    <div class="col-md-3" style="margin-bottom: 15px;">
                                        <div class="thumbnail text-center">
                                            @if($isImage)
                                                <a href="{{ asset($attachment) }}" target="_blank">
                                                    <img src="{{ asset($attachment) }}" style="max-height: 150px; object-fit: cover;">
                                                </a>
                                            @else
                                                <a href="{{ asset($attachment) }}" target="_blank">
                                                    <i class="fa fa-file-{{ $isPdf ? 'pdf' : 'text' }}-o fa-4x" style="color: #3c8dbc; margin: 20px 0;"></i>
                                                </a>
                                            @endif
                                            <div class="caption">
                                                <small>{{ $filename }}</small><br>
                                                <a href="{{ asset($attachment) }}" target="_blank" class="btn btn-xs btn-primary">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No attachments</p>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Admin Notes -->
                @if($identification->admin_notes)
                    <h4 class="tw-font-bold tw-mb-4 tw-mt-4"><i class="fa fa-comment"></i> Admin Notes</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                {{ $identification->admin_notes }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-6">
                        <strong>Created By:</strong>
                        <p>{{ $identification->creator ? $identification->creator->username : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Created At:</strong>
                        <p>{{ $identification->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Last Updated:</strong>
                        <p>{{ $identification->updated_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <a href="{{ action([\App\Http\Controllers\BusinessIdentificationController::class, 'index']) }}" class="tw-dw-btn tw-dw-btn-primary">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                        @can('business_identification.update')
                            <a href="{{ action([\App\Http\Controllers\BusinessIdentificationController::class, 'edit'], [$identification->id]) }}" class="tw-dw-btn tw-dw-btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>

            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

