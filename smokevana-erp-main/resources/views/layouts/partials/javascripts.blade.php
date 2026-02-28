<script type="text/javascript">
    base_path = "{{ url('/') }}";

    // Fix black/broken page when using browser Back button on live (BFCache restore)
    (function() {
        var isLocalhost = document.getElementById('__is_localhost');
        if (!isLocalhost) {
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        }
    })();
    //used for push notification
    APP = {};
    APP.PUSHER_APP_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
    APP.PUSHER_APP_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    APP.INVOICE_SCHEME_SEPARATOR = '{{ config('constants.invoice_scheme_separator') }}';
    //variable from app service provider
    APP.PUSHER_ENABLED = '{{ $__is_pusher_enabled }}';
    @auth
    @php
        $user = Auth::user();
    @endphp
    APP.USER_ID = "{{ $user->id }}";
    @else
        APP.USER_ID = '';
    @endauth
</script>

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js?v=$asset_v"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js?v=$asset_v"></script>
<![endif]-->

<script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>

@if (file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@else
    <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif
@php
    $business_date_format = session('business.date_format', config('constants.default_date_format'));
    $datepicker_date_format = str_replace('d', 'dd', $business_date_format);
    $datepicker_date_format = str_replace('m', 'mm', $datepicker_date_format);
    $datepicker_date_format = str_replace('Y', 'yyyy', $datepicker_date_format);

    $moment_date_format = str_replace('d', 'DD', $business_date_format);
    $moment_date_format = str_replace('m', 'MM', $moment_date_format);
    $moment_date_format = str_replace('Y', 'YYYY', $moment_date_format);

    $business_time_format = session('business.time_format');
    $moment_time_format = 'HH:mm';
    if ($business_time_format == 12) {
        $moment_time_format = 'hh:mm A';
    }

    $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];

    $default_datatable_page_entries = !empty($common_settings['default_datatable_page_entries'])
        ? $common_settings['default_datatable_page_entries']
        : 25;
@endphp

