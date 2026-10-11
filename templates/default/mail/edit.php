<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-md-6">
                <form id="create-maccount-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="mail_account"> <?= __('Mail Address');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-mail_account">
                                <?= $form->render('mail_account',array('class' => 'form-control form-control-sm')); ?>
                                <span class="input-group-addon">@<?= $domain_name; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="password"> <?= __('Mail Password');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render("mail_password",array('class' => 'form-control form-control-sm')) ?>
                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="username"> <?= __('Confirm Password'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('mail_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="unlimited_quota"> <?= __('Unlimited');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('unlimited_quota',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="mail_quota"> <?= __('Mail Quota') . ' ' . '(MB)';?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" data-autoclose="true">
                                <?= $form->render('mail_quota',array('class' => 'form-control form-control-sm input-sm'));?>
                                <span class="input-group-addon">MB</span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="forwarding"><?= __('Forwarding');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('forwarding',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="forward_to_address"> <?= __('Forward Mail Address');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('forward_to_address',array('class' => 'form-control form-control-sm input-sm')); ?>
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
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>