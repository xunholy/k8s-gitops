<?php
/**
 * VMaNGOS Registration Portal - How to Connect Template
 */
?>
<div class="connect-steps" style="line-height: 1.8;">
    <h4><i class="fa fa-plug"></i> How to Connect</h4>
    <hr>
    <p>1. <?php elang('create_account_tip1'); ?></p>
    <p>2. <?php elang('create_account_tip2'); ?></p>
    <div style="margin: 12px 0 20px;">
        <a href="https://drive.google.com/open?id=1BVYyC49HXTUsv0E5gp7gSoDpmtsKqdIM" target="_blank" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa fa-download"></i> Download Client
        </a>
    </div>
    <p>3. <?php elang('create_account_tip3'); ?></p>
    <p>4. <?php elang('create_account_tip4'); ?></p>
    <pre class="realmlist-block">set realmlist <?php echo get_config('realmlist') . "\n"; ?>set realmname <?php echo $antiXss->xss_clean(get_config('page_title')); ?></pre>
</div>
