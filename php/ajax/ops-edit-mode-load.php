<?php
require_once('../../../../../wp-load.php');
require_once('../ops.php');
require_once('../ops-rank-reporter.php');

$i = 0;
$data = OPS_Rank_Reporter::ops_get_row_data($_POST['rowId']);
$links = unserialize($data['links']);
if (is_array($links) && count($links) != 0) {
// sort according to date
    usort($links, create_function( '$a, $b', 'return $b[\'date\'] - $a[\'date\'];'));
}
?>
<form method="post" action="" class="ops-backlink-edit" data-count="<?php echo count($links) ?>">
    <input type="hidden" name="rowid" value="<?php echo $_POST['rowId'] ?>" />
    <table>
        <?php if (is_array($links) && count($links) != 0) : ?>
            <?php foreach ($links as $backlink): ?>
                <tr class="ops-link-wrap">
                    <td class="ops-recip">
                        <input type="checkbox" name="links[<?php echo $i ?>][reciprocal]" <?php echo (isset($backlink['reciprocal']) && $backlink['reciprocal'] == 1) ? "checked" : ""; ?>/>
                        <input type="hidden" name="links[<?php echo $i ?>][reciprocal_referer]" value="<?php echo (isset($backlink['reciprocal_referer']) && is_numeric($backlink['reciprocal_referer'])) ? $backlink['reciprocal_referer'] : "0"; ?>" />
                        <input type="hidden" name="links[<?php echo $i ?>][reciprocal_status]" value="<?php echo (isset($backlink['reciprocal_status'])) ? $backlink['reciprocal_status'] : "4"; ?>" />
                    </td>
                    <td class="ops-url">
                        <input type="text" name="links[<?php echo $i ?>][url]" placeholder="URL" value="<?php echo $backlink['url'] ?>" />
                    </td>
                    <td>
                        <select name="links[<?php echo $i ?>][type]" >
                            <option value="backlink" <?php echo ($backlink['type'] == 'backlink') ? "selected" : ""; ?>>Backlink</option>
                            <option value="article" <?php echo ($backlink['type'] == 'article') ? "selected" : ""; ?>>Article</option>
                            <option value="comment" <?php echo ($backlink['type'] == 'comment') ? "selected" : ""; ?>>Comment</option>
                            <option value="sitewide" <?php echo ($backlink['type'] == 'sitewide') ? "selected" : ""; ?>>Sitewide</option>
                        </select>
                    </td>
                    <td class="ops-price" >
                        <input type="text" name="links[<?php echo $i ?>][price]" placeholder="Price" value="<?php echo $backlink['price'] ?>"/>
                    </td>
                    <td>
                        <input type="text" name="links[<?php echo $i ?>][date]" placeholder="Date" class="datepick" value="<?php echo date('d-m-Y', $backlink['date']) ?>" />
                    </td>
                    <td>
                        <input type="text" name="links[<?php echo $i ?>][comment]" placeholder="Comment" value="<?php echo (isset($backlink['comment'])) ? $backlink['comment']: ""; ?>" />
                    </td>
                    <td>
                        <a href="" class="ops-delete-link">x</a>
                    </td>
                    <?php $i++; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ops-no-link-yet">
                <b>You aren't tracking any backlinks yet.</b>  Add one by hitting "Add new link" below. <br/>
            </div>
        <?php endif; ?>
    </table>
    <input type="submit" class="button button-primary" value="Save" />
    <a href="#" class="ops-add-new-link">Add new link</a>
</form>
<script>
    jQuery(document).ready(function ($) {
        $('body').find('.datepick').datepicker({dateFormat: 'dd-mm-yy'});
    });
</script>




