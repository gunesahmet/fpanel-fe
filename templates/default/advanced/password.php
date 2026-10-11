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
                <li class=" nav-item active">
                    <a href="<?= $this->uri->url_encrypt('system/passwordsettings');?>">
                        <?= __('Password Rules'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/certificate');?>">
                        <?= __('System Certificate'); ?>
                    </a>
                </li>
                <li class="nav-item">
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
                <div class="tab-pane in active" id="tab-password-rule">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="system-notify-form" class="form-horizontal" method="post" action="<?= $this->uri->url_encrypt('system/passwordsettings')?>">
                                <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="alpha_chk"><?= __('Alphabetic Characters');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('alpha_chk',array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="numeric_chk"><?= __('Numeric Characters');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('numeric_chk',array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="special_chk"><?= __('Special Characters');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('special_chk',array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="minimum_len"><?= __('Minimum Length');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('minimum_len',array('class' => 'form-control form-control-sm input-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="maximum_len"><?= __('Maximum Length');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('maximum_len',array('class' => 'form-control form-control-sm input-sm')); ?>
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
