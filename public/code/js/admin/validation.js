var statusText = $(".status-container.live > .status");
var panel = $("#info-panel");
var formSubmittingActive = true;


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





function validateAndSubmitAdminLogin(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");

    var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
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
        myForm.submit();
    }, 1000);
}


function validateAndSubmitRegistration(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }

    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
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

        myForm.submit();

    }, 1000);

}



function validateAndSubmitPassChange(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
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
        myForm.submit();

    }, 1000);
}



function validateAndSubmitPassReset(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
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

        myForm.submit();

    }, 1000);

}


function validateAndSubmitAvatarUpload(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];


    expandInfoPanel("Provjera ispravnosti formata slike");

    var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
    setTimeout (function() {


        if (myForm["avatar_file"].value == "" && !myForm["delete_avatar"].checked) {
            if (!errorStatus("Nije odabrana slika za upload, " +
                "a nije niti označeno samo brisanje starog avatara!", myForm["avatar_file"])) {
                return false;
            }
        }

        if(myForm["avatar_file"].value != "")
        {
            if(myForm["avatar_file"].files[0]) {
                if(myForm["avatar_file"].files[0].size > 2*1024*1024) {
                    if (!errorStatus("Najveća dopuštena veličina slike je 2 MB. " +
                        "Odaberite manju sliku!", myForm["avatar_file"])) {
                        return false;
                    }
                }
                var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
                if (myForm["avatar_file"].files[0].type.length) {
                    var fileType = myForm["avatar_file"].files[0].type;
                    if (!acceptFileTypes.test(fileType)) {
                        if (!errorStatus("Neispravan tip slike! (" + fileType +
                            ")\nDopušteno: {jpg, jpeg, png, gif ...}", myForm["avatar_file"])) {
                            return false;
                        }
                    }
                }
                var acceptExtensions = /(gif|jpe?g|png)$/i;
                var extension = myForm["avatar_file"].files[0].name.substring(
                    myForm["avatar_file"].files[0].name.lastIndexOf('.') + 1);
                if (!acceptExtensions.test(extension)) {

                    if (!errorStatus("Neispravan format slike! (" + extension +
                        ")\nDopušteno: {jpg, jpeg, png, gif ...}", myForm["avatar_file"])) {
                        return false;
                    }
                }

            }

        }

        myForm.submit();

    }, 1000);

}


function validateAndSubmitPassRecovery(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");


    var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
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

