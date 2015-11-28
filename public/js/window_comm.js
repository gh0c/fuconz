
    function updateMessagesLabels(num) {
        var headerNumberLabel = $("#header-number-of-unread");
        if(headerNumberLabel[0]) {
            headerNumberLabel.text(num.toString());
            prependToTitle('(' + num.toString() + ') ');
            if(num < 1) {
                headerNumberLabel.removeClass("unread-exists");
                headerNumberLabel.prevAll("i.fa").removeClass("unread-exists");
                $("#unread-messages-header-alarm").remove();
                prependToTitle('');
            }
        }
    }



    $(function() {
        if (Intercom.supported) {
            var intercom = Intercom.getInstance();

            intercom.on('numOfMessagesChange', function(data) {
                updateMessagesLabels(data.numberOfUnread);
            });

        }
    });

