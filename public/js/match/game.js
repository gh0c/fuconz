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
        {x: 460, y: 205},
        {x: 80, y: 140},
        {x: 580, y: 130},
        {x: 320, y: 125}
    ].reverse();
    positions6playersTeamOne = [
        {x: 320, y: 225},
        {x: 170, y: 205},
        {x: 450, y: 190},
        {x: 590, y: 120},
        {x: 70, y: 140},
        {x: 320, y: 120}
    ].reverse();

    positions5playersTeamTwo = [
        {x: 320, y: -15},
        {x: 230, y: 25},
        {x: 410, y: 25},
        {x: 525, y: 60},
        {x: 130, y: 55}
    ].reverse();

    positions6playersTeamTwo = [
        {x: 320, y: -15},
        {x: 200, y: 25},
        {x: 440, y: 25},
        {x: 320, y: 25},
        {x: 515, y: 60},
        {x: 110, y: 60}
    ].reverse();



    var positionsTeamOne = {};
    positionsTeamOne["6"] = positions6playersTeamOne;
    positionsTeamOne["5"] = positions5playersTeamOne;
    var positionsTeamTwo = {};
    positionsTeamTwo["5"] = positions5playersTeamTwo;
    positionsTeamTwo["6"] = positions6playersTeamTwo;

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
        populateMatchData: function() {



            var dom_PlayersTeamOne = $(".stage .team.js-team .player.js-player.teamOne");
            var dom_PlayersTeamTwo = $(".stage .team.js-team .player.js-player.teamTwo");

            var dom_StubPlayersTeamOne = $(".stage .team.js-stub-team .stub-player.js-stub-player.teamOne");
            var dom_StubPlayersTeamTwo = $(".stage .team.js-stub-team .stub-player.js-stub-player.teamTwo");

            var numOfPlayersInTeamOne = dom_PlayersTeamOne.size();
            var numOfPlayersInTeamTwo = dom_PlayersTeamTwo.size();

            var playersInTeamOneInd = numOfPlayersInTeamOne.toString();
            if(numOfPlayersInTeamOne != "5" && numOfPlayersInTeamOne != "6") {
                playersInTeamOneInd = "5";
            }
            var playersInTeamTwoInd = numOfPlayersInTeamTwo.toString();
            if(numOfPlayersInTeamTwo != "5" && numOfPlayersInTeamTwo != "6") {
                playersInTeamTwoInd = "5";
            }

            dom_PlayersTeamOne.each(function(index) {
                data.players.teamOne.push({
                    name: $(this).data("name"),
                    origin: 'Brazil',
                    height: '1.88m',
                    shirt: '3',
                    pos: 'Defence',
                    dob: '32',
                    goals: 0,
                    games: 34,
                    side: "teamOne",
                    x: positionsTeamOne[playersInTeamOneInd][index].x,
                    y: positionsTeamOne[playersInTeamOneInd][index].y,
                    avatarExists: $(this).data("avatar-exists"),
                    avatarImgUrl: $(this).data("avatar-url")
                });
                $(this).attr("data-x", positionsTeamOne[playersInTeamOneInd][index].x);
                $(this).attr("data-y", positionsTeamOne[playersInTeamOneInd][index].y);
                $team.append($(this));
            });
            dom_StubPlayersTeamOne.each(function(index) {
                data.stubPlayers.teamOne.push({
                    name: $(this).data("name"),
                    side: "teamOne",
                    x: positionsTeamTwo[playersInTeamOneInd][index].x,
                    y: positionsTeamTwo[playersInTeamOneInd][index].y

                });
                $(this).attr("data-x", positionsTeamTwo[playersInTeamOneInd][index].x);
                $(this).attr("data-y", positionsTeamTwo[playersInTeamOneInd][index].y);
                $stubTeam.append($(this));
            });

            dom_PlayersTeamTwo.each(function(index) {
                data.players.teamTwo.push({
                    name: $(this).data("name"),
                    origin: 'Brazil',
                    height: '1.88m',
                    shirt: '3',
                    pos: 'Defence',
                    dob: '32',
                    goals: 0,
                    games: 34,
                    side: "teamTwo",
                    x: positionsTeamOne[playersInTeamTwoInd][index].x,
                    y: positionsTeamOne[playersInTeamTwoInd][index].y,
                    avatarExists: $(this).data("avatar-exists"),
                    avatarImgUrl: $(this).data("avatar-url")
                });
                $(this).attr("data-x", positionsTeamOne[playersInTeamTwoInd][index].x);
                $(this).attr("data-y", positionsTeamOne[playersInTeamTwoInd][index].y);
                $team.append($(this));
            });
            dom_StubPlayersTeamTwo.each(function(index) {
                data.stubPlayers.teamTwo.push({
                    name: $(this).data("name"),
                    side: "teamTwo",
                    x: positionsTeamTwo[playersInTeamTwoInd][index].x,
                    y: positionsTeamTwo[playersInTeamTwoInd][index].y

                });
                $(this).attr("data-x", positionsTeamTwo[playersInTeamTwoInd][index].x);
                $(this).attr("data-y", positionsTeamTwo[playersInTeamTwoInd][index].y);
                $stubTeam.append($(this));
            });
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
        addPlayers: function() {
            $players = $('.js-player');
            $playersImages = $('.js-player .player__img');
            $playersLabels = $('.js-player span.player__label');
            $playersTeamOne = $('.js-player[data-side="teamOne"]');
            $playersTeamTwo = $('.js-player[data-side="teamTwo"]');

            $stubPlayers = $('.js-stub-player');
            $playersStubTeamOne = $('.js-stub-player[data-side="teamOne"]');
            $playersStubTeamTwo = $('.js-stub-player[data-side="teamTwo"]');
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
                var $elem;
                e.preventDefault();
                $elem = $(this).parent();
                if ($('.active').length) {
//                    return false;
                    scenes.unfocusPlayer();

                } else {
                    $elem.addClass('active');
                    scenes.focusPlayer($elem);
                    return setTimeout((function() {
                        return events.attachClose();
                    }), 1);
                }

            });
            return $playersLabels.on('click', function(e) {
                var $elem;
                e.preventDefault();
                $elem = $(this).parent().parent();
                if ($('.active').length) {
//                    return false;
                    scenes.unfocusPlayer();

                } else {
                    $elem.addClass('active');
                    scenes.focusPlayer($elem);
                    return setTimeout((function() {
                        return events.attachClose();
                    }), 1);
                }
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
            $('main').css("pointer-events", "initial");
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
            anim.fadeInDir($heading, 300, delay + 600, 'block', 0, 30);
            anim.fadeInDir($subHeading, 300, delay + 800, 'block', 0, 30);

            anim.fadeInDir($teamListStubTeamTwo, 300, delay + 800, 'block', 0, 30);
            anim.fadeInDir($teamListTeamOne, 300, delay + 800, 'block', 0, 30);

            anim.fadeInDir($switcher, 400, delay + 900, 'block', 0, 30);
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
            return anim.fadeOutDir($loading, 300, 1000, 'block', 0, -20);
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
                return anim.fadeOutDir($unfocus, 300, 0, 'block', 0, 0, 0, null, 0.2, 1);
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
            var $elem;
            $elem = $('.js-player.active');
//            data = $el.data();
            anim.fadeInDir($('.js-player[data-side="' + state.curSide() + '"]').not('.active'), 300, 300, 'block', 0, 0, 0, null, 0.2, 2);
            $elem.removeClass('active');
            $world.velocity({
                translateX: pos.world.baseX,
                translateY: pos.world.baseY,
                translateZ: pos.world.baseZ
            }, 600);
            $terrain.velocity({
                opacity: 1
            }, 600);
            this.hidePlayerCard($elem, 600, 600);
        },
        hidePlayerCard: function($el, dur, delay) {
            var $card, $image;
            $card = $el.find('.player__card');
            $image = $el.find('.player__img');
            $image.velocity({
                translateY: 0
            }, 300);
            anim.fadeInDir($el.find('.player__label'), 200, delay, 'inline-block');
            return anim.fadeOutDir($card, 300, 0, 'block', 0, -100);
        },
        showPlayerCard: function($el, dur, delay) {
            var $card, $image;
            $card = $el.find('.player__card');
            $image = $el.find('.player__img');
            $image.velocity({
                translateY: '-=172px'
            }, 300);
            anim.fadeOutDir($el.find('.player__label'), 100, 0, 'inline-block');
            anim.fadeInDir($card, 300, 100, 'block', 0, 100);
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
                anim.fadeOutDir($el, 200, delay, 'block', 0, -60, 0);
                anim.fadeOutDir($el.find('.player__label'), 200, delay + 700, 'inline-block');
                return delay += delayInc;
            });
            $oldStub.each(function() {
                var $el;
                $el = $(this);
                anim.fadeOutDir($el, 200, delay, 0, 'block', -60, 0);
                anim.fadeOutDir($el.find('.player__placeholder'), 200, delay + 700, 'inline-block');
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
        fadeInDir: function($el, dur, delay, displayStyle, deltaX, deltaY, deltaZ, easing, opacity, zIndex) {
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
            $el.css('display', displayStyle);
            if (zIndex != null) {
                $el.css('z-index', zIndex);
            }
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
        fadeOutDir: function($el, dur, delay, displayStyle, deltaX, deltaY, deltaZ, easing, opacity, zIndex) {
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
                display = displayStyle;
            }
            if (zIndex != null) {
                $el.css('z-index', zIndex);
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

                anim.fadeInDir($el, 800, delay, 'block', 0, 100, 0, 'spring');
                anim.fadeInDir($el.find('.player__label'), 200, delay + 250, 'inline-block');
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
                anim.fadeInDir($el, 800, delay, 'block', 0, 50, 0, 'spring');
                anim.fadeInDir($el.find('.player__placeholder'), 200, delay + 250, 'block');
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

        anim.fadeInDir($loading, 500, 0, 'block', 0, -20);

        loader.populateMatchData();
        dom.addPlayers();


        scenes.preLoad();
        scenes.arrangePlayers();

        events.attachAll();

        return scenes.startLoading();

    };


    $(document).ready(function() {
        return init();
    });

}).call(this);