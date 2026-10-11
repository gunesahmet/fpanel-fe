<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
            <form id="edit-server" method="post" class="form-horizontal row" action="<?= $this->uri->encode("server/edit/$server_id");?>">
                <div class="col-md-7">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="server_os"> <strong><?= __('Operating System');?></strong> <b class="text-danger">*</b></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('server_os',array('class' => 'form-control form-control-sm select-picker', "disabled" => "disabled")); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="ip_address"> <strong><?= __('Hostname');?></strong> <b class="text-danger">*</b></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('hostname',array('class' => 'form-control form-control-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="ip_address"> <strong><?= __('IP Address');?></strong> <b class="text-danger">*</b></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('ip_address',array('class' => 'form-control form-control-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="description"> <strong><?= __("Description");?></strong></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('description',array('class' => 'form-control form-control-sm')); ?>
                        </div>
                    </div>

                    <hr />
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="domain_limit"> <strong><?= __('Domain Limit');?></strong> <b class="text-danger">*</b></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('domain_limit',array('class' => 'form-control form-control-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="database_limit"> <strong><?= __('Database Limit');?></strong> <b class="text-danger">*</b></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('database_limit',array('class' => 'form-control form-control-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="mail_limit"> <strong><?= __('Mail Limit');?></strong> <b class="text-danger">*</b></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('mail_limit',array('class' => 'form-control form-control-sm')); ?>
                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>