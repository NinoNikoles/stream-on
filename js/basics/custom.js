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
            self.userMenuBtn();
            //self.infinitLoad();
            self.movieTimeSafe();
            self.initPlayer();
            self.myList();
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

        modal: function() {
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
        },

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
        }*/

        initSlider: function () {
            sliderNumber = 1;

            $(".swiper").each(function () {
                var $el = $(this);
                const sliderClass = 'swiper-' + sliderNumber;
                const slider = '.swiper-' + sliderNumber;

                $el.addClass(sliderClass);
                var slidesPerViewMobile = 2;
                var slidesPerViewTablet = 6;
                var slidesPerViewDesktop = 8;
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
                    spaceBetween: 16,
                    allowTouchMove: true,
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

        initPlayer: function() {
            if ( $('#player').length > 0 ) {
                var player = videojs('player');

                video = $('video')[0];
                sekunde = $('span[data-time]').attr('data-time');

                // Warten Sie auf das "loadedmetadata"-Ereignis, um sicherzustellen, dass das Video geladen ist
                video.addEventListener("loadedmetadata", function() {
                    video.currentTime = sekunde;
                    $('#player-back-btn').appendTo(".video-js");
                });
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

        infinitLoad: function() {
            var self = this;
            $resultList = $('#movie-list');

            if ( $resultList.length === 1) {
                var movieList = []; // Hier gehen wir davon aus, dass movieList das Array der Filme enthält
                var startIndex = 0; // Startindex für die Ausgabe
                var itemsPerPage = 20; // Anzahl der Elemente pro Seite
                var isLoading = false; // Flag, um zu verhindern, dass mehrere AJAX-Anfragen gleichzeitig gesendet werden
                var secureEnd = false;
                var addTimer = false;

                // Funktion zum Ausgeben der nächsten Elemente
                function displayNextMovies() {
                    if (isLoading) {
                        return; // Wenn bereits eine AJAX-Anfrage läuft oder der Startindex größer oder gleich der Anzahl der Filme ist, beende die Funktion vorzeitig
                    }

                    if ( secureEnd !== true) {
                        isLoading = true; // Setze das Flag, um anzuzeigen, dass eine AJAX-Anfrage ausgeführt wird
                        $('#loading-message').css('display','block');

                        setTimeout(function() {
                            $('#loading-message').css('display','none');

                            var endIndex = Math.min(startIndex + itemsPerPage, movieList.length); // Endindex für die Ausgabe
                            var moviesToDisplay = movieList.slice(startIndex, endIndex); // Ausschnitt des movieList-Arrays für die aktuelle Ausgabe
    
                            for (var i = 0; i < moviesToDisplay.length; i++) {
                                // Hier kannst du den entsprechenden Code zum Erstellen und Anzeigen der einzelnen Filme einfügen
                                $('<div class="col-6 col-2-medium grid-padding">'+moviesToDisplay[i]+'</div>').appendTo('#movie-list');
                            }
    
                            startIndex += moviesToDisplay.length; // Aktualisiere den Startindex für die nächste Ausgabe
                            if (startIndex === movieList.length) {
                                secureEnd = true;
                            }

                            isLoading = false; // Setze das Flag zurück, um anzuzeigen, dass die AJAX-Anfrage abgeschlossen ist
                        }, 1000);                      
                    }
                }

                // Überprüfe, ob der Benutzer das Ende der Seite erreicht hat
                $(window).scroll(function() {
                    var wheight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                    var dheight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, document.body.offsetHeight, document.documentElement.offsetHeight);

                    if (window.scrollY + wheight >= dheight) {
                        displayNextMovies();
                    }
                });

                // Lade die Filme und rufe die erste Ausgabe auf
                function loadMovies() {

                    $.ajax({
                        url: '/movie-scroll-load',
                        method: 'GET',
                        //data: { page: page },
                        success: function(response) {
                            movieList = JSON.parse(response);
                            displayNextMovies();
                        },
                        error: function(xhr, status, error) {
                            console.log('Fehler beim Laden weiterer Filme.');
                            isLoading = false; // Setze das Flag zurück, um anzuzeigen, dass die AJAX-Anfrage abgeschlossen ist
                        }
                    });
                }

                // Lade die Filme und rufe die erste Ausgabe auf
                loadMovies();
            }
        },

        myList: function() {
            function btnAction(self) {
                $(self).addClass('loading');
                var movieID = $(self).attr('data-movie-id'),
                    type = $(self).attr('data-type');

                setTimeout(function() {
                    $.ajax({
                        url: '/add-to-list',
                        type: 'post',
                        data: { movieID: movieID, type: type },
                        success: function(response) {
                        }, error: function(xhr, status, error) {
                            // Hier wird eine Fehlermeldung ausgegeben
                            console.log('Fehler: ' + error);
                        }
                    });

                    $(self).removeClass('loading');

                    if ( type === 'add' ) {
                        $('a[data-movie-id="'+movieID+'"][data-type="add"]').addClass('hidden');
                        $('a[data-movie-id="'+movieID+'"][data-type="remove"]').removeClass('hidden');
                    } else if ( type === 'remove' ) {
                        $('a[data-movie-id="'+movieID+'"][data-type="remove"]').addClass('hidden');
                        $('a[data-movie-id="'+movieID+'"][data-type="add"]').removeClass('hidden');
                    }
                }, 1000);
            }

            $(document).on('click', 'a.add-to-list', function(e) {
                e.preventDefault();
                var self = this;

                if ( !$(self).hasClass('loading') ) {
                    btnAction(self);
                }
            })

            $(document).on('click', 'a.remove-from-list', function(e) {
                e.preventDefault();
                var self = this;

                if ( !$(self).hasClass('loading') ) {
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
                        $resultList = $('#test');

                        if ( currentSecond === totalDuration ) {
                            currentSecond = 0;
                        }

                        $.ajax({
                            url: '/movie-watch-time',
                            type: 'post',
                            data: { 
                                movieID: $(video).attr('data-id'),
                                time: currentSecond,
                                totalLength: totalDuration,
                            },
                            success: function(response) {
                                $resultList.html(response);
                            }, error: function(xhr, status, error) {
                                // Hier wird eine Fehlermeldung ausgegeben
                                console.log('Fehler: ' + error);
                            }
                        });
                    }
                });
            }
        }
    }

    page.init();
});
