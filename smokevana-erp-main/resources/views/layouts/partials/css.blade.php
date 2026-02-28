<!-- Amazon Ember Fonts - Applied globally -->
<link rel="stylesheet" href="{{ asset('css/amazon-ember-fonts.css?v='.$asset_v) }}">

<!-- Font Awesome CDN - Fallback for missing font files -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="{{ asset('css/tailwind/app.css?v='.$asset_v) }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}
</style>
@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif

<!-- Amazon Admin Panel Theme -->
<link rel="stylesheet" href="{{ asset('css/amazon-theme.css?v='.$asset_v) }}">

<!-- Fix for Font Awesome icons displaying as boxes -->
<style>
    /* Ensure Font Awesome icons load correctly */
    .fa, .fas, .far, .fal, .fab, .fad, .fak,
    [class*="fa-"], [class^="fa-"],
    i[class*="fa-"], i[class^="fa-"] {
        font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 5 Free", "Font Awesome 5 Pro", "FontAwesome" !important;
        font-style: normal !important;
        font-variant: normal !important;
        text-rendering: auto !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
        display: inline-block !important;
    }
    
    /* Solid icons */
    .fa, .fas {
        font-weight: 900 !important;
    }
    
    /* Regular icons */
    .far {
        font-weight: 400 !important;
    }
    
    /* Light icons */
    .fal {
        font-weight: 300 !important;
    }
    
    /* Brands */
    .fab {
        font-family: "Font Awesome 6 Brands", "Font Awesome 5 Brands", "FontAwesome" !important;
        font-weight: 400 !important;
    }
    
    /* Ensure icons don't show as boxes - remove any background or border */
    .fa:before, .fas:before, .far:before, .fal:before, .fab:before,
    [class*="fa-"]:before, [class^="fa-"]:before {
        display: inline-block !important;
        background: none !important;
        border: none !important;
    }
    
    /* Fix for Glyphicon icons - convert to Font Awesome */
    .glyphicon {
        font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
        font-weight: 900 !important;
        font-style: normal !important;
    }
    
    /* Map common Glyphicon classes to Font Awesome equivalents */
    .glyphicon-edit:before {
        content: "\f044" !important; /* fa-edit */
    }
    
    .glyphicon-trash:before {
        content: "\f2ed" !important; /* fa-trash */
    }
    
    .glyphicon-eye-open:before,
    .glyphicon-eye:before {
        content: "\f06e" !important; /* fa-eye */
    }
    
    .glyphicon-remove:before,
    .glyphicon-remove-circle:before {
        content: "\f00d" !important; /* fa-times */
    }
    
    .glyphicon-plus:before {
        content: "\f067" !important; /* fa-plus */
    }
    
    .glyphicon-minus:before {
        content: "\f068" !important; /* fa-minus */
    }
    
    .glyphicon-ok:before,
    .glyphicon-check:before {
        content: "\f00c" !important; /* fa-check */
    }
    
    .glyphicon-chevron-left:before {
        content: "\f053" !important; /* fa-chevron-left */
    }
    
    .glyphicon-chevron-right:before {
        content: "\f054" !important; /* fa-chevron-right */
    }
    
    .glyphicon-chevron-up:before {
        content: "\f077" !important; /* fa-chevron-up */
    }
    
    .glyphicon-chevron-down:before {
        content: "\f078" !important; /* fa-chevron-down */
    }
</style>