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
                <form id="edit-vdir-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <h4 class="form-section"><?= __('Virtual Directory');?></h4>
                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Directory Name');?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('dir_name',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Physical Path');?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('physical_path',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Access Policy');?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('accessPolicy',array('class' => 'form-control form-control-sm multiple-select'));?>
                        </div>
                    </div>

                    <h4 class="form-section"><?= __('Application Settings');?></h4>

                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Create as Application');?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('create_application',array('class' => 'checkbox-blue'));?>

                        </div>
                    </div>

                    <h4 class="form-section"><?= __('ASP Settings');?></h4>

                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Max Size of ASP Buffer (Byte)');?>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm" data-autoclose="true">
                                <?= $form->render('buffer_limit',array('class' => 'form-control form-control-sm input-sm'));?>
                                <span class="input-group-addon">Byte</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Max Size of Entity Bod of an ASP Request (Byte)');?>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm" data-autoclose="true">
                                <?= $form->render('entity_size',array('class' => 'form-control form-control-sm input-sm'));?>
                                <span class="input-group-addon">Byte</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Enable ASP Server Side Script Debugging');?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('server_debugging',array('class' => 'checkbox-blue'));?>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <?= __('Enable ASP Client Side Script Debugging');?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('client_debugging',array('class' => 'checkbox-blue'));?>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
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
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>