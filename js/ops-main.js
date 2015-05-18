jQuery(document).ready(function ($) {

    $('#ops-rank-reporter .ops-show-graph a.button').click(function (e) {
        e.preventDefault();
        if ($(this).closest('.ops-kw-graph-wrapper').find('.ops-graph-wrapper').hasClass('ops-show') === false) {

            $(this).closest('.ops-kw-graph-wrapper').css('z-index', '5000').find('.ops-graph-wrapper').addClass('ops-show');
            $(this).closest('.ops-kw-wrapper').addClass('ops-active');
            $(this).closest('.ops-kw-graph-wrapper').find('.ops-backlinks-list').addClass('ops-open').slideDown();
            $('.ops-black-overlay').fadeIn();
            $('html, body').animate({
                scrollTop: $(this).closest('.ops-kw-graph-wrapper').offset().top -50
            }, 500);
        } else {
            $(this).closest('.ops-kw-graph-wrapper').css('z-index', '100').find('.ops-graph-wrapper').removeClass('ops-show');
            $(this).closest('.ops-kw-wrapper').removeClass('ops-active');
            $(this).closest('.ops-kw-graph-wrapper').find('.ops-backlinks-list').removeClass('ops-open').slideUp();
            $('.ops-black-overlay').fadeOut();
        }
    });

    // CLOSE CLICKING ON OVERLAY
    $('.ops-black-overlay').on('click', function () {
        $('.ops-kw-graph-wrapper').css('z-index', '100').find('.ops-graph-wrapper.ops-show').removeClass('ops-show');
        $('.ops-kw-graph-wrapper .ops-kw-wrapper.ops-active').removeClass('ops-active');
        $('.ops-kw-graph-wrapper .ops-backlinks-list.ops-open').removeClass('ops-open').slideUp();
        $('.ops-black-overlay').fadeOut();
    });


    // BACKLINKS    
    $('body').on('click', '.ops-add-new-link', function (e) {
        e.preventDefault();

        // hide message
        $('.ops-no-link-yet').fadeOut();

        var allId = $(this).closest('form').data('count');
        var newId = allId++;
        $(this).closest('form').data('count', allId);
        $(this).closest('form').find('table').prepend('<tr class="ops-link-wrap"><input type="hidden" name="reciprocal_referers" /><td><input type="checkbox" name="links[' + allId + '][reciprocal]" checked="checked" /><input type="hidden" name="links[' + allId + '][reciprocal_status]" value="4" /></td><td class="ops-url"><input type="text" name="links[' + allId + '][url]" placeholder="URL"></td><td><select name="links[' + allId + '][type]"><option value="backlink" selected="selected">Backlink</option><option value="article">Article</option><option value="comment">Comment</option><option value="sitewide">Sitewide</option></select></td><td class="ops-price"><input type="text" name="links[' + allId + '][price]" placeholder="Price"></td><td><input type="text" class="datepick" name="links[' + allId + '][date]" placeholder="Date" /></td><td><input type="text" name="links[' + allId + '][comment]" placeholder="Comment" /></td><td><a class="ops-delete-link">x</a></td></tr>');
        $('.datepick').datepicker({dateFormat: 'dd-mm-yy'});
    });
    $('body').on('click', 'a.ops-delete-link', function (e) {
        e.preventDefault();
        $(this).closest('tr').empty();
    });
    $('.ops-show-backlinks').click(function (e) {
        e.preventDefault();
        if ($(this).closest('.ops-kw-graph-wrapper').find('.ops-backlinks-list').hasClass('ops-open') === false) {
            $(this).closest('.ops-kw-graph-wrapper').find('.ops-backlinks-list').addClass('ops-open').slideDown();
        
        } else {
            $(this).closest('.ops-kw-graph-wrapper').find('.ops-backlinks-list').removeClass('ops-open').slideUp();
        }
    });

});
