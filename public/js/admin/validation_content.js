




function validateAndSubmitTrainingCourse(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");

    var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
    setTimeout (function() {
        if (myForm["title"].value == "" ) {
            if (!errorStatus("Niste upisali naziv!", myForm["title"])) {
                return false;
            }
        }
        if (myForm["date-from"].value == "") {
            if (!errorStatus("Niste odabrali datum početka!", myForm["date-from"])) {
                return false;
            }
        }
        if (myForm["start-time"].value == "") {
            if (!errorStatus("Niste odabrali vrijeme početka!", myForm["start-time"])) {
                return false;
            }
        }
        if (myForm["end-time"].value == "") {
            if (!errorStatus("Niste odabrali vrijeme kraja!", myForm["end-time"])) {
                return false;
            }
        }
        if (myForm["capacity"].value == "" ||
            parseInt(myForm["capacity"].value) <= 0)  {
            if (!errorStatus("Niste odabrali ispravan kapacitet termina!", myForm["capacity"])) {
                return false;
            }
        }
        if (myForm["min-reservations"].value == "" ||
            parseInt(myForm["min-reservations"].value) <= 0)  {
            if (!errorStatus("Niste odabrali ispravan minimalni broj rezervacija!", myForm["min-reservations"])) {
                return false;
            }
        }
        if(myForm["repeating"].checked) {
            if (myForm["date-until"].value == "") {
                if (!errorStatus("Odabrana je opcija ponavljanja, ali ne i datuma do kad.", myForm["date-until"])) {
                    return false;
                }
            }
            if (myForm["repeating-interval"].value == "") {
                if (!errorStatus("Odabrana je opcija ponavljanja, ali ne i interval ponavljanja.", myForm["repeating-interval"])) {
                    return false;
                }
            }
            if (myForm["repeating-frequency"].value == "" ||
                parseInt(myForm["repeating-frequency"].value) <= 0) {
                if (!errorStatus("Odabrana je opcija ponavljanja, ali ne i ispravna frekvencija ponavljanja.\nTo mora biti pozitivni cijeli broj", myForm["repeating-frequency"])) {
                    return false;
                }
            }

            var td1 = new Date();
            var dateFrom = myForm["date-from"].value;
            var day = parseInt(dateFrom.split(".")[0]);
            var month = parseInt(dateFrom.split(".")[1]);
            var year = parseInt(dateFrom.split(".")[2]);
            td1.setFullYear(year, month-1, day);

            var td2 = new Date();
            var dateUntil = myForm["date-until"].value;
            day = parseInt(dateUntil.split(".")[0]);
            month = parseInt(dateUntil.split(".")[1]);
            year = parseInt(dateUntil.split(".")[2]);
            td2.setFullYear(year, month-1, day);

            if (td1 >= td2) {
                if (!errorStatus("Datum početka termina je nakon datuma do kad se termin ponavlja", myForm["date-until"])) {
                    return false;
                }
            }

        }

        myForm.submit();
    }, 1000);
}






function validateAndSubmitGame(formName) {
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms[formName];

    expandInfoPanel("Provjera ispravnosti formata unesenih podataka");

    setTimeout (function() {
        if (myForm["title"].value == "" ) {
            if (!errorStatus("Niste upisali naziv!", myForm["title"])) {
                return false;
            }
        }
//
//        if (myForm["capacity"].value == "" ||
//            parseInt(myForm["capacity"].value) <= 0)  {
//            if (!errorStatus("Niste odabrali ispravan kapacitet termina!", myForm["capacity"])) {
//                return false;
//            }
//        }
//        if (myForm["min-reservations"].value == "" ||
//            parseInt(myForm["min-reservations"].value) <= 0)  {
//            if (!errorStatus("Niste odabrali ispravan minimalni broj rezervacija!", myForm["min-reservations"])) {
//                return false;
//            }
//        }
//        if(myForm["repeating"].checked) {
//            if (myForm["date-until"].value == "") {
//                if (!errorStatus("Odabrana je opcija ponavljanja, ali ne i datuma do kad.", myForm["date-until"])) {
//                    return false;
//                }
//            }
//            if (myForm["repeating-interval"].value == "") {
//                if (!errorStatus("Odabrana je opcija ponavljanja, ali ne i interval ponavljanja.", myForm["repeating-interval"])) {
//                    return false;
//                }
//            }
//            if (myForm["repeating-frequency"].value == "" ||
//                parseInt(myForm["repeating-frequency"].value) <= 0) {
//                if (!errorStatus("Odabrana je opcija ponavljanja, ali ne i ispravna frekvencija ponavljanja.\nTo mora biti pozitivni cijeli broj", myForm["repeating-frequency"])) {
//                    return false;
//                }
//            }



        myForm.submit();
    }, 1000);
}