<? $server_link = (isset($server_id) ? "/server/$server_id" : "");?>
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
            <div class="col-md-12">
                <div class="tabbable-line">
                    <ul class="nav nav-pills nav-fill" id="ftp-settings-tabs">
                        <li class="nav-item active">
                            <a href="<?= $this->uri->url_encrypt("ftp/settings$server_link");?>" href="#tab-ftp-general-settings"><?= __('General Settings'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("ftp/sessions$server_link");?>" href="#tab-active-sessions"><?= __('Active Sessions'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-ftp-general-settings">
                            <div class="row">
                                <div class="col-md-6">
                                    <form id="create-database-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                                        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="timeout_idle"> <strong><?= __('Timeout Idle');?></strong></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('timeout_idle',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="timeout_no_transfer"> <strong><?= __('Timeout No Transfer');?></strong></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('timeout_no_transfer',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="maxinstances"> <strong><?= __('Max Instances');?></strong></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('maxinstances',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="max_clients"> <strong><?= __('Max Clients');?></strong></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('max_clients',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="welcome_message"> <strong><?= __('Welcome Message');?></strong></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('welcome_message',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-10">
                                                <div class="float-right">
                                                    <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                                    <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-active-sessions">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>