<form method="post" autocomplete="off" class="form-horizontal" id="add-mig-server-form">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="migration_type"> <?= __('Server Panel Type');?> </label>
                        </div>
                        <div class="col-md-3">
                            <?= $form->render('migration_type',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="row" id="fpanel">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="system"> <?= __('Operating System');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('system',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="ip_address"> <?= __('Source IP Address and Port');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('ip_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="username"> <?= __('Server Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="password"> <?= __('Server Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <?= $form->render('password',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_uname"> <?= __('Panel Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('panel_uname',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_upass"> <?= __('Panel Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <?= $form->render('panel_upass',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="description"> <?= __('Description');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('description',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="cpanel">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="ip_address"> <?= __('Source IP Address and Port');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('ip_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="username"> <?= __('Server Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="password"> <?= __('Server Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <?= $form->render('password',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="description"> <?= __('Description');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('description',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="plesk">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="system"> <?= __('Operating System');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('system',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="ip_address"> <?= __('Source IP Address and Port');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('ip_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="username"> <?= __('Server Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="password"> <?= __('Server Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <?= $form->render('password',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_uname"> <?= __('Panel Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('panel_uname',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_upass"> <?= __('Panel Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <?= $form->render('panel_upass',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="description"> <?= __('Description');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->render('description',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<style>
    #fpanel {
        display: none;
    }
    #plesk {
        display: none;
    }
    #cpanel {
        display: none;
    }
</style>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>