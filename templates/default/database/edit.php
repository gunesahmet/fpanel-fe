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
            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
            <div class="row">
                <div class="col-md-7">
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
                            <?= $form->render('database_server_type',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="mysql_db_edit">
                        <div class="col-md-4">
                            <label for="mysql_database_server" class="strong"><?= __('Database Server');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('mysql_database_server',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="pgsql_db_edit">
                        <div class="col-md-4">
                            <label for="pgsql_database_server" class="strong"><?= __('Database Server');?></label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('pgsql_database_server',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row" id="mssql_db_edit">
                        <div class="col-md-4">
                            <label for="mssql_database_server" class="strong"><?= __('Database Server');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('mssql_database_server',array('class' => 'form-control form-control-sm input-sm')); ?>
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
                            <label for="confirm-password"> <?= __('Confirm Password'); ?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('database_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                <? if((isset($domain_id))) {?>
                                    <a class="btn btn-info btn-sm" data-target="_blank" data-url="<?= $this->uri->url_encrypt('database/dbAdmin/'.$id.'/domain/'.$domain_id);?>" ><i class="fa fa-edit"></i> DB Admin</a>
                                <? } else { ?>
                                    <a class="btn btn-info btn-sm" data-target="_blank" data-url="<?= $this->uri->url_encrypt('database/dbAdmin/'.$id);?>" ><i class="fa fa-edit"></i> DB Admin</a>
                                <? } ?>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <? if( (isset($remote_connection_status)) && ($remote_connection_status == "on") ) { ?>
                    <div class="col-md-5">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="add_remote_host"> <?= __('Remote Hosts'); ?> </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render('add_remote_host',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                            <div class="col-md-2">
                                <a class="btn btn-primary btn-xs" data-toggle="tooltip" title="<?= __('Add');?>" id="btn-add-host"><i class="fa fa-plus"></i></a>
                                <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="<?= __('Remove');?>" id="btn-remove-host"><i class="fa fa-times"></i></a>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4"></div>
                            <div class="col-md-6">
                                <?= $form->render('remote_hosts',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </form>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>