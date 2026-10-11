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
                <form id="create-support-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="support_status"> <?= __('Support Status'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('support_status',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="disable_logins"> <?= __('Disable Client Logins'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('disable_logins',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="request_user_email"> <?= __('Email Address for Contact'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('request_user_email',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="username"> <?= __('Support Username'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="password"> <?= __('Support User Password'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render('password',array('class' => 'form-control form-control-sm')); ?>
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
                            <?= $form->render('password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="title"> <?= __('Support Message Title'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('title',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="support_message"> <?= __('Support Message'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('support_message',array('class' => 'form-control form-control-sm','style' => 'resize:none')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
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