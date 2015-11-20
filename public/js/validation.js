var statusText = $(".status-container.live > .status");
var panel = $("#info-panel");
var formSubmittingActive = true;

function myFunn() {
    $(".submitter-cont .submitter span").text(carName)
}

function haltFormSubmitting() {
    if (!formSubmittingActive) {
        return false;
    }
    else {
        formSubmittingActive = false;
        $(".submitter-cont .submitter").addClass("disabledbutton");
        $(".submitter-cont .submitter").css("cursor", "not-allowed");
        $(".submitter-cont .submitter").css("pointer-events", "none");
        return true;
    }
}





function validateAndSubmitLogin(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");

    setTimeout (function() {
        if (myForm["username"].value == "" ) {
            if (!errorStatus("Niste upisali korisničko ime!", myForm["email"])) {
                return false;
            }
        }
        if (myForm["password"].value == "") {
            if (!errorStatus("Niste upisali lozinku!", myForm["password"])) {
                return false;
            }
        }
        infoPanelSuccess();

        myForm.submit();
    }, 1000);
}


function validateAndSubmitRegistration(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }

    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    setTimeout (function() {


        if (myForm["email"].value == "" ) {
            if (!errorStatus("Niste upisali e-mail adresu!", myForm["email"])) {
                return false;
            }
        }
        else {
            var atpos = myForm["email"].value.indexOf("@");
            var dotpos = myForm["email"].value.lastIndexOf(".");
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= myForm["email"].value.length) {
                if (!errorStatus("Neispravna e-mail adresa!", myForm["email"])) {
                    return false;
                }
            }
        }

        if (myForm["username"].value == "" ) {
            if (!errorStatus("Niste upisali korisničko ime (nadimak)!", myForm["username"])) {
                return false;
            }
        }
        if(!(/^(\w|\s|[^\x00-\x7F]|-)+$/.test(myForm["username"].value))) {
            if (!errorStatus("Korisničko ime smije sadržavati samo brojke, slova i crtice.", myForm["username"])) {
                return false;
            }
        }

        if (myForm["new-password"].value == "") {
            if (!errorStatus("Niste upisali lozinku!", myForm["new-password"])) {
                return false;
            }
        }


        if (myForm["new-password-repeated"].value == "") {
            if (!errorStatus("Niste ponovili lozinku!", myForm["new-password-repeated"])) {
                return false;
            }
        }

        if (myForm["new-password-repeated"].value != myForm["new-password"].value) {
            if (!errorStatus("Lozinka nije jednaka u oba predviđena polja forme!",
                myForm["new-password"])) {
                return false;
            }
        }
        infoPanelSuccess();

        myForm.submit();

    }, 1000);

}




function validateAndSubmitProfileDataChange(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }

    var myForm = document.forms[formName];

    expandInfoPanel("Provjera...");


    setTimeout (function() {


        if (myForm["email"].value == "" ) {
            if (!errorStatus("Niste upisali e-mail adresu!", myForm["email"])) {
                return false;
            }
        }
        else {
            var atpos = myForm["email"].value.indexOf("@");
            var dotpos = myForm["email"].value.lastIndexOf(".");
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= myForm["email"].value.length) {
                if (!errorStatus("Neispravna e-mail adresa!", myForm["email"])) {
                    return false;
                }
            }
        }

        infoPanelSuccess();

        myForm.submit();

    }, 1000);

}



function validateAndSubmitPassChange(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    setTimeout (function() {
        if (myForm["old-password"].value == "") {
            if (!errorStatus("Niste upisali trenutnu lozinku!", myForm["old-password"])) {
                return false;
            }
        }

        if (myForm["new-password"].value == "") {
            if (!errorStatus("Niste upisali novu lozinku!", myForm["new-password"])) {
                return false;
            }
        }

        if (myForm["new-password-repeated"].value == "") {
            if (!errorStatus("Niste ponovili novu lozinku!", myForm["new-password-repeated"])) {
                return false;
            }
        }

        if (myForm["new-password-repeated"].value != myForm["new-password"].value) {
            if (!errorStatus("Nova lozinka nije jednaka u oba predviđena polja forme!",
                myForm["new-password"])) {
                return false;
            }
        }
        infoPanelSuccess();

        myForm.submit();

    }, 1000);
}



