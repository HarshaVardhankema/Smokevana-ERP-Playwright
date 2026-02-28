@extends('layouts.app')

@section('title', __( 'lang_v1.view_user' ))

@section('css')
<style>
/* Amazon-style View User Page */
.amazon-view-container {
    padding: 16px 20px;
    background: #EAEDED;
    min-height: 100vh;
}

.amazon-view-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.amazon-view-title {
    font-size: 22px;
    font-weight: 400;
    color: #0F1111;
    margin: 0;
}

.amazon-user-selector {
    width: 280px;
}

.amazon-user-selector .select2-container .select2-selection {
    height: 36px;
    border: 1px solid #888C8C;
    border-radius: 4px;
}

.amazon-user-selector .select2-container .select2-selection__rendered {
    line-height: 34px;
    padding-left: 12px;
}

/* Layout Grid */
.amazon-view-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 16px;
}

@media (max-width: 992px) {
    .amazon-view-grid {
        grid-template-columns: 1fr;
    }
}

/* Profile Sidebar Card */
.amazon-profile-card {
    background: #FFFFFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    overflow: hidden;
    position: sticky;
    top: 16px;
}

.amazon-profile-header {
    background: linear-gradient(135deg, #232F3E 0%, #37475A 100%);
    padding: 24px;
    text-align: center;
}

.amazon-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid #FF9900;
    margin: 0 auto 12px;
    object-fit: cover;
    background: #FFF;
}

.amazon-profile-name {
    font-size: 20px;
    font-weight: 600;
    color: #FFFFFF;
    margin: 0 0 4px;
}

.amazon-profile-role {
    display: inline-block;
    background: rgba(255, 153, 0, 0.2);
    border: 1px solid #FF9900;
    color: #FFB84D;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.amazon-profile-body {
    padding: 0;
}

.amazon-profile-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #E7E7E7;
}

.amazon-profile-item:last-child {
    border-bottom: none;
}

.amazon-profile-label {
    font-size: 13px;
    font-weight: 600;
    color: #565959;
}

.amazon-profile-value {
    font-size: 13px;
    color: #0F1111;
    text-align: right;
    word-break: break-all;
}

.amazon-profile-value a {
    color: #007185;
    text-decoration: none;
}

.amazon-profile-value a:hover {
    color: #C7511F;
    text-decoration: underline;
}

/* Status Badge */
.amazon-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.amazon-badge-success {
    background: #067D62;
    color: #FFFFFF;
}

.amazon-badge-danger {
    background: #C40000;
    color: #FFFFFF;
}

/* Profile Actions */
.amazon-profile-actions {
    padding: 16px;
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
}

.amazon-btn-edit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 500;
    color: #0F1111;
    background: linear-gradient(to bottom, #FFD814 0%, #FF9900 100%);
    border: 1px solid #FCD200;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s ease;
}

.amazon-btn-edit:hover {
    background: linear-gradient(to bottom, #F7CA00 0%, #E47911 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.4);
    text-decoration: none;
    color: #0F1111;
}

/* Main Content Card */
.amazon-content-card {
    background: #FFFFFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    overflow: hidden;
}

/* Amazon Tabs */
.amazon-tabs {
    display: flex;
    background: linear-gradient(to bottom, #F7F8F8, #EAEDED);
    border-bottom: 1px solid #D5D9D9;
}

.amazon-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 20px;
    font-size: 14px;
    font-weight: 500;
    color: #565959;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all .2s ease;
    text-decoration: none;
}

.amazon-tab:hover {
    color: #C7511F;
    background: rgba(255, 153, 0, 0.05);
    text-decoration: none;
}

.amazon-tab.active {
    color: #C7511F;
    border-bottom-color: #FF9900;
    background: #FFFFFF;
}

.amazon-tab svg {
    flex-shrink: 0;
}

/* Tab Content */
.amazon-tab-content {
    display: none;
    padding: 20px;
}

.amazon-tab-content.active {
    display: block;
}

/* Info Cards Grid */
.amazon-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
}

/* Info Card */
.amazon-info-card {
    background: #FAFAFA;
    border: 1px solid #E7E7E7;
    border-radius: 8px;
    overflow: hidden;
}

.amazon-info-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: linear-gradient(to bottom, #F7F8F8, #F0F2F2);
    border-bottom: 1px solid #E7E7E7;
    font-size: 14px;
    font-weight: 700;
    color: #0F1111;
}

