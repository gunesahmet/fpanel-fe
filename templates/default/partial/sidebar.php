<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element text-center">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="text-white text-center"><i class="fp fp-fp-logo fp-5x img-thumbnail rounded-circle"></i></span>
                    </a>
                    <? if( $controller_name != "install") { ?>
                    <ul class="dropdown-menu   m-t-xs">
                        <? if( $user_type < RESELLER_USER) { ?>
                        <li><a href="<?= $this->uri->url_encrypt("services/management");?>"><?= __('Check Services')?></a></li>
                        <li><a href="<?= $this->uri->url_encrypt("update");?>"><?= __('Check Updates')?></a></li>
                        <li><a href="<?= $this->uri->url_encrypt("session");?>"><?= __('Who is logged')?></a></li>
                        <li class="divider"></li>
                        <li><a href="<?= $this->uri->url_encrypt("logout", true);?>"><?= __('Logout')?></a></li>
                        <? } ?>
                    </ul>
                    <? } ?>
                </div>
                <div class="logo-element">
                    <? if($this->config->application->type == CLOUD) { ?>
                        <span><i class="fa fa-cloud"></i></span>
                    <? } else { ?>
                        <? if(PANEL_OS_TYPE == WINDOWS) { ?>
                            <span><i class="fp fp-windows"></i></span>
                        <? } else { ?>
                            <span><i class="fp fp-linux"></i></span>
                        <? } ?>
                    <? } ?>

                </div>
            </li>
            <? if(!empty($system_items['sidebar'])) {

                foreach ($system_items['sidebar'] as $user_menu) {
                    if(!empty($user_menu['submenu'])) {
                        $href = "";
                        $class = "active";
                    }
                    else {
                        $href = $user_menu['link'];
                        $class = $user_menu['liclass'];
                    }
                    ?>
                    <li class="active">
                        <a href="<?= $href;?>"<?= isset($user_menu["id"]) ? "id='$user_menu[id]'" : "";?>><?= $user_menu['icon'];?><span class="nav-label"><?= $user_menu['name']?></span>
                            <?
                            if(!empty($user_menu['submenu'])) {
                                echo '<span class="fa arrow "></span>';
                            }
                            ?>
                        </a>

                        <? if(!empty($user_menu['submenu'])) { ?>
                            <ul class="nav nav-second-level">
                                <? foreach ( $user_menu['submenu'] as $sub_menu) {
                                    if(!empty($sub_menu['subsubmenu'])) { ?>
                                        <li>
                                            <a href="#"><?= $sub_menu['icon'];?><?= $sub_menu['subname']?>
                                                <span class="fa arrow"></span>
                                            </a>
                                            <ul class="nav nav-third-level">
                                                <? foreach($sub_menu['subsubmenu'] as $sub_sub_menu) { ?>
                                                    <li>
                                                        <a href="<?= $sub_sub_menu['sub_sublink'];?>"><?= $sub_sub_menu['sub_subname'];?></a>
                                                    </li>
                                                <? }?>
                                            </ul>
                                        </li>
                                    <? } else { ?>
                                        <li>
                                            <a href="<?= $sub_menu['sublink']; ?>"<?= isset($sub_menu["id"]) ? "data-id='$sub_menu[id]'" : "";?>><?= $sub_menu['icon'];?><?= $sub_menu['subname']?></a>
                                        </li>
                                    <? } ?>
                                <? } ?>
                            </ul>
                        <? } ?>
                    </li>
                <? }
            } ?>
        </ul>
    </div>
</nav>


