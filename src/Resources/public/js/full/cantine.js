jQuery("document").ready(function ($) {
    //#region Tableau de saisie des repas
    let majListeRepas = function () {
        console.clear();
        console.log("Maj de la liste des Repas");
        $("#loading").show();

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
                            soir = "#soir_" + element.enfant,
                            abs = "#absence_" + element.enfant,
                            adh = "#adhesion_" + element.enfant;
                        $(row).show("slow");
                        $(matin).prop("checked", element.matin == "1" ? true : false);
                        $(midi).prop("checked", element.midi == "1" ? true : false);
                        $(soir).prop("checked", element.soir == "1" ? true : false);
                        $(abs).prop("checked", element.abs == "1" ? true : false);
                        $(adh).prop("checked", element.adh == "1" ? true : false);
                    });
                    $("#loading").hide();
                }
            },
            error: function (error) {
                console.error(error);
                $("#loading").hide();
            },
        });
    };

    $("select#nomClasse, select#nomEtablissement, input#date").change(function () {
        if ($("#MODIFNONSAVE").val() == "NOK") {
            if (window.confirm("Vous avez des modifications non enregistrées, souhaitez-vous continuer ?")) {
                $("#MODIFNONSAVE").val("OK");
                majListeRepas();
            }
        } else {
            majListeRepas();
        }
    });

    //Afficher la date dans l'input date
    /*
    if ($("input#date").length > 0) {
        let dateActuelle = new Date(),
            dateString = dateActuelle.toISOString().slice(0, 10),
            inputDate = document.getElementById("date");

        inputDate.value = dateString;
    }*/
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

    //#region Cocher tout
    $("input[value=all]").on("click", function () {
        let checked = this.checked,
            selecteur = this.id.split("_")[0];
        $("#presences tbody tr").each(function () {
            if (this.style.display != "none") {
                let id = this.id.split("_")[1],
                    input = selecteur + "_" + id;
                document.getElementById(input).checked = checked;
            }
        });
    });
    //#endregion

    //#region CheckBoxChange
    $("input[type=checkbox]").on("change", function () {
        let item = $("#MODIFNONSAVE")[0];
        if (item.value != "NOK") {
            item.value = "NOK";
        }
    });
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
                            console.log(datas);
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
            title: "Date et type de paiement ?",
            html: `<input type="date" id="date" class="swal2-input" placeholder="date du paiement"><br/>
                    <div class="swal2-radio" style="display: flex;"><label><input type="radio" name="swal2-radio" value="esp"><span class="swal2-label">Espèce</span></label>
                    <label><input type="radio" name="swal2-radio" value="chk"><span class="swal2-label">Chèque</span></label>
                    <label><input type="radio" name="swal2-radio" value="vir"><span class="swal2-label">Virement</span></label></div>`,
            preConfirm: () => {
                let date = Swal.getPopup().querySelector("#date").value,
                    paiement = Swal.getPopup().querySelector("input[name=swal2-radio]:checked").value;
                if (!date || !paiement) {
                    Swal.showValidationMessage(`Merci de renseigner tous les champs`);
                }
                return { date: date, paiement: paiement };
            },
            confirmButtonText: "Valider",
        }).then((result) => {
            if (result.isConfirmed == true) {
                let datas = {
                    action: "majPaiement",
                    item: id,
                    date: result.value.date,
                    choix: result.value.paiement,
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

    //#region Envoi du formulaire
    $("#saisieEnfants").on("submit", function (event) {
        $("#loading").show();
        if ($("input[name=SENDING]").val() == "NOK") {
            event.preventDefault();
            $("#presences tbody tr").each(function () {
                if (this.style.display == "none") {
                    this.remove();
                }
            });
            $("input[name=SENDING]").val("OK");
            $("#saisieEnfants").submit();
        }
    });
    //#endregion
});
