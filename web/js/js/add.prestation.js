$(document).ready(function () {
    $('#listeSite').click(function (e) {
        e.preventDefault();
    });

    $('#deleteSite').click(function (e) {
        e.preventDefault();
        $('#prestation_bc_siteId').val(null);
        $('#prestation_bc_siteCode').val(null);
        $('#prestation_bc_siteNom').val(null);
    });

    $('#chercherSite').click(function (e) {
        e.preventDefault();
        var pagenum = 0;
        var client = $('#client_id').html();
        var site = $('#site_recherche_client_nom').val();
        var zone = $('#prestation_bc_zone').val();

        listeSites(client, pagenum,zone,site)
    });
    $('#modalSite').on('shown.bs.modal', function (e) {
        var pagenum = 0;
        var client = $('#client_id').html();
        var site = $('#site_recherche_client_nom').val();
        var zone = $('#prestation_bc_zone').val();
        $('#zoneId').html(' : ' + zone);
        listeSites(client, pagenum,zone,site)
    });
})