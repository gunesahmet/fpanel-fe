<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <form id="create-database-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
            <div class="row">
                <div class="col-md-6">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="database_name" class="strong"> <?= __('Database Name');?></label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-database_name">
                                <span class="input-group-addon"><?= $prefix;?>_</span>
                                <?= $form->render('database_name',array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="database_server_type" class="strong"><?= __('Database Server Type');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('database_server_type',array('class' => 'form-control select-picker')); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="mysql_db_create">
                        <div class="col-md-4">
                            <label for="mysql_database_server" class="strong"><?= __('Database Server');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('mysql_database_server',array('class' => 'form-control select-picker')); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="pgsql_db_create">
                        <div class="col-md-4">
                            <label for="pgsql_database_server" class="strong"><?= __('Database Server');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('pgsql_database_server',array('class' => 'form-control form-control-sm select-picker')); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="mssql_db_create">
                        <div class="col-md-4">
                            <label for="mssql_database_server" class="strong"><?= __('Database Server');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('mssql_database_server',array('class' => 'form-control form-control-sm select-picker')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="database_username" class="strong"><?= __('Database Username');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('database_username',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="password" class="strong"> <?= __('Database Password');?></label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render("database_password",array('class' => 'form-control form-control-sm')) ?>
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
                            <?= $form->render('database_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')); ?>
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
                </div>
            </div>
        </form>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>