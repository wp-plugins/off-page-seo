<?php
require_once('../../../../../wp-load.php');
require_once('../ops.php');
require_once('../ops-rank-reporter.php');

$i = 0;
$data = OPS_Rank_Reporter::ops_get_row_data($_POST['rowId']);
$links = unserialize($data['links']);
?>
<form method="post" action="" class="ops-backlink-edit" data-count="<?php echo count($links) ?>">
    <input type="hidden" name="rowid" value="<?php echo $_POST['rowId'] ?>" />
    <?php if (is_array($links)): ?>
        <?php foreach ($links as $backlink): ?>
            <div class="ops-link-wrap">
                <input type="text" name="links[<?php echo $i ?>][url]" value="<?php echo $backlink['url'] ?>" class="ops-url "/>
                <select name="links[<?php echo $i ?>][type]" class="ops-type">
                    <option value="backlink" <?php echo ($backlink['type'] == 'backlink') ? "selected" : ""; ?>>Backlink</option>
                    <option value="article" <?php echo ($backlink['type'] == 'article') ? "selected" : ""; ?>>Article</option>
                    <option value="comment" <?php echo ($backlink['type'] == 'comment') ? "selected" : ""; ?>>Comment</option>
                </select>
                <input type="text" name="links[<?php echo $i ?>][price]" value="<?php echo $backlink['price'] ?>" class="ops-price" />
                
                <?php echo date('F d, Y', $backlink['date']) ?>
                <input type="hidden" name="links[<?php echo $i ?>][date]" value="<?php echo $backlink['date']?>" />
                
                <button class="ops-delete-link">x</button>
            </div>
            <?php $i++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <input type="submit" class="button button-primary" value="Save" />
    <a href="#" class="ops-add-new-link">Add new link</a>
</form>




