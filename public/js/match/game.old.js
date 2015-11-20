(function() {

    var $closeBtn,
        $heading,
        $loadBtn,
        $loading,
        $players,
        $stubPlayers,
        $playersImages,
        $playersLabels,
        $playersTeamTwo,
        $playersTeamOne,
        $playersStubTeamOne,
        $playersStubTeamTwo,
        $stage,
        $subHeading,
        $switchBtn,
        $switcher,
        $team,
        $stubTeam,
        $teamListTeamOne,
        $teamListStubTeamTwo,
        $terrain,
        $terrainHolder,
        $world,
        anim,
        data,
        dom,
        events,
        loader,
        init,
        pos,
        scenes,
        state,
        positions5playersTeamOne, positions6playersTeamOne,
        positions5playersTeamTwo, positions6playersTeamTwo;


    $stage = null;
    $world = null;
    $terrain = null;
    $terrainHolder = null;
    $team = null;
    $stubTeam = null;
    $teamListTeamOne = null;
    $teamListStubTeamTwo = null;
    $players = null;
    $stubPlayers = null;
    $playersImages = null;
    $playersLabels = null;
    $playersTeamOne = null;
    $playersTeamTwo = null;
    $playersStubTeamOne = null;
    $playersStubTeamTwo = null;
    $switchBtn = null;
    $loadBtn = null;
    $closeBtn = null;
    $heading = null;
    $subHeading = null;
    $loading = null;
    $switcher = null;

    // dimensions of field: 40em x 20em = 640px x 320px
    positions5playersTeamOne = [
        {x: 190, y: 215},
        {x: 480, y: 190},
        {x: 80, y: 140},
        {x: 580, y: 130},
        {x: 320, y: 70}
    ];
    positions5playersTeamTwo = [
        {x: 320, y: -10},
        {x: 210, y: 15},
        {x: 160, y: 65},
        {x: 430, y: 15},
        {x: 450, y: 80}
    ];
    positions6playersTeamOne = [
        {x: 100, y: 160},
        {x: -100, y: 160},
        {x: 240, y: 90},
        {x: -240, y: 90},
        {x: 0, y: 120},
        {x: 0, y: 20}
    ];



    var positionsTeamOne = {};
    positionsTeamOne["6"] = positions6playersTeamOne;
    positionsTeamOne["5"] = positions5playersTeamOne;
    var positionsTeamTwo = {};
    positionsTeamTwo["5"] = positions5playersTeamTwo;
    data = {
        players: {
            teamOne: [],
            teamTwo: []
        },
        stubPlayers: {
            teamOne: [],
            teamTwo: []
        }
    };

    loader = {
        populateMatchData: function(matchData) {
            var numOfPlayersInTeam = matchData.game.number_of_players_in_a_team;
            var numOfPlayersInTeamOne = matchData.game.team_one_players.length;
            var numOfPlayersInTeamTwo = matchData.game.team_two_players.length;

            var playersInTeamOneInd = numOfPlayersInTeamOne.toString();
            if(numOfPlayersInTeamOne != 5 && numOfPlayersInTeamOne != 6) {
                playersInTeamOneInd = "5";
            }
            var playersInTeamTwoInd = numOfPlayersInTeamTwo.toString();
            if(numOfPlayersInTeamTwo != 5 && numOfPlayersInTeamTwo != 6) {
                playersInTeamTwoInd = "5";
            }
            for(var i = 0; i < numOfPlayersInTeamOne; i++) {
                data.players.teamOne.push({
                    name: matchData.game.team_one_players[i].username,
                    origin: 'Brazil',
                    height: '1.88m',
                    shirt: '3',
                    pos: 'Defence',
                    dob: '32',
                    goals: 0,
                    games: 34,
                    x: positionsTeamOne[playersInTeamOneInd][i].x,
                    y: positionsTeamOne[playersInTeamOneInd][i].y,
                    avatarExists: matchData.game.team_one_players[i].avatar_exists,
                    avatarImgUrl: matchData.game.team_one_players[i].avatar_img_url
                });
                data.stubPlayers.teamOne.push({
                    name: matchData.game.team_one_players[i].username,
                    x: positionsTeamTwo[playersInTeamOneInd][i].x,
                    y: positionsTeamTwo[playersInTeamOneInd][i].y

                });
            }

            for(var i = 0; i < numOfPlayersInTeamTwo; i++) {
                data.players.teamTwo.push({
                    name: matchData.game.team_two_players[i].username,
                    origin: 'Brazil',
                    height: '1.88m',
                    shirt: '3',
                    pos: 'Defence',
                    dob: '32',
                    goals: 0,
                    games: 34,
                    x: positionsTeamOne[playersInTeamTwoInd][i].x,
                    y: positionsTeamOne[playersInTeamTwoInd][i].y,
                    avatarExists: matchData.game.team_two_players[i].avatar_exists,
                    avatarImgUrl: matchData.game.team_two_players[i].avatar_img_url
                });
                data.stubPlayers.teamTwo.push({
                    name: matchData.game.team_two_players[i].username,
                    x: positionsTeamTwo[playersInTeamTwoInd][i].x,
                    y: positionsTeamTwo[playersInTeamTwoInd][i].y
                });
            }
        }
    };




    state = {
        teamOne: true,
        stubTeamOne: false,

        disabHover: false,
        swapSides: function() {
            if (this.teamOne) {
                this.stubTeamOne = true;
                return this.teamOne = false;
            } else {
                this.stubTeamOne = false;
                return this.teamOne = true;
            }
        },
        curSide: function() {
            if (this.teamOne) {
                return 'teamOne';
            } else {
                return 'teamTwo';
            }
        }
    };

    pos = {
        world: {
            baseX: 0,
            baseY: 0,
            baseZ: 0
        },
        ground: {
            x: 640,
            y: 320
        }

    };

    dom = {
        addPlayers: function(side) {
            var $el, key, ref, val, key2, ref2, val2;
            ref = data.players[side];
            for (key in ref) {
                val = ref[key];
                val.side = side;
                $el = this.addPlayer(val);
                $team.append($el);
            }
            ref2 = data.stubPlayers[side];
            for (key2 in ref2) {
                val2 = ref2[key2];
                val2.side = side;
                $el = this.addStubPlayer(val2);
                $stubTeam.append($el);
            }
            $players = $('.js-player');
            $playersImages = $('.js-player .player__img');
            $playersLabels = $('.js-player .player__label span');
            $playersTeamOne = $('.js-player[data-side="teamOne"]');

            $stubPlayers = $('.js-stub-player');
            $playersStubTeamOne = $('.js-stub-player[data-side="teamOne"]');

            $playersStubTeamTwo = $('.js-stub-player[data-side="teamTwo"]');

            return $playersTeamTwo = $('.js-player[data-side="teamTwo"]');
        },
        addPlayer: function(data) {
            var $el;
            $el = $('<div class="js-player player ' + data.side + '"  data-name="' + data.name + '" data-side="' + data.side + '" data-x="' + data.x + '" data-y="' + data.y + '"></div>');
            $el.append('<div class="player__label"><span>' + data.name + '</span></div>');

            var avatarHtml = '<div class="player__img std-item">' +
                '<div class = "icon-holder std-icon-holder">' +
                '<div class = "user-avatar-cont std-icon-cont">' +
                '<div class = "thumbnail user user-icon std-icon no-avatar">' +
                '<div class="pic-cont std-pic-cont">';
            if(data.avatarExists) {
                avatarHtml += '<span class = "v-align-helper"></span>' +
                    '<img src = "' + data.avatarImgUrl +'">';
            } else {
                avatarHtml += '<i class = "fa icon fa-fw fa-male"></i>';
            }
            avatarHtml += '</div></div></div></div></div>';
            $el.append(avatarHtml);

            $el.prepend('<div class="player__card"> </div>');
            $el.prepend('<div class="player__placeholder"></div>');
            this.populateCard($el.find('.player__card'), data);
            return $el;
        },
        addStubPlayer: function(data) {
            var $el;
            $el = $('<div class="js-stub-player stub-player ' + data.side + '"  data-name="' + data.name +
                '" data-side="' + data.side + '" data-x="' + data.x + '" data-y="' + data.y + '"></div>');
            $el.append('<div class="player__label"><span>' + data.name + '</span></div>');
            $el.prepend('<div class="player__placeholder"></div>');
            return $el;
        },
        preloadImages: function(preload) {

            var i, promises;
            promises = [];
            i = 0;
            while (i < preload.length) {
                (function(url, promise) {
                    var img;
                    img = new Image;
                    img.onload = function() {
                        return promise.resolve();
                    };
                    return img.src = url;
                })(preload[i], promises[i] = $.Deferred());
                i++;
            }
            scenes.endLoading();

            return scenes.loadIn(1600);

//            return $.when.apply($, promises).done(function() {
//                scenes.endLoading();
//                console.log("request ended");
//
//                return scenes.loadIn(1600);
//            });
        },
        populateCard: function($el, data) {
            return $el.append('<h3>' + data.name + '</h3>' +
                '<ul class="player__card__list"><li><span>DOB</span><br/>' +
                data.dob + ' yr</li><li><span>Height</span><br/>' +
                data.height + '</li><li><span>Origin</span><br/>' +
                data.origin + '</li></ul>' +
                '<ul class="player__card__list player__card__list--last"><li><span>Games</span><br/>' +
                data.games + '</li><li><span>Goals</span><br/>' + data.goals + '</li></ul>');
        },
        displayNone: function($el) {
            return $el.css('display', 'none');
        }
    };

    events = {
        attachAll: function() {
            $switchBtn.on('click', function(e) {
                var $el;
                e.preventDefault();
                $el = $(this);
                if ($el.hasClass('disabled')) {
                    return;
                }
                scenes.switchSides();
                $switchBtn.removeClass('disabled');
                return $el.addClass('disabled');
            });
            $loadBtn.on('click', function(e) {
                e.preventDefault();
                return scenes.loadIn();
            });
            $playersImages.on('click', function(e) {
                var $el;
                e.preventDefault();
                $el = $(this).parent();
                if ($('.active').length) {
                    return false;
                }
                $el.addClass('active');
                scenes.focusPlayer($el);
                return setTimeout((function() {
                    return events.attachClose();
                }), 1);
            });
            return $playersLabels.on('click', function(e) {
                var $el;
                e.preventDefault();
                $el = $(this).parent().parent();
                if ($('.active').length) {
                    return false;
                }
                $el.addClass('active');
                scenes.focusPlayer($el);
                return setTimeout((function() {
                    return events.attachClose();
                }), 1);
            });
        },
        attachClose: function() {
            return $stage.one('click', function(e) {
                e.preventDefault();
                return scenes.unfocusPlayer();
            });
        }
    };

    scenes = {
        preLoad: function() {
            $teamListTeamOne.velocity({
                opacity: 0
            }, 0);
            $players.velocity({
                opacity: 0
            }, 0);
            $teamListStubTeamTwo.velocity({
                opacity: 0
            }, 0);
            $stubPlayers.velocity({
                opacity: 0
            }, 0);
            $loadBtn.velocity({
                opacity: 0
            }, 0);
            $switcher.velocity({
                opacity: 0
            }, 0);
            $heading.velocity({
                opacity: 0
            }, 0);
            $subHeading.velocity({
                opacity: 0
            }, 0);
            $playersTeamTwo.css('display', 'none');
            $playersStubTeamOne.css('display', 'none');

            $world.velocity({
                opacity: 0,
                translateZ: 0,
                translateY: -60
            }, 0);
            return $('main').velocity({
                opacity: 1
            }, 0);
        },
        loadIn: function(delay) {

            var delayInc;
            if (delay == null) {
                delay = 0;
            }
            $world.velocity({
                opacity: 1,
                translateY: 0,
                translateZ: 0
            }, {
                duration: 1000,
                delay: delay,
                easing: 'spring'
            });
            anim.fadeInDir($heading, 300, delay + 600, 0, 30);
            anim.fadeInDir($subHeading, 300, delay + 800, 0, 30);

            anim.fadeInDir($teamListStubTeamTwo, 300, delay + 800, 0, 30);
            anim.fadeInDir($teamListTeamOne, 300, delay + 800, 0, 30);

            anim.fadeInDir($switcher, 300, delay + 900, 0, 30);
            delay += 1200;
            delayInc = 30;

            anim.dropStubPlayers($playersStubTeamTwo, delay, delayInc);

            setTimeout (function() {
                return anim.dropPlayers($playersTeamOne, delay, delayInc);
            }, 1000);

        },
        startLoading: function() {
            var images, key, ref, val;
            images = [];
            ref = data.players.teamOne && data.players.teamTwo;
            for (key in ref) {
                val = ref[key];
                images.push(val.avatarImgUrl);
            }
            return dom.preloadImages(images);
        },
        endLoading: function() {
            return anim.fadeOutDir($loading, 300, 1000, 0, -20);
        },
        arrangePlayers: function() {
            $stubPlayers.each(function() {
                var $el;
                $el = $(this);
                return $el.velocity({
                    translateX: parseInt($el.attr('data-x')),
                    translateY: parseInt($el.attr('data-y'))
                });
            });
            return $players.each(function() {
                var $el;
                $el = $(this);
                return $el.velocity({
                    translateX: parseInt($el.attr('data-x')),
                    translateY: parseInt($el.attr('data-y'))
                });
            });
        },
        focusPlayer: function($el) {
            var shiftY;
            data = $el.data();
            shiftY = (data.y - 160) / 2;
            if (data.y - 160 < 0) {
                shiftY = (data.y - 160);
            }
            $('.js-player[data-side="' + state.curSide() + '"]').not('.active').each(function() {
                var $unfocus;
                $unfocus = $(this);
                return anim.fadeOutDir($unfocus, 300, 0, 0, 0, 0, null, 0.2);
            });
            $world.velocity({
                translateX: pos.world.baseX - (data.x - pos.ground.x / 2),
                translateY: pos.world.baseY - shiftY + 20,
                translateZ: pos.world.baseZ
            }, 600);
            $terrain.velocity({
                opacity: 0.65
            }, 600);
            return this.showPlayerCard($el, 600, 600);
        },
        unfocusPlayer: function() {
            var $el;
            $el = $('.js-player.active');
            data = $el.data();
            anim.fadeInDir($('.js-player[data-side="' + state.curSide() + '"]').not('.active'), 300, 300, 0, 0, 0, null, 0.2);
            $el.removeClass('active');
            $world.velocity({
                translateX: pos.world.baseX,
                translateY: pos.world.baseY,
                translateZ: pos.world.baseZ
            }, 600);
            $terrain.velocity({
                opacity: 1
            }, 600);
            return this.hidePlayerCard($el, 600, 600);
        },
        hidePlayerCard: function($el, dur, delay) {
            var $card, $image;
            $card = $el.find('.player__card');
            $image = $el.find('.player__img');
            $image.velocity({
                translateY: 0
            }, 300);
            anim.fadeInDir($el.find('.player__label', 200, delay));
            return anim.fadeOutDir($card, 300, 0, 0, -100);
        },
        showPlayerCard: function($el, dur, delay) {
            var $card, $image;
            $card = $el.find('.player__card');
            $image = $el.find('.player__img');
            $image.velocity({
                translateY: '-=150px'
            }, 300);
            anim.fadeOutDir($el.find('.player__label', 200, delay));
            return anim.fadeInDir($card, 300, 200, 0, 100);
        },
        switchSides: function() {
            var $new, $old, delay, delayInc, $newStub, $oldStub;
            delay = 0;
            delayInc = 20;
            $old = $playersTeamOne;
            $new = $playersTeamTwo;
            $oldStub = $playersStubTeamTwo;
            $newStub = $playersStubTeamOne;
            if (!state.teamOne) {
                $old = $playersTeamTwo;
                $new = $playersTeamOne;
                $oldStub = $playersStubTeamOne;
                $newStub = $playersStubTeamTwo;
            }
            state.swapSides();
            $old.each(function() {
                var $el;
                $el = $(this);
                anim.fadeOutDir($el, 200, delay, 0, -60, 0);
                anim.fadeOutDir($el.find('.player__label'), 200, delay + 700);
                return delay += delayInc;
            });
            $oldStub.each(function() {
                var $el;
                $el = $(this);
                anim.fadeOutDir($el, 200, delay, 0, -60, 0);
                anim.fadeOutDir($el.find('.player__placeholder'), 200, delay + 700);
                return delay += delayInc;
            });
            $terrainHolder.velocity({
                rotateY: '+=180deg'
            }, {
                delay: 150,
                duration: 1200
            });
            anim.dropStubPlayers($newStub, 1500, 30);
            setTimeout (function() {
                return anim.dropPlayers($new, 1500, 30);
            }, 1000);
        }
    };

    anim = {
        fadeInDir: function($el, dur, delay, deltaX, deltaY, deltaZ, easing, opacity) {
            if (deltaX == null) {
                deltaX = 0;
            }
            if (deltaY == null) {
                deltaY = 0;
            }
            if (deltaZ == null) {
                deltaZ = 0;
            }
            if (easing == null) {
                easing = null;
            }
            if (opacity == null) {
                opacity = 0;
            }
            $el.css('display', 'block');
            $el.velocity({
                translateX: '-=' + deltaX,
                translateY: '-=' + deltaY,
                translateZ: '-=' + deltaZ
            }, 0);
            return $el.velocity({
                opacity: 1,
                translateX: '+=' + deltaX,
                translateY: '+=' + deltaY,
                translateZ: '+=' + deltaZ
            }, {
                easing: easing,
                delay: delay,
                duration: dur
            });
        },
        fadeOutDir: function($el, dur, delay, deltaX, deltaY, deltaZ, easing, opacity) {
            var display;
            if (deltaX == null) {
                deltaX = 0;
            }
            if (deltaY == null) {
                deltaY = 0;
            }
            if (deltaZ == null) {
                deltaZ = 0;
            }
            if (easing == null) {
                easing = null;
            }
            if (opacity == null) {
                opacity = 0;
            }
            if (!opacity) {
                display = 'none';
            } else {
                display = 'block';
            }
            return $el.velocity({
                opacity: opacity,
                translateX: '+=' + deltaX,
                translateY: '+=' + deltaY,
                translateZ: '+=' + deltaZ
            }, {
                easing: easing,
                delay: delay,
                duration: dur
            }).velocity({
                opacity: opacity,
                translateX: '-=' + deltaX,
                translateY: '-=' + deltaY,
                translateZ: '-=' + deltaZ
            }, {
                duration: 0,
                display: display
            });
        },
        dropPlayers: function($els, delay, delayInc) {
            return $els.each(function() {
                var $el;
                $el = $(this);
                $el.css({
                    display: 'block',
                    opacity: 0
                });
                anim.fadeInDir($el, 800, delay, 0, 50, 0, 'spring');
                anim.fadeInDir($el.find('.player__label'), 200, delay + 250);
                return delay += delayInc;

            });
        },
        dropStubPlayers: function($els, delay, delayInc) {
            return $els.each(function() {
                var $el;
                $el = $(this);
                $el.css({
                    display: 'block',
                    opacity: 0
                });
                anim.fadeInDir($el, 800, delay, 0, 50, 0, 'spring');
                anim.fadeInDir($el.find('.player__placeholder'), 200, delay + 250);
                return delay += delayInc;

            });
        }
    };


    init = function() {

        $stage = $('.js-stage');
        $world = $('.js-world');
        $switchBtn = $('.js-switch');
        $loadBtn = $('.js-load');
        $heading = $('.js-heading');
        $switcher = $('.js-switcher');
        $closeBtn = $('.js-close');
        $subHeading = $('.js-subheading');
        $terrain = $('.js-terrain');
        $terrainHolder = $('.terrain-holder');
        $team = $('.js-team');
        $stubTeam = $('.js-stub-team');
        $teamListTeamOne = $('.js-team-teamOne');
        $teamListStubTeamTwo = $('.js-stub-team-teamTwo');
        $loading = $('.loading-info-cont');


        var loadUrl = $("input[name=load-url]").val();
        var gameId = $("input[name=game-id]").val();
        var csrfToken = $("input[name=csrf-token]").val();
        var params = {};
        params["csrf-token"] = csrfToken;
        params["game-id"] = gameId;

        anim.fadeInDir($loading, 500, 0, 0, -20);


        var request = $.ajax({
            url: loadUrl,
            type: "POST",
            data: JSON.stringify(params),
            dataType: "json",
            contentType: "application/json; charset=utf-8"

        });
        console.log("Request sent");

        request.done(function( reply ) {
            console.log("request ended");
            console.log(reply); console.log("Success?");
            loader.populateMatchData(reply);

            dom.addPlayers('teamOne');
            dom.addPlayers('teamTwo');

            scenes.preLoad();
            scenes.arrangePlayers();

            events.attachAll();

            return scenes.startLoading();

        });
        request.fail(function(jqXHr, textStatus, errorThrown){
            console.log("ERROR!");
            console.log(jqXHr);
            console.log(textStatus);
            console.log(errorThrown);
        });


    };


    $(document).ready(function() {
        return init();
    });

}).call(this);