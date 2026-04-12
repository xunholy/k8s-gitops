<?php
/**
 * Froststone Registration Portal - How to Connect Template
 */
?>
<div class="connect-steps" style="line-height: 1.8;">
    <h4><i class="fa fa-plug"></i> How to Connect</h4>
    <hr>
    <p>1. <?php elang('create_account_tip1'); ?></p>
    <p>2. Download and install a <strong>World of Warcraft 3.3.5a</strong> client (build 12340). Search for "WoW 3.3.5a client download" or check WoW preservation communities.</p>
    <p>3. <?php elang('create_account_tip3'); ?></p>
    <p>4. <?php elang('create_account_tip4'); ?></p>
    <pre class="realmlist-block">set realmlist <?php echo get_config('realmlist') . "\n"; ?>set realmname <?php echo $antiXss->xss_clean(get_config('page_title')); ?></pre>
    <hr>
    <h5><i class="fa fa-info-circle"></i> Client Requirements</h5>
    <p>You need the WoW <strong>3.3.5a</strong> client (Wrath of the Lich King, build 12340). Retail or Classic clients will not work. The client does not require any patches or modifications beyond editing the realmlist.</p>
</div>
