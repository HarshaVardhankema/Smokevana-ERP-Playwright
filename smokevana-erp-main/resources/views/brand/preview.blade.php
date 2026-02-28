<div class="modal-dialog modal-md" role="document" id="viewBrandModal" aria-labelledby="viewBrandModalLabel">
    <div class="modal-content">
        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            @php
                $front_end_url = config('app.front-url')??'';
                $brand_url = $front_end_url.'/brands/'.$brand->slug;
            @endphp
            <h4 class="modal-title" id="viewBrandModalLabel">Brand Preview @if (!empty($brand->slug)&&$front_end_url!='')<a href={{$brand_url}} target="_blank" class="tw-text-sm tw-text-gray-500"><i class="fa fa-link"></i></a>@endif</h4>
            
        </div>
        <div class="modal-body">
            <style>
                .scrollable-description {
                    max-height: 80px;
                    /* overflow-x: auto; */
                    overflow-y: auto;
                    white-space: wrap;
                    padding: 10px;
                    background-color: #fff;
                    border: 1px solid #eee;
                    border-radius: 4px;
                    margin-top: 5px;
                    font-size: 14px;
                    line-height: 1.5;
                }

                
            </style>


            <div class="row tw-gap-3" style="display: flex; align-items: center; flex-wrap:wrap;">
                <div class="col-sm-1 col-md-2 invoice-col" style="text-align: center;">
                    <div class="thumbnail" style="max-width: 150px; display: inline-block;">
                        <img src="{{ $brand->logo ? url('uploads/img/' . $brand->logo) : url('img/default.png') }}"
                            class="brand-img" alt="Brand Logo">
                    </div>
                </div>



                <div class="col-md-6 invoice-col row "
                    style="display: flex; align-items: center; flex-wrap: wrap; background-color: #f3fbfe; border:2px solid #17A7E4; border-radius: 5px; padding:auto; margin: 10px;">
                    <div class="col-md-6">
                        <table style=" border-collapse: collapse; font-size: 14px;">
                            <tbody>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="font-weight: bold; color: #094C89; padding: 4px;">Name:</td>
                                    <td style="padding: 4px;">{{$brand->name}}</td>
                                </tr>

                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="font-weight: bold; color: #094C89; padding: 4px;">Slug:</td>
                                    <td style="padding: 4px;">{{ $brand->slug }}</td>
                                </tr>

                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="font-weight: bold; color: #094C89; padding: 4px;">Visibility:</td>
                                    <td style="padding: 4px;">{{ ($brand->visibility) }}</td>
                                </tr>
                               


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12 row justify-content-center">
                    <div class="col-md-12 text-center">
                        <div class="" style="max-width: 100%; display: inline-block;">
                            <img src="{{ $brand->banner ? url('uploads/img/' . $brand->banner) : url('img/default.png') }}"
                                alt="Brand Banner"
                                style="max-width: 100%; height: auto; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; padding: 4px; background: #fff;">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label style="font-weight: bold; color: #094C89;">Description:</label>
                    <div class="scrollable-description">
                        {{ $brand->description }}
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>