var inputTimer,
throttel = function(e) {
    clearTimeout(resizeTimer);
    inputTimer = setTimeout(function() {
        page.resizeHook();
    }, 50);
};


$(document).ready(function() {
    page = {
        $window: $(window),
        $html: $('html'),
        $body: $('body'),
        $header: $('header'),
        $masterWrap: $('#masterWrap'),
        $menuButton: $('.menu-button'),
        $apiSearch: $('#movie-api-search'),
        $showApiSearch: $('#show-api-search'),
        $liveSearch: $('#movie-live-search'),
        $searchBtn: $('.search-btn'),

        mobileSize: 1080,
        tabletSize: 1600,
        activeClass: 'is-active',
        activeMenu: 'active-menu',
        activeButton:'active-button',
        src: "data-desktop-src",
        videosrc: "desktop-src",

        init: function() {
            var self = this;

            self.movieLiveSearch();
            self.showLiveSearch();
        },

        bindHandlers: function () {
            var self = this;

            window.addEventListener('resize', debounce);
            window.addEventListener('scroll', debounceScroll);
        },

        movieLiveSearch: function() {
            var self = this;

            self.$apiSearch.on('input', function() {
                $this = $(this);
                $resultList = $('#movieSearchResults');

                setTimeout(function() {
                    var movieName = $this.val();
                    $this.val(movieName);
                    
                    if (movieName.length == 0) {
                        $resultList.addClass('hidden'); ;
                    } else {
                        $resultList.removeClass('hidden'); 
                    }
    
                    $.ajax({
                        url: '/admin/movies/movie-api-search',
                        type: 'post',
                        data: { movie: movieName },
                        success: function(response) {
                            $resultList.html(response);
                            self.fancyLoad();
                        }, error: function(xhr, status, error) {
                            // Hier wird eine Fehlermeldung ausgegeben
                            console.log('Fehler: ' + error);
                        }
                    });
                }, 500);
            });
            
            self.$liveSearch.on('input', function() {
                $this = $(this);
                $resultList = $('#movieLivesearchResults');
                $resultPageList = $('#moviePageLivesearchResults');
                $searchValueEl = $('#search-value');

                setTimeout(function() {
                    var movieName = $this.val();

                    if ( $('#searchpage').length > 0 ) {
                        $searchValueEl.text(movieName);

                        $.ajax({
                            url: '/live-search',
                            type: 'post',
                            data: { movie: movieName },
                            success: function(response) {
                                $resultPageList.html(response);
                                self.fancyLoad();
                            }, error: function(xhr, status, error) {
                                // Hier wird eine Fehlermeldung ausgegeben
                                console.log('Fehler: ' + error);
                            }
                        });
                    } else {
                        if (movieName.length == 0) {
                            $resultList.addClass('hidden'); ;
                        } else {
                            $resultList.removeClass('hidden'); 
                        }

                        $.ajax({
                            url: '/searchbar',
                            type: 'post',
                            data: { movie: movieName },
                            success: function(response) {
                                $resultList.html(response);
                                self.fancyLoad();
                            }, error: function(xhr, status, error) {
                                // Hier wird eine Fehlermeldung ausgegeben
                                console.log('Fehler: ' + error);
                            }
                        });
                    }
                }, 500);
            });

            self.$searchBtn.on('click', function(e) {
                if ( !(self.$liveSearch.val().length > 0) ) {
                    e.preventDefault();
                    if ( !$('.search-bar').hasClass('active-search') ) {
                        $('.search-bar').addClass('active-search');
                    } else {
                        self.$liveSearch.val('');
                        $('.search-bar').removeClass('active-search');
                    }
                }
            });
        },

        showLiveSearch: function() {
            var self = this;

            self.$showApiSearch.on('input', function() {
                console.log('test');
                $this = $(this);
                $resultList = $('#showSearchResults');

                setTimeout(function() {
                    var showName = $this.val();
                    $this.val(showName);
                    
                    if (showName.length == 0) {
                        $resultList.addClass('hidden'); ;
                    } else {
                        $resultList.removeClass('hidden'); 
                    }
    
                    $.ajax({
                        url: '/admin/shows/show-api-search',
                        type: 'post',
                        data: { show: showName },
                        success: function(response) {
                            $resultList.html(response);
                            self.fancyLoad();
                        }, error: function(xhr, status, error) {
                            // Hier wird eine Fehlermeldung ausgegeben
                            console.log('Fehler: ' + error);
                        }
                    });
                }, 500);
            });
        },

        fancyLoad: function() {
            $('img').each(function(i, el) {
                var img = $(el).attr('data-img');
                console.log(img);
                $(el).attr('src', img).removeAttr('data-img');
            });
        }
    }

    page.init();
});