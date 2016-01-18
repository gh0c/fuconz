
(function() {

    var $bodyEl = $("body"),
        $content = $( '.content-wrap' ),
        $openbtn = $( '#open-chat-menu-button' ),
        $closebtn = $( '#close-chat-overlay-button' ),
        chatMenuIsOpen = false;

    function init() {
        initEvents();
    }

    function initEvents() {

        $openbtn.click(function() {
            toggleMenu();
        });


        if( $closebtn ) {
            $closebtn.click(function() {
                toggleMenu();
            });
        }



        // close the menu element if the target it´s not the menu element
        // or one of its descendants..
        $content.click(function(ev) {
            var target = ev.target;
            if( chatMenuIsOpen &&
                !($bodyEl.find(".chat-overlay-wrap.disabled-button").length > 0) &&
                target !== $openbtn[0] &&
                !($openbtn.has(target).length > 0) ) {
                toggleMenu();
            }
        });
    }

    function toggleMenu() {
//        if( isOpen ) {
//            classie.remove( bodyEl, 'show-menu' );
//        }
//        else {
//            classie.add( bodyEl, 'show-menu' );
//        }
        $bodyEl.toggleClass("show-menu");
        chatMenuIsOpen = !chatMenuIsOpen;
    }

    init();

})();


