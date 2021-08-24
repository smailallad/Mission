$(document).ready(function () {

    if ($('#msg-danger').html().length > 0) {
        $('#msg-mdl-danger').html($('#msg-danger').html());
        $('#modalDanger').modal('show');
    } else if ($('#msg-warning').html().length > 0) {
        $('#msg-mdl-warning').html($('#msg-warning').html());
        $('#modalWarning').modal('show');
    } else if ($('#msg-info').html().length > 0) {
        $('#msg-mdl-info').html($('#msg-info').html());
        $('#modalInfo').modal('show');
    } else if ($('#msg-success').html().length > 0) {
        $('#msg-mdl-success').html($('#msg-success').html());
        $('#modalSuccess').modal('show');
    }


    $('#modalDanger').on('hidden.bs.modal', function (e) {
        if ($('#msg-warning').html().length > 0) {
            $('#msg-mdl-warning').html($('#msg-warning').html());
            $('#modalWarning').modal('show');
        } else if ($('#msg-info').html().length > 0) {
            $('#msg-mdl-info').html($('#msg-info').html());
            $('#modalInfo').modal('show');
        } else if ($('#msg-success').html().length > 0) {
            $('#msg-mdl-success').html($('#msg-success').html());
            $('#modalSeccess').modal('show');
        }
    });

    $('#modalWarning').on('hidden.bs.modal', function (e) {
        if ($('#msg-info').html().length > 0) {
            $('#msg-mdl-info').html($('#msg-info').html());
            $('#modalInfo').modal('show');
        } else if ($('#msg-success').html().length > 0) {
            $('#msg-mdl-success').html($('#msg-success').html());
            $('#modalSuccess').modal('show');
        }
    });

    $('#modalInfo').on('hidden.bs.modal', function (e) {
        if ($('#msg-success').html().length > 0) {
            $('#msg-mdl-success').html($('#msg-success').html());
            $('#modalSuccess').modal('show');
        }
    });
})