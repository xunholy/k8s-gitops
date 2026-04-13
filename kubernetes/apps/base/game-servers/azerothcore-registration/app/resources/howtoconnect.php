<?php
/**
 * Froststone Registration Portal - How to Connect Template
 */
?>
<div class="connect-steps" style="line-height: 1.8;">
    <h4><i class="fa fa-plug"></i> How to Connect</h4>
    <hr>
    <p>1. <?php elang('create_account_tip1'); ?></p>
    <p>2. Download and install the <strong>World of Warcraft 3.3.5a</strong> client (build 12340).</p>
    <div style="margin: 12px 0 20px; text-align: center;">
        <a href="https://mega.nz/folder/klkUgayB#4uy6l0nKCl2Vx0yO_v42yw" target="_blank" rel="noopener" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa fa-download"></i> Download Client (MEGA)
        </a>
    </div>
    <p>3. <?php elang('create_account_tip3'); ?></p>
    <p>4. <?php elang('create_account_tip4'); ?></p>
    <pre class="realmlist-block">set realmlist <?php echo get_config('realmlist') . "\n"; ?>set realmname <?php echo $antiXss->xss_clean(get_config('page_title')); ?></pre>
    <p>5. Launch <code>Wow.exe</code> directly. Do not use the Launcher or it will try to update the client. Log in with your username, not your email.</p>
</div>
