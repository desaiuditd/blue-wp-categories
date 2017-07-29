/**
 * Created by udit on 7/28/17.
 */

jQuery(document).ready(function ($) {

    $('#bwpc-sync').on('click', function (e) {

        e.preventDefault();

        $('#bwpc-sync-spinner').addClass('is-active');

        $.post(ajaxurl, {
            action: 'bwpc_sync_categories'
        }, function (res) {

            $('#bwpc-sync-spinner').removeClass('is-active');

            if ( res.success ) {
                $('#bwpc-sync-msg').html('Categories are synced successfully.');
                $('#bwpc-sync-msg').show();
            } else {
                $('#bwpc-sync-error').html('Something went wrong. Please contact the developer.');
                $('#bwpc-sync-error').show();
            }

            setTimeout( function () {
                $('#bwpc-sync-msg').hide();
                $('#bwpc-sync-error').hide();
            }, 3000 );
        });
    });

});