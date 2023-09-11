$(document).ready(function() {
    custom = {
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
            self.remoteWatch();
            self.highlight();
            self.mediaPopUp();
            self.sorting();
            self.updateUserImg();
        },

        bindHandlers: function () {
            var self = this;

            window.addEventListener('resize', debounce);
            window.addEventListener('scroll', debounceScroll);
        },

        movieLiveSearch: function() {
            var self = this;

            $('#close-search').on('click', function(e) {
                e.preventDefault();
                $('#searchpage').removeClass('hidden');
                $('body').removeClass('active-search');
            });

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
                $searchPage = $('#searchpage');

                setTimeout(function() {
                    $searchPage.removeClass('hidden');
                    $('body').addClass('active-search');

                    var mediaName = $this.val();

                    if ( $('#searchpage').length > 0 ) {
                        $searchValueEl.text(mediaName);

                        $.ajax({
                            url: '/live-search',
                            type: 'post',
                            data: { media: mediaName },
                            success: function(response) {
                                $resultPageList.html(response);
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
                e.preventDefault();
                $('.search-bar').toggleClass('active-search');

                if ( $('.search-bar').hasClass('active-search') ) {
                    $('#searchpage').removeClass('hidden');
                    $('body').addClass('active-search');
                } else {
                    $('#searchpage').addClass('hidden');
                    $('body').removeClass('active-search');
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
                videoJS = $('video')[0];
                videoJSPlayer = videojs('player');
                
                // Ausführen, wenn die Metadaten geladen sind
                videoJS.addEventListener("loadedmetadata", function() {
                    videoJS.currentTime = parseInt($('span#time').attr('data-time'));
                    videoJSPlayer.volume( parseFloat($('span#time').attr('data-volume')) );
                    var interval = false;
                    var isVideoEnded = false;

                    videoJSPlayer.on('play', function() {
                        isVideoEnded = false;
                        clearInterval(interval);
                        saveTime(videoJS.currentTime, videoJS.duration);
                        interval = setInterval(function() {
                            saveTime(videoJS.currentTime, videoJS.duration)
                        }, 30000);
                    });

                    videoJSPlayer.on('ended', function() {
                        isVideoEnded = true;
                        clearInterval(interval);
                        saveTime(videoJS.duration, videoJS.duration);
                    });

                    videoJSPlayer.on('seeking', function () {
                        if ( $(videoJS).hasClass('vjs-scrubbing') ) {
                            clearInterval(interval);
                            saveTime(videoJS.duration, videoJS.duration);
                            interval = setInterval(function() {
                                saveTime(videoJS.currentTime, videoJS.duration)
                            }, 30000);
                        }            
                    });

                    videoJSPlayer.on('pause', function() {                        
                        if ( videoJS.currentTime != videoJS.duration) {
                            clearInterval(interval);
                            saveTime(videoJS.currentTime, videoJS.duration);
                        }
                    });

                    $('.play-trigger').on('click', function() {
                        clearInterval(interval);
                        saveTime(videoJS.duration, videoJS.duration);
                    });

                    $('#next-episode-btn').on('click', function() {
                        clearInterval(interval);
                        saveTime(videoJS.duration, videoJS.duration);
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
                                mediaID: $(videoJS).attr('data-id'),
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

                    videoJSPlayer.on('volumechange', () => {
                        var currentVolume = videoJSPlayer.volume();
                        saveVolume(currentVolume);
                    });

                    function saveVolume(currentVolume) {
                        $resultList = $('#time');

                        $.ajax({
                            url: '/save-volume',
                            type: 'post',
                            data: { 
                                volume: currentVolume
                            },
                            success: function(response) {
                                $resultList.attr('data-volume', currentVolume);
                            }, error: function(xhr, status, error) {
                                // Hier wird eine Fehlermeldung ausgegeben
                                console.log('Fehler: ' + error);
                            }
                        });
                    }
                });
            }
        },

        remoteWatch: function() {
            var self = this;
            const host = window.location.hostname;
            if ( $('#mainPlayer').length > 0 ) {
                videoPlayer = videojs('player');
                const startButton = document.querySelector('.vjs-big-play-button');

                // Ausführen, wenn die Metadaten geladen sind
                videoPlayer.on("loadedmetadata", () => {
                    const remotesessionID = $('span#time').attr('data-session');

                    if ( remotesessionID ) {
                        const socket = new WebSocket(`ws://${host}:3000/?remotesessionID=${remotesessionID}`);
                        let isFirstPlay = true;

                        socket.onopen = () => {
                            var userID = $('#message-use-id').val();
                                username = $('#message-use-name').val();
                                send = `joined:${userID}:${username}`;
                            joinedMessage(userID, username);
                            socket.send(send);                            
                        }

                        socket.onerror = (error) => {
                            console.error('WebSocket error:', error);
                        };

                        socket.onmessage = (event) => {
                            // Empfange Aktionen von anderen Benutzern und steuere den Video-Player entsprechend
                            if (event.data.startsWith('joined:')) {
                                const userID = event.data.split(':')[1];
                                const username = event.data.split(':')[2];
                                joinedMessage(userID, username);
                            } else if (event.data === 'play') {
                                videoPlayer.play();
                            } else if (event.data === 'pause') {
                                videoPlayer.pause();
                            } else if (event.data.startsWith('timeupdate:')) {
                                const newTime = parseFloat(event.data.split(':')[1]);
                                videoPlayer.currentTime(newTime);
                            } else if (event.data.startsWith('url:')) {
                                const url = event.data.split(':')[1];
                                window.location.href = url;
                            } else if (event.data.startsWith('msg:')) {
                                const message = event.data.split(':')[1];
                                const userID = event.data.split(':')[2];
                                const username = event.data.split(':')[3];
                                ajaxMessage(message, userID, username);
                            }
                        };

                        videoPlayer.on('play', () => {
                            // Sende Aktion "Play" an den Server
                            if ( isFirstPlay ) {
                                videoPlayer.pause();
                                isFirstPlay = false;
                            } else {
                                synchTime();
                                socket.send('play');
                            }
                        });
                
                        videoPlayer.on('pause', () => {
                            if ( !isFirstPlay ) {
                                // Sende Aktion "Pause" an den Server
                                synchTime();
                                socket.send('pause');
                            }
                        });
                
                        videoPlayer.on('seeking', function () {
                            if ( videoPlayer.hasClass('vjs-scrubbing') ) {
                                synchTime();
                            }            
                        });
    
                        $('#player-sek-forward').on('click', function() {
                            synchTime();
                        });
    
                        $('#player-sek-back').on('click', function() {
                            synchTime();
                        });

                        $('.play-trigger').on('click', function(e) {
                            e.preventDefault();
                            changeMedia(this);
                        });

                        $('#next-episode-btn').on('click', function(e) {
                            e.preventDefault();
                            changeMedia(this);
                        });

                        function changeMedia($his) {
                            var url = $($his).attr('href');
                            socket.send(`url:${url}`)
                            window.location.href = url;
                        }

                        function synchTime() {
                            const currentTime = videoPlayer.currentTime();
                            const actionTimeUpdate = `timeupdate:${currentTime}`;
                            socket.send(actionTimeUpdate);
                        }

                        $msgInput = $('#message-input');
                        
                        $msgInput.on('keyup', function(event) {
                            if (event.key === 'Enter') {
                                sendMessage();
                            }
                        });

                        $('#chatMSG').on('click', function() {
                            sendMessage();                           
                        });

                        function sendMessage() {
                            var message = $('#message-input').val(),
                            userID = $('#message-use-id').val();
                            username = $('#message-use-name').val();
                            send = `msg:${message}:${userID}:${username}`;

                            $('#message-input').val("");

                            if ( message.length > 0) {
                                socket.send(send);
                                ajaxMessage(message, userID, username)
                            }   
                        }

                        function joinedMessage(userID, username) {
                            const joined = 'true';

                            $.ajax({
                                url: '/msg',
                                type: 'post',
                                data: { 
                                    joined: joined,
                                    userID: userID,
                                    username: username,
                                },
                                success: function(response) {
                                    var newMessage = response;
                                    $('#message-wrap').append(newMessage);
                                    var objDiv = document.getElementById("message-wrap");
                                    objDiv.scrollTop = objDiv.scrollHeight;
                                }, error: function(xhr, status, error) {
                                    // Hier wird eine Fehlermeldung ausgegeben
                                    console.log('Fehler: ' + error);
                                }
                            });
                        }

                        function ajaxMessage(ajaxMessage, ajaxUserID, ajaxUsername) {
                            $.ajax({
                                url: '/msg',
                                type: 'post',
                                data: { 
                                    message: ajaxMessage,
                                    userID: ajaxUserID,
                                    username: ajaxUsername
                                },
                                success: function(response) {
                                    var newMessage = response;
                                    $('#message-wrap').append(newMessage);
                                    var objDiv = document.getElementById("message-wrap");
                                    objDiv.scrollTop = objDiv.scrollHeight;
                                }, error: function(xhr, status, error) {
                                    // Hier wird eine Fehlermeldung ausgegeben
                                    console.log('Fehler: ' + error);
                                }
                            });
                        }
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

        updateUserImg: function() {
            $('input[name="userImg"]').on('click', function() {
                console.log($(this).attr('data-current'));
                if ( $(this).attr('data-current') == 0 ) {
                    $('#updateUserImg').css('display', 'inline-flex');
                } else {
                    $('#updateUserImg').css('display', 'none');
                }
            });

            $('#updateUserImg').on('click', function(e) {
                e.preventDefault();

                var img = $('.user-img-select input[type="radio"]:checked').val();
                var userID = $('.user-img-select input[type="radio"]:checked').attr('data-id');

                $.ajax({
                    url: '/updateUserImg',
                    type: 'post',
                    data: { 
                        img: img,
                        userID: userID,
                    },
                    success: function(response) {
                        $('body').append(response);
                    }, error: function(xhr, status, error) {
                        // Hier wird eine Fehlermeldung ausgegeben
                        console.log('Fehler: ' + error);
                    }
                });
            });
        }
    }

    custom.init();
});