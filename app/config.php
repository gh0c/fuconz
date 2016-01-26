<?php

use app\helpers\Configuration;

// Session keys
Configuration::write('session.user_logged_in', 's_user_logged_in');
Configuration::write('session.logged_user', 's_logged_user');
Configuration::write('session.logged_user_email', 's_logged_user_email');
Configuration::write('session.logged_user_id', 's_logged_user_id');

Configuration::write('session.admin_logged_in', 's_admin_logged_in');
Configuration::write('session.logged_admin', 's_logged_admin');
Configuration::write('session.logged_admin_email', 's_logged_user_email');
Configuration::write('session.logged_admin_id', 's_logged_admin_id');

Configuration::write('session.status.error', 's_status_error');
Configuration::write('session.status.neutral', 's_status_neutral');
Configuration::write('session.status.success', 's_status_success');

Configuration::write('session.admin.status.error', 's_admin_status_error');
Configuration::write('session.admin.status.neutral', 's_admin_status_neutral');
Configuration::write('session.admin.status.success', 's_admin_status_success');

Configuration::write('session.csrf_token', 'csrf-token');

// Cookies (rememberMe)

Configuration::write('cookie.user_remember_me', 'registered_user_rm');
Configuration::write('cookie.admin_remember_me', 'admin_rm');
Configuration::write('cookie.path', '/');
Configuration::write('cookie.user_remember_me_duration', 60*24*60*60);
Configuration::write('cookie.admin_remember_me_duration', 60*24*60*60);
// Paths

Configuration::write('path.user.avatar', 'public/images/registered-users');



Configuration::write('repeating.interval.none', 'none');
Configuration::write('repeating.interval.day', 'day');
Configuration::write('repeating.interval.week', 'week');
Configuration::write('repeating.interval.month.1', 'month.1');
Configuration::write('repeating.interval.month.2', 'month.2');

Configuration::write('calendar.date.offset.day.left', -4);
Configuration::write('calendar.date.offset.day.right', 11);
Configuration::write('calendar.date.offset.day.std', 11);


// Images subpaths

Configuration::write('path.images.full', 'full');
Configuration::write('path.images.t1', 'thumb1');
Configuration::write('path.images.t2', 'thumb2');
Configuration::write('path.images.t3', 'thumb3');


// Avatar

Configuration::write('avatar.minimum.width', 55);
Configuration::write('avatar.minimum.height', 55);

Configuration::write('user.avatar.t1.height', 55);
Configuration::write('user.avatar.t1.width', 55);

Configuration::write('user.avatar.t2.height', 170);
Configuration::write('user.avatar.t2.width', 170);

Configuration::write('user.avatar.full.width', 700);
Configuration::write('user.avatar.full.height', 650);


Configuration::write('jpeg.quality', 100);


// Database defaults
Configuration::write('db.sex.male', 'M');
Configuration::write('db.sex.female', 'F');



Configuration::write('password.reset.duration', 2*60*60);


// Emoticons

// Standard (20x20)

