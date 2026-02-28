<div class="pos-tab-content">

    @php
        $templateData = session('business.templateData');
        if ($templateData) {
            // Header CSS
            $header_bg = $templateData['header_css']['final_string'] ?? '';
            $header_text = $templateData['header_css']['text'] ?? '';
            $header_type = $templateData['header_css']['type_of_background'] ?? '';
            $header_solid = $templateData['header_css']['solid_color_value'] ?? '';
            $header_image = $templateData['header_css']['image_url'] ?? '';
            $header_gradient_from = $templateData['header_css']['gradient_from_value'] ?? '';
            $header_gradient_to = $templateData['header_css']['gradient_to_value'] ?? '';
            $header_gradient_deg = $templateData['header_css']['gradient_degree'] ?? '';

            // Sidebar CSS
            $sidebar_bg = $templateData['sidebar_css']['final_string'] ?? '';
            $sidebar_text = $templateData['sidebar_css']['text'] ?? '';
            $sidebar_type = $templateData['sidebar_css']['type_of_background'] ?? '';
            $sidebar_solid = $templateData['sidebar_css']['solid_color_value'] ?? '';
            $sidebar_image = $templateData['sidebar_css']['image_url'] ?? '';
            $sidebar_gradient_from = $templateData['sidebar_css']['gradient_from_value'] ?? '';
            $sidebar_gradient_to = $templateData['sidebar_css']['gradient_to_value'] ?? '';
            $sidebar_gradient_deg = $templateData['sidebar_css']['gradient_degree'] ?? '';
            $sidebar_text_active = $templateData['sidebar_text_active'] ?? '';
            $sidebar_text_hover = $templateData['sidebar_text_hover'] ?? '';

            // Modal CSS
            $modal_bg = $templateData['modal_css']['final_string'] ?? '';
            $modal_text = $templateData['modal_css']['text'] ?? '';
            $modal_type = $templateData['modal_css']['type_of_background'] ?? '';
            $modal_solid = $templateData['modal_css']['solid_color_value'] ?? '';
            $modal_image = $templateData['modal_css']['image_url'] ?? '';
            $modal_gradient_from = $templateData['modal_css']['gradient_from_value'] ?? '';
            $modal_gradient_to = $templateData['modal_css']['gradient_to_value'] ?? '';
            $modal_gradient_deg = $templateData['modal_css']['gradient_degree'] ?? '';

            // Table CSS
            $tabel_bg = $templateData['tabel_css']['final_string'] ?? '';
            $tabel_text = $templateData['tabel_css']['text'] ?? '';
            $tabel_type = $templateData['tabel_css']['type_of_background'] ?? '';
            $tabel_solid = $templateData['tabel_css']['solid_color_value'] ?? '';
            $tabel_image = $templateData['tabel_css']['image_url'] ?? '';
            $tabel_gradient_from = $templateData['tabel_css']['gradient_from_value'] ?? '';
            $tabel_gradient_to = $templateData['tabel_css']['gradient_to_value'] ?? '';
            $tabel_gradient_deg = $templateData['tabel_css']['gradient_degree'] ?? '';

            // Logo CSS
            $logo_bg = $templateData['logo_css']['final_string'] ?? '';
            $logo_type = $templateData['logo_css']['type_of_background'] ?? '';
            $logo_solid = $templateData['logo_css']['solid_color_value'] ?? '';
            $logo_image = $templateData['logo_css']['image_url'] ?? '';
            $logo_gradient_from = $templateData['logo_css']['gradient_from_value'] ?? '';
            $logo_gradient_to = $templateData['logo_css']['gradient_to_value'] ?? '';
            $logo_gradient_deg = $templateData['logo_css']['gradient_degree'] ?? '';

            // Homepage CSS
            $homepage_bg = $templateData['homepage_css']['final_string'] ?? '';
            $homepage_type = $templateData['homepage_css']['type_of_background'] ?? '';
            $homepage_solid = $templateData['homepage_css']['solid_color_value'] ?? '';
            $homepage_image = $templateData['homepage_css']['image_url'] ?? '';
            $homepage_gradient_from = $templateData['homepage_css']['gradient_from_value'] ?? '';
            $homepage_gradient_to = $templateData['homepage_css']['gradient_to_value'] ?? '';
            $homepage_gradient_deg = $templateData['homepage_css']['gradient_degree'] ?? '';

            // Misc
            $header_button = $templateData['header_button'] ?? '';
        } else {
            $header_bg = '';
            $header_text = '';
            $header_type = '';
            $header_solid = '';
            $header_image = '';
            $header_gradient_from = '';
            $header_gradient_to = '';
            $header_gradient_deg = '';

            $sidebar_bg = '';
            $sidebar_text = '';
            $sidebar_type = '';
            $sidebar_solid = '';
            $sidebar_image = '';
            $sidebar_gradient_from = '';
            $sidebar_gradient_to = '';
            $sidebar_gradient_deg = '';
            $sidebar_text_active = '';
            $sidebar_text_hover = '';

            $modal_bg = '';
            $modal_text = '';
            $modal_type = '';
            $modal_solid = '';
            $modal_image = '';
            $modal_gradient_from = '';
            $modal_gradient_to = '';
            $modal_gradient_deg = '';

            $tabel_bg = '';
            $tabel_text = '';
            $tabel_type = '';
            $tabel_solid = '';
            $tabel_image = '';
            $tabel_gradient_from = '';
            $tabel_gradient_to = '';
            $tabel_gradient_deg = '';

            $logo_bg = '';
            $logo_type = '';
            $logo_solid = '';
            $logo_image = '';
            $logo_gradient_from = '';
            $logo_gradient_to = '';
            $logo_gradient_deg = '';

            $homepage_bg = '';
            $homepage_type = '';
            $homepage_solid = '';
            $homepage_image = '';
            $homepage_gradient_from = '';
            $homepage_gradient_to = '';
            $homepage_gradient_deg = '';

            $header_button = '';
        }

    @endphp
    <div class="row">
        <div class="col-sm-2">
            <label class="tw-font-semibold">Theme</label>
            {!! Form::select(
                'barcode_type',
                ['customized' => 'Customized', 'default' => 'Default'],
                $templateData ? 'customized' : 'default',
                ['class' => 'form-control select1', 'id' => 'theme-type', 'required'],
            ) !!}
        </div>
        <!-- Submit Button -->
        <div class="col-sm-2 tw-text-center tw-mt-6">
            <button id="css_submit" class="tw-bg-blue-500 tw-text-white tw-px-4 tw-py-2 tw-rounded">Save</button>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $page_entries = [25 => 25, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000, -1 => __('lang_v1.all')];
                @endphp
                {!! Form::label('default_datatable_page_entries', __('lang_v1.default_datatable_page_entries')); !!}
                {!! Form::select('common_settings[default_datatable_page_entries]', $page_entries, !empty($common_settings['default_datatable_page_entries']) ? $common_settings['default_datatable_page_entries'] : 25 , 
                    ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'default_datatable_page_entries']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_tooltip', 1, $business->enable_tooltip , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'business.show_help_text' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden section that will be shown for "Customized" theme -->
    <div id="custom-theme-settings" class="tw-hidden tw-mt-4 tw-border tw-p-4 tw-rounded">
        <div class="row">
            <!-- Header -->
            <label class="tw-font-semibold">Header CSS</label>
            <div class="tw-mb-4 tw-p-6 row">
                <div class="col-sm-4">
                    <label class="tw-font-semibold">Header Background</label>
                    {!! Form::select(
                        'barcode_type',
                        ['solid' => 'Solid Color', 'gradient' => 'Gradient', 'image' => 'Image'],
                        $header_type,
                        ['class' => 'form-control select1', 'id' => 'header-bg-type', 'required'],
                    ) !!}
                    <input id="header-bg-solid" style="width:80px; height:40px;" type="color"
                        class="tw-hidden  tw-mt-2 tw-p-2 tw-border tw-rounded" value={{ $header_solid }}>
                    <div id="header-bg-gradient" class="tw-hidden tw-mt-2">
                        <label>From: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $header_gradient_from }}></label>
                        <label>To: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $header_gradient_to }}></label>
                        <label>Angle: <input type="number" min="0" max="360"
                                class="tw-w-16 tw-border tw-rounded tw-p-2" value={{ $header_gradient_deg }}></label>
                    </div>
                    <div id="header-bg-image" class="tw-hidden tw-mt-2">
                        {!! Form::file('header-bg-image', ['id' => 'image-bg-upload', 'accept' => 'image/*']) !!}
                        {{-- <img src="{{ $header_image }}" style="width:80px; height:40px;" class="img-thumbnail"
                            alt="Header Background Image"> --}}
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="tw-font-semibold">App Background Color</label>
                    <input id="header-text-solid" style="width:80px; height:40px;" type="color"
                        class="  tw-mt-6 tw-p-2 tw-border tw-rounded" value={{ $header_text }}>
                </div>
                <div class="col-sm-3">
                    <label class="tw-font-semibold">Header Button Color</label>
                    <input id="header-button-solid" style="width:80px; height:40px;" type="color"
                        class="  tw-mt-6 tw-p-2 tw-border tw-rounded" value={{ $header_button }}>
                </div>
            </div>

            <div class="row tw-mb-4 tw-p-6 ">
                <div class="col-sm-4">
                    <label class="tw-font-semibold">logo Background</label>
                    {!! Form::select(
                        'barcode_type',
                        ['solid' => 'Solid Color', 'gradient' => 'Gradient', 'image' => 'Image'],
                        $logo_type,
                        ['class' => 'form-control select1', 'id' => 'logo-bg-type', 'required'],
                    ) !!}
                    <input id="logo-bg-solid" style="width:80px; height:40px;" type="color"
                        class="tw-hidden  tw-mt-2 tw-p-2 tw-border tw-rounded" value={{ $logo_solid }}>
                    <div id="logo-bg-gradient" class="tw-hidden tw-mt-2">
                        <label>From: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $logo_gradient_from }}></label>
                        <label>To: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $logo_gradient_to }}></label>
                        <label>Angle: <input type="number" min="0" max="360"
                                class="tw-w-16 tw-border tw-rounded tw-p-2" value={{ $logo_gradient_deg }}></label>
                    </div>
                    <div id="logo-bg-image" class="tw-hidden tw-mt-2">
                        {!! Form::file('logo-bg-image', ['id' => 'image-bg-upload', 'accept' => 'image/*']) !!}
                        {{-- <img src="{{ $header_image }}" style="width:80px;" class="img-thumbnail"
                            alt="Header Background Image"> --}}
                    </div>
                </div>
                <div class="col-sm-4">
                    <label class="tw-font-semibold">Homepage Background</label>
                    {!! Form::select(
                        'barcode_type',
                        ['solid' => 'Solid Color', 'gradient' => 'Gradient', 'image' => 'Image'],
                        $homepage_type,
                        ['class' => 'form-control select1', 'id' => 'homepage-bg-type', 'required'],
                    ) !!}
                    <input id="homepage-bg-solid" style="width:80px; height:40px;" type="color"
                        class="tw-hidden  tw-mt-2 tw-p-2 tw-border tw-rounded" value={{ $homepage_solid }}>
                    <div id="homepage-bg-gradient" class="tw-hidden tw-mt-2">
                        <label>From: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $homepage_gradient_from }}></label>
                        <label>To: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $homepage_gradient_to }}></label>
                        <label>Angle: <input type="number" min="0" max="360"
                                class="tw-w-16 tw-border tw-rounded tw-p-2" value={{ $homepage_gradient_deg }}></label>
                    </div>
                    <div id="homepage-bg-image" class="tw-hidden tw-mt-2">
                        {!! Form::file('homepage-bg-image', ['id' => 'image-bg-upload', 'accept' => 'image/*']) !!}
                    </div>
                </div>

            </div>

            <!-- Sidebar  -->
            <label class="tw-font-semibold">Sidebar CSS</label>
            <div class="tw-mb-4 tw-p-6 row">
                <div class="col-sm-4">
                    <label class="tw-font-semibold">Sidebar Background</label>
                    {!! Form::select(
                        'barcode_type',
                        ['solid' => 'Solid Color', 'gradient' => 'Gradient', 'image' => 'Image'],
                        $sidebar_type,
                        ['class' => 'form-control select1', 'id' => 'sidebar-bg-type', 'required'],
                    ) !!}
                    <input id="sidebar-bg-solid" style="width:80px; height:40px;" type="color"
                        class="tw-hidden  tw-mt-2 tw-p-2 tw-border tw-rounded" value={{ $sidebar_solid }}>
                    <div id="sidebar-bg-gradient" class="tw-hidden tw-mt-2">
                        <label>From: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $sidebar_gradient_from }}></label>
                        <label>To: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $sidebar_gradient_to }}></label>
                        <label>Angle: <input type="number" min="0" max="360"
                                class="tw-w-16 tw-border tw-rounded tw-p-2" value={{ $sidebar_gradient_deg }}></label>
                    </div>
                    <div id="sidebar-bg-image" class="tw-hidden tw-mt-2">
                        {!! Form::file('sidebar-bg-image', ['id' => 'image-bg-upload', 'accept' => 'image/*']) !!}
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="tw-font-semibold">Sidebar Text Color</label>
                    <input id="sidebar-text-solid" style="width:80px; height:40px;" type="color"
                        class="tw-mt-6 tw-p-2 tw-border tw-rounded" value={{ $sidebar_text }}>
                </div>
                <div class="col-sm-3">
                    <label class="tw-font-semibold">Sidebar Active Text Color</label>
                    <input id="sidebar-active-text-solid" style="width:80px; height:40px;" type="color"
                        class="tw-mt-6 tw-p-2 tw-border tw-rounded" value={{ $sidebar_text_active }}>
                </div>
                <div class="col-sm-3">
                    <label class="tw-font-semibold">Sidebar hovar Text Color</label>
                    <input id="sidebar-hover-text-solid" style="width:80px; height:40px;" type="color"
                        class="tw-mt-6 tw-p-2 tw-border tw-rounded" value={{ $sidebar_text_hover }}>
                </div>
            </div>


            <!-- Table Header -->
            <label class="tw-font-semibold">Table CSS</label>
            <div class="tw-mb-4 tw-p-6 row">
                <div class="col-sm-4">
                    <label class="tw-font-semibold">Table Header Background</label>
                    {!! Form::select(
                        'barcode_type',
                        ['solid' => 'Solid Color', 'gradient' => 'Gradient', 'image' => 'Image'],
                        $tabel_type,
                        ['class' => 'form-control select1', 'id' => 'table-header-bg-type', 'required'],
                    ) !!}
                    <input id="table-header-bg-solid" style="width:80px; height:40px;" type="color"
                        class="tw-hidden  tw-mt-2 tw-p-2 tw-border tw-rounded" value={{ $tabel_solid }}>
                    <div id="table-header-bg-gradient" class="tw-hidden tw-mt-2">
                        <label>From: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $tabel_gradient_from }}></label>
                        <label>To: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $tabel_gradient_to }}></label>
                        <label>Angle: <input type="number" min="0" max="360"
                                class="tw-w-16 tw-border tw-rounded tw-p-2" value={{ $tabel_gradient_deg }}></label>
                    </div>
                    <div id="table-header-bg-image" class="tw-hidden tw-mt-2">
                        {!! Form::file('table-header-bg-image', ['id' => 'image-bg-upload', 'accept' => 'image/*']) !!}
                    </div>
                </div>
                <div class="col-sm-3 ">
                    <label class="tw-font-semibold">Table Header Text</label>
                    <input id="table-header-text-solid" type="color" style="width:80px; height:40px;"
                        class=" tw-mt-6 tw-p-2 tw-border tw-rounded" value={{ $tabel_text }}>
                </div>
            </div>

            <!-- Modal Header -->
            <label class="tw-font-semibold">Modal CSS</label>
            <div class="tw-mb-4 tw-p-6 row">
                <div class="col-sm-4">
                    <label class="tw-font-semibold">Modal Background</label>
                    {!! Form::select(
                        'barcode_type',
                        ['solid' => 'Solid Color', 'gradient' => 'Gradient', 'image' => 'Image'],
                        $modal_type,
                        ['class' => 'form-control select1', 'id' => 'modal-header-bg-type', 'required'],
                    ) !!}

                    <input id="modal-header-bg-solid" style="width:80px; height:40px;" type="color"
                        class="tw-hidden  tw-mt-2 tw-p-2 tw-border tw-rounded" value={{ $modal_solid }}>
                    <div id="modal-header-bg-gradient" class="tw-hidden tw-mt-2">
                        <label>From: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $modal_gradient_from }}></label>
                        <label>To: <input type="color" style="width:80px; height:40px;"
                                class="tw-border tw-rounded tw-p-2" value={{ $modal_gradient_to }}></label>
                        <label>Angle: <input type="number" min="0" max="360"
                                class="tw-w-16 tw-border tw-rounded tw-p-2" value={{ $modal_gradient_deg }}></label>
                    </div>
                    <div id="modal-header-bg-image" class="tw-hidden tw-mt-2">
                        {!! Form::file('modal-header-bg-image', ['id' => 'image-bg-upload', 'accept' => 'image/*']) !!}
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="tw-font-semibold">Modal Header Text</label>
                    <input id="modal-header-text-solid" type="color" style="width:80px; height:40px;"
                        class=" tw-mt-6 tw-p-2 tw-border tw-rounded" value={{ $modal_text }}>
                </div>
            </div>
        </div>
    </div>

