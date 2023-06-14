jQuery("document").ready(function ($) {
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

        //Afficher la date dans l'input date
        let dateActuelle = new Date(),
            dateString = dateActuelle.toISOString().slice(0, 10),
            inputDate = document.getElementById("date");

        inputDate.value = dateString;
});