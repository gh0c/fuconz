function shuffle(array) {
    var currentIndex = array.length, temporaryValue, randomIndex ;

    // While there remain elements to shuffle...
    while (0 !== currentIndex) {

        // Pick a remaining element...
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;

        // And swap it with the current element.
        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
    }

    return array;
}

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
        $gameData,
        $team,
        $stubTeam,
        $teamListTeamOne,
        $teamListStubTeamTwo,
        $terrain,
        $terrainHolder,
        $world,
        animation,
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
    $gameData = null;


    // dimensions of field: 40em x 20em = 640px x 320px


    positions6playersTeamTwo = [
        {x: 320, y: -15},
        {x: 200, y: 25},
        {x: 440, y: 25},
        {x: 320, y: 25},
        {x: 515, y: 60},
        {x: 110, y: 60}
    ].reverse();




    positions5playersTeamTwo = [
        {x: 36, y: 19},
        {x: 64, y: 19},
        {x: 18, y: 25},
        {x: 82, y: 25},
        {x: 50, y: 10}
    ];


    var positionsTeamOne = {
        "3" : shuffle([
            {x: 50, y: 62}, // DM/DMC
            {x: 25, y: 50}, // AML
            {x: 75, y: 50}  // AMR
        ]),
        "4" : shuffle([
            {x: 50, y: 78}, // DC
            {x: 18, y: 55}, // ML
            {x: 82, y: 55}, // MR
            {x: 50, y: 43}  // STC
        ]),
        "5" : shuffle([
            {x: 30, y: 74}, // DL
            {x: 70, y: 74}, // DR
            {x: 12, y: 52}, // FWL
            {x: 88, y: 52}, // FWR
            {x: 50, y: 43}  // STC
        ]),
        "6" : shuffle([
            {x: 26, y: 70}, // DL
            {x: 74, y: 70}, // DR
            {x: 50, y: 78}, // SW
            {x: 10, y: 48}, // FWL
            {x: 90, y: 48}, // FWR
            {x: 50, y: 42}  // STC
        ]),
        "7" : shuffle([
            {x: 25, y: 77}, // DL
            {x: 75, y: 77}, // DR
            {x: 50, y: 82}, // SW
            {x: 6, y: 52}, // AML
            {x: 94, y: 52}, // AMR
            {x: 33, y: 42},  // STC
            {x: 67, y: 42}  // STC
        ])
    };

    var positionsTeamTwo = {
        "3" : shuffle([
            {x: 50, y: 10}, // GK
            {x: 35, y: 19}, // DL
            {x: 65, y: 19}  // DR
        ]),
        "4" : shuffle([
            {x: 50, y: 10},  // GK
            {x: 29, y: 20}, // DL
            {x: 71, y: 20}, // DR
            {x: 50, y: 19}  // DC
        ]),
        "5" : shuffle([
            {x: 50, y: 10}, // GK
            {x: 35, y: 20}, // DL
            {x: 65, y: 20}, // DR
            {x: 18, y: 23}, // WBL
            {x: 82, y: 23}  // WBR
        ]),
        "6" : shuffle([
            {x: 50, y: 10}, // GK
            {x: 50, y: 18}, // SW
            {x: 36, y: 20}, // DL
            {x: 64, y: 20}, // DR
            {x: 15, y: 21}, // WBL
            {x: 85, y: 21}  // WBR
        ]),
        "7" : shuffle([
            {x: 50, y: 10}, // GK
            {x: 41, y: 15}, // SW
            {x: 59, y: 15}, // SW
            {x: 31, y: 23}, // DML
            {x: 69, y: 23}, // DMR
            {x: 15, y: 21}, // WBL
            {x: 85, y: 21}  // WBR
        ])
    };

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
            var playersInTeamTwoInd = numOfPlayersInTeamTwo.toString();


            dom_PlayersTeamOne.each(function(index) {
                data.players.teamOne.push({
                    name: $(this).data("name"),
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
            x: 100,
            y: 100
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
                if ($('.active-player').length) {
//                    return false;
                    scenes.unfocusPlayer();

                } else {
                    $elem.addClass('active-player');
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
                if ($('.active-player').length) {
//                    return false;
                    scenes.unfocusPlayer();

                } else {
                    $elem.addClass('active-player');
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
            $gameData.velocity({
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
            $('main').css("pointer-events", "auto");
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

            anim.fadeInDir($gameData, 400, delay + 900, 'block', 0, 30);

            delay += 1200;
            delayInc = 30;

            animation.dropStubPlayers($playersStubTeamTwo, delay, delayInc);

            setTimeout (function() {
                return animation.dropPlayers($playersTeamOne, delay, delayInc);
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
                    left: parseInt($el.attr('data-x')) + '%',
                    top: parseInt($el.attr('data-y')) + '%'
                });
            });
            return $players.each(function() {
                var $el;
                $el = $(this);
                return $el.velocity({
                    left: parseInt($el.attr('data-x')) + '%',
                    top: parseInt($el.attr('data-y')) + '%'
                });
            });
        },
        focusPlayer: function($el) {
            var deltaY, shiftY;
            data = $el.data();

            deltaY = 45 - data.y;
            shiftY = (23.25 + deltaY)/1.55;
//            shiftY = (data.y - 50) / 2;
//            if (data.y - 50 < 0) {
//                shiftY = (data.y - 50);
//            }

            $('.js-player[data-side="' + state.curSide() + '"]').not('.active-player').each(function() {
                var $unfocus;
                $unfocus = $(this);


                return anim.fadeOutDir($unfocus, 300, 0, 'block', 0, 0, 0, null, 0.2, 1);
            });
            $world.velocity({
                translateX: (pos.world.baseX - (data.x - pos.ground.x / 2)) + '%',
                translateY: (pos.world.baseY + shiftY) + '%',
                translateZ: pos.world.baseZ
            }, 600);
            $terrain.velocity({
                opacity: 0.65
            }, 600);
            return this.showPlayerCard($el, 600, 600);
        },
        unfocusPlayer: function() {
            var $elem;
            $elem = $('.js-player.active-player');
//            data = $el.data();
            anim.fadeInDir($('.js-player[data-side="' + state.curSide() + '"]').not('.active-player'), 300, 300, 'block', 0, 0, 0, null, 0.2, 2);
            $elem.removeClass('active-player');
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
            animation.dropStubPlayers($newStub, 1500, 30);
            setTimeout (function() {
                return animation.dropPlayers($new, 1500, 30);
            }, 1000);
        }
    };

    animation = {

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
        $gameData = $('.game-data-holder');
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