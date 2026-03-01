<?php
/**
 * Created by Amin.MasterkinG
 * Website : MasterkinG32.CoM
 * Email : lichwow_masterking@yahoo.com
 * Date: 11/26/2018 - 8:36 PM
 */
require_once 'header.php'; ?>
<div class="row">
    <div class="main-box">
        <div class="col-md-8" style="margin-top: 20px;">
            <div>
                <ul class="nav nav-tabs" style="display: none;">
                    <li><a data-toggle="tab" href="#pills-register" id="register"><?php elang('register'); ?></a></li>
                    <li><a data-toggle="tab" href="#pills-howtoconnect" id="howtoconnect"><?php elang('how_to_connect'); ?></a></li>
                    <?php if (!get_config('disable_online_players')) { ?>
                        <li><a data-toggle="tab" href="#pills-serverstatus" id="serverstatus"><?php elang('server_status'); ?><</a></li>
                    <?php }
                    if (!get_config('disable_top_players')) { ?>
                        <li><a data-toggle="tab" href="#pills-topplayers" id="topplayers"><?php elang('top_players'); ?></a></li>
                    <?php } ?>
                    <li><a data-toggle="tab" href="#pills-contact" id="contact"><?php elang('contact'); ?></a></li>
                </ul>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade in <?php echo((empty($error_error) && empty($success_msg)) ? 'active' : ''); ?>"
                         id="pills-main">
                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
                            <!-- Indicators -->
                            <ol class="carousel-indicators">
                                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                <li data-target="#myCarousel" data-slide-to="1"></li>
                                <li data-target="#myCarousel" data-slide-to="2"></li>
                            </ol>
                            <!-- Wrapper for slides -->
                            <div class="carousel-inner">
                                <div class="item active">
                                    <img src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/images/slide1.jpg"
                                         alt="WoW" style="width:100%;">
                                </div>
                                <div class="item">
                                    <img src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/images/slide1.jpg"
                                         alt="WoW" style="width:100%;">
                                </div>
                                <div class="item">
                                    <img src="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/template/<?php echo $antiXss->xss_clean(get_config("template")); ?>/images/slide1.jpg"
                                         alt="WoW" style="width:100%;">
                                </div>
                            </div>
                        </div>
                        <?php require_once base_path . 'template/' . $antiXss->xss_clean(get_config("template")) . '/tpl/posts.php'; ?>
                    </div>
                    <div class="tab-pane fade in <?php echo(!(empty($error_error) && empty($success_msg)) ? 'active' : ''); ?>"
                         id="pills-register">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="" method="post">
                                    <div class="box1" style="margin-top: 10px;padding: 10px;">
                                        <?php error_msg();
                                        success_msg(); //Display message. ?>
                                        <div class="input-group">
                                            <span class="input-group"><?php elang('email'); ?></span>
                                            <input type="email" class="form-control" placeholder="<?php elang('email'); ?>" name="email">
                                        </div>
                                        <?php if (!get_config('battlenet_support')) { ?>
                                            <div class="input-group">
                                                <span class="input-group"><?php elang('username'); ?></span>
                                                <input type="text" class="form-control" placeholder="<?php elang('username'); ?>"
                                                       name="username">
                                            </div>
                                        <?php } ?>
                                        <div class="input-group">
                                            <span class="input-group"><?php elang('password'); ?></span>
                                            <input type="password" class="form-control" placeholder="<?php elang('password'); ?>"
                                                   name="password">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group"><?php elang('retype_password'); ?></span>
                                            <input type="password" class="form-control" placeholder="<?php elang('retype_password'); ?>"
                                                   name="repassword">
                                        </div>
                                        <?php echo GetCaptchaHTML();?>
                                        <input name="submit" type="hidden" value="register">
                                        <div class="text-center" style="margin-top: 10px;"><input type="submit"
                                                                                                  class="btn btn-success"
                                                                                                  value="<?php elang('register'); ?>">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="box1 content_box1">
                                    <?php require_once base_path . 'template/' . $antiXss->xss_clean(get_config("template")) . '/tpl/rules.php'; ?>
                                    <hr>
                                    <div class="text-center">
                                        <?php if (empty(get_config('disable_changepassword'))) { ?>
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#changepassword-modal">
                                                <?php elang('change_password'); ?>
                                            </button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-info" data-toggle="modal"
                                                data-target="#restorepassword-modal">
                                            <?php elang('restore_password'); ?>
                                        </button>
                                    </div>
                                    <?php if (get_config('2fa_support')) { ?>
                                        <div class="text-center" data-aos="fade-up" data-aos-delay="100" style="margin-top: 5px;">
                                            <button type="button" class="btn btn-default" data-toggle="modal"
                                                    data-target="#e2fa-modal">
                                                <?php elang('two_factor_authentication'); ?>
                                            </button>
                                        </div>
                                        <div class="modal" id="e2fa-modal">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title"><?php elang('two_factor_authentication'); ?></h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/index.php#register"
                                                              method="post">
                                                            <div>
                                                                <ul>
                                                                    <li><?php elang('two_factor_authentication_tip1'); ?> <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Google Store</a> - <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank">Apple Store</a></li>
                                                                </ul>
                                                            </div>
                                                            <div class="input-group">
                                                                <span class="input-group"><?php elang('email'); ?></span>
                                                                <input type="email" class="form-control" placeholder="<?php elang('email'); ?>"
                                                                       name="email">
                                                            </div>
                                                            <?php if (empty(get_config('battlenet_support'))) { ?>
                                                                <div class="input-group">
                                                                    <span class="input-group"><?php elang('username'); ?></span>
                                                                    <input type="text" class="form-control" placeholder="<?php elang('username'); ?>"
                                                                           name="username">
                                                                </div>
                                                            <?php } echo GetCaptchaHTML();?>
                                                            <input name="submit" type="hidden" value="etfa">
                                                            <div class="text-center" style="margin-top: 10px;"><input
                                                                        type="submit"
                                                                        class="btn btn-primary"
                                                                        value="<?php elang('two_factor_authentication_enable'); ?>"></div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                            <?php elang('close'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } if (get_config('vote_system')) { ?>
                                        <div class="text-center" style="margin-top: 5px;">
                                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                                    data-target="#vote-modal">
                                                <?php elang('vote_for_us'); ?>
                                            </button>
                                        </div>
                                        <div class="modal" id="vote-modal">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title"><?php elang('vote'); ?></h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?php echo $antiXss->xss_clean(get_config("baseurl")); ?>/index.php#register"
                                                              method="post" target="_blank">
                                                            <?php if (get_config('battlenet_support')) { ?>
                                                                <div class="input-group">
                                                                    <span class="input-group"><?php elang('email'); ?></span>
                                                                    <input type="email" class="form-control"
                                                                           placeholder="<?php elang('email'); ?>"
                                                                           name="account">
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="input-group">
                                                                    <span class="input-group"><?php elang('username'); ?></span>
                                                                    <input type="text" class="form-control"
                                                                           placeholder="<?php elang('username'); ?>"
                                                                           name="account">
                                                                </div>
                                                            <?php } ?>
                                                            <div class="text-center" style="margin-top: 10px;">
                                                                <?php
                                                                $vote_sites = get_config('vote_sites');
                                                                if (!empty($vote_sites)) {
                                                                    foreach ($vote_sites as $siteID => $vote_site) {
                                                                        $tmp_id = $siteID + 1;
                                                                        echo '<button type="submit" name="siteid" value="' . $tmp_id . '" style="border:none; background-color: transparent;"><img src="' . $vote_site['image'] . '"></button>';
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                                data-dismiss="modal">
                                                            <?php elang('close'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="modal" id="restorepassword-modal">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><?php elang('restore_password'); ?></h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="post">
                                                        <?php if (get_config('battlenet_support')) { ?>
                                                            <div class="input-group">
                                                                <span class="input-group"><?php elang('email'); ?></span>
                                                                <input type="email" class="form-control"
                                                                       placeholder="<?php elang('email'); ?>"
                                                                       name="email">
                                                            </div>
                                                        <?php } else { ?>
                                                            <div class="input-group">
                                                                <span class="input-group"><?php elang('username'); ?></span>
                                                                <input type="text" class="form-control"
                                                                       placeholder="<?php elang('username'); ?>"
                                                                       name="username">
                                                            </div>
                                                        <?php }
                                                        echo GetCaptchaHTML();?>
                                                        <input name="submit" type="hidden" value="restorepassword">
                                                        <div class="text-center" style="margin-top: 10px;"><input
                                                                    type="submit"
                                                                    class="btn btn-primary"
                                                                    value="<?php elang('restore_password'); ?>"></div>

                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                        <?php elang('close'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal" id="changepassword-modal">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><?php elang('change_password'); ?></h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="post">
                                                        <?php if (get_config('battlenet_support')) { ?>
                                                            <div class="input-group">
                                                                <span class="input-group"><?php elang('email'); ?></span>
                                                                <input type="email" class="form-control"
                                                                       placeholder="<?php elang('email'); ?>"
                                                                       name="email">
                                                            </div>
                                                        <?php } else { ?>
                                                            <div class="input-group">
                                                                <span class="input-group"><?php elang('username'); ?></span>
                                                                <input type="text" class="form-control"
                                                                       placeholder="<?php elang('username'); ?>"
                                                                       name="username">
                                                            </div>
                                                        <?php } ?>
                                                        <div class="input-group">
                                                            <span class="input-group"><?php elang('old_password'); ?></span>
                                                            <input type="password" class="form-control"
                                                                   placeholder="<?php elang('old_password'); ?>"
                                                                   name="old_password">
                                                        </div>
                                                        <div class="input-group">
                                                            <span class="input-group"><?php elang('password'); ?></span>
                                                            <input type="password" class="form-control"
                                                                   placeholder="<?php elang('password'); ?>"
                                                                   name="password">
                                                        </div>
                                                        <div class="input-group">
                                                            <span class="input-group"><?php elang('retype_password'); ?></span>
                                                            <input type="password" class="form-control"
                                                                   placeholder="<?php elang('retype_password'); ?>"
                                                                   name="repassword">
                                                        </div>
                                                        <?php echo GetCaptchaHTML();?>
                                                        <input name="submit" type="hidden" value="changepass">
                                                        <div class="text-center" style="margin-top: 10px;"><input
                                                                    type="submit"
                                                                    class="btn btn-primary"
                                                                    value="<?php elang('change_password'); ?>"></div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">
                                                        <?php elang('close'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="pills-howtoconnect">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box1" style="margin-top: 10px;padding: 10px;text-align: left">
                                    <?php require_once base_path . 'template/' . $antiXss->xss_clean(get_config("template")) . '/tpl/howtoconnect.php'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (!get_config('disable_online_players')) { ?>
                        <div class="tab-pane fade in" id="pills-serverstatus">
                            <div class="box1" style="margin-top: 10px;">
                                <?php
                                foreach (get_config('realmlists') as $onerealm_key => $onerealm) {
                                    echo "<p><span style='color: #005cbf;font-weight: bold;'>{$onerealm['realmname']}</span> <span style='font-size: 12px;'>(" . lang('online_players_msg1') . " " . user::get_online_players_count($onerealm['realmid']) . ")</span></p><hr>";
                                    $online_chars = user::get_online_players($onerealm['realmid']);
                                    if (!is_array($online_chars)) {
                                        echo "<span style='color: #0d99e5;'>" . lang('online_players_msg2') . "</span>";
                                    } else {
                                        echo '<table class="table table-dark"><thead><tr><th scope="col">' . lang('name') . '</th><th scope="col">' . lang('race') . '</th> <th scope="col">' . lang('class') . '</th><th scope="col">' . lang('level') . '</th></tr></thead><tbody>';
                                        foreach ($online_chars as $one_char) {
                                            echo '<tr><th scope="row">' . $antiXss->xss_clean($one_char['name']) . '</th><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/race/' . $antiXss->xss_clean($one_char["race"]) . '-' . $antiXss->xss_clean($one_char["gender"]) . '.gif\'></td><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/class/' . $antiXss->xss_clean($one_char["class"]) . '.gif\'></td><td>' . $antiXss->xss_clean($one_char['level']) . '</td></tr>';
                                        }
                                        echo '</table>';
                                    }
                                    echo "<hr>";
                                }
                                ?>
                            </div>
                        </div>
                    <?php }
                    if (!get_config('disable_top_players')) { ?>
                        <div class="tab-pane fade in" id="pills-topplayers">
                            <div class="box1" style="margin-top: 10px;">
                                <?php
                                $i = 1;
                                foreach (get_config('realmlists') as $onerealm_key => $onerealm) {
                                    echo "<h6 style='color: #005cbf;font-weight: bold;'>{$onerealm['realmname']}</h6><hr>";
                                    $data2show = status::get_top_playtime($onerealm['realmid']);
                                    echo "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"modal\"  data-aos=\"fade-up\" data-aos-delay=\"100\"data-target=\"#modal-id$i\">" . lang('play_time') . "</button><div class=\"modal\" id=\"modal-id$i\"><div class=\"modal-dialog modal-lg\"><div class=\"modal-content\">
                                            <div class=\"modal-header\"><h4 class=\"modal-title\">" . lang('top_players') . " - " . lang('play_time') . "</h4><button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button></div><div class=\"modal-body\">";

                                    if (!is_array($data2show)) {
                                        echo "<span style='color: #0d99e5;'>" . lang('online_players_msg2') . "</span>";
                                    } else {
                                        echo '<table class="table table-striped"><thead><tr><th scope="col">' . lang('rank') . '</th><th scope="col">' . lang('name') . '</th><th scope="col">' . lang('race') . '</th> <th scope="col">' . lang('class') . '</th><th scope="col">' . lang('level') . '</th><th scope="col">' . lang('play_time') . '</th></tr></thead><tbody>';
                                        $m = 1;
                                        foreach ($data2show as $one_char) {
                                            if (empty($one_char['name'])) {
                                                continue;
                                            }
                                            echo '<tr><td>' . $m++ . '<th scope="row">' . $antiXss->xss_clean($one_char['name']) . '</th><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/race/' . $antiXss->xss_clean($one_char["race"]) . '-' . $antiXss->xss_clean($one_char["gender"]) . '.gif\'></td><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/class/' . $antiXss->xss_clean($one_char["class"]) . '.gif\'></td><td>' . $antiXss->xss_clean($one_char['level']) . '</td><td>' . $antiXss->xss_clean(get_human_time_from_sec($one_char['totaltime'])) . '</td></tr>';
                                        }
                                        echo '</table>';
                                    }
                                    echo "</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button></div></div></div></div>";
                                    $i++;

                                    $data2show = status::get_top_killers($onerealm['realmid']);
                                    echo "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"modal\"  data-aos=\"fade-up\" data-aos-delay=\"100\"data-target=\"#modal-id$i\">" . lang('killers') . "</button><div class=\"modal\" id=\"modal-id$i\"><div class=\"modal-dialog modal-lg\"><div class=\"modal-content\">
                                            <div class=\"modal-header\"><h4 class=\"modal-title\">" . lang('top_players') . " - " . lang('killers') . "</h4><button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button></div><div class=\"modal-body\">";
                                    if (!is_array($data2show)) {
                                        echo "<span style='color: #0d99e5;'>" . lang('online_players_msg2') . "</span>";
                                    } else {
                                        echo '<table class="table table-striped"><thead><tr><th scope="col">' . lang('rank') . '</th><th scope="col">' . lang('name') . '</th><th scope="col">' . lang('race') . '</th> <th scope="col">' . lang('class') . '</th><th scope="col">' . lang('level') . '</th><th scope="col">' . lang('kills') . '</th></tr></thead><tbody>';
                                        $m = 1;
                                        foreach ($data2show as $one_char) {
                                            if (empty($one_char['name'])) {
                                                continue;
                                            }
                                            echo '<tr><td>' . $m++ . '<th scope="row">' . $antiXss->xss_clean($one_char['name']) . '</th><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/race/' . $antiXss->xss_clean($one_char["race"]) . '-' . $antiXss->xss_clean($one_char["gender"]) . '.gif\'></td><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/class/' . $antiXss->xss_clean($one_char["class"]) . '.gif\'></td><td>' . $antiXss->xss_clean($one_char['level']) . '</td><td>' . $antiXss->xss_clean($one_char['totalKills']) . '</td></tr>';
                                        }
                                        echo '</table>';
                                    }
                                    echo "</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">" . lang('close') . "</button></div></div></div></div>";
                                    $i++;

                                    $data2show = status::get_top_honorpoints($onerealm['realmid']);
                                    echo "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"modal\"  data-aos=\"fade-up\" data-aos-delay=\"100\"data-target=\"#modal-id$i\">" . lang('honor_points') . "</button><div class=\"modal\" id=\"modal-id$i\"><div class=\"modal-dialog modal-lg\"><div class=\"modal-content\">
                                            <div class=\"modal-header\"><h4 class=\"modal-title\">" . lang('top_players') . " - " . lang('honor_points') . "</h4><button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button></div><div class=\"modal-body\">";
                                    if (!is_array($data2show)) {
                                        echo "<span style='color: #0d99e5;'>" . lang('online_players_msg2') . "</span>";
                                    } else {
                                        echo '<table class="table table-striped"><thead><tr><th scope="col">' . lang('rank') . '</th><th scope="col">' . lang('name') . '</th><th scope="col">' . lang('race') . '</th> <th scope="col">' . lang('class') . '</th><th scope="col">' . lang('rank') . '</th>';

                                        if (get_config('expansion') >= 6) {
                                            echo '<th scope="col">' . lang('honor_level') . '</th>';
                                        }

                                        echo '<th scope="col">' . lang('honor_points') . '</th></tr></thead><tbody>';
                                        $m = 1;
                                        foreach ($data2show as $one_char) {
                                            if (empty($one_char['name'])) {
                                                continue;
                                            }
                                            echo '<tr><td>' . $m++ . '<th scope="row">' . $antiXss->xss_clean($one_char['name']) . '</th><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/race/' . $antiXss->xss_clean($one_char["race"]) . '-' . $antiXss->xss_clean($one_char["gender"]) . '.gif\'></td><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/class/' . $antiXss->xss_clean($one_char["class"]) . '.gif\'></td><td>' . $antiXss->xss_clean($one_char['level']) . '</td>';

                                            if (get_config('expansion') >= 6) {
                                                echo '<td>' . $antiXss->xss_clean($one_char['honorLevel']) . '</td>';
                                                echo '<td>' . $antiXss->xss_clean($one_char['honor']) . '</td>';
                                            } else {
                                                echo '<td>' . $antiXss->xss_clean($one_char['totalHonorPoints']) . '</td>';
                                            }

                                            echo '</tr>';
                                        }
                                        echo '</table>';
                                    }
                                    echo "</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">" . lang('close') . "</button></div></div></div></div>";
                                    $i++;

                                    $data2show = status::get_top_arenapoints($onerealm['realmid']);
                                    echo "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"modal\"  data-aos=\"fade-up\" data-aos-delay=\"100\"data-target=\"#modal-id$i\">" . lang('arena_points') . "</button><div class=\"modal\" id=\"modal-id$i\"><div class=\"modal-dialog modal-lg\"><div class=\"modal-content\">
                                            <div class=\"modal-header\"><h4 class=\"modal-title\">" . lang('top_players') . " - " . lang('arena_points') . ":</h4><button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button></div><div class=\"modal-body\">";
                                    if (!is_array($data2show)) {
                                        echo "<span style='color: #0d99e5;'>" . lang('online_players_msg2') . "</span>";
                                    } else {
                                        echo '<table class="table table-striped"><thead><tr><th scope="col">' . lang('rank') . '</th><th scope="col">' . lang('name') . '</th><th scope="col">' . lang('race') . '</th> <th scope="col">' . lang('class') . '</th><th scope="col">' . lang('level') . '</th><th scope="col">' . lang('arena_points') . '</th></tr></thead><tbody>';
                                        $m = 1;
                                        foreach ($data2show as $one_char) {
                                            if (empty($one_char['name'])) {
                                                continue;
                                            }
                                            echo '<tr><td>' . $m++ . '<th scope="row">' . $antiXss->xss_clean($one_char['name']) . '</th><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/race/' . $antiXss->xss_clean($one_char["race"]) . '-' . $antiXss->xss_clean($one_char["gender"]) . '.gif\'></td><td><img src=\'' . get_config("baseurl") . '/template/' . $antiXss->xss_clean(get_config("template")) . '/images/class/' . $antiXss->xss_clean($one_char["class"]) . '.gif\'></td><td>' . $antiXss->xss_clean($one_char['level']) . '</td><td>' . $antiXss->xss_clean($one_char['arenaPoints']) . '</td></tr>';
                                        }
                                        echo '</table>';
                                    }
                                    echo "</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">" . lang('close') . "</button></div></div></div></div>";
                                    $i++;

                                    $data2show = status::get_top_arenateams($onerealm['realmid']);
                                    echo "<button type=\"button\" class=\"btn btn-info\" data-toggle=\"modal\"  data-aos=\"fade-up\" data-aos-delay=\"100\"data-target=\"#modal-id$i\">" . lang('arena_teams') . "</button><div class=\"modal\" id=\"modal-id$i\"><div class=\"modal-dialog modal-lg\"><div class=\"modal-content\">
                                            <div class=\"modal-header\"><h4 class=\"modal-title\">" . lang('top_players') . " - " . lang('arena_teams') . "</h4><button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button></div><div class=\"modal-body\">";
                                    if (!is_array($data2show)) {
                                        echo "<span style='color: #0d99e5;'>" . lang('online_players_msg2') . "</span>";
                                    } else {
                                        echo '<table class="table table-striped"><thead><tr><th scope="col">' . lang('rank') . '</th><th scope="col">' . lang('name') . '</th><th scope="col">' . lang('rating') . '</th><th scope="col">' . lang('captain_name') . '</th></tr></thead><tbody>';
                                        $m = 1;
                                        foreach ($data2show as $one_char) {
                                            $character_data = status::get_character_by_guid($onerealm['realmid'], $one_char['captainGuid']);

                                            if (empty($character_data['name'])) {
                                                continue;
                                            }

                                            echo '<tr><td>' . $m++ . '<th scope="row">' . $antiXss->xss_clean($one_char['name']) . '</th><td>' . $antiXss->xss_clean($one_char['rating']) . '</td><td>' . (!empty($character_data["name"]) ? $antiXss->xss_clean($character_data['name']) : '-') . '</td></tr>';
                                        }
                                        echo '</table>';
                                    }
                                    echo "</div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">" . lang('close') . "</button></div></div></div></div>";
                                    $i++;
                                    echo "<hr>";
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="tab-pane fade in" id="pills-contact">
                        <div class="box1" style="margin-top: 10px;">
                            <?php require_once base_path . 'template/' . $antiXss->xss_clean(get_config("template")) . '/tpl/contactus.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 sidebar">
            <div class="box1">
                <?php elang('server_information'); ?>
                <hr style="border-color: #F1A40F;">
                <p><?php elang('realmlist'); ?>: <span style="color: yellow;"><?php echo get_config('realmlist'); ?></span></p>
                <?php echo(!empty(get_config("game_version")) ? '<p>' . lang('game_version') . ': <span style="color: yellow;">' . get_config("game_version") . '</span></p>' : ''); ?>
                <?php echo(!empty(get_config("patch_location")) ? '<p>' . lang('server_patch') . ' : <a href="' . get_config("patch_location") . '" style="color: yellow;">' . lang('download') . '</a></p>' : ''); ?>
            </div>
             <?php if(!empty(get_config('supported_langs'))) { ?>
            <div class="box1">
            <form action="" method="post">
                <div class="form-group">
                    <label for="lang"><?php elang('change_lang_form_head'); ?></label>
                        <select class="form-control" id="langchange" name="langchange">
                        <?php
                            $supported_langs = get_config('supported_langs');
                            foreach($supported_langs as $val => $lang) {
                                echo '<option value="' . $val . '">' . $lang . '</option>';
                            }
                        ?>
                        </select>
                </div>
                    <input name="langchangever" type="hidden" value="langchanger">
                    <button type="submit" class="btn btn-primary"><?php elang('change_lang_sub'); ?></button>
            </form>
            </div>
             <?php } ?>
            <div class="box1">
                Discord
                <hr style="border-color: #F1A40F;">
                <iframe src="https://discordapp.com/widget?id=376650959532589057&theme=dark" width="330"
                        height="600" allowtransparency="true" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
