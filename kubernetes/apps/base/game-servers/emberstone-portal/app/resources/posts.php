<?php
/**
 * Emberstone Portal - Posts Template
 * Content switches based on selected expansion.
 */
$selected = get_config('_selected');

if ($selected === 'wotlk') { ?>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-shield"></i> Welcome to Emberstone</h4>
    <hr>
    <p style="text-align: justify">
        Emberstone is a Wrath of the Lich King realm running patch 3.3.5a with enhanced rates
        and quality-of-life features designed for fun. Whether you are a returning veteran or
        experiencing Northrend for the first time, Emberstone offers a polished WotLK experience
        with AI companions, solo-friendly dungeons, and a living world.
    </p>
</div>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-bolt"></i> Server Rates &amp; Features</h4>
    <hr>
    <p style="text-align: justify">
        <strong>5x XP</strong> on kills, quests, exploration, and battlegrounds.
        <strong>5x loot</strong> for common and uncommon drops, <strong>3x</strong> for rare, <strong>2x</strong> for epic.
        <strong>5x gold</strong>, <strong>5x reputation</strong>, and <strong>5x honor</strong> gains.
        <strong>5x profession</strong> skill-ups for crafting and gathering.
        All <strong>flight paths unlocked</strong> with instant travel.
        <strong>10g starting gold</strong> (50g for Death Knights).
        Mail delivered <strong>instantly</strong>. Max level <strong>80</strong>.
    </p>
</div>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-users"></i> Modules &amp; Extras</h4>
    <hr>
    <p style="text-align: justify">
        <strong>PlayerBots</strong> &mdash; AI-controlled players that quest, group, and PvP alongside you.
        <strong>AutoBalance</strong> &mdash; Dungeons and raids scale to your party size, solo or with friends.
        <strong>Transmogrification</strong> &mdash; Change the look of your gear at the Transmog NPC.
        <strong>Auction House Bot</strong> &mdash; The AH is always stocked with items.
        <strong>AoE Loot</strong> &mdash; Loot all nearby corpses at once.
        <strong>Auto-Learn Spells</strong> &mdash; New abilities learned automatically on level up.
        <strong>Solo Dungeon Finder</strong> &mdash; Queue for dungeons solo or with any group size.
        <strong>Cross-Faction BGs</strong> &mdash; Battlegrounds pop regardless of faction balance.
    </p>
</div>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-play-circle"></i> Getting Started</h4>
    <hr>
    <p style="text-align: justify">
        Create your account to begin your adventure. Once registered, download a WoW 3.3.5a client,
        update your <code>realmlist.wtf</code> file, and log in to explore Northrend, storm Icecrown Citadel,
        battle in Wintergrasp, and conquer Ulduar. Check the "How to Connect" page for setup instructions.
    </p>
</div>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-thumbs-up"></i> Vote for Emberstone</h4>
    <hr>
    <p style="text-align: justify">
        Enjoying the server? Help us grow by voting! Every vote helps new players discover Emberstone.
    </p>
    <div style="text-align: center; margin-top: 12px;">
        <a href="https://www.xtremetop100.com/in.php?site=1132378609" target="_blank" rel="noopener noreferrer" title="Vote for Emberstone on XtremeTop100">
            <img src="https://www.xtremeTop100.com/votenew.jpg" border="0" alt="Vote for Emberstone on XtremeTop100" style="border-radius: 4px;">
        </a>
    </div>
</div>

<?php } else { ?>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-shield"></i> Welcome to Emberstone</h4>
    <hr>
    <p style="text-align: justify">
        Emberstone is a Classic World of Warcraft realm running the original 1.12.1 patch with faithful game
        mechanics, class balance, and progressive content unlocks. Whether you are a returning veteran or stepping
        into Azeroth for the first time, Emberstone offers a stable, blizzlike experience with an active community
        and dedicated staff.
    </p>
</div>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-bolt"></i> Server Rates &amp; Features</h4>
    <hr>
    <p style="text-align: justify">
        <strong>3x XP</strong> on kills, quests, and exploration.
        <strong>3x loot</strong> for common and uncommon drops, <strong>2x</strong> for rare, and <strong>1x</strong> (blizzlike) for epic items.
        <strong>3x gold</strong> and <strong>3x reputation</strong> gains across the board.
        Mail is delivered <strong>instantly</strong> with no delay.
        Max level is <strong>60</strong> with up to <strong>10 characters</strong> per account.
    </p>
</div>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-play-circle"></i> Getting Started</h4>
    <hr>
    <p style="text-align: justify">
        Create your account to begin your adventure. Once registered, update your <code>realmlist.wtf</code>
        file and log in to explore the Eastern Kingdoms and Kalimdor, run dungeons with friends, raid Molten Core
        and Onyxia, and fight for honor in Alterac Valley. Check the "How to Connect" page for setup instructions.
    </p>
</div>

<div class="box1 post-card" style="margin-top: 16px;">
    <h4><i class="fa fa-thumbs-up"></i> Vote for Emberstone</h4>
    <hr>
    <p style="text-align: justify">
        Enjoying the server? Help us grow by voting! Every vote helps new players discover Emberstone.
    </p>
    <div style="text-align: center; margin-top: 12px;">
        <a href="https://www.xtremetop100.com/in.php?site=1132378609" target="_blank" rel="noopener noreferrer" title="Vote for Emberstone on XtremeTop100">
            <img src="https://www.xtremeTop100.com/votenew.jpg" border="0" alt="Vote for Emberstone on XtremeTop100" style="border-radius: 4px;">
        </a>
    </div>
</div>

<?php } ?>