Configuration::write("emoticons.std", array(
    'smile' => array(
        'codes' => array(
            ':-)', '=)', ':)', ':=)'
        )
    ),
    'sad-smile' => array(
        'codes' => array(
            ':-(', '=(', ':(', ':=('
        )
    ),
    'big-smile' => array(
        'codes' => array(
            ':D', ':-D', 'xD', ':=D', '=D'
        )
    ),
    'cool' => array(
        'codes' => array(
            '8)', '8=)', '8-)', 'B)', 'B=)', 'B-)', '(cool)'
        )
    ),
    'surprised' => array(
        'codes' => array(
            ':O', ':-O', '=O', ':=O'
        )
    ),
    'wink' => array(
        'codes' => array(
            ';)', ';-)', ';=)', ';D', ';-D', ';=D'
        )
    ),
    'crying' => array(
        'codes' => array(
            ';(', ';-(', ';=('
        )
    ),
    'speechless' => array(
        'codes' => array(
            ':|', ':=|', ':-|'
        )
    ),'kiss' => array(
        'codes' => array(
            ':*', ':=*', ':-*'
        )
    ),
    'tongue-out' => array(
        'codes' => array(
            ':P', ':=P', ':-P'
        )
    ),
    'heart' => array(
        'codes' => array(
            '<3', '(L)', '(H)'
        )
    ),
    'angry' => array(
        'codes' => array(
            ':@', '=@', ':=@', ':-@'
        )
    ),
    'yes' => array(
        'codes' => array(
            '(y)', '(yes)'
        )
    ),
    'no' => array(
        'codes' => array(
            '(n)', '(no)'
        )
    ),
    'rofl' => array(
        'codes' => array(
            '(rofl)'
        )
    ),
    'beer' => array(
        'codes' => array(
            '(beer)'
        )
    ),
    'party' => array(
        'codes' => array(
            '(party)'
        )
    ),
    'sleepy' => array(
        'codes' => array(
            '|=)', 'I=)', '|)', 'I)', '|-)', 'I-)'
        )
    ),
    'worried' => array(
        'codes' => array(
            ':S', ':-S', '=S', ':=S'
        )
    )
));


Configuration::write("emoticons.big", array(
    'wink' => array(
        'codes' => array(
            '[[wink]]'
        )
    ),
    'angry' => array(
        'codes' => array(
            '[[angry]]', '[[mad]]'
        )
    ),
    'hilarious' => array(
        'codes' => array(
            '[[lol]]', '[[hilarious]]'
        )
    ),
    'wtf' => array(
        'codes' => array(
            '[[wtf]]', '[[what-the-fuck]]'
        )
    ),
    'wierdo' => array(
        'codes' => array(
            '[[wierdo]]'
        )
    ),
    'evil-plan' => array(
        'codes' => array(
            '[[evil-plan]]'
        )
    ),
    'mad-in-love' => array(
        'codes' => array(
            '[[mad-in-love]]'
        )
    ),
    'embarassed' => array(
        'codes' => array(
            '[[embarassed]]'
        )
    ),
    'oh-no' => array(
        'codes' => array(
            '[[oh-no]]'
        )
    ),
    'cool' => array(
        'codes' => array(
            '[[cool]]'
        )
    ),
    'geek' => array(
        'codes' => array(
            '[[geek]]', '[[nerdy]]'
        )
    ),
    'frown' => array(
        'codes' => array(
            '[[frown]]'
        )
    ),
    'furious' => array(
        'codes' => array(
            '[[furious]]'
        )
    ),
    'doh' => array(
        'codes' => array(
            '[[doh]]'
        )
    ),
    'yawn' => array(
        'codes' => array(
            '[[yawn]]', '[[sleepy]]'
        )
    ),
    'cry' => array(
        'codes' => array(
            '[[cry]]'
        )
    ),
    'grimace' => array(
        'codes' => array(
            '[[grimace]]'
        )
    ),
    'whoopsy' => array(
        'codes' => array(
            '[[oops]]', '[[whoopsy]]'
        )
    ),
    'rose-for-you' => array(
        'codes' => array(
            '[[rose-for-you]]'
        )
    ),
    'make-face' => array(
        'codes' => array(
            '[[make-face]]'
        )
    ),
    'running-eyes' => array(
        'codes' => array(
            '[[running-eyes]]'
        )
    ),
    'very-sad' => array(
        'codes' => array(
            '[[very-sad]]'
        )
    ),
    'are-you-kidding-me' => array(
        'codes' => array(
            '[[r-u-kidding-me]]'
        )
    ),
    'exhausting' => array(
        'codes' => array(
            '[[exhausting]]'
        )
    ),
    'this-is-sparta' => array(
        'codes' => array(
            '[[this-is-sparta]]', '[[very-angry]]'
        )
    )

));