<script>
    Dropzone.autoDiscover = false;
    moment.tz.setDefault('{{ Session::get('business.time_zone') }}');
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        @if (config('app.debug') == false)
            $.fn.dataTable.ext.errMode = 'throw';
        @endif
    });

    var financial_year = {
        start: moment('{{ Session::get('financial_year.start') }}'),
        end: moment('{{ Session::get('financial_year.end') }}'),
    }
    @if (file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
        //Default setting for select2
        $.fn.select2.defaults.set("language", "{{ session()->get('user.language', config('app.locale')) }}");
    @endif

    var datepicker_date_format = "{{ $datepicker_date_format }}";
    var moment_date_format = "{{ $moment_date_format }}";
    var moment_time_format = "{{ $moment_time_format }}";

    var app_locale = "{{ session()->get('user.language', config('app.locale')) }}";

    var non_utf8_languages = [
        @foreach (config('constants.non_utf8_languages') as $const)
            "{{ $const }}",
        @endforeach
    ];

    var __default_datatable_page_entries = "{{ $default_datatable_page_entries }}";

    var __new_notification_count_interval = "{{ config('constants.new_notification_count_interval', 60) }}000";
</script>

@if (file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@else
    <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif

<script src="{{ asset('js/functions.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/common.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/app.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/help-tour.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/documents_and_note.js?v=' . $asset_v) }}"></script>

<!-- TODO -->
@if (file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script
        src="{{ asset('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@endif
@php
    $validation_lang_file = 'messages_' . session()->get('user.language', config('app.locale')) . '.js';
@endphp
@if (file_exists(public_path() . '/js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file))
    <script src="{{ asset('js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file . '?v=' . $asset_v) }}">
    </script>
@endif

@if (!empty($__system_settings['additional_js']))
    {!! $__system_settings['additional_js'] !!}
@endif
@yield('javascript')

@if (Module::has('Essentials'))
    @includeIf('essentials::layouts.partials.footer_part')
@endif

<script type="text/javascript">
    $(document).ready(function() {
        var locale = "{{ session()->get('user.language', config('app.locale')) }}";

        // Quick Actions bookmarks: render from localStorage into #quick_actions_bookmarks_list
        window.renderQuickActionsBookmarks = function() {
            var list = $('#quick_actions_bookmarks_list');
            var emptyHint = $('#quick_actions_bookmarks_empty');
            if (!list.length) return;
            var arr = [];
            try { arr = JSON.parse(localStorage.getItem('quick_actions_bookmarks') || '[]'); } catch (e) { arr = []; }
            list.empty();
            if (arr.length === 0) {
                emptyHint.show();
                return;
            }
            emptyHint.hide();
            arr.forEach(function(b) {
                var $li = $('<li>').addClass('tw-flex tw-items-center tw-gap-1 tw-rounded-lg');
                var $a = $('<a>').attr('href', b.url).addClass('quick_actions_panel__item tw-flex-1 tw-mr-0').text(b.title);
                var $btn = $('<button type="button">').attr('data-url', b.url).attr('title', 'Remove').addClass('unpin-bookmark tw-flex-shrink-0 tw-p-1 tw-text-gray-400 hover:tw-text-red-600 tw-rounded tw-bg-transparent tw-border-0 tw-cursor-pointer').text('×');
                $li.append($a).append($btn);
                list.append($li);
            });
        };

        // Dashboard search functionality (global)
        if ($('#dashboard_search').length > 0) {
            var searchTimeout;
            var searchResults = $('#dashboard_search_results');
            var searchInput = $('#dashboard_search');
            var _searchBookmarkLabel = {!! json_encode(__('home.pinned')) !!};

            // Move dropdown to body so it is never clipped by parent overflow
            searchResults.appendTo('body');

            function positionSearchDropdown() {
                if (!searchResults.is(':visible')) return;
                var searchBar = searchInput.closest('.tw-relative');
                var el = (searchBar.length ? searchBar[0] : searchInput[0]);
                if (!el) return;
                var r = el.getBoundingClientRect();
                var minWidth = 320;
                var padding = 16;
                var winW = window.innerWidth || document.documentElement.clientWidth;
                var dropdownWidth = Math.max(r.width, minWidth);
                var left = r.left;
                if (left + dropdownWidth > winW - padding) {
                    left = winW - dropdownWidth - padding;
                }
                if (left < padding) {
                    left = padding;
                    dropdownWidth = Math.min(dropdownWidth, winW - 2 * padding);
                }
                searchResults.css({
                    position: 'fixed',
                    zIndex: 999999,
                    top: (r.bottom + 4) + 'px',
                    left: left + 'px',
                    width: dropdownWidth + 'px',
                    maxWidth: (winW - 2 * padding) + 'px'
                });
            }

            function showSearchResults(html) {
                searchResults.html(html).show();
                positionSearchDropdown();
            }

            searchInput.on('input', function() {
                var query = $(this).val().trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    searchResults.hide().empty();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: '/home/search',
                        method: 'GET',
                        data: { q: query },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.results && response.results.length > 0) {
                                var escAttr = function(s) { if (s == null) return ''; return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); };
                                var html = '<div class="tw-py-1 dashboard-search-results-inner">';
                                response.results.forEach(function(item) {
                                    html += '<a href="' + escAttr(item.url) + '" data-title="' + escAttr(item.title) + '" data-url="' + escAttr(item.url) + '" class="dashboard-search-item tw-block tw-px-4 tw-py-3 tw-text-sm tw-text-gray-700 hover:tw-bg-primary-50 hover:tw-text-primary-700 tw-transition-colors tw-duration-150 tw-border-b tw-border-gray-100 last:tw-border-0">';
                                    html += '<div class="tw-font-semibold tw-text-gray-900">' + (item.title || '').replace(/</g,'&lt;') + '</div>';
                                    if (item.subtitle) {
                                        html += '<div class="dashboard-search-subtitle tw-text-xs tw-text-gray-500 tw-mt-1">' + (item.subtitle || '').replace(/</g,'&lt;') + '</div>';
                                    }
                                    html += '</a>';
                                });
                                html += '</div>';
                                showSearchResults(html);
                            } else {
                                showSearchResults('<div class="tw-p-4 tw-text-sm tw-text-gray-500 tw-text-center">No results found</div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Search error:', status, error);
                            showSearchResults('<div class="tw-p-3 tw-text-sm tw-text-red-500 tw-text-center">Error searching. Please try again.</div>');
                        }
                    });
                }, 300);
            });

            // Keep dropdown visible when focusing on search input
            searchInput.on('focus', function(e) {
                e.stopPropagation();
                if (searchResults.html().trim() !== '') {
                    searchResults.show();
                    positionSearchDropdown();
                }
            });

            // Prevent input click from bubbling
            searchInput.on('click', function(e) {
                e.stopPropagation();
            });

            // Prevent hiding when clicking inside results
            searchResults.on('click mousedown', function(e) {
                e.stopPropagation();
            });

            // Reposition on scroll and resize
            $(window).on('scroll resize', function() {
                positionSearchDropdown();
            });
            if ($('#scrollable-container').length) {
                $('#scrollable-container').on('scroll', function() {
                    positionSearchDropdown();
                });
            }

            // Hide search results when clicking outside - with delay to allow clicks inside
            var hideTimeout;
            $(document).on('click', function(e) {
                var target = $(e.target);
                var isInsideSearch = target.closest('#dashboard_search').length > 0;
                var isInsideResults = target.closest('#dashboard_search_results').length > 0;
                
                if (!isInsideSearch && !isInsideResults) {
                    clearTimeout(hideTimeout);
                    hideTimeout = setTimeout(function() {
                        searchResults.hide();
                    }, 100);
                } else {
                    clearTimeout(hideTimeout);
                }
            });

            // Handle Enter key to go to first result
            searchInput.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var firstResult = searchResults.find('a').first();
                    if (firstResult.length) {
                        window.location.href = firstResult.attr('href');
                    }
                }
            });
        }

        // Header star: show filled (black) when current page is in Quick Actions bookmarks
        window.updateHeaderBookmarkStar = function() {
            var $star = $('#header_bookmark_star');
            if (!$star.length) return;
            function norm(u) {
                if (!u || typeof u !== 'string') return '';
                try { var o = new URL(u, window.location.origin); return o.pathname + (o.search || ''); } catch (e) { return u; }
            }
            var cur = norm(window.location.href);
            var arr = [];
            try { arr = JSON.parse(localStorage.getItem('quick_actions_bookmarks') || '[]'); } catch (e) { arr = []; }
            var isBookmarked = arr.some(function(b) { return norm(b.url) === cur; });
            var $path = $star.find('svg path').eq(1);
            if ($path.length) {
                if (isBookmarked) { $path.attr('fill', '#111111').attr('stroke', 'none'); }
                else { $path.attr('fill', 'none').removeAttr('stroke'); }
            }
        };
        if (typeof window.updateHeaderBookmarkStar === 'function') window.updateHeaderBookmarkStar();

        // Star button: bookmark/unbookmark the current page only when clicking the star
        $(document).on('keydown', '#header_bookmark_star', function(e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); $(this).click(); }
        });
        $(document).on('click', '#header_bookmark_star', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $star = $(this);
            if (!$star.length) return;
            function norm(u) {
                if (!u || typeof u !== 'string') return '';
                try { var o = new URL(u, window.location.origin); return o.pathname + (o.search || ''); } catch (e) { return u; }
            }
            var cur = norm(window.location.href);
            var key = 'quick_actions_bookmarks';
            var arr = [];
            try { arr = JSON.parse(localStorage.getItem(key) || '[]'); } catch (err) { arr = []; }
            var isBookmarked = arr.some(function(b) { return norm(b.url) === cur; });
            if (isBookmarked) {
                arr = arr.filter(function(b) { return norm(b.url) !== cur; });
                localStorage.setItem(key, JSON.stringify(arr));
                if (typeof window.renderQuickActionsBookmarks === 'function') window.renderQuickActionsBookmarks();
                if (typeof window.updateHeaderBookmarkStar === 'function') window.updateHeaderBookmarkStar();
                if (typeof toastr !== 'undefined') toastr.success({!! json_encode(__('home.removed_from_quick_actions')) !!});
            } else {
                arr = arr.filter(function(b) { return norm(b.url) !== cur; });
                arr.push({ title: document.title || 'Page', url: window.location.href });
                localStorage.setItem(key, JSON.stringify(arr));
                if (typeof window.renderQuickActionsBookmarks === 'function') window.renderQuickActionsBookmarks();
                if (typeof window.updateHeaderBookmarkStar === 'function') window.updateHeaderBookmarkStar();
                if (typeof toastr !== 'undefined') toastr.success({!! json_encode(__('home.added_to_quick_actions')) !!});
            }
        });

        // Quick Actions (thunder) – left-side panel: open/close
        (function() {
            var btn = document.getElementById('quick_actions_btn');
            var panel = document.getElementById('quick_actions_panel');
            var backdrop = document.getElementById('quick_actions_backdrop');
            var closeBtn = document.getElementById('quick_actions_panel_close');
            if (!btn || !panel || !backdrop) return;

            // Ensure panel is closed on page load/refresh (never open by default)
            function ensureClosed() {
                backdrop.classList.remove('quick_actions_backdrop--open');
                panel.classList.remove('quick_actions_panel--open');
                backdrop.setAttribute('aria-hidden', 'true');
                panel.setAttribute('aria-hidden', 'true');
                backdrop.style.opacity = '0';
                backdrop.style.visibility = 'hidden';
                backdrop.style.pointerEvents = 'none';
                panel.style.transform = 'translateX(100%)';
            }
            ensureClosed();

            function open(e) {
                // Prevent event bubbling to avoid conflicts with sidebar menu clicks
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                backdrop.style.opacity = '';
                backdrop.style.visibility = '';
                backdrop.style.pointerEvents = '';
                panel.style.transform = '';
                backdrop.classList.add('quick_actions_backdrop--open');
                panel.classList.add('quick_actions_panel--open');
                backdrop.setAttribute('aria-hidden', 'false');
                panel.setAttribute('aria-hidden', 'false');
            }
            function close(e) {
                // Prevent event bubbling
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                backdrop.classList.remove('quick_actions_backdrop--open');
                panel.classList.remove('quick_actions_panel--open');
                backdrop.setAttribute('aria-hidden', 'true');
                panel.setAttribute('aria-hidden', 'true');
                backdrop.style.opacity = '0';
                backdrop.style.visibility = 'hidden';
                backdrop.style.pointerEvents = 'none';
                panel.style.transform = 'translateX(100%)';
            }

            // Only open when the button itself is clicked, not on event bubbling
            btn.addEventListener('click', function(e) {
                // Ensure we're clicking the button or its direct child, not something else
                // Also check that the click didn't originate from sidebar
                var clickedElement = e.target;
                var isFromSidebar = clickedElement.closest('.side-bar') !== null;
                
                if (!isFromSidebar && (e.target === btn || btn.contains(e.target))) {
                    open(e);
                } else {
                    // If click came from sidebar, stop propagation
                    e.stopPropagation();
                }
            });
            
            backdrop.addEventListener('click', close);
            if (closeBtn) closeBtn.addEventListener('click', close);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && panel.classList.contains('quick_actions_panel--open')) {
                    close(e);
                }
            });
        })();

        // Unpin from Quick Actions bookmarks
        $(document).on('click', '#quick_actions_bookmarks_list .unpin-bookmark', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var url = $(this).data('url');
            var arr = [];
            try { arr = JSON.parse(localStorage.getItem('quick_actions_bookmarks') || '[]'); } catch (err) { arr = []; }
            arr = arr.filter(function(b) { return b.url !== url; });
            localStorage.setItem('quick_actions_bookmarks', JSON.stringify(arr));
            if (typeof window.renderQuickActionsBookmarks === 'function') window.renderQuickActionsBookmarks();
            if (typeof window.updateHeaderBookmarkStar === 'function') window.updateHeaderBookmarkStar();
        });

        if (typeof window.renderQuickActionsBookmarks === 'function') window.renderQuickActionsBookmarks();

        var isRTL =
            @if (in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')))
                true;
            @else
                false;
            @endif

        $('#calendar').fullCalendar('option', {
            locale: locale,
            isRTL: isRTL
        });
        // side bar toggle  
        $(".drop_down").click(function(event) {
            event.preventDefault();
            event.stopPropagation(); // Prevent event from bubbling to quick actions
            var $chiled = $(this).next(".chiled");
            var svgElement = $(this).find(".svg");
            $(".chiled").not($chiled).slideUp();
            $chiled.slideToggle(function() {
                $(".svg").each(function() {
                    var $currentSvgElement = $(this);
                    if ($currentSvgElement.closest(".drop_down").next(".chiled").is(
                            ":visible")) {
                        // If the corresponding menu is visible, set the arrow pointing upwards
                        $currentSvgElement.html(
                            '<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 9l6 6l6 -6" />'
                        );
                    } else {
                        // Otherwise, set the arrow pointing downwards
                        $currentSvgElement.html(
                            '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" />'
                        );
                    }
                });
            });
        });

        // Prevent sidebar menu link clicks from triggering quick actions
        $(document).on('click', '.side-bar a, .side-bar button', function(e) {
            // Stop propagation to prevent quick actions from opening
            e.stopPropagation();
            // Allow normal navigation for links
            if ($(this).attr('href') && $(this).attr('href') !== '#' && !$(this).attr('href').startsWith('javascript:')) {
                // Normal link navigation - don't prevent default
                return true;
            }
        });

        $('.small-view-button').on('click', function() {
            $('.side-bar').addClass('small-view-side-active');
            $('.overlay').fadeIn('slow');
        });

        $('.overlay').on('click', function() {
            $('.overlay').fadeOut('slow');
            $('.side-bar').removeClass('small-view-side-active');
        });

        $(window).on('resize', function() {
            if ($(window).width() >= 992) {
                $('.overlay').fadeOut('slow');
                $('.side-bar').removeClass('small-view-side-active');
            }

            if($('.side-bar').hasClass('small-view-side-active')){
                $('.overlay').fadeIn('slow');
            }
        });

        $(document).on('click', function (e) {
            $('[data-toggle="popover"]').popover();

            $(document).on('click', function (e) {
                $('[data-toggle="popover"]').each(function () {
                    // Check if the clicked element is the popover button or inside the popover
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            });
            
        });

        $('.side-bar-collapse').click(function() {
            $('.side-bar').toggle('slow');
        });

        $('.dt-buttons.btn-group').find('a.btn').removeClass('btn-default');
        $('.dt-buttons.btn-group').find('a.btn').removeClass('btn');
        
        // $('.date_range').on('show.daterangepicker', function (ev, picker) {
        //     $(picker.container).insertAfter($(this));
        // });

        // Toggle Location Function
        $('#toggle-location-btn').on('click', function() {
            const $btn = $(this);
            const locations = JSON.parse($btn.attr('data-permitted-locations') || '[]');
            const currentId = parseInt($btn.attr('data-current-location-id')) || null;

            if (locations.length < 2) {
                toastr.warning('You need at least 2 locations to toggle between them.');
                return;
            }

            const location1 = parseInt(locations[0]);
            const location2 = parseInt(locations[1]);
            const nextLocationId = (currentId === location1) ? location2 : location1;

            $btn.prop('disabled', true);
            toastr.info('Switching location...');

            $.ajax({
                url: '/users/update-location',
                type: 'POST',
                data: {
                    location_permissions: [`location.${nextLocationId}`],
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.msg || 'Location switched successfully');
                        setTimeout(() => window.location.href = "/home", 1000);
                    } else {
                        toastr.error(response.msg || 'Failed to switch location');
                        $btn.prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.msg || 'Failed to switch location');
                    $btn.prop('disabled', false);
                }
            });
        });
   
    });