function validateAndSubmitPassReset(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }

    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    setTimeout (function() {
        if (myForm["email"].value == "" ) {
            if (!errorStatus("Niste upisali e-mail adresu!", myForm["email"])) {
                return false;
            }
        }
        else {
            var atpos = myForm["email"].value.indexOf("@");
            var dotpos = myForm["email"].value.lastIndexOf(".");
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= myForm["email"].value.length) {
                if (!errorStatus("Neispravna e-mail adresa!", myForm["email"])) {
                    return false;
                }
            }
        }
        if (myForm["new-password"].value == "") {
            if (!errorStatus("Niste upisali novu lozinku!", myForm["new-password"])) {
                return false;
            }
        }

        if (myForm["new-password-repeated"].value == "") {
            if (!errorStatus("Niste ponovili novu lozinku!", myForm["new-password-repeated"])) {
                return false;
            }
        }

        if (myForm["new-password-repeated"].value != myForm["new-password"].value) {
            if (!errorStatus("Nova lozinka nije jednaka u oba predviđena polja forme!",
                myForm["new-password"])) {
                return false;
            }
        }
        infoPanelSuccess();

        myForm.submit();

    }, 1000);

}


function validateAndSubmitAvatarChange(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    $("#user-avatar-change").addClass("disabled-button");

    var myForm = document.forms[formName];


    expandInfoPanel("Provjera...");

    setTimeout (function() {

        if (myForm["avatar-file"].value == "" && !myForm["delete-avatar"].checked && !myForm["use-fb-avatar"].checked) {
            if (!errorStatus("Nije odabrana slika za upload, " +
                "a nije niti označeno samo brisanje starog avatara ili korištenje FB profilne slike!", myForm["avatar-file"])) {
                $("#user-avatar-change").removeClass("disabled-button");
                return false;
            }
        }

        if (myForm["use-fb-avatar"].checked && myForm["fb-id"].value == "") {
            if (!errorStatus("Odabrano je korištenje FB profilne slike kao avatara, ali nije unesen FB identifikator!", myForm["fb-id"])) {
                $("#user-avatar-change").removeClass("disabled-button");
                return false;
            }
        }

        if(myForm["avatar-file"].value != "")
        {
            if(myForm["avatar-file"].files[0]) {
                if(myForm["avatar-file"].files[0].size > 2*1024*1024) {
                    if (!errorStatus("Najveća dopuštena veličina slike je 2 MB. " +
                        "Odaberite manju sliku!", myForm["avatar-file"])) {
                        $("#user-avatar-change").removeClass("disabled-button");
                        return false;
                    }
                }
                var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
                if (myForm["avatar-file"].files[0].type.length) {
                    var fileType = myForm["avatar-file"].files[0].type;
                    if (!acceptFileTypes.test(fileType)) {
                        if (!errorStatus("Neispravan tip slike! (" + fileType +
                            ")\nDopušteno: {jpg, jpeg, png, gif ...}", myForm["avatar-file"])) {
                            $("#user-avatar-change").removeClass("disabled-button");
                            return false;
                        }
                    }
                }
                var acceptExtensions = /(gif|jpe?g|png)$/i;
                var extension = myForm["avatar-file"].files[0].name.substring(
                    myForm["avatar-file"].files[0].name.lastIndexOf('.') + 1);
                if (!acceptExtensions.test(extension)) {

                    if (!errorStatus("Neispravan format slike! (" + extension +
                        ")\nDopušteno: {jpg, jpeg, png, gif ...}", myForm["avatar-file"])) {
                        $("#user-avatar-change").removeClass("disabled-button");
                        return false;
                    }
                }

            }

        }
        if(!myForm["use-fb-avatar"].checked && (!myForm["uploaded-img-hash"] || myForm["uploaded-img-hash"].value === "")) {
            if (!errorStatus("Došlo je do pogreške prilikom procedure nakon uploada. Refreshajte stranicu i pokušajte ponovo")) {
                $("#user-avatar-change").removeClass("disabled-button");
                return false;
            }
        }
        infoPanelSuccess("Sad će, još malo pa je...");

        myForm.submit();

    }, 1000);

}


