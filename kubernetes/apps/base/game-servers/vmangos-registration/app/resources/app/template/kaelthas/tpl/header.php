<?php
/**
 * Created by Amin.MasterkinG
 * Website : MasterkinG32.CoM
 * Email : lichwow_masterking@yahoo.com
 * Date: 11/26/2018 - 8:36 PM
 */
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
    <?php echo(!empty(lang('tpl_kaelthas_custom_css')) ? '<style>' . lang('tpl_kaelthas_custom_css') . '</style>' : ''); ?>
</head>
<body>
<div class="content1">
    <div class="container">
        <nav class="navbar navbar-default brand-center bootsnav">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="./index.php"><img
                                src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/images/wow-logo.png"
                                class="logo" alt="<?php echo $antiXss->xss_clean(get_config("page_title")); ?>"></a>
                </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="nav navbar-nav" data-in="fadeInDown" data-out="fadeOutUp">
                        <li><a href="./index.php"><?php elang('home'); ?></a></li>
                        <li><a onclick="$('#register').trigger('click')"><?php elang('register'); ?></a></li>
                        <li><a onclick="$('#howtoconnect').trigger('click')"><?php elang('how_to_connect'); ?></a></li>
                        <?php if (!get_config('disable_online_players')) { ?>
                            <li><a onclick="$('#serverstatus').trigger('click')"><?php elang('server_status'); ?></a></li>
                        <?php }
                        if (!get_config('disable_top_players')) { ?>
                            <li><a onclick="$('#topplayers').trigger('click')"><?php elang('top_players'); ?></a></li>
                        <?php } ?>
                        <li><a onclick="$('#contact').trigger('click')"><?php elang('contact'); ?></a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="hero-banner">
            <img class="hero-logo"
                 src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/images/wow-logo.png"
                 alt="">
            <h1><?php echo $antiXss->xss_clean(get_config("page_title")); ?></h1>
            <div class="hero-divider"></div>
            <div class="hero-sub">Vanilla &middot; Patch <?php echo $antiXss->xss_clean(get_config("game_version")); ?></div>
            <div class="realm-badge">
                <i class="fa fa-globe"></i>&nbsp;
                <?php elang('realmlist'); ?>: <span><?php echo get_config('realmlist'); ?></span>
            </div>
        </div>
