{{-- Right-side Quick Actions drawer – Amazon Seller Central style --}}
@php
    $pos_settings = json_decode(session('business.pos_settings') ?? '{}', true) ?? [];
    $is_admin = auth()->user()->hasRole('Admin#' . session('business.id'));
@endphp

{{-- Backdrop --}}
<div id="quick_actions_backdrop" class="qa-backdrop" aria-hidden="true" style="opacity:0; visibility:hidden; pointer-events:none;"></div>

{{-- Panel --}}
<aside id="quick_actions_panel" class="qa-panel" role="dialog" aria-labelledby="quick_actions_title" aria-modal="true" aria-hidden="true" style="transform: translateX(100%);">
    {{-- Header --}}
    <div class="qa-panel__header">
        <div class="qa-panel__header-left">
            <svg class="qa-panel__header-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                <path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11z" />
            </svg>
            <h2 id="quick_actions_title" class="qa-panel__title">@lang('home.quick_actions')</h2>
        </div>
        <button type="button" id="quick_actions_panel_close" class="qa-panel__close" title="@lang('messages.close')" aria-label="@lang('messages.close')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6l-12 12"/>
                <path d="M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Pinned / Bookmarks --}}
    <div class="qa-panel__body">
        <div id="quick_actions_bookmarks_container" class="qa-section">
            <h3 class="qa-section__label">@lang('home.pinned')</h3>
            <ul id="quick_actions_bookmarks_list" class="qa-bookmarks-list"></ul>
            <p id="quick_actions_bookmarks_empty" class="qa-section__hint" style="display: none;">@lang('home.pin_from_search')</p>
        </div>

        <div class="qa-divider"></div>

        {{-- Action items --}}
        @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
        <a href="{{ url('contacts?type=customer&open_create=1') }}" class="qa-item">
            <span class="qa-item__icon qa-item__icon--blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </span>
            <span class="qa-item__text">@lang('contact.add_contact')</span>
            <svg class="qa-item__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endif

        @can('product.create')
        <a href="{{ action([\App\Http\Controllers\ProductController::class, 'create']) }}" class="qa-item">
            <span class="qa-item__icon qa-item__icon--emerald">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8 4-8-4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </span>
            <span class="qa-item__text">@lang('lang_v1.add_new_product')</span>
            <svg class="qa-item__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endcan

        @if(!empty($pos_settings['enable_sales_order']) && ($is_admin || auth()->user()->hasAnyPermission(['so.view_own', 'so.view_all', 'so.create'])))
        <a href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}?sale_type=sales_order" class="qa-item">
            <span class="qa-item__icon qa-item__icon--amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </span>
            <span class="qa-item__text">@lang('lang_v1.add_sales_order')</span>
            <svg class="qa-item__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endif

        @if(in_array('add_sale', (array)(session('business.enabled_modules') ?? [])) && auth()->user()->can('direct_sell.access'))
        <a href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}" class="qa-item">
            <span class="qa-item__icon qa-item__icon--violet">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <span class="qa-item__text">@lang('lang_v1.add_sell')</span>
            <svg class="qa-item__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endif

        @can('business_settings.access')
        <a href="{{ action([\App\Http\Controllers\CustomDiscountController::class, 'index']) }}" class="qa-item">
            <span class="qa-item__icon qa-item__icon--rose">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7a1.994 1.994 0 01-.586-1.414V7a4 4 0 014-4z"/></svg>
            </span>
            <span class="qa-item__text">@lang('lang_v1.add_discount')</span>
            <svg class="qa-item__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endcan

        @can('brand.create')
        <a href="{{ url('brands?open_create=1') }}" class="qa-item">
            <span class="qa-item__icon qa-item__icon--cyan">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            </span>
            <span class="qa-item__text">@lang('brand.add_brand')</span>
            <svg class="qa-item__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endcan

        @can('category.create')
        <a href="{{ url('taxonomies?type=product&open_create=1') }}" class="qa-item">
            <span class="qa-item__icon qa-item__icon--indigo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </span>
            <span class="qa-item__text">@lang('category.add_category')</span>
            <svg class="qa-item__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endcan
    </div>
</aside>