function validateAndSubmitPassRecovery(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    setTimeout (function() {
        if (myForm["email"].value == "" ) {
            if (!errorStatus("Niste upisali e-mail adresu!", myForm["email"])) {
                return false;
            }
        }
        else {
            var atpos = myForm["email"].value.indexOf("@");
            var dotpos = myForm["email"].value.lastIndexOf(".");
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= myForm["email"].value.length) {
                if (!errorStatus("Neispravna e-mail adresa!", myForm["email"])) {
                    return false;
                }
            }

        }
        infoPanelSuccess();
        myForm.submit();

    }, 1000);

}



function validateAndSubmitUserBooking(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    $("#user-course-booking").addClass("disabled-button");

    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");



    setTimeout (function() {

        var selectedSpans = $("#selected-spans input[name='selected-spans\\[\\]']");
        var selectedSpansLabels = $("#selected-spans input[name = 'selected-spans-description-labels\\[\\]']");
        var selectedSpansLen = $("#selected-spans input[name='selected-spans\\[\\]']").length;
        if (selectedSpansLen < 1) {
            if (!errorStatus("Niste odabrali datum termina!", myForm["selected-spans[]"][selectedSpansLen])) {
                $("#user-course-booking").removeClass("disabled-button");

                return false;
            }
        }
        else {
            for (var i = 0; i < selectedSpansLen; i++) {
                var span = selectedSpans[i];
                if (!(/^\d{4}-\d{2}-\d{2} \d{2}-\d{2} \d+$/.test(span.value))) {
                    if (!errorStatus("Neispravan format odabranog termina!", [selectedSpansLabels[i]])) {
                        $("#user-course-booking").removeClass("disabled-button");

                        return false;
                    }
                }

//                /^\d{4}-\d{2}-\d{2} \d{2}-\d{2} \d+$/.test(span.value)
//
                var day = parseInt(span.value.split(" ")[0].split("-")[2]);
                var month = parseInt(span.value.split(" ")[0].split("-")[1]);
                var year = parseInt(span.value.split(" ")[0].split("-")[0]);

                var hours = parseInt(span.value.split(" ")[1].split("-")[0]);
                var minutes = parseInt(span.value.split(" ")[1].split("-")[1]);

                var courseId = parseInt(span.value.split(" ")[2]);

                var spanDate = new Date();
                var todayDate = new Date();
                var reservationTimeDate = new Date();

                var slot = $("#slot-" + span.value.split(" ")[0] + "-" + span.value.split(" ")[1] + "-" + courseId.toString());


                spanDate.setFullYear(year, month-1, day);
                spanDate.setHours(hours);
                spanDate.setMinutes(minutes);

                if (spanDate < todayDate) {
                    if(!errorStatus("Odabrani termin " + slot.data("description") + " je završen!", [selectedSpansLabels[i]])) {
                        $("#user-course-booking").removeClass("disabled-button");

                        return false;
                    }
                }

                reservationTimeDate.setHours(reservationTimeDate.getHours() + parseInt(parseInt(slot.data("reservation-time"))/3600));
                if (spanDate < reservationTimeDate) {
                    if(!errorStatus("Za odabrani termin " + slot.data("description") + " završeno je vrijeme rezervacije!", [selectedSpansLabels[i]])) {
                        $("#user-course-booking").removeClass("disabled-button");

                        return false;
                    }
                }
            }
        }

        infoPanelSuccess();
        myForm.submit();

    }, 1000);

}




