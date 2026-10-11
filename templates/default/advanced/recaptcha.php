<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="tabbable-line">
            <ul class="nav nav-pills nav-fill" id="advanced-settings-tabs">
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/passwordsettings');?>">
                        <?= __('Password Rules'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/certificate');?>">
                        <?= __('System Certificate'); ?>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?= $this->uri->url_encrypt('system/recaptchasettings');?>">
                        <?= __('Recaptcha Settings'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/addnewadmin');?>">
                        <?= __('Additional System Administrator'); ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane in active" id="tab-recaptcha">
                    <form id="system-notify-form" class="form-horizontal" method="post" action="<?= $this->uri->url_encrypt('system/recaptchasettings')?>">
                        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="activate_recaptcha"><?= __('Activate Recaptcha');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('activate_recaptcha',array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="site_key"><?= __('Site Key');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('site_key',array('class' => 'form-control form-control-sm input-sm')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="site_secret"><?= __('Site Secret');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('site_secret',array('class' => 'form-control form-control-sm input-sm')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="float-right">
                                            <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                            <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <? if($recaptcha_activate != false) { ?>
                                    <h2><?= __('Recaptcha Preview');?></h2>
                                    <div class="g-recaptcha" data-sitekey="<?= $site_key;?>"></div>
                                <? } else { ?>
                                    <div class="row margin-top-10">
                                        <?= __('Generate your own Google Captcha keys and fill them in').' <a href="http://www.google.com/recaptcha" target="_blank">'.__('here').'</a>';?>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

<? if($recaptcha_activate != false) { ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
<? } ?>

