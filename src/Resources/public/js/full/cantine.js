jQuery("document").ready(function ($) {
    //#region Tableau de saisie des repas
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

    //Afficher la date dans l'input date
    if ($("input#date").length > 0) {
        let dateActuelle = new Date(),
            dateString = dateActuelle.toISOString().slice(0, 10),
            inputDate = document.getElementById("date");

        inputDate.value = dateString;
    }
    //#endregion

    //#region Tableau de bord
    if ($("table#datatable").length > 0) {
        let dt = new DataTable("#datatable", {
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"],
            ],
        });
    }
    //#endregion

    //#region Envois de toutes les factures
    $("#sendFactures").on("click", function (event) {
        event.preventDefault();
        const vert = "#9ed19e",
            rouge = "#e98080";

        let progressMax = 70,
            progress = 0,
            reussites = 0,
            echecs = 0,
            majProgress = function () {
                progress++;
                let pourcentage = (progress * 100) / progressMax + "%";
                $(".progress span").css("width", pourcentage);
                $(".state span")[0].innerText = progress;
            };

        Swal.fire({
            icon: "question",
            text: "Quels type de mails souhaitez vous envoyer avec les factures cochées ci-dessus ?",
            input: "select",
            inputOptions: {
                nouveau: "Nouvelle facture",
                relance: "Relance de facture impayé",
            },
            confirmButtonText: "Envoyer",
            showCancelButton: true,
            cancelButtonText: "Annuler",
            showLoaderOnConfirm: true,
        }).then((choix) => {
            if (choix.isConfirmed == true) {
                let envois = $("input.factures:checked");
                progressMax = envois.length;
                $(".state span")[1].innerText = progressMax;
                $("#envoiEnCours").show();
                (async function loop() {
                    return new Promise((resolve, reject) => {
                        for (let i = 0; i < progressMax; i++) {
                            let datas = {
                                action: "sendMailFacture",
                                item: envois[i].value,
                                typeMail: choix.value,
                            };
                            $.ajax({
                                url: "/ajax.html",
                                data: datas,
                                type: "POST",
                                success: function (result) {
                                    let json = $.parseJSON(result);
                                    if (json.result == "error") {
                                        $(envois[i]).closest("tr").css("background", rouge);
                                        echecs++;
                                    } else {
                                        $(envois[i]).closest("tr").css("background", vert);
                                        reussites++;
                                    }
                                    majProgress();

                                    if (reussites + echecs == progressMax) {
                                        resolve();
                                    }
                                },
                            });
                        }
                    });
                })().then(() => {
                    Swal.fire({
                        title: "Envois terminés",
                        html: `${reussites} réussite(s) pour ${echecs} échec(s)`,
                    });
                    $("#envoiEnCours").hide();
                });
            }
        });
    });
    //#endregion

    //#region Payement d'une facture
    $("a.paiement").on("click", function (event) {
        event.preventDefault();
        let rgx = /id=([^&]*)/,
            id = event.delegateTarget.href.match(rgx)[1];
        Swal.fire({
            icon: "question",
            text: "Quel moyen de paiement a été choisi ?",
            input: "select",
            inputOptions: {
                esp: "Espèce",
                chk: "Chèque",
                vir: "Virement",
            },
            confirmButtonText: "Valider",
        }).then((result) => {
            if (result.isConfirmed == true) {
                let datas = {
                    action: "majPaiement",
                    item: id,
                    choix: result.value,
                };
                $.ajax({
                    url: "/ajax.html",
                    data: datas,
                    type: "POST",
                    success: function (result) {
                        let json = $.parseJSON(result);
                        if (json.result == "error") {
                            alert("Erreur lors du paiement");
                            console.error(json);
                        } else {
                            if ($("form#genFacture").length > 0) {
                                $("form#genFacture").submit();
                            } else {
                                document.location.href = document.location.href;
                            }
                        }
                    },
                });
            }
        });
    });
    //#endregion

    //#region Champs date pour la génération de facture
    $("form#genFacture input[name=dateDebut]").on("change", function () {
        let debut = moment(this.value),
            fin = moment(this.value);
        debut.date(1);
        fin.endOf("month");
        this.value = debut.format("YYYY-MM-DD");
        $("input[name=dateFin]").val(fin.format("YYYY-MM-DD"));
    });
    //#endregion
});
