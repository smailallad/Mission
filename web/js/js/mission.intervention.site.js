function listeSites(client,site,pagenum) { 
    
    if (site.length == 0) {
        var cURL = Routing.generate('search_site_client', { client: client, pagenum });
    } else {
        var cURL = Routing.generate('search_site_client', { client: client, pagenum, site: site });
    }
    $.post(
        cURL,
        function (data) {
            $('#listeSiteTable tbody > tr').remove();
            ligne = '';
            if (data.pagenum != 0) {
                ligne += '<button id="firstSite" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="firstSite()" class="ml-2 btn btn-primary glyphicon glyphicon-fast-backward">  </button>';
            }
            if (data.pagenum > 0) {
                ligne += '<button id="previousSite" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="previousSite()" class="ml-2 btn btn-primary glyphicon glyphicon-backward">  </button>';
            }
            if (data.pagenum < data.totalpages) {
                ligne += '<button id="nextSite" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="nextSite()" class="ml-2 btn btn-primary glyphicon glyphicon-forward">  </button>';
            }
            if (data.pagenum < data.totalpages) {
                ligne += '<button id="lastSite" data-pagenum="' + data.pagenum + '" data-totalpages="' + data.totalpages + '" onclick="lastSite()" class="ml-2 btn btn-primary glyphicon glyphicon-fast-forward">  </button>';
            }
            ligne += '<span id="pages" class="badge bg-primary ml-4 pt-3 pb-3">';
            if (data.totalpages>=0){
                var tt = parseInt(data.pagenum) + 1;
                ligne += tt;
                ligne += '/';
                ligne += data.totalpages + 1;
            }else{
                ligne += '0/0';
            }
            ligne += '</span>';
            $('#btnNavigationSite').html(ligne);
            var sites = JSON.parse(data.sites);
            
            for (i = 0; i < sites.length; i++) {
                //console.log(sites[i]);
                ligne = '<tr><td>';
                ligne += "<a id = 'siteCode_" + sites[i].id +"' href='#' onclick='selectSite(\"" + sites[i].id + "\"); return false;'>";
                ligne += sites[i].code;
                ligne += '</a></td><td id = "siteNom_' + sites[i].id + '">';
                ligne += sites[i].nom;
                ligne += '</td><td>';
                ligne += sites[i].wilaya.nom;
                ligne += '</td><td>';
                ligne += sites[i].client.nom;
                ligne += '</td></tr>';
                $('#listeSiteTable > tbody:last').append(ligne);
            }
        }
    )
}

function firstSite() {
    var pagenum = 0;
    var client = $('#site_recherche_client').val();
    var site = $('#site_recherche_nom').val();
    listeSites(client,site,pagenum);
}
function previousSite() {
    var pagenum = $('#previousSite').attr('data-pagenum');
    var totalpages = $('#previousSite').attr('data-totalpages');
    var client = $('#site_recherche_client').val();
    var site = $('#site_recherche_nom').val();
    pagenum--;
    pagenum = Math.max(0, pagenum);
    listeSites(client,site,pagenum);
}
function nextSite() {
    var pagenum = $('#nextSite').attr('data-pagenum');
    var totalpages = $('#nextSite').attr('data-totalpages');
    var client = $('#site_recherche_client').val();
    var site = $('#site_recherche_nom').val();
    pagenum++;
    pagenum = Math.min(totalpages, pagenum);
    listeSites(client,site,pagenum);
}
function lastSite() {
    var totalpages = $('#lastSite').attr('data-totalpages');
    var pagenum = totalpages;
    var client = $('#site_recherche_client').val();
    var site = $('#site_recherche_nom').val();
    listeSites(client,site,pagenum);
}
function selectSite(id) {
    var code = $('#siteCode_' + id).html();
    var nom = $('#siteNom_' + id).html();
    $('#intervention_siteid').val(id);
    $('#intervention_sitecode').val(code);
    $('#intervention_sitenom').val(nom);
    $('#intervention_projet').val(null);
    $('#intervention_prestationnom').val(null);

    $('#modalSite').modal('hide');
}
function fSiteChange(){
    var pagenum = 0;
    var client = $('#site_recherche_client').val();
    var site = $('#site_recherche_nom').val();
    listeSites(client,site,pagenum);
}