.amazon-info-card-header svg {
    color: #FF9900;
}

.amazon-info-card-body {
    padding: 16px;
}

/* Info Row */
.amazon-info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #F0F2F2;
}

.amazon-info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.amazon-info-row:first-child {
    padding-top: 0;
}

.amazon-info-label {
    font-size: 13px;
    color: #565959;
    font-weight: 500;
}

.amazon-info-value {
    font-size: 13px;
    color: #0F1111;
    font-weight: 400;
    text-align: right;
    max-width: 60%;
    word-break: break-word;
}

.amazon-info-value.empty {
    color: #888C8C;
    font-style: italic;
}

.amazon-info-value a {
    color: #007185;
    text-decoration: none;
}

.amazon-info-value a:hover {
    color: #C7511F;
    text-decoration: underline;
}

/* Address Card */
.amazon-address-card {
    background: #FAFAFA;
    border: 1px solid #E7E7E7;
    border-radius: 8px;
    padding: 16px;
}

.amazon-address-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #0F1111;
    margin-bottom: 12px;
}

.amazon-address-title svg {
    color: #FF9900;
}

.amazon-address-text {
    font-size: 13px;
    color: #0F1111;
    line-height: 1.5;
}

.amazon-address-text.empty {
    color: #888C8C;
    font-style: italic;
}

/* Activities Table */
.amazon-activities-table {
    width: 100%;
    border-collapse: collapse;
}

