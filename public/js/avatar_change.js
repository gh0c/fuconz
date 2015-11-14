function prettydump(obj) {
    ret = ""
    $.each(obj, function(key, value) {
        ret += "<tr><td>" + key + "</td><td>" + value + "</td></tr>";
    });
    return ret;
}

var cldFU = $('#avatar-file').cloudinary_fileupload();

$(document).ready(function(){

    $(function() {
        $('.cloudinary-fileupload')
            .fileupload({
                autoUpload: false,
                replaceFileInput: false,

    //                imageMaxWidth: 800,
    //                imageMaxHeight: 600,
    //                acceptFileTypes: /(\.|\/)(gif|jpe?g|png|bmp|ico)$/i,
    //                maxFileSize: 5000000 // .5MB


                start: function () {
                    if(!haltFormSubmitting()) {
                        return false;
                    }
                    expandInfoPanel("Započinje upload...");
                },
                progress: function () {
                    changePanelText("Upload u tijeku...");
                }


            })

            .on('cloudinarydone', function (e, data) {
                infoPanelSuccess("Upload završen.", true);
                var url = $(this).data("submit-url");
    //                                                $.post('url', data.result);
                var csrfKeyName = $(this).data("csrf-key-name");
                var csrfToken = $("input[name=" + csrfKeyName + "]").val();


                var params = {};
                params["img-data"] = data.result;
                params[csrfKeyName] = csrfToken;

                infoPanelSuccess("Pohrana u bazu...");

                var request = $.ajax({
                    url: url,
                    type: "POST",
                    data: params,
                    dataType: "json"
                });

                request.done(function( msg ) {
                    $( ".box .t1" ).html( msg );
                    infoPanelSuccess("Pohranjeno. \nSpremite promjene!", true);
    //                alert(msg["hash"]);
                    $("#uploaded-img-hash").val(msg["hash"]);
                    enableFormSubmiting();

                });

                var info = $('<div class="uploaded-info"/>');
                $(info).append($('<div class="image"/>').html(
                    $.cloudinary.image(data.result.public_id, {
                        format: data.result.format, width: 150, height: 150, crop: "fill", version: data.result.version
                    })
                ));
                $('.uploaded-info-holder').html(info);
            });
    });

});


cldFU.bind('fileuploadadd', function (e, data) {
/* store data somewhere - called for each file added */
    if(!haltFormSubmitting()) {
        return false;
    }
    var myForm = document.forms["user-avatar-change"];

    expandInfoPanel("Provjera...");

    setTimeout (function() {

        if (myForm["avatar-file"].value == "" && !myForm["delete-avatar"].checked) {
            if (!errorStatus("Nije odabrana slika za upload, " +
                "a nije niti označeno samo brisanje starog avatara!", myForm["avatar_file"])) {
                return false;
            }
        }

        if(myForm["avatar-file"].value != "")
        {
            if(myForm["avatar-file"].files[0]) {
                if(myForm["avatar-file"].files[0].size > 2*1024*1024) {
                    if (!errorStatus("Najveća dopuštena veličina slike je 2 MB. " +
                        "Odaberite manju sliku!", myForm["avatar-file"])) {
                        return false;
                    }
                }
                var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
                if (myForm["avatar-file"].files[0].type.length) {
                    var fileType = myForm["avatar-file"].files[0].type;
                    if (!acceptFileTypes.test(fileType)) {
                        if (!errorStatus("Neispravan tip slike! (" + fileType +
                            ")\nDopušteno: {jpg, jpeg, png, gif ...}", myForm["avatar-file"])) {
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
                        return false;
                    }
                }

            }

        }
//        infoPanelSuccess();
        enableFormSubmiting();

        data.submit();
    }, 1000);
});

$(document).on('change', '.file-input:file', function() {
    var input = $(this),
    numFiles = input.get(0).files ? input.get(0).files.length : 1,
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

$(document).ready( function() {
    $('.file-input:file').on('fileselect', function(event, numFiles, label) {
        var input = $(this).parents('.vertical-section').find('.selected-file > span'),
            button = $(this).parents('.vertical-section').find('.button-label > span'),
            log = numFiles > 1 ? ' Odabranih datoteka: ' + numFiles : label;

        if( input.length ) {
            if(log === "") {
                input.html(log);
                button.text('Odaberite datoteku');
            } else {
                input.html('<i class = "fa fa-fw fa-file-image-o"></i> ' + log);
                button.text('Odabrano: ');
            }
        } else {
            if( log ) alert(log);
        }

    });
});