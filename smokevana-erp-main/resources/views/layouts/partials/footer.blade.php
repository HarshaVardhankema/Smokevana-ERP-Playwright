{{-- Amazon-style footer: same position (bottom of main content), visible, dynamic year --}}
<footer class="amazon-app-footer no-print" role="contentinfo">
    <div class="amazon-app-footer__stripe"></div>
    <div class="amazon-app-footer__bar">
        <div class="amazon-app-footer__content">
            <span class="amazon-app-footer__brand">Smokevana (ERP Suit)</span>
            <span class="amazon-app-footer__version">- V{{ config('author.app_version', '1.0') }}</span>
            <span class="amazon-app-footer__sep">·</span>
            <span class="amazon-app-footer__copy">Copyright © {{ date('Y') }} All rights reserved.</span>
        </div>
    </div>
</footer>
<style>
    .amazon-app-footer {
        width: 100%;
        flex-shrink: 0;
        margin-top: 0;
    }
    .amazon-app-footer__stripe { height: 4px; background: #ff9900; width: 100%; border-radius: 2px 2px 0 0; }
    .amazon-app-footer__bar {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        padding: 2px 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    .amazon-app-footer__content {
        color: rgba(255,255,255,0.95);
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
    }
    .amazon-app-footer__brand { color: #fff; font-weight: 600; }
    .amazon-app-footer__sep { opacity: 0.75; user-select: none; }
    .amazon-app-footer__version { font-family: ui-monospace, monospace; font-size: 12px; opacity: 0.95; }
    .amazon-app-footer__copy { opacity: 0.95; }
    @media (max-width: 640px) {
        .amazon-app-footer__content { font-size: 12px; gap: 4px; }
        .amazon-app-footer__bar { padding: 10px 14px; }
    }
</style>
