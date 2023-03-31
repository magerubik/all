define(["jquery", "jquery-ui-modules/tooltip",'domReady!', "toggleAdvanced", "matchMedia", 'mage/tabs'], function($) {
    if (typeof window.mrUtilities == 'undefined') {
        window.mrUtilities = {};
    }
    $.fn._buildToggle = function() {
        $("[data-mr-toggle]").each(function() {
            $(this).toggleAdvanced({
                selectorsToggleClass: "active",
                baseToggleClass: "expanded",
                toggleContainers: $(this).data('mr-toggle'),
            });
        });
    };
    $.fn._tooltip = function() {
        var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (iOS == false) {
            $('.show-tooltip').each(function() {
                $(this).tooltip({
                    position: {
                        my: "center top-80%",
                        at: "center top",
                        using: function(position, feedback) {
                            $(this).css(position);
                            $(this).addClass("mr-tooltip");
                        }
                    }
                });
            })
        }
    };
    mrUtilities.popup = function() {
        var $popupContainer, $ppContainerInner, $openedPopup, $backface;
        function _prepare() {
            $popupContainer = $('#mr-popup-area');
            if ($popupContainer.length == 0) {
                $popupContainer = $('<div class="mr-popup-area" id="mr-popup-area">');
                $popupContainer.appendTo('body');
                $ppContainerInner = $('<div class="mr-popup-area-inner" >').appendTo($popupContainer);
                $backface = $('<div class="mr-backface" data-role="close-mrpopup">').appendTo($ppContainerInner);
            }
        }
        function _buildPopup() {
            $('[data-mrpopup]').each(function() {
                var $popup = $(this);
                var $wrap = $('<div class="mr-popup">').appendTo($ppContainerInner);
                var wrapClass = $popup.attr('data-size') ? $popup.attr('data-size') : $popup.attr('id');
                $wrap.addClass('popup-' + wrapClass);
                var $inner = $('<div class="mr-popup-inner">').appendTo($wrap);
                var $content = $('<div class="mr-popup-content">').appendTo($inner);
                var $closeBtn = $('<button type="button" class="close-mrpopup" data-role="close-mrpopup"><i class="fas fa-times"></i></button>').appendTo($wrap);
                $popup.removeAttr('data-mrpopup');
                $popup.appendTo($content);
                if (!$popup.hasClass('no-nice-scroll')) {
                    $content.addClass('nice-scroll');
                }
                if ($popup.hasClass('hidden-overflow')) {
                    $content.css({
                        overflow: 'hidden'
                    });
                }
                if ($popup.data('parentclass')) {
                    $wrap.addClass($popup.data('parentclass'));
                }
                $popup.on('triggerPopup', function() {
                    mrUtilities.triggerPopup($popup.attr('id'));
                });
            });
        }
        this.triggerPopup = function(popupId, $trigger) {
            var $popup = $('#' + popupId);
            if ($popup.length) {
                if ($popup.parents('.mr-popup').length) {
                    $popup.parents('.mr-popup').first().addClass('opened').siblings().removeClass('opened');
                    $('body').css({
                        overflow: 'hidden'
                    });
                    $('.js-sticky-menu.active').css({
                        right: 'auto',
                        width: 'calc(100% - ' + mrUtilities.scrollBarWidth + 'px)'
                    });
                    $('body').addClass('mr-popup-opened');
                    setTimeout(function() {
                        $popup.trigger('mr_popup_opened');
                        if ($trigger) {
                            if (typeof $trigger.data('event') === 'string') {
                                $popup.trigger($trigger.data('event'));
                            }
                        }
                    }, 300);
                }
            }
        }
        function _bindEvents() {
            $('body').on('click', '[data-mrpopuptrigger]', function(e) {
                e.preventDefault();
                var $trigger = $(this);
                var popupId = $trigger.data('mrpopuptrigger');
                mrUtilities.triggerPopup(popupId, $trigger);
            });
            function closePopup() {
                $('.mr-popup.opened').removeClass('opened');
                $('body').removeClass('mr-popup-opened');
                $('body').css({
                    overflow: ''
                });
                $('.js-sticky-menu').css({
                    right: '',
                    width: ''
                });
            }
            function modifyButton($button, it) {
                $button.attr('id', 'btn-minicart-close-popup');
                if (!$button.data('popup_bind_event')) {
                    $button.data('popup_bind_event', true);
                    $button.on('click', closePopup);
                    $popupContainer.find('#top-cart-btn-checkout').on('click', closePopup);
                    if (it) clearInterval(it);
                }
            }
            if ($popupContainer.find('div.block.block-minicart').length) {
                var it = setInterval(function() {
                    var $button = $popupContainer.find('#btn-minicart-close');
                    if ($button.length) {
                        modifyButton($button, it);
                    }
                }, 2000);
                require(['Magento_Customer/js/customer-data'], function(customerData) {
                    var cartData = customerData.get('cart');
                    cartData.subscribe(function(updatedCart) {
                        var $button = $popupContainer.find('#btn-minicart-close');
                        if ($button.length) {
                            setTimeout(function() {
                                modifyButton($button, false);
                            }, 1000);
                        }
                    });
                });
            }
            $popupContainer.on('click', '[data-role=close-mrpopup]', closePopup);
        }
        _prepare();
        _buildPopup();
        _bindEvents();
        $('body').on('mrBuildPopup', _buildPopup);
    };
    $.fn._tooltip();
    $.fn._buildToggle();
    mrUtilities.init = function() {
        this.popup();
    };
    if (document.readyState == 'complete') {
        mrUtilities.init();
    } else {
        $(document).ready(function() {
            mrUtilities.init();
        });
    }
});