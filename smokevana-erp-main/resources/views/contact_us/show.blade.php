<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                Send Mail
            </h4>
        </div>
        <div class="modal-body">
            <div style=" margin: auto; padding: 1rem;">
                <div
                    style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;  padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <div>
                        <div class="tw-flex tw-gap-3">
                            <i class="fas fa-user" style="color: #6b46c1;"></i>
                            <p style="color: #6b7280;">Customer name</p>
                        </div>
                        <h4 style="font-weight: 600;">{{ $contect_us->fname }} {{ $contect_us->lname }}</h4>
                    </div>
                    <div>
                        <div class="tw-flex tw-gap-3">
                            <i class="fas fa-envelope" style="color: #6b46c1;"></i>
                            <p style="color: #6b7280;">Email</p>
                        </div>
                        <h4 style="font-weight: 600;"> {{ $contect_us->email }}</h4>
                    </div>
                    <div>
                        <div class="tw-flex tw-gap-3 ">
                            <i class="fas fa-phone" style="color: #6b46c1;"></i>
                            <p style="color: #6b7280;">Contact Number</p>
                        </div>
                        <h4 style="font-weight: 600;">{{ $contect_us->phone }} </h4>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding-top: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <div>
                        <div class="tw-flex tw-gap-3">
                            <i class="fas fa-map-marker-alt" style="color: #6b46c1;"></i>
                            <p style="color: #6b7280;">Location</p>
                        </div>
                        <h4 style="font-weight: 600;">{{ $contect_us->location ? $contect_us->location->name : 'N/A' }}</h4>
                    </div>
                    <div>
                        <div class="tw-flex tw-gap-3">
                            <i class="fas fa-globe" style="color: #6b46c1;"></i>
                            <p style="color: #6b7280;">Website</p>
                        </div>
                        <h4 style="font-weight: 600;">{{ $contect_us->brand ? $contect_us->brand->name : ($contect_us->location_id == 1 ? '—' : 'N/A') }}</h4>
                    </div>
                </div>
                <div style="padding-top: 1rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <p style="font-weight: 600;">Subject :</p>
                        <p>{{ $contect_us->subject }}</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem; padding-top: 0.5rem;">
                        <p style="font-weight: 600;">Message :</p>
                        <p>{{ $contect_us->message }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
