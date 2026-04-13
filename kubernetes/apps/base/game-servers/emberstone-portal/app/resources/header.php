<?php
/**
 * Emberstone Portal - Header Template
 * Dynamic theming based on selected expansion.
 */
$theme    = get_config('_expansion_theme');
$meta     = get_config('_expansion_meta');
$selected = get_config('_selected');
$allExp   = get_config('_expansions');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo $antiXss->xss_clean(get_config("page_title")); ?>">
    <title><?php echo $antiXss->xss_clean(get_config("page_title")); ?></title>
    <link rel="icon" href="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/favicon.ico" type="image/x-icon">
    <link rel="stylesheet"
          href="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/css/bootsnav.css">
    <link rel="stylesheet"
          href="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/css/animate.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet"
          href="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/css/style.css">
    <script src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/js/bootsnav.js"></script>
    <script src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/js/popper.min.js"></script>
    <?php echo getCaptchaJS(); ?>

    <?php echo(!empty(lang('custom_css')) ? '<style>' . lang('custom_css') . '</style>' : ''); ?>
    <!-- Dynamic expansion theme overrides -->
    <style>
    :root {
        --gold: <?php echo $theme['gold']; ?>;
        --gold-bright: <?php echo $theme['gold_bright']; ?>;
        --gold-dark: <?php echo $theme['gold_dark']; ?>;
        --gold-muted: <?php echo $theme['gold_muted']; ?>;
        --gold-glow: <?php echo $theme['gold_glow']; ?>;
        --gold-dim: <?php echo $theme['gold_dim']; ?>;
        --border: <?php echo $theme['border']; ?>;
        --border-hover: <?php echo $theme['border_hover']; ?>;
        --border-active: <?php echo $theme['border_active']; ?>;
    }
    <?php if (!empty($meta['bg_glow_css'])) { ?>
    .bg-glow { background: <?php echo $meta['bg_glow_css']; ?>; }
    <?php } ?>
    <?php if (!empty($meta['hero_filter'])) { ?>
    .hero::before { filter: <?php echo $meta['hero_filter']; ?>; }
    <?php } ?>
    .expansion-select {
        background: rgba(0,0,0,0.3);
        border: 1px solid <?php echo $theme['border_hover']; ?>;
        color: <?php echo $theme['gold_bright']; ?>;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85em;
        cursor: pointer;
        outline: none;
    }
    .expansion-select:hover,
    .expansion-select:focus {
        border-color: <?php echo $theme['gold']; ?>;
    }
    .expansion-select option {
        background: #1a1a2e;
        color: #e0e0e0;
    }
    </style>
</head>
<body>

<!-- Arcane canvas background -->
<canvas id="arcane-canvas"></canvas>
<div class="bg-glow"></div>
<div class="grain-overlay"></div>

<div class="content1">
    <!-- Fixed navbar -->
    <nav class="site-nav" id="site-nav">
        <div class="nav-inner">
            <a class="nav-logo" href="./index.php"><?php echo $antiXss->xss_clean(get_config("page_title")); ?></a>
            <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-links" id="nav-links">
                <li>
                    <select class="expansion-select" onchange="window.location.href='?expansion='+this.value">
                        <?php foreach ($allExp as $key => $exp_item) { ?>
                            <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $selected === $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($exp_item['label']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </li>
                <li><a href="./index.php">Home</a></li>
                <li><a onclick="$('#register').trigger('click'); scrollToContent();">Register</a></li>
                <li><a onclick="$('#howtoconnect').trigger('click'); scrollToContent();">Connect</a></li>
                <?php if (!get_config('disable_online_players')) { ?>
                    <li><a onclick="$('#serverstatus').trigger('click'); scrollToContent();"><?php elang('server_status'); ?></a></li>
                <?php }
                if (!get_config('disable_top_players')) { ?>
                    <li><a onclick="$('#topplayers').trigger('click'); scrollToContent();"><?php elang('top_players'); ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    <div class="nav-mobile" id="nav-mobile">
        <select class="expansion-select" style="margin: 8px 0; width: 100%;" onchange="window.location.href='?expansion='+this.value">
            <?php foreach ($allExp as $key => $exp_item) { ?>
                <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $selected === $key ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($exp_item['label']); ?>
                </option>
            <?php } ?>
        </select>
        <a href="./index.php">Home</a>
        <a onclick="$('#register').trigger('click'); closeMobileNav(); scrollToContent();">Register</a>
        <a onclick="$('#howtoconnect').trigger('click'); closeMobileNav(); scrollToContent();">Connect</a>
        <?php if (!get_config('disable_online_players')) { ?>
            <a onclick="$('#serverstatus').trigger('click'); closeMobileNav(); scrollToContent();"><?php elang('server_status'); ?></a>
        <?php }
        if (!get_config('disable_top_players')) { ?>
            <a onclick="$('#topplayers').trigger('click'); closeMobileNav(); scrollToContent();"><?php elang('top_players'); ?></a>
        <?php } ?>
    </div>

    <!-- Also keep a hidden bootsnav navbar so Bootstrap tab JS still works -->
    <nav class="navbar navbar-default bootsnav" style="display:none !important;">
        <div class="collapse navbar-collapse"><ul class="nav navbar-nav"></ul></div>
    </nav>

    <div class="container">
        <!-- Hero -->
        <section class="hero">
            <div class="hero-card">
                <img class="hero-wow-logo" src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/images/<?php echo htmlspecialchars($meta['hero_logo']); ?>" alt="World of Warcraft">
                <div class="hero-badge">
                    <span class="pulse-dot"></span>
                    <?php echo htmlspecialchars($meta['hero_badge']); ?> &middot; Patch <?php echo $antiXss->xss_clean(get_config("game_version")); ?>
                </div>
                <h1 class="hero-title"><?php echo $antiXss->xss_clean(get_config("page_title")); ?></h1>
                <p class="hero-sub"><?php echo htmlspecialchars($meta['hero_sub']); ?></p>
                <div class="hero-actions">
                    <a class="hero-btn hero-btn-primary" onclick="$('#register').trigger('click'); scrollToContent();">
                        <i class="fa fa-bolt"></i> Create Account
                    </a>
                    <a class="hero-btn hero-btn-ghost" onclick="$('#howtoconnect').trigger('click'); scrollToContent();">
                        <i class="fa fa-plug"></i> How to Connect
                    </a>
                </div>
                <div class="hero-realm">
                    <i class="fa fa-globe"></i>
                    <?php elang('realmlist'); ?>: <strong><?php echo get_config('realmlist'); ?></strong>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>
