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

        init: function() {
            var self = this;

            self.movieLiveSearch();
        },

        movieLiveSearch: function() {
            $('#movie-search').on('input', function() {
                $this = $(this);
                $resultList = $('#movieSearchResults');
                var movieName = $this.val();
                $this.val(movieName);
                console.log(movieName);
                
                if (movieName.length == 0) {
                    $resultList.addClass('hidden'); ;
                } else {
                    $resultList.removeClass('hidden'); 
                }

                $.ajax({
                    url: '/movies/movie-search',
                    type: 'post',
                    data: { movie: movieName },
                    success: function(response) {
                        $resultList.html(response);
                    }, error: function(xhr, status, error) {
                        // Hier wird eine Fehlermeldung ausgegeben
                        console.log('Fehler: ' + error);
                    }
                });
            })
        }
    }

    page.init();
});