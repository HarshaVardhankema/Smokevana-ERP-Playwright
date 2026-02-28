<style>
    .visit-details-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    
    .visit-section {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e9ecef;
    }
    
    .visit-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f3f5;
        display: flex;
        align-items: center;
    }
    
    .visit-section-title i {
        margin-right: 10px;
        font-size: 18px;
        color: #667eea;
    }
    
    .info-group {
        margin-bottom: 20px;
    }
    
    .info-group:last-child {
        margin-bottom: 0;
    }
    
    .info-label {
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }
    
    .info-value {
        font-size: 15px;
        font-weight: 500;
        color: #2c3e50;
        line-height: 1.6;
        display: block;
    }
    
    .info-value strong {
        color: #1a252f;
        font-weight: 600;
    }
    
    .info-value small {
        font-size: 13px;
        color: #868e96;
        display: block;
        margin-top: 4px;
    }
    
    .status-badge-modern {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-completed {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .status-in_progress {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .status-missing_proof {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: #721c24;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: #004085;
    }
    
    .proof-card {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    .proof-card:hover {
        border-color: #667eea;
        background: #f8f9ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    
    .proof-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .proof-card-title i {
        margin-right: 8px;
        font-size: 18px;
    }
    
    .proof-image {
        max-width: 100%;
        max-height: 180px;
        border-radius: 8px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .proof-video {
        max-width: 100%;
        max-height: 180px;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    
    .proof-action-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        margin-top: auto;
    }
    
    .proof-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .proof-action-btn i {
        margin-right: 6px;
    }
    
    .no-proof-message {
        color: #868e96;
        font-size: 14px;
        font-style: italic;
        text-align: center;
        padding: 20px;
    }
    
    .remarks-box {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 16px 20px;
        border-radius: 6px;
        color: #2c3e50;
        line-height: 1.8;
        font-size: 14px;
    }
    
    .location-box {
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        border: 1px solid #667eea40;
        border-radius: 8px;
        padding: 16px;
        margin-top: 10px;
    }
    
    .location-box i {
        color: #667eea;
        margin-right: 8px;
    }
    
    /* Grid alignment improvements */
    .visit-details-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -12px;
    }
    
    .visit-details-col {
        padding: 0 12px;
        margin-bottom: 24px;
    }
    
    .visit-details-col-6 {
        width: 50%;
    }
    
    .visit-details-col-12 {
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .visit-details-col-6 {
            width: 100%;
        }
        
        .proof-card {
            min-height: 160px;
        }
    }
</style>

<div class="visit-details-container">
    <!-- Sales Rep and Lead Info -->
    <div class="visit-section">
        <div class="visit-section-title">
            <i class="fa fa-info-circle"></i> Visit Overview
        </div>
        <div class="visit-details-row">
            <div class="visit-details-col visit-details-col-6">
                <div class="info-group">
                    <span class="info-label"><i class="fa fa-user"></i> Sales Rep</span>
                    <span class="info-value">
                        <strong>{{ $visit->salesRep->first_name }} {{ $visit->salesRep->last_name }}</strong>
                        <small>{{ $visit->salesRep->username ?? 'N/A' }}</small>
                    </span>
                </div>
            </div>
            <div class="visit-details-col visit-details-col-6">
                <div class="info-group">
                    <span class="info-label"><i class="fa fa-building"></i> Lead</span>
                    <span class="info-value">
                        <strong>{{ $visit->lead->store_name }}</strong>
                        <small>{{ $visit->lead->reference_no ?? 'N/A' }}</small>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="visit-details-row">
            <div class="visit-details-col visit-details-col-12">
                <div class="info-group">
                    <span class="info-label"><i class="fa fa-map-marker"></i> Location</span>
                    <div class="location-box">
                        <i class="fa fa-map-marker"></i>
                        {{ $visit->lead->full_address ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Details -->
    <div class="visit-section">
        <div class="visit-section-title">
            <i class="fa fa-clock-o"></i> Visit Details
        </div>
        <div class="visit-details-row">
            <div class="visit-details-col visit-details-col-6">
                <div class="info-group">
                    <span class="info-label"><i class="fa fa-calendar"></i> Start Time</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($visit->start_time)->format('M d, Y h:i A') }}</span>
                </div>
            </div>
            <div class="visit-details-col visit-details-col-6">
                <div class="info-group">
                    <span class="info-label"><i class="fa fa-hourglass-half"></i> Duration</span>
                    <span class="info-value">{{ $visit->duration ? $visit->duration . ' minutes' : 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        <div class="visit-details-row">
            <div class="visit-details-col visit-details-col-6">
                <div class="info-group">
                    <span class="info-label"><i class="fa fa-tasks"></i> Status</span>
                    <span class="info-value">
                        <span class="status-badge-modern status-{{ $visit->status }}">
                            {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                        </span>
                    </span>
                </div>
            </div>
            <div class="visit-details-col visit-details-col-6">
                <div class="info-group">
                    <span class="info-label"><i class="fa fa-tag"></i> Type</span>
                    <span class="info-value">{{ ucfirst(str_replace('_', ' ', $visit->visit_type ?? 'N/A')) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Proof Collection -->
    <div class="visit-section">
        <div class="visit-section-title">
            <i class="fa fa-camera"></i> Proof Collection
        </div>
        
        @php
            $hasProof = $visit->location_proof || $visit->photo_proof || $visit->signature_proof || $visit->video_proof;
        @endphp
        
        @if($hasProof)
            <div class="visit-details-row">
                @if($visit->location_proof && $visit->location_proof_path)
                <div class="visit-details-col visit-details-col-6">
                    <div class="proof-card">
                        <div class="proof-card-title">
                            <i class="fa fa-map-marker text-success"></i> Location Proof
                        </div>
                        <i class="fa fa-map-marker" style="font-size: 48px; color: #27ae60; margin: 20px 0;"></i>
                        <a href="{{ asset('storage/' . $visit->location_proof_path) }}" target="_blank" class="proof-action-btn">
                            <i class="fa fa-eye"></i> View File
                        </a>
                    </div>
                </div>
                @endif
                
                @if($visit->photo_proof && $visit->photo_proof_paths)
                    @php
                        $photos = is_array($visit->photo_proof_paths) ? $visit->photo_proof_paths : json_decode($visit->photo_proof_paths, true);
                    @endphp
                    @foreach($photos as $photo)
                    <div class="visit-details-col visit-details-col-6">
                        <div class="proof-card">
                            <div class="proof-card-title">
                                <i class="fa fa-camera text-info"></i> Photo Proof
                            </div>
                            <img src="{{ asset('storage/' . $photo) }}" class="proof-image" alt="Photo Proof">
                            <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="proof-action-btn">
                                <i class="fa fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    @endforeach
                @endif
                
                @if($visit->signature_proof && $visit->signature_proof_path)
                <div class="visit-details-col visit-details-col-6">
                    <div class="proof-card">
                        <div class="proof-card-title">
                            <i class="fa fa-pencil text-warning"></i> Signature Proof
                        </div>
                        <i class="fa fa-pencil-square-o" style="font-size: 48px; color: #f39c12; margin: 20px 0;"></i>
                        <a href="{{ asset('storage/' . $visit->signature_proof_path) }}" target="_blank" class="proof-action-btn">
                            <i class="fa fa-eye"></i> View File
                        </a>
                    </div>
                </div>
                @endif
                
                @if($visit->video_proof && $visit->video_proof_path)
                <div class="visit-details-col visit-details-col-6">
                    <div class="proof-card">
                        <div class="proof-card-title">
                            <i class="fa fa-video-camera text-primary"></i> Video Proof
                        </div>
                        <video controls class="proof-video">
                            <source src="{{ asset('storage/' . $visit->video_proof_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <a href="{{ asset('storage/' . $visit->video_proof_path) }}" target="_blank" class="proof-action-btn">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                </div>
                @endif
            </div>
        @else
            <div class="no-proof-message">
                <i class="fa fa-info-circle"></i> No proof files have been uploaded for this visit.
            </div>
        @endif
    </div>

    <!-- Remarks -->
    @if($visit->remarks)
    <div class="visit-section">
        <div class="visit-section-title">
            <i class="fa fa-comment"></i> Remarks
        </div>
        <div class="remarks-box">
            {{ $visit->remarks }}
        </div>
    </div>
    @endif
</div>