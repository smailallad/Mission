function listePrestations(projet,prestation,pagenum) {
    if (prestation.length == 0) {
        var cURL = Routing.generate('search_prestation', { projet: projet, pagenum });
    } else {
        var cURL = Routing.generate('search_prestation', { projet: projet, pagenum, prestation: prestation });
    }
    $.post(
        cURL,
        function (data) {
            var prestations = JSON.parse(data.prestations);
            $('#listePrestationTable tbody > tr').remove();
            ligne = '';
            if (data.pagenum != 0) {
                ligne += '<button id="firstPrestation" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="firstPrestation()" class="ml-2 btn btn-primary glyphicon glyphicon-fast-backward">  </button>';
            }
            if (data.pagenum > 0) {
                ligne += '<button id="previousPrestation" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="previousPrestation()" class="ml-2 btn btn-primary glyphicon glyphicon-backward">  </button>';
            }
            if (data.pagenum < data.totalpages) {
                ligne += '<button id="nextPrestation" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="nextPrestation()" class="ml-2 btn btn-primary glyphicon glyphicon-forward">  </button>';
            }
            if (data.pagenum < data.totalpages) {
                ligne += '<button id="lastPrestation" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="lastPrestation()" class="ml-2 btn btn-primary glyphicon glyphicon-fast-forward">  </button>';
            }
            ligne += '<span id="pages" class="badge bg-primary ml-4 pt-3 pb-3">';
            if (data.totalpages >=0){
                var tt = parseInt(data.pagenum) + 1;
                ligne += tt;
                ligne += '/';
                ligne += data.totalpages + 1;
            }else{
                ligne += '0/0';
            }
            ligne += '</span>';
            $('#btnNavigationPrestation').html(ligne);
            for (i = 0; i < prestations.length; i++) {
                ligne = '<tr><td>';
                ligne += "<a id = 'pres_" + prestations[i].id  +"' href='#' onclick='selectPrestation(\"" + prestations[i].id + "\",\"" + prestations[i].projet + "\"); return false;'>";
                ligne += prestations[i].nom ;
                ligne += '</a></td><td>';
                ligne += prestations[i].projet;
                ligne += '</td></tr>';
                $('#listePrestationTable > tbody:last').append(ligne);
            }
        }
    )
}

function firstPrestation() {
    var pagenum = 0;
    var projet = $('#prestation_filter_projet').val();
    var prestation = $('#prestation_filter_nom').val();
    listePrestations(projet,prestation,pagenum);
}
function previousPrestation() {
    var pagenum = $('#previousPrestation').attr('data-pagenum');
    var projet = $('#prestation_filter_projet').val();
    var prestation = $('#prestation_filter_nom').val();
    pagenum--;
    pagenum = Math.max(0, pagenum);
    listePrestations(projet,prestation,pagenum);
}
function nextPrestation() {
    var pagenum = $('#nextPrestation').attr('data-pagenum');
    var totalpages = $('#nextPrestation').attr('data-totalpages');
    var projet = $('#prestation_filter_projet').val();
    var prestation = $('#prestation_filter_nom').val();
    pagenum++;
    pagenum = Math.min(totalpages, pagenum);
    listePrestations(projet,prestation,pagenum);
}
function lastPrestation() {
    var totalpages = $('#lastPrestation').attr('data-totalpages');
    var pagenum = totalpages;
    var projet = $('#prestation_filter_projet').val();
    var prestation = $('#prestation_filter_nom').val();
    listePrestations(projet,prestation,pagenum);
}
function selectPrestation(id, projet) {
    var nom = $('#pres_' + id).html();
    $('#intervention_prestationid').val(id);
    $('#intervention_projet').val(projet);
    $('#intervention_prestationnom').val(nom);
    $('#modalPrestation').modal('hide');
}
function fPrestationChange(){
    var pagenum = 0;
    var projet = $('#prestation_filter_projet').val();
    var prestation = $('#prestation_filter_nom').val();
    listePrestations(projet,prestation,pagenum);
}
