@section('title', __('Edit Location Tax Type'))

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">{{ __('Edit Location Tax Type') }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="{{ route('locationtaxtype.update', $locationTaxType->id) }}" method="POST" id="location_tax_type_edit_form">
            <div class="modal-body">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $locationTaxType->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            </div>
        </form>
    </div>
</div>