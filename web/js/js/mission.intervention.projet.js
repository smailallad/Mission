function listeProjetsClient(client) {
    var cURL = Routing.generate('search_projets_client', { client: client });
    $.post(
        cURL,
        function (data) {
            $('#prestation_filter_projet option').remove();
            var projets = JSON.parse(data.projets);
            for (i = 0; i < projets.length; i++) {
                var id = projets[i]['id'];
                var nom = projets[i]['nom'];
                $('#prestation_filter_projet').append($('<option></option>').val(id).html(nom));
            }
            fPrestationChange();
        }
    )
}