Configuration::write("emoticons.meme", array(
    'trollface' => array(
        'codes' => array(
            '#troll#', '#trollface#', '#troll-face#'
        )
    ),
    'gtfo' => array(
        'codes' => array(
            '#gtfo#', '#get-the-fuck-out#'
        )
    ),
    'lololo' => array(
        'codes' => array(
            '#lololo#', '#lol#'
        )
    ),
    'jackie-wtf' => array(
        'codes' => array(
            '#wtf#', '#jackie-wtf#'
        )
    ),
    'i-see' => array(
        'codes' => array(
            '#i-see#', '#i-see-what-you-did-there#', '#i-c-what-u-did-there#'
        )
    ),
    'stoned' => array(
        'codes' => array(
            '#stoned#', '#smoke-trees#'
        )
    ),
    'not-okay' => array(
        'codes' => array(
            '#not-okay#'
        )
    ),
    'thumbs-up' => array(
        'codes' => array(
            '#thumbs-up#'
        )
    ),
    'no-angry' => array(
        'codes' => array(
            '#no!#', '#no#'
        )
    ),
    'hitler' => array(
        'codes' => array(
            '#hitler#'
        )
    ),
    'badass' => array(
        'codes' => array(
            '#got-a-badass#', '#got-a-badass-over-here#', '#we-got-a-badass-over-here#'
        )
    ),
    'cuteness' => array(
        'codes' => array(
            '#cuteness#', '#cuteness-overload#'
        )
    ),
    'derpina' => array(
        'codes' => array(
            '#derpina#'
        )
    ),
    'derpina-rage' => array(
        'codes' => array(
            '#derpina-fffuuu#', '#derpina-rage#', '#female-rage#'
        )
    ),
    'ahaha' => array(
        'codes' => array(
            '#ahaha#'
        )
    ),
    'close-enough' => array(
        'codes' => array(
            '#close-enough#'
        )
    ),
    'y-u-no' => array(
        'codes' => array(
            '#y-u-no#', '#why-you-no#'
        )
    ),
    'if-you-know-what-i-mean' => array(
        'codes' => array(
            '#if-u-know#', '#if-u-know-what-i-mean#', '#if-you-know-what-i-mean#'
        )
    ),
    'nerd' => array(
        'codes' => array(
            '#nerd#'
        )
    ),
    'mother-of-god' => array(
        'codes' => array(
            '#mother-of-god#'
        )
    ),
    'surprised' => array(
        'codes' => array(
            '#surprised#'
        )
    ),
    'poker-face' => array(
        'codes' => array(
            '#poker-face#'
        )
    ),
    'pedobear' => array(
        'codes' => array(
            '#pedobear#'
        )
    ),
    'not-bad-obama' => array(
        'codes' => array(
            '#not-bad#', '#not-bad-obama#'
        )
    ),
    'scared-yao' => array(
        'codes' => array(
            '#ewww#', '#scared-yao#'
        )
    ),
    'hmm-questioning' => array(
        'codes' => array(
            '#hm-hm#'
        )
    ),
    'reservoir-fuuus' => array(
        'codes' => array(
            '#reservoir-dogs#'
        )
    ),
    'fffuuu' => array(
        'codes' => array(
            '#fffuuu#', '#ffffuu#', '#ffuuuu#'
        )
    ),
    'suspicious' => array(
        'codes' => array(
            '#suspicious#'
        )
    ),
    'baby-troll' => array(
        'codes' => array(
            '#baby-troll#'
        )
    ),
    'cereal-guy' => array(
        'codes' => array(
            '#cereal-guy#', '#newspaper-guy#'
        )
    ),
    'not-impressed' => array(
        'codes' => array(
            '#not-impressed#'
        )
    ),
    'forever-alone' => array(
        'codes' => array(
            '#aaaaa#', '#forever-alone#'
        )
    ),
    'genius' => array(
        'codes' => array(
            '#genius#'
        )
    ),
    'grandma' => array(
        'codes' => array(
            '#grandma#'
        )
    ),
    'hmm-determined' => array(
        'codes' => array(
            '#determined#', '#hm-hm-determined#'
        )
    ),
));
