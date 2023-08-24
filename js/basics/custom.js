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
            //self.initPictures();
            //self.accordion();
            self.tabs();
            self.selectTabs();
            //self.modal();
            self.initSlider();
            //self.customPagination();
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
            self.updateOverlay();
            self.pageReady();
            self.mediaPopUp();
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
            self.fancyLoad();
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

        /*customPagination: function(){
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
		},*/

        jstree: function() {
            if ( $('#file-tree').length > 0 ) {
                console.log('test');
                $.ajax({
                    url: '/admin/file-api', // Hier den Pfad zur API auf deinem Server einfügen
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                    // Die Antwort enthält die Daten für den jsTree
                        $('#file-tree').jstree({
                            "core": {
                                //"animation" : 0,
                                "themes" : { 
                                    "stripes" : true,
                                },
                                'data': response,
                                "multiple": false,
                            },
                            "checkbox": {
                                "three_state": false
                            },
                            "plugins": [
                                /*"checkbox", */"search", "state", "wholerow"
                            ],
                        })

                        $('#file-tree').on('select_node.jstree', function(e, data) {
                            var node = data.instance.get_node(data.selected[0]);
                            if (node.text.endsWith('.mp4')) {
                                var path = $('#file-tree').jstree('get_path', data.node, '/');
                                console.log(path);
                                $('#inputMoviePath').attr('value', '/media/'+path);
                                $('#inputMovieSubmit').css('display', 'inline-flex');
                            } else {
                                data.instance.deselect_node(data.selected[0]);
                                $('#inputMovieSubmit').css('display', 'none');
                            }                      
                        });

                        var to = false;
                        $('#jstree-search').keyup(function () {
                            if(to) { clearTimeout(to); }
                            to = setTimeout(function () {
                            var v = $('#jstree-search').val();
                            $('#file-tree').jstree(true).search(v);
                            }, 250);
                        });
                    },
                    error: function(xhr, status, error) {
                        // Fehlerbehandlung, wenn die Anfrage fehlschlägt
                        console.error(error);
                    }
                });
            }

            if ( $('.file-tree-episode').length > 0 ) {
                $.ajax({
                    url: '/admin/file-api', // Hier den Pfad zur API auf deinem Server einfügen
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                    // Die Antwort enthält die Daten für den jsTree
                        $('.file-tree-episode').jstree({
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
                        });

                        $('.file-tree-episode').on('select_node.jstree', function(e, data) {        
                            var node = data.instance.get_node(data.selected[0]);
                            var episodeID = $(this).attr('data-element-id');

                            if (node.text.endsWith('.mp4')) {
                                var path = $(this).jstree('get_path', data.node, '/');
                                $('#inputEpisodePath-'+episodeID).attr('value', '/media/'+path);
                                $('#inputEpisodeSubmit-'+episodeID).css('display', 'inline-flex');

                            } else {
                                data.instance.deselect_node(data.selected[0]);
                                $('#inputEpisodeSubmit-'+episodeID).css('display', 'none');
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
                //if ( !self.isSmartphone() ) {
                var player = videojs('player');
                video = $('video')[0];
                sekunde = $('span[data-time]').attr('data-time');

                // Warten Sie auf das "loadedmetadata"-Ereignis, um sicherzustellen, dass das Video geladen ist
                video.addEventListener("loadedmetadata", function() {
                    video.currentTime = sekunde;
                    $('#player-back-btn').appendTo(".video-js");
                    
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

                $(showContainer+' .menu li .sub-menu .back').on('click', function(e) {
                    e.preventDefault();

                    $(showContainer).removeClass('active-submenu');

                    $(showContainer+' ul.sub-menu').removeClass('active');
                });

                $('a#show-eps-btn').on('click', function(e) {
                    e.preventDefault();

                    $(showContainer).toggleClass('visible');
                });

                /*} else {
                    video = $('video')[0];
                    sekunde = $('span[data-time]').attr('data-time');
        
                    video.addEventListener("loadedmetadata", function() {
                        video.currentTime = sekunde;
                    });
                }*/
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
                video = $('#player')[0];
                
                // Ausführen, wenn die Metadaten geladen sind
                $(video).on('loadedmetadata', function() {
                    var interval = false;
                    var isVideoEnded = false;

                    $(video).on('play', function() {
                        isVideoEnded = false;
                        saveTime(video.currentTime, video.duration);
                        clearInterval(interval);
                        interval = setInterval(saveTime, 30000);
                    });

                    $(video).on('ended', function() {
                        isVideoEnded = true;
                        clearInterval(interval);
                        saveTime(video.duration, video.duration);
                    });

                    $(video).on('pause', function() {
                        if (!isVideoEnded && video.currentTime !== video.duration ) {
                            clearInterval(interval);
                            saveTime(video.currentTime, video.duration);
                        }
                    });

                    function saveTime(currentVideoTime, videoDuration) {
                        var currentSecond = currentVideoTime;
                        var totalDuration = videoDuration;
                        $resultList = $('#time');
                        var showID = $resultList.attr('data-show');

                        if ( currentSecond === totalDuration ) {
                            watched = 1;
                        } else {
                            watched = 0;
                        }

                        $.ajax({
                            url: '/movie-watch-time',
                            type: 'post',
                            data: { 
                                mediaID: $(video).attr('data-id'),
                                show: showID,
                                time: currentSecond,
                                watched: watched,
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

        highlight: function() {
            $('.highlight-change').on('change', function() {

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

        mediaPopUp: function() {
            var self = this;

            if ( !($('#modal').length > 0) ) {
                self.$body.append('<div class="modal" id="modal"><div class="modal-overlay"></div><div class="modal-wrap large"><div class="modal-inner-wrap"></div><a href="#" class="modal-close"></a></div></div>');
            }

            $modal = $('#modal');
            $modalWrap = $('#modal .modal-wrap');
            $modalInnerWrap = $('#modal .modal-inner-wrap');
            $modalOverlay = $('#modal .modal-overlay');
            $modalCloseBtn = $('#modal .modal-close');

            $(document).on('click', '.info-trigger', function(e) {
                e.preventDefault();
                var id = $(this).attr('data-src');

                if ( $('#'+id).length == 0 ) {
                    $.ajax({
                        url: '/media-popup',
                        type: 'post',
                        data: { 
                            mediaID: id
                        },
                        success: function(response) {
                            var mediaSrc = response;
                            $('body').append(mediaSrc);
                            $modalInnerWrap.empty();
                           $('.modal-inner-wrap').append(mediaSrc);
                            $('body').addClass('active-modal');
                            $modal.addClass('active');
                            self.fancyLoad();

                        }, error: function(xhr, status, error) {
                            // Hier wird eine Fehlermeldung ausgegeben
                            console.log('Fehler: ' + error);
                        }
                    });
                } else {
                    var mediaSrc = $('#'+id);
                    $modalInnerWrap.empty();
                    mediaSrc.clone().appendTo('.modal-inner-wrap');
                    $('body').addClass('active-modal');
                    $modal.addClass('active');
                }
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
        },

        pageReady: function() {
            var self = this;
            $('#loader').addClass('hidden');
            $("body").removeClass('loading');
            
            self.fancyLoad();
        },

        sorting: function() {
            var self = this;

            function orderSetup() {
                var genreID = $('#genre-filter').val();
                var order = $('#title-filter').val();

                $.ajax({
                    url: '/sorting',
                    type: 'post',
                    data: { 
                        genreID: genreID,
                        order: order
                    },
                    success: function(response) {
                        var mediaSrc = response;
                        $('#media-list').empty();
                        $('#media-list').append(mediaSrc);
                    }, error: function(xhr, status, error) {
                        // Hier wird eine Fehlermeldung ausgegeben
                        console.log('Fehler: ' + error);
                    }
                });
            }

            $('#genre-filter').on('change', function() {
                orderSetup();
            });

            $('#title-filter').on('change', function() {
                orderSetup();
            });
        },

        fancyLoad: function() {
            var self = this;
            self.src = "data-img";

            forEach(document.querySelectorAll('img[data-img]'), function(el){
                el.setAttribute('src', el.getAttribute(self.src));
                $(el).removeAttr('data-img');
            });

        }
    }

    page.init();
});
