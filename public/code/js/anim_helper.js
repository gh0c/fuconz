/**
 * Created by gh0c on 16.12.15..
 */

var anim;

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
    }
};