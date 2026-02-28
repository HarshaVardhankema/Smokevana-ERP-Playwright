<div class="{{$class ?? ''}} tw-mb-4 tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw-translate-y-0.5 tw-ring-gray-200"
    @if (!empty($id)) id="{{ $id }}" @endif style="{{ $style ?? '' }}">
    @php
        $type= $type??null;
    @endphp
    <div class="tw-p-2 sm:tw-p-2">
        @if (empty($header))
            @if (!empty($title) || !empty($tool))
            @if ($type=='customer' || $type == 'supplier')
            {{-- <div class="box-header">
                {!! $icon ?? '' !!}
                <h3 class="box-title">{{ $title ?? '' }}</h3> 
                {!! $tool ?? '' !!}

                @if (isset($help_text))
                    <br />
                    <small>{!! $help_text !!}</small>
                @endif
            </div> --}}
            @else
            <div class="box-header">
                {!! $icon ?? '' !!}
                <h3 class="box-title">{!! $title_svg ?? '' !!} {{ $title ?? '' }}</h3>
                {!! $tool ?? '' !!}

                @if (isset($help_text))
                    <br />
                    <small>{!! $help_text !!}</small>
                @endif
            </div>
            @endif
            @endif
        @else
            <div class="box-header">
                {!! $header !!}
            </div>
        @endif
        <div class="tw-flow-root tw-border-gray-200">
            <div class="">
                <div class="tw-py-2 tw-align-middle sm:tw-px-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
