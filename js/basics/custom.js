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
            self.tabs();
            self.selectTabs();
            //self.modal();
            self.initSlider();
            self.customPagination();
            self.initScrolltrigger();
            self.jstree();
            self.jstreeEpisode();
            self.userMenuBtn();
            //self.infinitLoad();
            self.movieTimeSafe();
            self.initPlayer();
            self.myList();
            self.highlight();
            self.fancyBox();
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
			trigger.add('.card-slider');
			trigger.add('.genre-slider');
            trigger.add('#load-count');
            trigger.add('.currentWatch-slider');
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

        /*modal: function() {
            var self = this;

            if ( !($('#modal').length > 0) ) {
                self.$body.append('<div class="modal" id="modal"><div class="modal-overlay"></div><div class="modal-wrap large"><div class="modal-inner-wrap"></div><a href="#" class="modal-close"></a></div></div>');
            }

            $modal = $('#modal');
            $modalWrap = $('#modal .modal-wrap');
            $modalInnerWrap = $('#modal .modal-inner-wrap');
            $modalOverlay = $('#modal .modal-overlay');
            $modalCloseBtn = $('#modal .modal-close');

            $(document).on('click', 'a[data-modal]', function(e) {
                e.preventDefault();
                var self = this,
                    $this = $(self);
                    $src = $($this.attr('data-src'));
                $modalInnerWrap.empty();
                $src.clone().appendTo('.modal-inner-wrap');
                $('body').addClass('active-modal');
                $modal.addClass('active');
            });

            $modalOverlay.on('click', function() {
                $modal.removeClass('active');
                $('body').removeClass('active-modal');
            });

            $modalCloseBtn.on('click', function(e) {
                e.preventDefault();
                $modal.removeClass('active');
                $('body').removeClass('active-modal');
            });
        },*/

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

                var $this = $(this),
                    $accordionItem = $this.parent(),
                    $accordion = $accordionItem.parent(),
                    $accordionContent = $accordionItem.children('.accordion-content');

                //-- Prevent animation bugging
                if ( $this.hasClass('animating') ) return false;
                $this.addClass('animating');

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
                        $this.removeClass('animating');
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
                        $this.removeClass('animating');
                        $accordionContent.attr('style', 'display:none;');
                    });
                }
            })
        },*/

        tabs: function () {
            console.log('Tabs loaded');

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
            console.log('Tabs loaded');

            var self = this;

            $(document).on('change', '.tab-select', function (e) {
                e.preventDefault();
                console.log('change');
                var $this = $(this),
                    valAtr = $this.val();
                    tabsClass = $this.attr('id');
                    $currTab = $('.'+tabsClass+'[data-select-tab="'+valAtr+'"]');
                
                $('body select.'+tabsClass).each(function(index, e) {
                    $(e).val(valAtr);
                    console.log($(e));                    
                });

                $('body select.'+tabsClass).val(valAtr);
                $('body select.'+tabsClass+' option[value="'+valAtr+'"]').prop('selected', true);
                
                $('.'+tabsClass+'.is-active').toggleClass('is-active');
                $currTab.toggleClass('is-active');
            })
        },

        fancyBox: function() {
            /*$('.info-popup').each(function(index, el) {
                var self = el;
                $self = $(el);
                

            });*/


            Fancybox.bind('[data-fancybox]', {
                dragToClose: false,
            });

            console.log(Fancybox);
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
                    url: '/admin/file-api', // Hier den Pfad zur API auf deinem Server einfügen
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

        jstreeEpisode: function() {
            if ( $('.file-tree-episode').length > 0 ) {
                $('.file-tree-episode').each(function(i, el) {
                    console.log('test');
                    $.ajax({
                        url: '/admin/file-api', // Hier den Pfad zur API auf deinem Server einfügen
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {
                        // Die Antwort enthält die Daten für den jsTree
                            $(el).jstree({
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
    
                            $(el).on('select_node.jstree', function(e, data) {
                                var node = data.instance.get_node(data.selected[0]);
                                if (node.text.endsWith('.mp4')) {
                                    var path = $(el).jstree('get_path', data.node, '/');
                                    elID = $(el).attr('data-element-id');
                                    $('[data-submit="'+elID+'"]').attr('data-path', '/media/'+path);
                                    $('[data-submit="'+elID+'"]').css('display', 'inline-flex');
                                } else {
                                    data.instance.deselect_node(data.selected[0]);
                                    $('[data-submit="'+elID+'"]').css('display', 'none');
                                }                      
                            });
                        },
                        error: function(xhr, status, error) {
                            // Fehlerbehandlung, wenn die Anfrage fehlschlägt
                            console.error(error);
                        }
                    });
                });

                $('.episode-path-submit').on('click', function() {
                    var path = $(this).attr('data-path'),
                        mediaID = $(this).attr('data-submit');

                    $.ajax({
                        url: '/admin/episode-file-path',
                        type: 'post',
                        data: { mediaID: mediaID, path: path },
                        success: function(response) {
                            alert('Gespeichert');
                        }, error: function(xhr, status, error) {
                            // Hier wird eine Fehlermeldung ausgegeben
                            console.log('Fehler: ' + error);
                        }
                    });
                });
            }
        },

        initPlayer: function() {
            var self = this;

            if ( $('#player').length > 0 ) {
                if ( !self.isSmartphone() ) {
                    var player = videojs('player');

                    video = $('video')[0];
                    sekunde = $('span[data-time]').attr('data-time');
                    // Warten Sie auf das "loadedmetadata"-Ereignis, um sicherzustellen, dass das Video geladen ist
                    video.addEventListener("loadedmetadata", function() {
                        video.currentTime = sekunde;
                        $('#player-back-btn').appendTo(".video-js");
                    });

                    player.on('play', function() {
                        $currTime = $('.vjs-current-time');
                        $divider = $('.vjs-time-divider');
                        $duration = $('.vjs-duration');
    
                        $divider.css('right', (60+$duration.outerWidth()));
                        $currTime.css('right', (60+$duration.outerWidth()+$divider.outerWidth()));
                    });

                } else {
                    video = $('video')[0];
                    sekunde = $('span[data-time]').attr('data-time');
        
                    video.addEventListener("loadedmetadata", function() {
                        video.currentTime = sekunde;
                    });
                }
            }
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
                console.log('change');
                var $this = $(this);

                if ( !$this.is(':hover') && $this.hasClass('active') ) {
                    $this.removeClass('active');
                }
            });
        },

        myList: function() {
            function btnAction(self) {
                $(self).addClass('is-loading');
                var mediaID = $(self).attr('data-media-id'),
                    type = $(self).attr('data-type');

                setTimeout(function() {
                    $.ajax({
                        url: '/add-to-list',
                        type: 'post',
                        data: { mediaID: mediaID, type: type },
                        success: function(response) {
                        }, error: function(xhr, status, error) {
                            // Hier wird eine Fehlermeldung ausgegeben
                            console.log('Fehler: ' + error);
                        }
                    });

                    $(self).removeClass('is-loading');

                    if ( type === 'add' ) {
                        $('a[data-media-id="'+mediaID+'"][data-type="add"]').addClass('hidden');
                        $('a[data-media-id="'+mediaID+'"][data-type="remove"]').removeClass('hidden');
                    } else if ( type === 'remove' ) {
                        $('a[data-media-id="'+mediaID+'"][data-type="remove"]').addClass('hidden');
                        $('a[data-media-id="'+mediaID+'"][data-type="add"]').removeClass('hidden');
                    }
                }, 1000);
            }

            $(document).on('click', 'a.add-to-list', function(e) {
                e.preventDefault();
                var self = this;

                if ( !$(self).hasClass('is-loading') ) {
                    btnAction(self);
                }
            })

            $(document).on('click', 'a.remove-from-list', function(e) {
                e.preventDefault();
                var self = this;

                if ( !$(self).hasClass('is-loading') ) {
                    btnAction(self);
                }
            })
        },

        movieTimeSafe: function() {
            var self = this;
            if ( $('#mainPlayer').length > 0 ) {
                $player = $('#mainPlayer');
                video = $('video')[0];

                // Ausführen, wenn die Metadaten geladen sind
                $(video).on('loadedmetadata', function() {
                    var interval = false;

                    $(video).on('play', function() {
                        saveTime();
                        clearInterval(interval);
                        interval = setInterval(saveTime, 30000);
                    });

                    $(video).on('pause', function() {
                        clearInterval(interval);
                        saveTime();
                    });

                    $(video).on('stop', function() {
                        clearInterval(interval);
                        saveTime();
                    });

                    function saveTime() {
                        var currentSecond = video.currentTime;
                        var totalDuration = video.duration;
                        var showID = $('#time').attr('data-show');
                        $resultList = $('#time');

                        if ( currentSecond === totalDuration ) {
                            currentSecond = 0;
                        }

                        $.ajax({
                            url: '/movie-watch-time',
                            type: 'post',
                            data: { 
                                mediaID: $(video).attr('data-id'),
                                show: showID,
                                time: currentSecond,
                                totalLength: totalDuration,
                            },
                            success: function(response) {
                                $resultList.attr('data-time', currentSecond);
                                $resultList.attr('data-show', showID);
                            }, error: function(xhr, status, error) {
                                // Hier wird eine Fehlermeldung ausgegeben
                                console.log('Fehler: ' + error);
                            }
                        });
                    }
                });
            }
        },

        highlight: function() {
            $('.highlight-change').on('change', function() {
                console.log('change');
                var highlightID = $(this).attr('data-media'),
                    status = $(this).prop('checked');

                    if (status) {
                        status = 1;
                    } else {
                        status = 0;
                    }

                console.log(status);

                $.ajax({
                    url: '/highlight-status',
                    type: 'post',
                    data: { 
                        highlightID: highlightID,
                        status: status
                    },
                    success: function(response) {
                    }, error: function(xhr, status, error) {
                        // Hier wird eine Fehlermeldung ausgegeben
                        console.log('Fehler: ' + error);
                    }
                });
            });
        },

        isSmartphone: function() {
            if (/iPhone|iPad|iPod|Android|Windows Phone/i.test(navigator.userAgent)) {
                return true;
            }

            if ('ontouchstart' in window || navigator.maxTouchPoints) {
                return true;
            }

            if ( $(window).innerWidth() <= 1080 ) {
                return true;
            }

            return false;
        }
    }

    page.init();
});
