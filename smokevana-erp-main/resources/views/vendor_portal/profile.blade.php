@extends('layouts.vendor_portal')
@section('title', 'Settings')

@section('css')
<style>
/* Profile Header */
.profile-header {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    background: var(--amazon-orange);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}

.profile-info h2 {
    margin: 0 0 4px 0;
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
}

.profile-info p {
    margin: 0 0 8px 0;
    color: var(--gray-600);
    font-size: 14px;
}

.profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: #d4edda;
    color: var(--amazon-success);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.profile-badge i {
    font-size: 10px;
}

/* Form Styling */
.form-section {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
}

.form-row.single {
    grid-template-columns: 1fr;
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--gray-300);
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.15s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--amazon-orange);
    box-shadow: 0 0 0 2px rgba(255,153,0,0.15);
}

.form-group input:disabled {
    background: var(--gray-100);
    color: var(--gray-500);
}

.form-help {
    font-size: 11px;
    color: var(--gray-500);
    margin-top: 4px;
}

/* Account Status Card */
.status-card {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    border: 1px solid var(--amazon-success);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
}

.status-icon {
    width: 60px;
    height: 60px;
    background: var(--amazon-success);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.status-icon i {
    font-size: 28px;
    color: #fff;
}

.status-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--amazon-success);
    margin-bottom: 4px;
}

.status-desc {
    font-size: 13px;
    color: var(--gray-600);
}

/* Info List */
.info-list {
    padding: 0;
    margin: 0;
    list-style: none;
}

.info-list li {
    padding: 14px 0;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
}

.info-list li:last-child {
    border-bottom: none;
}

.info-list .label {
    font-size: 13px;
    color: var(--gray-600);
}

.info-list .value {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-800);
}

/* Help Card */
.help-card {
    background: #e7f3fe;
    border: 1px solid #b6d4fe;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.help-card h4 {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 8px;
}

.help-card p {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 16px;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="sc-page-header">
    <h1 class="sc-page-title"><strong>Settings</strong></h1>
</div>

<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-avatar">
        {{ strtoupper(substr($vendor->display_name ?? 'V', 0, 1)) }}
    </div>
    <div class="profile-info">
        <h2>{{ $vendor->display_name ?? 'Vendor' }}</h2>
        <p>{{ $vendor->email ?? '' }}</p>
        <span class="profile-badge">
            <i class="bi bi-circle-fill"></i>
            {{ ucfirst(str_replace('_', ' ', $vendor->vendor_type ?? 'Vendor')) }}
        </span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Business Information -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-building"></i>
                    Business Information
                </h3>
            </div>
            <div class="sc-card-body">
                <form action="{{ route('vendor.profile.update') }}" method="POST" id="profile-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Display Name</label>
                            <input type="text" value="{{ $vendor->display_name }}" disabled>
                            <div class="form-help">Contact admin to change</div>
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" value="{{ $vendor->company_name ?? $vendor->display_name }}" placeholder="Your company name">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="{{ $vendor->email }}" disabled>
                            <div class="form-help">Contact admin to change</div>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="{{ $vendor->phone ?? '' }}" placeholder="Your phone number">
                        </div>
                    </div>
                    <div class="form-row single">
                        <div class="form-group">
                            <label>Business Address</label>
                            <input type="text" name="address" value="{{ $vendor->address ?? '' }}" placeholder="Your business address">
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <button type="submit" class="sc-btn sc-btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-lock"></i>
                    Change Password
                </h3>
            </div>
            <div class="sc-card-body">
                <form action="{{ route('vendor.profile.password') }}" method="POST" id="password-form">
                    @csrf
                    <div class="form-row single">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required placeholder="Enter current password">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" required placeholder="Confirm new password">
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <button type="submit" class="sc-btn sc-btn-secondary">
                            <i class="bi bi-key"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Account Status -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-shield-check"></i>
                    Account Status
                </h3>
            </div>
            <div class="sc-card-body">
                <div class="status-card">
                    <div class="status-icon">
                        <i class="bi bi-check"></i>
                    </div>
                    <div class="status-title">Active</div>
                    <div class="status-desc">Your account is in good standing</div>
                </div>
                
                <ul class="info-list">
                    <li>
                        <span class="label">Member Since</span>
                        <span class="value">{{ $vendor->created_at->format('M d, Y') }}</span>
                    </li>
                    <li>
                        <span class="label">Vendor Type</span>
                        <span class="value">{{ ucfirst(str_replace('_', ' ', $vendor->vendor_type ?? 'Vendor')) }}</span>
                    </li>
                    <li>
                        <span class="label">Vendor ID</span>
                        <span class="value">#{{ $vendor->id }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Help Card -->
        <div class="help-card">
            <h4><i class="bi bi-headset"></i> Need Help?</h4>
            <p>If you need assistance with your account or have questions, please contact our support team.</p>
            <a href="mailto:info@smokevana.com" class="sc-btn sc-btn-primary" style="width: 100%;">
                <i class="bi bi-envelope"></i> Contact Support
            </a>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Profile form submission
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Saving...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Profile updated successfully');
                } else {
                    toastr.error(response.msg || 'Failed to update profile');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('Failed to update profile');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-save"></i> Save Changes');
            }
        });
    });

    // Password form submission
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Updating...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Password updated successfully');
                    form[0].reset();
                } else {
                    toastr.error(response.msg || 'Failed to update password');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.msg) {
                    toastr.error(xhr.responseJSON.msg);
                } else {
                    toastr.error('Failed to update password');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-key"></i> Update Password');
            }
        });
    });
});
</script>
@endsection
