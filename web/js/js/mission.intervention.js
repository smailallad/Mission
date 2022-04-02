$(document).ready(function () {
    //************* Site *************
    $('#listeSite').click(function (e) {
        e.preventDefault();
    });
    $('#chercherSite').click(function (e) {
        e.preventDefault();
        var pagenum = 0;
        var client = $('#site_recherche_client').val();
        var site = $('#site_recherche_nom').val();
        listeSites(client,site,pagenum);
    });
    $('#modalSite').on('shown.bs.modal', function (e) {
        var pagenum = 0;
        var client = $('#site_recherche_client').val();
        var site = $('#site_recherche_nom').val();
        listeSites(client,site,pagenum);
    });
    //************ Prestation **************
    $('#listePrestation').click(function (e) {
        e.preventDefault();
    });
    $('#chercherPrestation').click(function (e) {
        e.preventDefault();
        var pagenum = 0;
        var projet = $('#prestation_filter_projet').val();
        var prestation = $('#prestation_filter_nom').val();
        listePrestations(projet,prestation,pagenum);
    });
    $('#modalPrestation').on('shown.bs.modal', function (e) {
        e.preventDefault();
        var pagenum = 0;
        // Chercher les projets du Client
        var client = $('#site_recherche_client').val();
        listeProjetsClient(client);
        var projet = $('#prestation_filter_projet').val();
        var prestation = $('#prestation_filter_nom').val();
        listePrestations(projet,prestation,pagenum);
    });
})