</script>

<script>
    (function ($) {
        var dropdownSelector = '.scroll-safe-dropdown';
        var menuClass = 'scroll-safe-dropdown__menu';
        var openClass = 'scroll-safe-dropdown__menu--open';
        var dropupClass = 'scroll-safe-dropdown__menu--dropup';
        var scrollableContainers = '.table-responsive, .dataTables_scrollBody';
        var repositionScheduled = false;

        function scheduleReposition() {
            if (repositionScheduled) {
                return;
            }

            repositionScheduled = true;
            window.requestAnimationFrame(function () {
                repositionScheduled = false;
                repositionOpenMenus();
            });
        }

        function repositionOpenMenus() {
            $('.' + openClass).each(function () {
                var $menu = $(this);
                var $toggle = $menu.data('scrollSafeToggle');

                if ($toggle && $toggle.length) {
                    positionMenu($toggle, $menu);
                }
            });
        }

        function positionMenu($toggle, $menu) {
            if (!$toggle || !$toggle.length || !$menu || !$menu.length) {
                return;
            }

            var offset = $toggle.offset();
            var height = $toggle.outerHeight();
            var minWidth = Math.max($toggle.outerWidth(), 180);
            var menuHeight = $menu.outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            var spaceBelow = viewportBottom - (offset.top + height);
            var spaceAbove = offset.top - viewportTop;

            var topPosition = offset.top + height;
            var dropup = false;

            if (spaceBelow < menuHeight && spaceAbove > menuHeight) {
                topPosition = offset.top - menuHeight;
                dropup = true;
            }

            $menu
                .css({
                    display: 'block',
                    position: 'absolute',
                    top: topPosition,
                    left: offset.left,
                    minWidth: minWidth
                })
                .toggleClass(dropupClass, dropup);
        }

        function closeDropdown($container) {
            if (!$container || !$container.length) {
                return;
            }

            var $menu = $container.data('scrollSafeMenu');
            var $toggle = $container.find('[data-toggle="dropdown"]').first();

            if ($toggle && $toggle.length) {
                $container.removeClass('open');
                $toggle.attr('aria-expanded', 'false');
            }

            if ($menu && $menu.length) {
                $menu
                    .removeClass(openClass + ' ' + dropupClass)
                    .removeData('scrollSafeToggle')
                    .removeData('scrollSafeContainer')
                    .css({
                        display: '',
                        position: '',
                        top: '',
                        left: '',
                        minWidth: ''
                    });

                $container.append($menu);
                $container.removeData('scrollSafeMenu');
            }
        }

        // Handle clicks on menu items - prevent Bootstrap from closing and ensure handlers work
        $(document).on('click', '.' + menuClass + ' a, .' + menuClass + ' button', function (e) {
            var $item = $(this);
            var $menu = $item.closest('.' + menuClass);
            
            if ($menu.length && $menu.hasClass(openClass) && $menu.parent().is('body')) {
                var $container = $menu.data('scrollSafeContainer');
                var href = $item.attr('href');
                var itemClasses = $item.attr('class') || '';
                
                // Mark that we're clicking on a menu item
                $menu.data('menuItemClicked', true);
                
                // Check if this is an action button (delete, activate, deactivate, view, edit, etc.)
                var isActionButton = itemClasses.match(/\b(delete|activate|deactivate|view|edit|remove|update|toggle|discontinue)-\w+\b/i) ||
                                    $item.data('href') || ($item.is('button') && !href);
                
                // For action buttons that use tbody-level delegation
                if (isActionButton && href) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Find table tbody
                    var $table = $container ? $container.closest('table') : null;
                    if (!$table || !$table.length) {
                        $table = $('table').filter(function() {
                            return $(this).DataTable || $(this).closest('.dataTables_wrapper').length;
                        }).first();
                    }
                    
                    if ($table && $table.length) {
                        var $tbody = $table.find('tbody').first() || 
                                    $table.closest('.dataTables_wrapper').find('.dataTables_scrollBody tbody').first();
                        
                        if ($tbody.length) {
                            var $clone = $item.clone(true);
                            $clone.css({
                                position: 'absolute',
                                left: '-9999px',
                                opacity: 0,
                                pointerEvents: 'auto'
                            });
                            
                            $tbody.append($clone);
                            setTimeout(function() {
                                $clone[0].click();
                                setTimeout(function() {
                                    $clone.remove();
                                    closeDropdown($container);
                                }, 600);
                            }, 20);
                            return;
                        }
                    }
                    closeDropdown($container);
                } 
                // For navigation links
                else if (href && href !== '#' && !href.startsWith('javascript:')) {
                    setTimeout(function() {
                        closeDropdown($container);
                    }, 100);
                } 
                // For other cases
                else {
                    setTimeout(function() {
                        closeDropdown($container);
                    }, 50);
                }
            }
        });

        $(document).on('shown.bs.dropdown', dropdownSelector, function () {
            var $container = $(this);
            var $toggle = $container.find('[data-toggle="dropdown"]').first();
            var $menu = $container.find('.dropdown-menu').first();

            if (!$menu.length || !$toggle.length) {
                return;
            }

            $container.data('scrollSafeMenu', $menu);

            $menu
                .addClass(menuClass + ' ' + openClass)
                .data('scrollSafeToggle', $toggle)
                .data('scrollSafeContainer', $container);

            $('body').append($menu);
            positionMenu($toggle, $menu);
        });

        // Prevent Bootstrap from closing when clicking on menu items
        $(document).on('hide.bs.dropdown', dropdownSelector, function (e) {
            var $menu = $(this).data('scrollSafeMenu');
            if ($menu && $menu.hasClass(openClass) && $menu.parent().is('body') && $menu.data('menuItemClicked')) {
                $menu.removeData('menuItemClicked');
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        $(document).on('hidden.bs.dropdown', dropdownSelector, function () {
            var $container = $(this);
            var $menu = $container.data('scrollSafeMenu');

            if (!$menu || !$menu.length) {
                return;
            }

            // Clean up if menu is still in body
            if ($menu.parent().is('body')) {
                $menu
                    .removeClass(openClass + ' ' + dropupClass)
                    .removeData('scrollSafeToggle')
                    .removeData('scrollSafeContainer')
                    .removeData('menuItemClicked')
                    .css({
                        display: '',
                        position: '',
                        top: '',
                        left: '',
                        minWidth: ''
                    });

                $container.append($menu);
                $container.removeData('scrollSafeMenu');
            }
        });

        $(window).on('scroll resize', scheduleReposition);
        $(document).on('scroll', scrollableContainers, scheduleReposition);
    })(jQuery);
</script>


