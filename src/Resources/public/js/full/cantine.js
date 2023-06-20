jQuery("document").ready(function ($) {
    let majListeRepas = function () {
        console.clear();
        console.log("Maj de la liste des Repas");

        let datas = {
            action: "getListeRepas",
            date: $("input#date").val(),
        };

        if ($("select#nomClasse").val() != "") {
            datas.classe = $("select#nomClasse").val();
        }

        if ($("select#nomEtablissement").val() != "") {
            datas.etablissement = $("select#nomEtablissement").val();
        }

        $.ajax({
            url: "/ajax.html",
            data: datas,
            type: "POST",
            success: function (code_html) {
                let json = $.parseJSON(code_html);
                if (json.result == "error") {
                    alert("Erreur Ajax : " + json.details.msg);
                } else {
                    $("#presences tbody tr").hide();
                    if (json.data.length == 0) {
                        alert("Aucun enfant trouvé");
                    }
                    json.data.forEach((element) => {
                        let row = "#enfant_" + element.enfant,
                            matin = "#matin_" + element.enfant,
                            midi = "#midi_" + element.enfant,
                            soir = "#soir_" + element.enfant;
                        $(row).show("slow");
                        $(matin).prop("checked", element.matin == "1" ? true : false);
                        $(midi).prop("checked", element.midi == "1" ? true : false);
                        $(soir).prop("checked", element.soir == "1" ? true : false);
                    });
                }
            },
            error: function (error) {
                console.error(error);
            },
        });
    };

    $("select#nomClasse, select#nomEtablissement, input#date").change(function () {
        majListeRepas();
    });
    /*
    $('#nomClasse, #nomEtablissement').change(function(){
        let filter_class = $(nomClasse).val(),
            filter_etablissement = $(nomEtablissement).val();
            $('.listing').hide();
            if(filter_class == '' && filter_etablissement == ''){
                $('.listing').show();
            } else {
                let afficher = ".listing";

                if(filter_class != "") {
                    afficher += ".classe_"+filter_class;
                }

                if(filter_etablissement != ""){
                    afficher += ".etablissement_"+filter_etablissement;
                }

                $(afficher).show('slow');
            }
        });
        */

    //Afficher la date dans l'input date
    if ($("input#date").length > 0) {
        let dateActuelle = new Date(),
            dateString = dateActuelle.toISOString().slice(0, 10),
            inputDate = document.getElementById("date");

        inputDate.value = dateString;
    }
});