<style>
/* ===== Backdrop ===== */
.qa-backdrop {
    position: fixed; left: 0; top: 0; width: 100%; height: 100%;
    background: rgba(15, 17, 17, 0.55);
    z-index: 99998;
    opacity: 0; visibility: hidden; pointer-events: none;
    transition: opacity 0.25s ease, visibility 0.25s ease;
    backdrop-filter: blur(2px);
}
.qa-backdrop.quick_actions_backdrop--open {
    opacity: 1; visibility: visible; pointer-events: auto;
}

/* ===== Panel ===== */
.qa-panel {
    position: fixed; right: 0; top: 0;
    width: 340px; max-width: 92vw; height: 100%;
    background: #EAEDED;
    box-shadow: -6px 0 30px rgba(15, 17, 17, 0.25);
    z-index: 99999;
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex; flex-direction: column;
    border-left: 1px solid #D5D9D9;
}
.qa-panel.quick_actions_panel--open {
    transform: translateX(0);
}

/* ===== Header ===== */
.qa-panel__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    height: 56px;
    background: linear-gradient(180deg, #232F3E 0%, #37475A 100%);
    border-bottom: 3px solid #FF9900;
    flex-shrink: 0;
}
.qa-panel__header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.qa-panel__header-icon {
    width: 20px; height: 20px;
    color: #FF9900;
    flex-shrink: 0;
}
.qa-panel__title {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #FFFFFF;
    letter-spacing: 0.2px;
}
.qa-panel__close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px; height: 32px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 6px;
    color: rgba(255,255,255,0.8);
    cursor: pointer;
    transition: all 0.15s;
}
.qa-panel__close svg {
    width: 16px; height: 16px;
}
.qa-panel__close:hover {
    background: rgba(255,255,255,0.2);
    color: #FFFFFF;
}

/* ===== Body ===== */
.qa-panel__body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

/* ===== Section (Pinned) ===== */
.qa-section {
    background: #FFFFFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    padding: 14px 16px;
    margin-bottom: 12px;
}
.qa-section__label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #565959;
    margin: 0 0 8px;
}
.qa-section__hint {
    font-size: 12px;
    color: #565959;
    font-style: italic;
    margin: 4px 0 0;
}
.qa-bookmarks-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.qa-bookmarks-list li {
    margin-bottom: 2px;
}

/* ===== Divider ===== */
.qa-divider {
    height: 1px;
    background: #D5D9D9;
    margin: 4px 0 12px;
}

/* ===== Action Items ===== */
.qa-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    margin-bottom: 4px;
    background: #FFFFFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #0F1111;
    text-decoration: none;
    transition: all 0.15s;
    cursor: pointer;
}
.qa-item:hover {
    border-color: #FF9900;
    background: #FFFAF0;
    color: #0F1111;
    text-decoration: none;
    box-shadow: 0 1px 4px rgba(255, 153, 0, 0.15);
}
.qa-item:active {
    background: #FFF3E0;
}
.qa-item__text {
    flex: 1;
    line-height: 1.3;
}
.qa-item__arrow {
    width: 16px; height: 16px;
    color: #D5D9D9;
    flex-shrink: 0;
    transition: color 0.15s, transform 0.15s;
}
.qa-item:hover .qa-item__arrow {
    color: #FF9900;
    transform: translateX(2px);
}

/* ===== Icon Badges ===== */
.qa-item__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px; height: 36px;
    border-radius: 8px;
    flex-shrink: 0;
}
.qa-item__icon svg {
    width: 18px; height: 18px;
}
.qa-item__icon--blue    { background: #E7F4FF; color: #0066C0; }
.qa-item__icon--emerald { background: #E3F5E1; color: #067D62; }
.qa-item__icon--amber   { background: #FEF3CD; color: #B7791F; }
.qa-item__icon--violet  { background: #EDE9FE; color: #6D28D9; }
.qa-item__icon--rose    { background: #FFEBE5; color: #B12704; }
.qa-item__icon--cyan    { background: #E0F7FA; color: #007185; }
.qa-item__icon--indigo  { background: #E8EAF6; color: #3949AB; }

/* ===== Scrollbar ===== */
.qa-panel__body::-webkit-scrollbar { width: 6px; }
.qa-panel__body::-webkit-scrollbar-track { background: transparent; }
.qa-panel__body::-webkit-scrollbar-thumb { background: #D5D9D9; border-radius: 3px; }
.qa-panel__body::-webkit-scrollbar-thumb:hover { background: #B0B3B3; }
</style>
