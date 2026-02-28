/**
 * Modal Debug and Fix Script
 */

(function($) {
    'use strict';
    
    // Add a global function to manually show a modal if Bootstrap fails
    window.forceShowModal = function(modalId) {
        const $modal = $(modalId);
        
        if ($modal.length === 0) {
            console.error('[Modal] Modal not found:', modalId);
            return false;
        }
        
        console.log('[Modal] Force showing modal:', modalId);
        
        // Remove any existing backdrops
        $('.modal-backdrop').remove();
        
        // Create backdrop
        const $backdrop = $('<div class="modal-backdrop fade in"></div>');
        $backdrop.css({
            'position': 'fixed',
            'top': '0',
            'right': '0',
            'bottom': '0',
            'left': '0',
            'z-index': '1040',
            'background-color': '#000',
            'opacity': '0.5'
        });
        $('body').append($backdrop);
        
        // Show modal
        $modal.css({
            'display': 'block',
            'z-index': '1050',
            'position': 'fixed'
        });
        
        $modal.addClass('in');
        $('body').addClass('modal-open');
        
        return true;
    };
    
    // Add a function to check modal state
    window.checkModalState = function(modalId) {
        const $modal = $(modalId);
        
        if ($modal.length === 0) {
            console.error('[Modal] Modal not found:', modalId);
            return;
        }
        
        console.log('[Modal] State for:', modalId);
        console.log('  Display:', $modal.css('display'));
        console.log('  Z-index:', $modal.css('z-index'));
        console.log('  Classes:', $modal.attr('class'));
        console.log('  Backdrop count:', $('.modal-backdrop').length);
        
        return {
            exists: true,
            display: $modal.css('display'),
            zIndex: $modal.css('z-index'),
            visible: $modal.css('display') === 'block',
            backdropCount: $('.modal-backdrop').length
        };
    };
    
    console.log('[Modal] Debug script loaded');
    
})(jQuery);
