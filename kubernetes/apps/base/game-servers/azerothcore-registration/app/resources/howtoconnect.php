<?php
/**
 * Froststone Registration Portal - How to Connect Template
 */
?>
<div class="connect-steps" style="line-height: 1.8;">
    <h4><i class="fa fa-plug"></i> How to Connect</h4>
    <hr>
    <p>1. <?php elang('create_account_tip1'); ?></p>
    <p>2. Download and install the <strong>World of Warcraft 3.3.5a</strong> client (build 12340). You will need a torrent client such as <a href="https://www.qbittorrent.org/" target="_blank" rel="noopener">qBittorrent</a> to download (~6.3 GB).</p>
    <div style="margin: 12px 0 20px; text-align: center;">
        <a href="magnet:?xt=urn:btih:322598D924EBA39CB15002928C7933E1B5C28DC6&dn=Wrath+of+the+Lich+King+3.3.5a" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa fa-windows"></i> Windows Client
        </a>
        <a href="magnet:?xt=urn:btih:4795C2C70355992039EE7D895730DD906FF595FA&dn=Wrath%20of%20the%20Lich%20King%203.3.5a%20%28Mac%29" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; margin-left: 8px;">
            <i class="fa fa-apple"></i> Mac Client
        </a>
    </div>
    <p>3. <?php elang('create_account_tip3'); ?></p>
    <p>4. <?php elang('create_account_tip4'); ?></p>
    <pre class="realmlist-block">set realmlist <?php echo get_config('realmlist') . "\n"; ?>set realmname <?php echo $antiXss->xss_clean(get_config('page_title')); ?></pre>
    <p>5. Launch <code>Wow.exe</code> directly. Do not use the Launcher or it will try to update the client. Log in with your username, not your email.</p>
</div>
