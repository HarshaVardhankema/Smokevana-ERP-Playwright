<div class="modal-dialog modal-md " role="document" >
    <div class="modal-content">
        {!! Form::open([ 'method' => 'POST', 'id' => 'held_modal']) !!}
        <div class="modal-header" style="padding: 5px 10px;">
            <div class="tw-flex tw-justify-between">
                <h4 class="modal-title tw-flex" style="align-items: center" ></h4>
                <div>
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="held_submit_button">Assign Again</button>
                    <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" id='close_button' data-dismiss="modal">@lang(
                    'messages.close' )</button>
                </div>
                
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group justify-content-center align-items-center">
                        {!! Form::label('handoverType', 'Assign:') !!}
                        <div class="input-group">
                            {!! Form::select(
                                'handoverType',
                                ['picker' => 'Picker', 'verifier' => 'Verifier'],
                                'picker', // default selected value
                                ['class' => 'form-control', 'id' => 'handoverType']
                            ) !!}
                        </div>
                    </div>
                    
                    
                </div>
                <div class="col-md-12">
                    <div class="form-group ">
                        {!! Form::label('staff_label', 'Staff:') !!}
                    
                        
                            {!! Form::text('staff_label', null, [
                                'class' => 'form-control',
                                'id' => 'staff-search',
                                'placeholder' => 'Search or select a staff member',
                                'autocomplete' => 'off',
                                'required'
                            ]) !!}
                    
                            <div class="dropdown border shadow-sm rounded" id="staff-dropdown" style="background-color: #fff;border: 1px solid #ddd;border-radius: 6px;max-height: 200px;overflow-y: auto;overflow-x: auto;width: 100%;white-space: nowrap;display: none;z-index: 1000;font-family: 'Segoe UI', Tahoma, sans-serif;box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);padding: 5px 0;"
                        >
                                @foreach ($users as $id => $name)
                                    <div data-value="{{ $id }}" class="dropdown-item p-2 cursor-pointer">{{ $name }}</div>
                                @endforeach
                            </div>
                        
                    
                        {!! Form::hidden('type', null, ['id' => 'selected-staff']) !!}


                        <div class="col-sm-2 hide">
                            @foreach($orderIds as $key => $value)
                            <div class="form-check">
                                {!! Form::checkbox(
                                    'orders[]',
                                    $value,
                                    1,
                                    ['class' => 'form-check-input carrier-checkbox']
                                ) !!}
                                  </div>
                            @endforeach
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="modal-footer">
            
            
        </div>
    
        {!! Form::close() !!}
    


    </div>
</div>
<script>
    $(function () {
        const $input = $('#staff-search');
        const $dropdown = $('#staff-dropdown');
        const $hiddenInput = $('#selected-staff');
        
    
        $input.on('input', function () {
            const search = $(this).val().toLowerCase();
            $dropdown.show();
            $dropdown.children('div').each(function () {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(search));
            });
        });
    
        $dropdown.on('click', 'div', function () {
            const name = $(this).text();
            const value = $(this).data('value');
            $input.val(name);
            $hiddenInput.val(value);
            $dropdown.hide();
        });
    
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.searchable-select').length) {
                $dropdown.hide();
            }
        });
    });
    </script>
