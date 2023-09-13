function forEach(ctn, callback) {
    return Array.prototype.forEach.call(ctn, callback);
}

var resizeTimer,
debounce = function(e) {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        page.resizeHook();
    }, 50);
};

var resizeTimerScroll,
debounceScroll = function(e){
	clearTimeout(resizeTimerScroll);
	resizeTimerScroll = setTimeout(function() {
		checkPosition();
	}, 10);
};

var checkPosition = function(e) {
	var top = $(window).scrollTop();

	if((top > 80) || (page.mobile && top > 50)) {
		$h.addClass('fluid');
		$('#masterWrap').addClass('fluid');		
	} else {
		$h.removeClass('fluid');
		$('#masterWrap').removeClass('fluid');
	}
};

$(document).ready(function() {
    page = {
        $window: $(window),
        $html: $('html'),
        $body: $('body'),
        $header: $('header'),
        $masterWrap: $('#masterWrap'),
        $menuButton: $('.menu-button'),

        mobileSize: 1080,
        tabletSize: 1600,
        activeClass: 'is-active',
        activeMenu: 'active-menu',
        activeButton:'active-button',
        src: "data-desktop-src",
        videosrc: "desktop-src",

        init: function () {
            var self = this;

            // self.initApp();
            self.bindHandlers();
            self.themeChange();
            self.desktopViewportCheck();
            self.fixedHeader();
            self.navMobile();
            self.tabs();
            self.selectTabs();
            self.initSlider();
            self.initScrolltrigger();
            self.userMenuBtn();
            self.initPlayer();
            self.fancyBox();
            self.updateOverlay();
            self.pageReady();
            self.passwordChange();
        },

        bindHandlers: function () {
            var self = this;

            window.addEventListener('resize', debounce);
            window.addEventListener('scroll', debounceScroll);
        },

        pageReady: function() {
            var self = this;
            $('#loader').addClass('hidden');
            $("body").removeClass('loading');
            
            self.fancyLoad();
        },

        desktopViewportCheck: function () {
            var self = this;
			extraMenuCtrl.init(true);

            if ( window.innerWidth >= self.mobileSize ) {
                self.mobile = false;
				self.$masterWrap.removeClass('mobile-state');
            } else {
                self.mobile = true;
				self.$masterWrap.addClass('mobile-state');
            }
        },

        lockScrollPosition: function () {
            var self = this,
                scrollPosition = [
                    document.documentElement.scrollLeft,
                    document.documentElement.scrollTop
                ];
            self.$html.data('scroll-position', scrollPosition);
            self.$html.css('overflow-y', 'hidden');
            window.scroll(scrollPosition[0], scrollPosition[1]);
        },

        unlockScrollPosition: function () {
            var self = this,
                scrollPosition = self.$html.data('scroll-position');
            if ( scrollPosition === undefined ) return false;
            self.$html.css('overflow-y', 'auto');
            window.scroll(scrollPosition[0], scrollPosition[1]);
        },

        resizeHook: function () {
            var self = this;

            self.desktopViewportCheck();
            self.fixedHeader();
            self.navMobile();
            self.fancyLoad();
            self.repositionPlayerButtons();
        },

        initScrolltrigger: function(){
			const trigger = new ScrollTrigger.default({
				trigger: {
					once: true
				},
				offset: {
					element: {
						x: 0,
						y: function(trigger, rect, direction) {
							return 0.5;
						}
					},
				}
			});
			trigger.add('[data-trigger]');
			trigger.add('.card-slider');
			trigger.add('.genre-slider');
            trigger.add('#load-count');
            trigger.add('.currentWatch-slider');
		},

        fixedHeader: function() {
            var self = this;

            headerHeight = self.$header.height();

            if( self.$header.hasClass('fixed-header') ) {
                self.$body.css('padding-top', headerHeight);
            } else {
                self.$body.css('padding-top', 0);
            }
        },

        navMobile: function () {
            var self = this,
                $nav = $('#navMain');

            headerHeight = self.$header.height();
            $nav.css({
                'top': headerHeight,
                'height': 'calc(100vh - ' + headerHeight + 'px)'
            });
        },

        fancyLoad: function() {
            var self = this;
            self.src = "data-img";

            forEach(document.querySelectorAll('img[data-img]'), function(el){
                el.setAttribute('src', el.getAttribute(self.src));
                $(el).removeAttr('data-img');
            });
        },

        themeChange: function () {
            var self = this,
                pageTheme = 'pageTheme',
                $themeSwitch = $('#theme-switch');

            $themeSwitch.on('click', function(e) {
                e.preventDefault();

                function getCookie(name) {
                    const value = `; ${document.cookie}`;
                    const parts = value.split(`; ${name}=`);
                    if (parts.length === 2) return parts.pop().split(';').shift();
                }

                expireDate = new Date(Math.floor(new Date().getTime() + (86400000 * 365)));

                if( self.$html.attr('data-theme') === 'light' ) {
                    document.cookie = pageTheme + "=dark; path=/; expires=" + expireDate;
                    self.$html.attr('data-theme', 'dark');
                } else {
                    document.cookie = pageTheme + "=light; path=/; expires=" + expireDate;
                    self.$html.attr('data-theme', 'light');
                }
            })
        },

        tabs: function () {
            var self = this,
                $tabTitleLink = $('.tabs .tabs-title > a');

            $tabTitleLink.on('click', function (e) {
                e.preventDefault();

                var $this = $(this),
                    panelID = $this.attr('href'),
                    $tabTitle = $this.parent(),
                    $tab = $tabTitle.parent(),
                    tabID = $tab.attr('id');

                if ( !$tabTitle.hasClass(self.activeClass) ) {
                    //-- Tabs --
                    $tab.children('.tabs-title').removeClass(self.activeClass).children('a').attr('aria-selected', 'false');
                    $tabTitle.addClass(self.activeClass);
                    $this.attr('aria-selected', 'true');

                    //-- Content --
                    var $tabsContent = $('[data-tabs-content="' + tabID + '"]'),
                        $tabPanel = $tabsContent.find(panelID);
                    $tabsContent.find('.tabs-panel').removeClass(self.activeClass);
                    $tabPanel.addClass(self.activeClass);
                }
            })
        },

        selectTabs: function () {
            var self = this;

            $(document).on('change', '.tab-select', function (e) {
                e.preventDefault();

                var $this = $(this),
                    valAtr = $this.val();
                    tabsClass = $this.attr('id');
                    $currTab = $('.'+tabsClass+'[data-select-tab="'+valAtr+'"]');
                
                $('body select.'+tabsClass).each(function(index, e) {
                    $(e).val(valAtr);                
                });

                $('body select.'+tabsClass).val(valAtr);
                $('body select.'+tabsClass+' option[value="'+valAtr+'"]').prop('selected', true);
                
                $('.'+tabsClass+'.is-active').toggleClass('is-active');
                $currTab.toggleClass('is-active');
            })
        },

        fancyBox: function() {
            Fancybox.bind('[data-fancybox]', {
                dragToClose: false,
            });
        },

        initSlider: function () {
            sliderNumber = 1;

            $(".swiper").each(function () {
                var $el = $(this);
                const sliderClass = 'swiper-' + sliderNumber;
                const slider = '.swiper-' + sliderNumber;

                $el.addClass(sliderClass);
                var slidesPerViewMobile = 2;
                var slidesPerViewSmallTablet = 3;
                var slidesPerViewTablet = 4;
                var slidesPerViewDesktop = 6;
                var smallTabletBP = 720;
                var tabletBP = 1080;
                var desktopBP = 1400;

                function swiperLoopCheck(swiper) {
                    bp = window.innerWidth;

                    if ( bp >= desktopBP ) {
                        if ( $(slider + ' .swiper-slide').length > slidesPerViewDesktop ) {
                            swiper.loop = true;
                        } else {
                            swiper.loop = false;
                        }
                    } else if ( bp >= tabletBP ) {
                        if ( $(slider + ' .swiper-slide').length > slidesPerViewTablet ) {
                            swiper.loop = true;
                        } else {
                            swiper.loop = false;
                        }
                    } else if ( bp >= smallTabletBP ) {
                        if ( $(slider + ' .swiper-slide').length > slidesPerViewSmallTablet ) {
                            swiper.loop = true;
                        } else {
                            swiper.loop = false;
                        }
                    } else {
                        if ( $(slider + ' .swiper-slide').length > slidesPerViewMobile ) {
                            swiper.loop = true;
                        } else {
                            swiper.loop = false;
                        }
                    }

                    swiper.update();
                }


                const swiper = new Swiper(slider, {
                    // Optional parameters
                    loop: true,
                    //effect: effect,
                    slidesPerView: slidesPerViewMobile,//itemsMobile,
                    spaceBetween: 16,
                    allowTouchMove: true,
                    breakpoints: {
                        // when window width is >= 320px
                        720: {
                            slidesPerView: slidesPerViewSmallTablet//itemsTablet,
                        },
                        1080: {
                            slidesPerView: slidesPerViewTablet//itemsTablet,
                        },
                        1400: {
                            slidesPerView: slidesPerViewDesktop//itemsDesktop,
                        }
                    },
                    
                    // Navigation arrows
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });

                swiper.on('resize', function() {
                    var self = this;
                    swiperLoopCheck(self);
                });

                $el.find('[data-fancybox="gallery"]').each(function() {
                    var $this = $(this);
                    $this.attr('data-fancybox', sliderClass);
                })
                
                sliderNumber++;
            });
        },

        initPlayer: function() {
            var self = this;

            if ( $('#player').length > 0 ) {
                var player = videojs('player');
                video = $('video')[0];

                // Warten Sie auf das "loadedmetadata"-Ereignis, um sicherzustellen, dass das Video geladen ist
                video.addEventListener("loadedmetadata", function() {
                    $('#player-back-btn').appendTo(".video-js");
                    $('#player-session-btn').appendTo(".video-js");
                    $('#chat-open').appendTo(".video-js");
                    $('#player-sek-forward').appendTo(".video-js .vjs-control-bar");
                    $('#player-sek-back').appendTo(".video-js .vjs-control-bar");
                    
                    if ( $('#next-episode-btn').length > 0 ) {
                        $('#next-episode-btn').appendTo(".video-js");
                    }
                    if ( $('#show-container').length > 0 ) {
                        $('#show-container').appendTo(".video-js");
                    }
                    if ( $('#show-eps-btn').length > 0 ) {
                        $('#show-eps-btn').appendTo(".video-js .vjs-control-bar");
                    }
                    
                });

                player.on('play', function() {
                    self.repositionPlayerButtons();
                });                

                $nextEpisodeBtn = $("#next-episode-btn");

                if ( $nextEpisodeBtn ){
                    video.addEventListener("timeupdate", function() {
                        const currentTime = video.currentTime;
                        const duration = video.duration;
                        const last20Seconds = duration - 20;

                        if (currentTime >= last20Seconds) {
                            $nextEpisodeBtn.addClass("visible");
                        }

                        if (currentTime <= last20Seconds && $nextEpisodeBtn.hasClass("visible") ) {
                            $nextEpisodeBtn.removeClass("visible");
                        }
                    });
                }

                showContainer = '#show-container';

                $(showContainer+' .menu li>a').on('click', function(e) {
                    e.preventDefault();

                    $(showContainer).addClass('active-submenu');
                    listID = $(this).attr('data-id');

                    $(showContainer+' ul.sub-menu#'+listID).addClass('active');
                });

                $(showContainer+' .sub-menu .back').on('click', function(e) {
                    e.preventDefault();

                    $(showContainer).removeClass('active-submenu');

                    $(showContainer+' ul.sub-menu').removeClass('active');
                });

                $('a#show-eps-btn').on('click', function(e) {
                    e.preventDefault();

                    $(showContainer).toggleClass('visible');
                });

                $('#player-sek-forward').on('click', function(e) {
                    e.preventDefault();

                    player.currentTime(player.currentTime() + 10);
                });

                $('#player-sek-back').on('click', function(e) {
                    e.preventDefault();

                    player.currentTime(player.currentTime() - 10);
                });

                $('#chat-open').on('click', function(e) {
                    e.preventDefault();
                    $('#chat').toggleClass('hidden');
                });

                $('#chat-close').on('click', function() {
                    $('#chat').toggleClass('hidden');
                });
            }
        },

        repositionPlayerButtons: function() {
            $fullscreen = $('.vjs-fullscreen-control');
            $currTime = $('.vjs-current-time');
            $divider = $('.vjs-time-divider');
            $duration = $('.vjs-duration');

            if ( $('#show-eps-btn').length > 0 ) {
                $epsBtnWidth = $('#show-eps-btn').outerWidth();
                $('#show-eps-btn').css('right', $fullscreen.outerWidth());
            } else {
                $epsBtnWidth = 0;
            }

            $duration.css('right', ( $fullscreen.outerWidth() + $epsBtnWidth ));
            $divider.css('right', ( $fullscreen.outerWidth() + $epsBtnWidth + $duration.outerWidth() ));
            $currTime.css('right', ( $fullscreen.outerWidth() + $epsBtnWidth + $duration.outerWidth() + $divider.outerWidth() ));
        },

        userMenuBtn: function() {
            $menuBtn = $('#user-menu-btn');

            $menuBtn.on('click', function(e) {
                var $this = $(this);

                if ( $this.hasClass('active') && ! $(e.target).is('.user-menu') ) {
                    $this.removeClass('active');
                } else {
                    $this.addClass('active');
                }
            });

            $menuBtn.on('blur', function() {
                var $this = $(this);

                if ( !$this.is(':hover') && $this.hasClass('active') ) {
                    $this.removeClass('active');
                }
            });
        },     

        updateOverlay: function () {
            function loader() {
                $('body').addClass('loading');
                $('#loader').removeClass('hidden');
                $('#loader span').addClass('visible');
            }
            
            $(document).on('click', '#add-movie', loader);
            $(document).on('click', '#delete-movie', loader);

            $(document).on('click', '#add-show', loader);
            $(document).on('click', '#update-show', loader);
            $(document).on('click', '#delete-show', loader);

            $(document).on('click', '#addHighlight', loader);            
        },

        passwordChange: function() {
            var password = false;
            var passwordCheck = false;
            var inputCheckID = false;

            $('input[name="password"]').on('input', function() {
                $this = $(this);
                inputCheckID = '#'+$this.attr('id')+'-check';
                password = $this.val();

                if ( password.length > 0) {
                    $(inputCheckID).removeAttr('disabled');
                } else {
                    $(inputCheckID).attr('disabled', 'disabled');
                }

                if ( passwordCheck === password ) {
                    $('.change-user-pw').removeClass('disabled').removeAttr('disabled');
                } else {
                    $('.change-user-pw').addClass('disabled').attr('disabled', 'disabled');
                }
            });

            $('input[name="password-check"]').on('input', function() {
                $this = $(this);

                if ( ('#'+$this.attr('id')) === inputCheckID ) {
                    passwordCheck = $this.val();
                    
                    if ( passwordCheck === password ) {
                        $('.change-user-pw').removeClass('disabled').removeAttr('disabled');
                    } else {
                        $('.change-user-pw').addClass('disabled').attr('disabled', 'disabled');
                    }
                }                
            });
        }
    }

    page.init();
});
