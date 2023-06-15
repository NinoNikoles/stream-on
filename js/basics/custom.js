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
            self.initPictures();
            //self.accordion();
            //self.tabs();
            self.modal();
            self.initSlider();
            self.customPagination();
            self.initScrolltrigger();
            self.jstree();
            //self.videoTriggerFullscreen();
        },

        bindHandlers: function () {
            var self = this;

            window.addEventListener('resize', debounce);
            window.addEventListener('scroll', debounceScroll);
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
            self.initPictures();
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
			trigger.add('.genre-slider');
		},

        initPictures: function () {
            var self = this;
            if( self.$window.width() < 860) {
                self.src = "data-mobile-src";
            } else if ( self.$window.width() < 1280 ){
                self.src = "data-tablet-src";
            } else {
                self.src = "data-desktop-src";
            }

            forEach(document.querySelectorAll('.lazyOwl'), function(el){
                el.setAttribute('src', el.getAttribute(self.src));
            });

            forEach(document.querySelectorAll('.lazyPic'), function(el){
                el.setAttribute('src', el.getAttribute(self.src));
            });

            forEach(document.querySelectorAll('.lazyBG'), function(el){
                el.parentElement.style.backgroundImage  = "url('"+el.getAttribute(self.src)+"')";
            });
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

        modal: function() {
            var self = this;

            self.$body.append('<div class="modal" id="modal"><div class="modal-overlay"></div><div class="modal-wrap"></div><a href="#" class="modal-close"></a></div>');
            $modal = $('#modal');
            $modalWrap = $('#modal .modal-wrap');
            $modalOverlay = $('#modal .modal-overlay');
            $modalCloseBtn = $('#modal .modal-close');

            $('[data-modal]').on('click', function(e) {
                e.preventDefault();
                var self = this,
                    $self = $(self);
                    $src = $($self.attr('data-src'));

                $src.clone().appendTo('.modal-wrap');
                $('body').addClass('active-modal');
                $modal.addClass('active');
            });

            $modalOverlay.on('click', function() {
                $modalWrap.empty();
                $modal.removeClass('active');
                $('body').removeClass('active-modal');
            });

            $modalCloseBtn.on('click', function() {
                $modalWrap.empty();
                $modal.removeClass('active');
                $('body').removeClass('active-modal');
            });
        },

        /*accordion: function () {
            console.log('Accordion loaded');

            var self = this,
                $accordionTitle = $('.accordion .accordion-item .accordion-title');

            $('.accordion-content').each(function() {
                $id = $(this).attr('id');
                $(this).appendTo($('[data-accordion-item="'+$id+'"'));
            })

            $('.accordion .accordion-item.is-active .accordion-content').css('display', 'block');

            $accordionTitle.on('click', function (e) {
                e.preventDefault();

                var $self = $(this),
                    $accordionItem = $self.parent(),
                    $accordion = $accordionItem.parent(),
                    $accordionContent = $accordionItem.children('.accordion-content');

                //-- Prevent animation bugging
                if ( $self.hasClass('animating') ) return false;
                $self.addClass('animating');

                //-- Check for animation speed
                if ( !parseInt($accordion.attr('data-speed')) ) {
                    var openSpeed = 0;
                } else {
                    openSpeed = parseInt($accordion.attr('data-speed'));
                }

                //-- When accordion is closed
                if ( !$accordionItem.hasClass(self.activeClass) ) {
                    $accordionItem.addClass(self.activeClass);
                    $accordionContent.attr('aria-expanded', 'true');

                    //-- Get correct Content height
                    var contentBorder = parseInt($accordionContent.css('border-top-width')),
                        contentHeight = $accordionContent.outerHeight() - contentBorder*2,
                        contentPadding = (contentHeight - $accordionContent.height()) / 2;

                    //-- Open animation
                    $accordionContent.attr('style', 'display:block;height:0;padding-top:0;padding-bottom:0;');
                    $accordionContent.animate({
                        height: contentHeight,
                        paddingTop: contentPadding,
                        paddingBottom: contentPadding,
                    }, openSpeed).promise().done(function () {
                        //-- Triggers when animation is done
                        $self.removeClass('animating');
                        $accordionContent.attr('style', 'display:block;');
                    });

                //-- When accordion is open
                } else {
                    //-- Get correct Content height
                    contentBorder = parseInt($accordionContent.css('border-top-width'));
                    contentHeight = $accordionContent.outerHeight() - contentBorder*2;
                    contentPadding = (contentHeight - $accordionContent.height()) / 2;

                    $accordionContent.attr('aria-expanded', 'false');

                    //-- Closing animation
                    $accordionContent.attr('style', 'display:block;height:'+contentHeight+'px;padding-top:'+contentPadding+'px;padding-bottom:'+contentPadding+'px;');
                    $accordionContent.animate({
                        height: 0,
                        paddingTop: 0,
                        paddingBottom: 0,
                    }, openSpeed).promise().done(function () {
                        //-- Triggers when animation is done
                        $accordionItem.removeClass(self.activeClass);
                        $self.removeClass('animating');
                        $accordionContent.attr('style', 'display:none;');
                    });
                }
            })
        },

        tabs: function () {
            console.log('Tabs loaded');

            var self = this,
                $tabTitleLink = $('.tabs .tabs-title > a');

            $tabTitleLink.on('click', function (e) {
                e.preventDefault();

                var $self = $(this),
                    panelID = $self.attr('href'),
                    $tabTitle = $self.parent(),
                    $tab = $tabTitle.parent(),
                    tabID = $tab.attr('id');

                if ( !$tabTitle.hasClass(self.activeClass) ) {
                    //-- Tabs --
                    $tab.children('.tabs-title').removeClass(self.activeClass).children('a').attr('aria-selected', 'false');
                    $tabTitle.addClass(self.activeClass);
                    $self.attr('aria-selected', 'true');

                    //-- Content --
                    var $tabsContent = $('[data-tabs-content="' + tabID + '"]'),
                        $tabPanel = $tabsContent.find(panelID);
                    $tabsContent.find('.tabs-panel').removeClass(self.activeClass);
                    $tabPanel.addClass(self.activeClass);
                }
            })
        }*/

        initSlider: function () {
            sliderNumber = 1;

            $(".swiper").each(function () {
                var $el = $(this);
                const sliderClass = 'swiper-' + sliderNumber;
                const slider = '.swiper-' + sliderNumber;

                $el.addClass(sliderClass);
                var slidesPerViewMobile = 2;
                var slidesPerViewTablet = 5;
                var slidesPerViewDesktop = 10;
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
                    spaceBetween: 8,
                    allowTouchMove: false,
                    breakpoints: {
                        // when window width is >= 320px
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
                    var $self = $(this);
                    $self.attr('data-fancybox', sliderClass);
                })
                
                sliderNumber++;
            });
        },

        customPagination: function(){
			$sortField = $('#sortField'); // Select: Sortierung
			$sizeField = $('#sizeField'); // Select: Anzahl Pro seite
			$btnPrev = $('.pag-prev'); // Btn vorherige Seite
			$btnCurrent = $('.pag-current'); // Btn aktuelle Seite
			$btnNext = $('.pag-next'); // Btn nächste Seite
			$outputCount = $('#outputCount');
            var sortOptions = $sortField.find('option');
            var sizeOptions = $sizeField.find('option');

			const urlParams = new URLSearchParams(window.location.search);
			$currentRows = urlParams.get('rows'); // Anzahl Paramater
			$currentSort = urlParams.get('sort'); // Sortierung Parameter
			$currentPage = urlParams.get('currentPage'); // Aktuelle Seite Parameter

            sortOptions.each(function() {
                if($currentSort == $(this).val()) {
                    $(this).prop('selected', true);
                }
            })

            sizeOptions.each(function() {
                if($currentRows == parseInt($(this).val())) {
                    $(this).prop('selected', true);
                }
            })

			// Parameter und URL Weiterleitung
			function getValues () {
				$sortVal = $sortField.find(':selected').val();
				$sizeVal = $sizeField.find(':selected').val();
				$pageVal = $btnCurrentVal;

				$options = '?currentPage=' + $pageVal + '&rows=' + $sizeVal + '&sort=' + $sortVal;//
				var url = window.location.origin + window.location.pathname + $options;
				window.location.href = url;
			}

			// Datums Sortierung
			$sortField.on('change', function() {
				$btnCurrentVal = parseInt($btnCurrent.attr('data-site'));
				getValues();
			});

			// Seitengröße
			$sizeField.on('change', function() {
				$btnCurrentVal = parseInt($btnCurrent.attr('data-site'));
				getValues();
			});
		},

        jstree: function() {
            if ( $('#file-tree').length > 0 ) {
                $.ajax({
                    url: '/file-api', // Hier den Pfad zur API auf deinem Server einfügen
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                    // Die Antwort enthält die Daten für den jsTree
                        $('#file-tree').jstree({
                            "core": {
                                "animation" : 0,
                                "check_callback" : true,
                                "themes" : { "stripes" : true },
                                'data': response,
                                "multiple": false,
                            },
                            "checkbox": {
                                "three_state": false
                            },
                            "types": {
                                "video": {
                                    "icon": 'jstree-file'
                                }
                            },
                            "plugins": [
                                "contextmenu", "dnd", "search",
                                "state", "types", "wholerow"
                            ],
                        })

                        $('#file-tree').on('select_node.jstree', function(e, data) {
                            var node = data.instance.get_node(data.selected[0]);
                            if (node.text.endsWith('.mp4')) {
                                var path = $('#file-tree').jstree('get_path', data.node, '/');
                                $('#inputMoviePath').attr('value', '/media/'+path);
                                $('#inputMovieSubmit').css('display', 'inline-flex');
                            } else {
                                data.instance.deselect_node(data.selected[0]);
                                $('#inputMovieSubmit').css('display', 'none');
                            }                      
                        });
                    },
                    error: function(xhr, status, error) {
                        // Fehlerbehandlung, wenn die Anfrage fehlschlägt
                        console.error(error);
                    }
                });
            }
        },

        videoTriggerFullscreen: function() {
            $videoJS = $('div.video-js');
            $video = $('div.video-js video');
            var fullscreen = $videoJS.attr('data-set');
            console.log(fullscreen);

            if (fullscreen == 'fullscreen') {
                console.log('true');
                var id = $video.attr('id');
                var src = $video.attr('src');
                var myPlayer = videojs(id);

                myPlayer.src({type: "video/mp4", src: src});
                myPlayer.ready(function() {
                    myPlayer.isFullscreen(true);
                });
            }
        }
    }

    page.init();
});