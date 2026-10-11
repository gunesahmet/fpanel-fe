<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <span class="label label-primary float-right"><?= __("Mail Notification");?></span>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-6">
                        <form id="system-notify-form" class="form-horizontal" method="post" action="<?= $this->uri->url_encrypt('system/notify')?>">
                            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="host"> <?= __('Mail Server Host');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('host',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="port"> <?= __('Mail Server Port');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('port',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="secure"> <?= __('Secure');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('secure',array('class' => 'form-control select-picker'));?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="from_fullname"> <?= __('From Name');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('from_fullname',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="email"> <?= __('Email Address');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('email',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="password"> <?= __('Password');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                        <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="email"> <?= __('Confirm Password');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render("password",array('id' => 'confirm-password' , 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                        <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
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
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>