.amazon-activities-table th {
    background: linear-gradient(to bottom, #232F3E, #37475A);
    color: #FFFFFF;
    font-size: 13px;
    font-weight: 600;
    padding: 12px 16px;
    text-align: left;
}

.amazon-activities-table td {
    padding: 12px 16px;
    font-size: 13px;
    color: #0F1111;
    border-bottom: 1px solid #E7E7E7;
}

.amazon-activities-table tr:hover td {
    background: #F7F8F8;
}

.amazon-activities-table tr:last-child td {
    border-bottom: none;
}

/* Documents Section */
.amazon-docs-header {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 16px;
}

.amazon-btn-add {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #FFFFFF;
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border: 1px solid #C45500;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s ease;
}

.amazon-btn-add:hover {
    background: linear-gradient(to bottom, #FFB84D 0%, #FF9900 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(255, 153, 0, 0.4);
    text-decoration: none;
    color: #FFFFFF;
}

/* DataTable Styling Override */
.amazon-tab-content .dataTables_wrapper {
    padding: 0;
}

.amazon-tab-content .dataTables_filter input {
    border: 1px solid #888C8C;
    border-radius: 4px;
    padding: 6px 12px;
}

.amazon-tab-content .dataTables_length select {
    border: 1px solid #888C8C;
    border-radius: 4px;
    padding: 4px 8px;
}

.amazon-tab-content .btn {
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    color: #0F1111;
    font-size: 12px;
    padding: 6px 12px;
    transition: all .2s ease;
}

.amazon-tab-content .btn:hover {
    background: linear-gradient(to bottom, #F7F8F8 0%, #E7E9EC 100%);
    transform: translateY(-1px);
}

/* Empty State */
.amazon-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #565959;
}

.amazon-empty-state svg {
    margin-bottom: 16px;
    opacity: 0.5;
}

.amazon-empty-state p {
    font-size: 14px;
    margin: 0;
}

/* Quick Stats */
.amazon-quick-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

.amazon-stat-card {
    background: linear-gradient(135deg, #232F3E 0%, #37475A 100%);
    border-radius: 8px;
    padding: 16px;
    text-align: center;
}

.amazon-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #FF9900;
    margin-bottom: 4px;
}

.amazon-stat-label {
    font-size: 12px;
    color: #A0A0A0;
}

/* Responsive */
@media (max-width: 768px) {
    .amazon-view-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .amazon-user-selector {
        width: 100%;
    }
    
    .amazon-tabs {
        flex-wrap: wrap;
    }
    
    .amazon-tab {
        flex: 1 1 100%;
    }
    
    .amazon-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="amazon-view-container">
    <!-- Header -->
    <div class="amazon-view-header">
        <h1 class="amazon-view-title">@lang('lang_v1.view_user')</h1>
        <div class="amazon-user-selector">
            {!! Form::select('user_id', $users, $user->id, ['class' => 'form-control select2', 'id' => 'user_id']) !!}
        </div>
    </div>

    <!-- Main Grid -->
    <div class="amazon-view-grid">
        <!-- Profile Sidebar -->
        <div class="amazon-profile-card">
            <div class="amazon-profile-header">
                @php
                    if(isset($user->media->display_url)) {
                        $img_src = $user->media->display_url;
                    } else {
                        $img_src = 'https://ui-avatars.com/api/?name='.$user->first_name.'&background=FF9900&color=fff&size=128';
                    }
                @endphp
                <img class="amazon-avatar" src="{{ $img_src }}" alt="{{ $user->user_full_name }}">
                <h2 class="amazon-profile-name">{{ $user->user_full_name }}</h2>
                <span class="amazon-profile-role">{{ $user->role_name }}</span>
            </div>
            
            <div class="amazon-profile-body">
                <div class="amazon-profile-item">
                    <span class="amazon-profile-label">Username</span>
                    <span class="amazon-profile-value">{{ $user->username }}</span>
                </div>
                <div class="amazon-profile-item">
                    <span class="amazon-profile-label">Email</span>
                    <span class="amazon-profile-value">
                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                    </span>
                </div>
                <div class="amazon-profile-item">
                    <span class="amazon-profile-label">Status</span>
                    <span class="amazon-profile-value">
                        @if($user->status == 'active')
                            <span class="amazon-badge amazon-badge-success">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Active
                            </span>
                        @else
                            <span class="amazon-badge amazon-badge-danger">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                Inactive
                            </span>
                        @endif
                    </span>
                </div>
                @if($user->contact_number)
                <div class="amazon-profile-item">
                    <span class="amazon-profile-label">Phone</span>
                    <span class="amazon-profile-value">
                        <a href="tel:+1{{ $user->contact_number }}">+1 {{ $user->contact_number }}</a>
                    </span>
                </div>
                @endif
            </div>
            
            @can('user.update')
            <div class="amazon-profile-actions">
                <a href="{{ action([\App\Http\Controllers\ManageUserController::class, 'edit'], [$user->id]) }}" class="amazon-btn-edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit User
                </a>
            </div>
            @endcan
        </div>

        <!-- Main Content -->
        <div class="amazon-content-card">
            <!-- Tabs -->
            <div class="amazon-tabs">
                <a href="#" class="amazon-tab active" data-tab="user_info">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    User Information
                </a>
                <a href="#" class="amazon-tab" data-tab="documents">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    Documents & Notes
                </a>
                <a href="#" class="amazon-tab" data-tab="activities">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Activities
                </a>
            </div>

            <!-- User Information Tab -->
            <div class="amazon-tab-content active" id="tab-user_info">
                <!-- Quick Stats -->
                <div class="amazon-quick-stats">
                    <div class="amazon-stat-card">
                        <div class="amazon-stat-value">{{ $user->cmmsn_percent ?? 0 }}%</div>
                        <div class="amazon-stat-label">Commission Rate</div>
                    </div>
                    <div class="amazon-stat-card">
                        @php
                            $selected_contacts = '';
                            if(count($user->contactAccess)) {
                                $selected_contacts = count($user->contactAccess) . ' Contacts';
                            } else {
                                $selected_contacts = 'All';
                            }
                        @endphp
                        <div class="amazon-stat-value">{{ $selected_contacts }}</div>
                        <div class="amazon-stat-label">Allowed Contacts</div>
                    </div>
                </div>

                <div class="amazon-info-grid">
                    <!-- Personal Information -->
                    <div class="amazon-info-card">
                        <div class="amazon-info-card-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Personal Information
                        </div>
                        <div class="amazon-info-card-body">
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Date of Birth</span>
                                <span class="amazon-info-value {{ empty($user->dob) ? 'empty' : '' }}">
                                    {{ !empty($user->dob) ? @format_date($user->dob) : 'Not provided' }}
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Gender</span>
                                <span class="amazon-info-value {{ empty($user->gender) ? 'empty' : '' }}">
                                    {{ !empty($user->gender) ? ucfirst($user->gender) : 'Not provided' }}
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Marital Status</span>
                                <span class="amazon-info-value {{ empty($user->marital_status) ? 'empty' : '' }}">
                                    {{ !empty($user->marital_status) ? ucfirst($user->marital_status) : 'Not provided' }}
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Blood Group</span>
                                <span class="amazon-info-value {{ empty($user->blood_group) ? 'empty' : '' }}">
                                    {{ $user->blood_group ?? 'Not provided' }}
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Guardian Name</span>
                                <span class="amazon-info-value {{ empty($user->guardian_name) ? 'empty' : '' }}">
                                    {{ $user->guardian_name ?? 'Not provided' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="amazon-info-card">
                        <div class="amazon-info-card-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            Contact Information
                        </div>
                        <div class="amazon-info-card-body">
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Mobile Number</span>
                                <span class="amazon-info-value {{ empty($user->contact_number) ? 'empty' : '' }}">
                                    @if($user->contact_number)
                                        <a href="tel:+1{{ $user->contact_number }}">+1 {{ $user->contact_number }}</a>
                                    @else
                                        Not provided
                                    @endif
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Alternate Number</span>
                                <span class="amazon-info-value {{ empty($user->alt_number) ? 'empty' : '' }}">
                                    @if($user->alt_number)
                                        <a href="tel:+1{{ $user->alt_number }}">+1 {{ $user->alt_number }}</a>
                                    @else
                                        Not provided
                                    @endif
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Family Contact</span>
                                <span class="amazon-info-value {{ empty($user->family_number) ? 'empty' : '' }}">
                                    @if($user->family_number)
                                        <a href="tel:+1{{ $user->family_number }}">+1 {{ $user->family_number }}</a>
                                    @else
                                        Not provided
                                    @endif
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Email</span>
                                <span class="amazon-info-value">
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Identification -->
                    <div class="amazon-info-card">
                        <div class="amazon-info-card-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                <line x1="7" y1="8" x2="7" y2="8"></line>
                                <line x1="7" y1="12" x2="17" y2="12"></line>
                                <line x1="7" y1="16" x2="13" y2="16"></line>
                            </svg>
                            Identification
                        </div>
                        <div class="amazon-info-card-body">
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">ID Proof Type</span>
                                <span class="amazon-info-value {{ empty($user->id_proof_name) ? 'empty' : '' }}">
                                    {{ $user->id_proof_name ?? 'Not provided' }}
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">ID Proof Number</span>
                                <span class="amazon-info-value {{ empty($user->id_proof_number) ? 'empty' : '' }}">
                                    {{ $user->id_proof_number ?? 'Not provided' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="amazon-info-card">
                        <div class="amazon-info-card-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                            Social Media
                        </div>
                        <div class="amazon-info-card-body">
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Facebook</span>
                                <span class="amazon-info-value {{ empty($user->fb_link) ? 'empty' : '' }}">
                                    @if($user->fb_link)
                                        <a href="{{ $user->fb_link }}" target="_blank">View Profile</a>
                                    @else
                                        Not provided
                                    @endif
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">Twitter</span>
                                <span class="amazon-info-value {{ empty($user->twitter_link) ? 'empty' : '' }}">
                                    @if($user->twitter_link)
                                        <a href="{{ $user->twitter_link }}" target="_blank">View Profile</a>
                                    @else
                                        Not provided
                                    @endif
                                </span>
                            </div>
                            <div class="amazon-info-row">
                                <span class="amazon-info-label">LinkedIn</span>
                                <span class="amazon-info-value {{ empty($user->social_media_1) ? 'empty' : '' }}">
                                    @if($user->social_media_1)
                                        <a href="{{ $user->social_media_1 }}" target="_blank">View Profile</a>
                                    @else
                                        Not provided
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Addresses -->
                <div style="margin-top: 16px;">
                    <div class="amazon-info-grid" style="grid-template-columns: repeat(2, 1fr);">
                        <div class="amazon-address-card">
                            <div class="amazon-address-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Permanent Address
                            </div>
                            @php
                                $perm_address = [];
                                if($user->permanent_address) $perm_address[] = $user->permanent_address;
                                if($user->permanent_city) $perm_address[] = $user->permanent_city;
                                if($user->permanent_state) $perm_address[] = $user->permanent_state;
                                if($user->permanent_zip) $perm_address[] = $user->permanent_zip;
                            @endphp
                            <p class="amazon-address-text {{ empty($perm_address) ? 'empty' : '' }}">
                                {{ !empty($perm_address) ? implode(', ', $perm_address) : 'Not provided' }}
                            </p>
                        </div>
                        <div class="amazon-address-card">
                            <div class="amazon-address-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Current Address
                            </div>
                            @php
                                $curr_address = [];
                                if($user->current_address) $curr_address[] = $user->current_address;
                                if($user->current_city) $curr_address[] = $user->current_city;
                                if($user->current_state) $curr_address[] = $user->current_state;
                                if($user->current_zip) $curr_address[] = $user->current_zip;
                            @endphp
                            <p class="amazon-address-text {{ empty($curr_address) ? 'empty' : '' }}">
                                {{ !empty($curr_address) ? implode(', ', $curr_address) : 'Not provided' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bank Details -->
                @php
                    $bank_details = !empty($user->bank_details) ? json_decode($user->bank_details, true) : [];
                @endphp
                <div style="margin-top: 16px;">
                    <div class="amazon-info-card">
                        <div class="amazon-info-card-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Bank Details
                        </div>
                        <div class="amazon-info-card-body">
                            <div class="amazon-info-grid" style="grid-template-columns: repeat(3, 1fr); gap: 16px;">
                                <div>
                                    <div class="amazon-info-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <span class="amazon-info-label">Account Holder</span>
                                        <span class="amazon-info-value {{ empty($bank_details['account_holder_name']) ? 'empty' : '' }}" style="text-align: left; max-width: 100%;">
                                            {{ $bank_details['account_holder_name'] ?? 'Not provided' }}
                                        </span>
                                    </div>
                                    <div class="amazon-info-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <span class="amazon-info-label">Account Number</span>
                                        <span class="amazon-info-value {{ empty($bank_details['account_number']) ? 'empty' : '' }}" style="text-align: left; max-width: 100%;">
                                            {{ $bank_details['account_number'] ?? 'Not provided' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="amazon-info-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <span class="amazon-info-label">Bank Name</span>
                                        <span class="amazon-info-value {{ empty($bank_details['bank_name']) ? 'empty' : '' }}" style="text-align: left; max-width: 100%;">
                                            {{ $bank_details['bank_name'] ?? 'Not provided' }}
                                        </span>
                                    </div>
                                    <div class="amazon-info-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <span class="amazon-info-label">Routing Number</span>
                                        <span class="amazon-info-value {{ empty($bank_details['bank_code']) ? 'empty' : '' }}" style="text-align: left; max-width: 100%;">
                                            {{ $bank_details['bank_code'] ?? 'Not provided' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="amazon-info-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <span class="amazon-info-label">Branch</span>
                                        <span class="amazon-info-value {{ empty($bank_details['branch']) ? 'empty' : '' }}" style="text-align: left; max-width: 100%;">
                                            {{ $bank_details['branch'] ?? 'Not provided' }}
                                        </span>
                                    </div>
                                    <div class="amazon-info-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <span class="amazon-info-label">Tax Payer ID</span>
                                        <span class="amazon-info-value {{ empty($bank_details['tax_payer_id']) ? 'empty' : '' }}" style="text-align: left; max-width: 100%;">
                                            {{ $bank_details['tax_payer_id'] ?? 'Not provided' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($view_partials))
                    @foreach($view_partials as $partial)
                        {!! $partial !!}
                    @endforeach
                @endif
            </div>

            <!-- Documents & Notes Tab -->
            <div class="amazon-tab-content" id="tab-documents">
                <input type="hidden" name="notable_id" id="notable_id" value="{{ $user->id }}">
                <input type="hidden" name="notable_type" id="notable_type" value="App\User">
                <div class="document_note_body">
                </div>
            </div>

            <!-- Activities Tab -->
            <div class="amazon-tab-content" id="tab-activities">
                @include('activity_log.activities')
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
@include('documents_and_notes.document_and_note_js')

<script type="text/javascript">
$(document).ready(function() {
    // User selector change
    $('#user_id').change(function() {
        if ($(this).val()) {
            window.location = "{{ url('/users') }}/" + $(this).val();
        }
    });

    // Tab switching
    $('.amazon-tab').on('click', function(e) {
        e.preventDefault();
        var tabId = $(this).data('tab');
        
        // Update active tab
        $('.amazon-tab').removeClass('active');
        $(this).addClass('active');
        
        // Update active content
        $('.amazon-tab-content').removeClass('active');
        $('#tab-' + tabId).addClass('active');
    });
});
</script>
@endsection
