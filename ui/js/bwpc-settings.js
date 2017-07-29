/**
 * Created by udit on 7/28/17.
 */

jQuery(document).ready(function ($) {

    $('#bwpc-sync').on('click', function (e) {

        e.preventDefault();

        $.post(ajaxurl, {
            action: 'bwpc_sync_categories'
        }, function (res) {
            console.log(res);
        });
    });

});