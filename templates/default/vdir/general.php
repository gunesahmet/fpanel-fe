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
                <form id="create-vdir-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="tabbable-line">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active"><a href="#vdir-index" data-toggle="tab"><i></i><span class="strong"><?= __('Virtual Directory');?></span></a></li>
                            <li><a href="#vdir-applications" data-toggle="tab"><i></i><span class="strong"><?= __('Application Settings');?></span></a></li>
                            <li><a href="#vdir-documents" data-toggle="tab"><i></i><span class="strong"><?= __('Default Documents Settings');?></span></a></li>
                            <li><a href="#vdir-directorysec" data-toggle="tab"><i></i><span class="strong"><?= __('Directory Security');?></span></a></li>
                            <li><a href="#vdir-aspsettings" data-toggle="tab"><i></i><span class="strong"><?= __('ASP Settings');?></span></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="vdir-index">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Path');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('directoryPath',array('class' => 'form-control form-control-sm input-sm'));?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Access Policy');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('accessPolicy',array('class' => 'select-multiple'));?>
                                            </div>
                                        </div>

                                        <hr />

                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Directory Browsing');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('directory_browse',array('class' => 'checkbox-blue'));?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Http Logging');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('logging',array('class' => 'checkbox-blue'));?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="vdir-applications">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Create Application');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('createapplication',array('class' => 'checkbox-blue'));?>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Allow application execurion in MTA (Multi Threaded Apartment) Mode');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('execute_in_mta',array('class' => 'checkbox-blue'));?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="vdir-documents">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Use Default Documents');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('use_default_document',array('class' => 'checkbox-blue'));?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Add New Document');?>
                                            </div>
                                            <div class="col-md-7">
                                                <?= $form->render('default_document_add',array('class' => 'form-control form-control-sm input-sm'));?>
                                            </div>
                                            <div class="col-md-1">
                                                <a class="btn btn-primary btn-xs" id="btn-add"><i class="fa fa-plus"></i></a>
                                                <a class="btn btn-danger btn-xs" id="btn-remove"><i class="fa fa-times"></i></a>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Order Default Documents');?>
                                            </div>
                                            <div class="col-md-7">
                                                <?= $form->render('default_document_order',array('class' => 'select-multiple'));?>
                                            </div>
                                            <div class="col-md-1">
                                                <a class="btn btn-default btn-xs" id="btn-up"><i class="fa fa-arrow-up"></i></a>
                                                <a class="btn btn-default btn-xs" id="btn-down"><i class="fa fa-arrow-down"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="vdir-directorysec">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Allow Anonymous Access');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('allow_anonymous',array('class' => 'checkbox-blue'));?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="vdir-aspsettings">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <?= __('Allow to use Parent Path');?>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('parent_path',array('class' => 'checkbox-blue'));?>
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
                                            <div class="col-md-4">
                                                <?= __('Max. Size of ASP Buffer (Byte)');?>
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
                                                <?= __('Max. Size of Entity Body of on ASP (Byte)');?>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm" data-autoclose="true">
                                                    <?= $form->render('entity_size',array('class' => 'form-control form-control-sm input-sm'));?>
                                                    <span class="input-group-addon">Byte</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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