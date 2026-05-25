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

    <style>
    /* ── Mobile polish (overlay-only, no Docker rebuild) ─────────────── */
    /* Belt-and-braces: never let the document overflow horizontally so
       the fixed-position nav (and its right-aligned hamburger) can't end
       up off-screen because of a stray child element. */
    html, body {
        overflow-x: hidden;
        max-width: 100%;
    }
    *, *::before, *::after {
        box-sizing: border-box;
    }

    /* Long words / URLs break instead of forcing horizontal scroll */
    body, .post-card p, .connect-steps p, .box1 p, .box1 li {
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
    code {
        word-break: break-word;
        white-space: normal;
    }
    /* Tables get a horizontal scrollbar instead of breaking the page */
    .box1 .table-responsive,
    .box1 table {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    img {
        max-width: 100%;
        height: auto;
    }

    /* Tablet (768-991): allow the 3 hero buttons to wrap onto two lines */
    @media (max-width: 991px) {
        .hero-actions {
            flex-wrap: wrap;
        }
        pre.realmlist-block {
            font-size: 1rem;
            padding: 14px 18px;
        }
        /* Keep the navbar fully inside the viewport so the hamburger
           sits at the right edge instead of past it */
        .site-nav,
        .site-nav.scrolled {
            padding-left: 14px;
            padding-right: 14px;
        }
        .nav-inner {
            max-width: 100%;
            width: 100%;
        }
    }

    /* Mobile (<768): tighten paddings + stack inline button rows */
    @media (max-width: 767px) {
        .connect-steps a.btn,
        .connect-steps .btn {
            margin: 6px 0 !important;
            margin-left: 0 !important;
            width: 100%;
            justify-content: center;
        }
        .hero-realm {
            font-size: 0.85rem;
            line-height: 1.5;
            word-break: break-word;
        }
        .hero-realm strong {
            display: inline-block;
        }
        pre.realmlist-block {
            font-size: 0.9rem;
            padding: 12px 14px;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .modal-dialog {
            margin: 8px;
            max-width: calc(100% - 16px);
        }
        .post-card {
            padding: 18px;
        }
        /* Form controls full-width on phones */
        .input-group input,
        .input-group select,
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            max-width: 100%;
            box-sizing: border-box;
        }
    }
    </style>
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
                <li><a href="/db/">Database</a></li>
                <?php if (!get_config('disable_online_players')) { ?>
                    <li><a onclick="$('#serverstatus').trigger('click'); scrollToContent();"><?php elang('server_status'); ?></a></li>
                <?php }
                if (!get_config('disable_top_players')) { ?>
                    <li><a onclick="$('#topplayers').trigger('click'); scrollToContent();"><?php elang('top_players'); ?></a></li>
                <?php } ?>
                <li><a href="https://discord.gg/quUmkgb5sD" target="_blank" rel="noopener noreferrer">Discord</a></li>
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
        <a href="/db/">Database</a>
        <?php if (!get_config('disable_online_players')) { ?>
            <a onclick="$('#serverstatus').trigger('click'); closeMobileNav(); scrollToContent();"><?php elang('server_status'); ?></a>
        <?php }
        if (!get_config('disable_top_players')) { ?>
            <a onclick="$('#topplayers').trigger('click'); closeMobileNav(); scrollToContent();"><?php elang('top_players'); ?></a>
        <?php } ?>
        <a href="https://discord.gg/quUmkgb5sD" target="_blank" rel="noopener noreferrer">Discord</a>
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
                    <a class="hero-btn hero-btn-ghost" href="https://discord.gg/quUmkgb5sD" target="_blank" rel="noopener noreferrer">
                        <i class="fa fa-comments"></i> Join Discord
                    </a>
                </div>
                <div class="hero-realm">
                    <i class="fa fa-globe"></i>
                    <?php elang('realmlist'); ?>: <strong><?php echo get_config('realmlist'); ?></strong>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>