function validateAndSubmitUserPreBooking(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }

    var myForm = document.forms[formName];

    expandInfoPanel("Provjera...");



    setTimeout (function() {
        if (!($("input[name='booking-selection']:checked").length > 0)) {
            if(!errorStatus("Niste odabrali niti jednu od ponuđenih opcija rezervacije/predbilježbe", [myForm["booking-selection"]][0])) {
                return false;
            }
        }

        if (!(parseInt($('input[name=booking-selection]:checked').val()) == 1 ||
            parseInt($('input[name=booking-selection]:checked').val()) == 2)) {
            if(!errorStatus("Neispravan odabir ponuđenih opcija rezervacije/predbilježbe", myForm["booking-selection"][0])) {
                return false;
            }
        }


        infoPanelSuccess();
        myForm.submit();

    }, 1000);

}





function expandInfoPanel(textToAppend, noLoader) {
    panel.find(".status-container").removeClass("successful-validation");

    if (panel.css("display") == "block") {
        $(".warning-msg .fa.warning").remove();
        statusText.parent().removeClass("warning-msg");
    }
    else  {
        panel.slideDown(400);
    }

    if(statusText && panel) {
        $(".pp-icon-spin").remove();

        statusText.text(textToAppend);
        if (!(typeof noLoader !== 'undefined' && noLoader)) {
            var iForGif = document.createElement("i");
            iForGif.className = "pp-icon-spin fa fa-fw fa-spinner fa-spin";
            panel.children().children()[0].appendChild(iForGif);
            var iForGifCont = document.createElement("i");
            iForGifCont.className = "pp-icon-spin fa fa-fw";
            panel.children().children()[0].insertBefore(iForGifCont, panel.children().children()[0].firstChild);
        }
    }
}


function changePanelText(text) {
    statusText.text(text);
}

function infoPanelSuccess(text, noGif) {
    if (panel.css("display") == "block") {
        $(".warning-msg .fa.warning").remove();
        statusText.parent().removeClass("warning-msg");
    }
    else  {
        panel.slideDown(400);
    }

    if(statusText && panel) {
        $(".pp-icon-spin").remove();

        if (!(typeof text !== 'undefined' && text)) {
            statusText.text("");
        } else {
            statusText.text(text);
        }

        if (!(typeof noGif !== 'undefined' && noGif)) {
            var iForGif = document.createElement("i");
            iForGif.className = "pp-icon-spin fa fa-fw fa-spinner fa-spin successful-validation";
            panel.children().children()[0].appendChild(iForGif);
            var iForGifCont = document.createElement("i");
            iForGifCont.className = "pp-icon-spin fa fa-fw";
            panel.children().children()[0].insertBefore(iForGifCont, panel.children().children()[0].firstChild);
        } else {

        }
        panel.find(".status-container").addClass("successful-validation");

    }
}



function errorStatus(errorText, focusElem )
{
    panel.find(".status-container").removeClass("successful-validation");

    $(".pp-icon-spin").remove();

    statusText.parent().addClass("warning-msg");

    warningIcon = document.createElement("i");
    warningIcon.className = "fa fa-warning status warning";

    statusText.parent().prepend(warningIcon);

    warningIcon2 = document.createElement("i");
    warningIcon2.className = "fa status warning";

    statusText.parent().append(warningIcon2);

    var escaped = $('<div>').text(errorText).text();
    statusText.html(escaped.replace(/\n/g, '<br />'));

    enableFormSubmiting();
    if (typeof focusElem !== 'undefined' && focusElem) {
        try {
            focusElem.focus();
        } catch (err) {

        } finally {
            return false;
        }
    }
    return false;
}



function enableFormSubmiting() {
    $(".submitter-cont .submitter").css("cursor", "pointer");
    $(".submitter-cont .submitter").removeClass("disabledbutton");
    $(".submitter-cont .submitter").css("pointer-events", "auto");
    formSubmittingActive = true;

}


// Handle the Ajax response
function submitFinished( response ) {
    response = $.trim( response );

    if ( response == "success" ) {
        alert ("success");
    }
    else  {
		alert(response);
    }
}

