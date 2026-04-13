<?php
/**
 * Froststone Registration Portal - How to Connect Template
 */
?>
<div class="connect-steps" style="line-height: 1.8;">
    <h4><i class="fa fa-plug"></i> How to Connect</h4>
    <hr>

    <p><strong>Step 1</strong> &mdash; <?php elang('create_account_tip1'); ?></p>

    <p><strong>Step 2</strong> &mdash; Download the <strong>World of Warcraft 3.3.5a</strong> client (build 12340).</p>
    <div style="margin: 16px 0 24px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
        <a href="magnet:?xt=urn:btih:322598D924EBA39CB15002928C7933E1B5C28DC6&dn=Wrath+of+the+Lich+King+3.3.5a" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa fa-windows"></i> Windows Client (Torrent)
        </a>
        <a href="magnet:?xt=urn:btih:4795C2C70355992039EE7D895730DD906FF595FA&dn=Wrath%20of%20the%20Lich%20King%203.3.5a%20%28Mac%29" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa fa-apple"></i> Mac Client (Torrent)
        </a>
    </div>
    <p style="font-size: 0.9em; color: var(--text-dim);">These are the standard community-shared 3.3.5a client (~6.3 GB). You will need a torrent client such as <a href="https://www.qbittorrent.org/" target="_blank" rel="noopener">qBittorrent</a> to download.</p>

    <p><strong>Step 3</strong> &mdash; <?php elang('create_account_tip3'); ?></p>

    <p><strong>Step 4</strong> &mdash; Open the <code>realmlist.wtf</code> file inside your WoW folder with a text editor. Delete everything and paste:</p>
    <pre class="realmlist-block">set realmlist <?php echo get_config('realmlist') . "\n"; ?>set realmname <?php echo $antiXss->xss_clean(get_config('page_title')); ?></pre>

    <p><strong>Step 5</strong> &mdash; Launch <code>Wow.exe</code> directly. Do <strong>not</strong> use the Launcher &mdash; it will try to update the client.</p>

    <hr>
    <h5><i class="fa fa-info-circle"></i> Important Notes</h5>
    <ul style="margin-top: 8px;">
        <li>You need the WoW <strong>3.3.5a</strong> client (Wrath of the Lich King, build 12340). Retail, Classic, or other expansion clients will not work.</li>
        <li>No patches or modifications are required beyond editing the realmlist.</li>
        <li>Log in with your <strong>username</strong>, not your email address.</li>
    </ul>
</div>
