jQuery(document).ready(function ($) {

    $('#ops-rank-reporter .ops-show-graph a.button').click(function (e) {
        e.preventDefault();
        if ($(this).closest('.ops-kw-graph-wrapper').find('.ops-graph-wrapper').hasClass('ops-show') === false) {
            $(this).closest('.ops-kw-graph-wrapper').find('.ops-graph-wrapper').addClass('ops-show');
            $(this).closest('.ops-kw-wrapper').addClass('ops-active');
        } else {
            $(this).closest('.ops-kw-graph-wrapper').find('.ops-graph-wrapper').removeClass('ops-show');
            $(this).closest('.ops-kw-wrapper').removeClass('ops-active');
        }
    });

});
