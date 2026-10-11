<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li>
                <span class="m-r-sm text-muted welcome-message"><?= __('Welcome') . ' ' . $this->auth->getUsername();?></span>
            </li>
            <li>
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <? $lang_c = ($this->fcookies->has("language") ? $this->fcookies->get("language") : "en_US"); ?>
                     <i class="fa fa-language"></i>  <span><?  $language_lists = $this->translate->getLangList(); echo $language_lists[$this->crypto->staticEncode($lang_c)];?></span>
                </a>
                <ul class="dropdown-menu">
                    <? foreach($this->sidebar->getSideLangsMenu() as $lang)  { ?>
                        <li>
                            <a href="<?= $this->uri->url_encrypt('language/'. $lang['img']);?>">
                                <?= $lang['title'];?>
                            </a>
                        </li>
                    <? } ?>
                </ul>
            </li>
            <? if(isset($sys_messages)) { ?>
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <? $msg_count = count($sys_messages);?>
                    <i class="fa fa-envelope"></i>  <span class="label label-warning"><?= $msg_count;?></span>
                </a>
                <? foreach($sys_messages as $index => $message) {?>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <div class="dropdown-messages-box">
                                <a href="#" class="float-left">
                                    <?= ($message['type'] == 'announcement' ? '<i class="fas fa-megaphone fa-2x"></i>' : '<i class="fa fa-exclamation-triangle fa-2x text-warning"></i>'); ?>
                                </a>
                                <div class="media-body">
                                    <small class="float-right"><?= $this->date->getOnlyDate($message['created_date']);?></small>
                                    <strong><?= $message['title'];?></strong><br><?= substr($message['message'], 0, 100);?>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                        </li>
                        <?= ($index == $msg_count ? '<li class="divider"></li>' : ''); ?>
                    </ul>
                <? } ?>
            </li>
            <? } ?>
            <li>
                <a href="<?= $this->uri->url_encrypt("logout", true);?>" data-id="logout">
                    <i class="fas fa-sign-out-alt"></i> <?= __("Log out");?>
                </a>
            </li>
        </ul>
    </nav>
</div>
<? $this->partial('partial/breadcrumbs'); ?>
<? $this->partial('partial/notifications'); ?>
