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
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/recaptchasettings');?>">
                        <?= __('Recaptcha Settings'); ?>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?= $this->uri->url_encrypt('system/addnewadmin');?>">
                        <?= __('Additional System Administrator'); ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane in active" id="tab-add-new-admin">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="system-notify-form" class="form-horizontal" method="post" action="<?= $this->uri->url_encrypt('system/addnewadmin')?>">
                                <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="username"><?= __('Username');?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="email"><?= __('Email Address');?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->render('email',array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="password"> <?= __('Password');?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group input-group-sm" id="input-group-password">
                                                <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="confirm-password"> <?= __('Confirm Password'); ?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->render('password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="site_secret"><?= __('Fullname');?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->render('fullname',array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
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
