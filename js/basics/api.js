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
            self.jstreeMovie();
            self.jstreeEpisode();
            self.myList();
            self.movieTimeSafe();
            self.highlight();
            self.mediaPopUp();
            self.sorting();
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
            var self = this;
            self.src = "data-img";

            forEach(document.querySelectorAll('img[data-img]'), function(el){
                el.setAttribute('src', el.getAttribute(self.src));
                $(el).removeAttr('data-img');
            });
        },

        jstreeMovie: function() {
            if ( $('#file-tree').length > 0 ) {
                $.ajax({
                    url: '/admin/file-api', // Hier den Pfad zur API auf deinem Server einfügen
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
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

        highlight: function() {
            $('.highlight-change').on('change', function() {

                var highlightID = $(this).attr('data-media'),
                    status = $(this).prop('checked');

                if (status) {
                    status = 1;
                } else {
                    status = 0;
                }

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

        sorting: function() {
            var self = this;

            function orderSetup() {
                var genreID = $('#genre-filter').val();
                var order = $('#title-filter').val();
                var type = $('#type-filter').attr('data-type');

                $.ajax({
                    url: '/filter',
                    type: 'post',
                    data: { 
                        genreID: genreID,
                        order: order,
                        type: type,
                    },
                    success: function(response) {
                        var mediaSrc = response;
                        $('#media-list').empty();
                        $('#media-list').append(mediaSrc);
                        self.fancyLoad();
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
    }

    page.init();
});