</div>

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            // Show/hide custom theme settings

            let uploadedImages = {};

            $('#theme-type').change(function() {
                if ($(this).val() === 'customized') {
                    $('#custom-theme-settings').removeClass('tw-hidden').addClass('tw-block');
                } else {
                    $('#custom-theme-settings').removeClass('tw-block').addClass('tw-hidden');
                }
            }).trigger('change');

            // Utility function to handle background selection logic
            function handleBackgroundTypeChange(selector, solidInput, gradientDiv, imageDiv) {
                $(selector).change(function() {
                    const value = $(this).val();
                    $(solidInput).addClass('tw-hidden');
                    $(gradientDiv).addClass('tw-hidden');
                    $(imageDiv).addClass('tw-hidden');

                    if (value === 'solid') $(solidInput).removeClass('tw-hidden');
                    if (value === 'gradient') $(gradientDiv).removeClass('tw-hidden');
                    if (value === 'image') $(imageDiv).removeClass('tw-hidden');
                }).trigger('change');
            }

            // Initialize background selectors
            handleBackgroundTypeChange('#header-bg-type', '#header-bg-solid', '#header-bg-gradient',
                '#header-bg-image');
            handleBackgroundTypeChange('#sidebar-bg-type', '#sidebar-bg-solid', '#sidebar-bg-gradient',
                '#sidebar-bg-image', );
            handleBackgroundTypeChange('#table-header-bg-type', '#table-header-bg-solid',
                '#table-header-bg-gradient', '#table-header-bg-image');
            handleBackgroundTypeChange('#modal-header-bg-type', '#modal-header-bg-solid',
                '#modal-header-bg-gradient', '#modal-header-bg-image');
            handleBackgroundTypeChange('#logo-bg-type', '#logo-bg-solid', '#logo-bg-gradient',
                '#logo-bg-image');
            handleBackgroundTypeChange('#homepage-bg-type', '#homepage-bg-solid', '#homepage-bg-gradient',
                '#homepage-bg-image');

            $("input[id='image-bg-upload']").on("change", function() {
                let input = $(this);
                let file = input[0].files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append("bg-image", file);

                let inputName = input.attr("name"); // e.g., "header-bg-image"
                let mappedKey = inputName.replace("-bg-image", "_background"); // e.g., "header_bg_image"

                $.ajax({
                    url: "/api/img",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success("Image Upload Success")
                            uploadedImages[mappedKey] = response.url; // Store URL
                        } else {
                            toastr.error("Image upload failed!");
                        }
                    },
                    error: function(xhr) {
                        alert("Error uploading image!");
                        console.error("Upload Error:", xhr.responseText);
                    }
                });
            });

            // Save button click logic
            $('#css_submit').click(function(e) {
                e.preventDefault();
                let theme = $('#theme-type').val()
                let formData;
                if (theme == 'customized') {
                    formData = {
                        header_css: collectBackgroundData('#header-bg-type', '#header-bg-solid',
                            '#header-bg-gradient', 'header', '#header-text-solid'),
                        sidebar_css: collectBackgroundData('#sidebar-bg-type', '#sidebar-bg-solid',
                            '#sidebar-bg-gradient', 'sidebar', '#sidebar-text-solid'),
                        modal_css: collectBackgroundData('#modal-header-bg-type',
                            '#modal-header-bg-solid',
                            '#modal-header-bg-gradient', 'modal-header',
                            '#modal-header-text-solid'),
                        tabel_css: collectBackgroundData('#table-header-bg-type',
                            '#table-header-bg-solid',
                            '#table-header-bg-gradient', 'table-header',
                            '#table-header-text-solid'),
                        logo_css: collectBackgroundData('#logo-bg-type', '#logo-bg-solid',
                            '#logo-bg-gradient', 'logo'),
                        homepage_css: collectBackgroundData('#homepage-bg-type', '#homepage-bg-solid',
                            '#homepage-bg-gradient', 'homepage'),
                        header_button: $('#header-button-solid').val(),
                        sidebar_text_hover: $('#sidebar-hover-text-solid').val(),
                        sidebar_text_active: $('#sidebar-active-text-solid').val(),


                    };
                } else {
                    formData = '';
                }



                // console.log('Form Data:', formData);

                $.ajax({
                    url: "/business/save-css-settings",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        data: formData,
                        _token: $('meta[name="csrf-token"]').attr(
                            "content")
                    }),
                    // headers: {
                    //     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    // },
                    success: function(response) {
                        console.log("Success:", response);
                        alert("CSS settings saved successfully!");
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        alert("Failed to save settings!");
                    }
                });
            });

            // Collect background data helper function
            function collectBackgroundData(typeSelector, solidSelector, gradientSelector, imageSelector,
                textSelector) {
                let type = $(typeSelector).val();
                let key = `${imageSelector}_background`;
                let data = {
                    final_string: '',
                    type_of_background: type,
                    solid_color_value: $(solidSelector).val(),
                    image_url: uploadedImages[key] || null,
                    gradient_from_value: $(gradientSelector).find('input[type="color"]').eq(0).val(),
                    gradient_to_value: $(gradientSelector).find('input[type="color"]').eq(1).val(),
                    gradient_degree: $(gradientSelector).find('input[type="number"]').val(),
                };

                if (type === 'solid') {
                    data.final_string = data.solid_color_value;
                } else if (type === 'gradient') {
                    data.final_string =
                        `linear-gradient(${data.gradient_degree}deg, ${data.gradient_from_value}, ${data.gradient_to_value})`;
                } else if (type === 'image') {
                    data.final_string = data.image_url;

                }

                data.text = $(textSelector).val();
                return data;
            }
        });
    </script>
@endsection
