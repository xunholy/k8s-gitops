<?php
/**
 * Created by Amin.MasterkinG
 * Website : MasterkinG32.CoM
 * Email : lichwow_masterking@yahoo.com
 * Date: 11/26/2018 - 8:36 PM
 */
?>

<p>
    <?php elang('read_before_register'); ?>
</p>
<ul>
    <li><?php elang('rule'); ?> 1.</li>
    <li><?php elang('rule'); ?> 2.</li>
    <li><?php elang('rule'); ?> 3.</li>
    <li><?php elang('rule'); ?> 4.</li>
    <li><?php elang('rule'); ?> 5.</li>
    <li><?php elang('rule'); ?> 6.</li>
    <li><?php elang('edit_on'); ?> <b>"/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/tpl/rules.php"</b>.</li>
</ul>
