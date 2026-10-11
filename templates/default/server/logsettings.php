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
                <form id="server-log-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="periodic_del"> <?= __('Periodically Delete Logs');?></label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('periodic_del',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="store_time"> <?= __('Keep Logs for (days)');?></label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('store_time',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="log_compress"> <?= __('Log Compression');?></label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('log_compress',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="send_information"> <?= __('Send Notification with Email');?></label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('send_information',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="email_address"> <?= __('Email Address');?></label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('email_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group">
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
