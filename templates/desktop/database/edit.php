<div class="row" id="edit-domain-database-panel" data-width="60%">
    <form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <div class="col-md-4">
                        <label for="database_name" class="strong"> <? echo __('Database Name');?></label>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('database_name',array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <label for="database_server_type" class="strong"><? echo __('Database Server Type');?></label>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('database_server_type',array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>

                <div class="form-group" id="mysql_db">
                    <div class="col-md-4">
                        <label for="mysql_database_server" class="strong"><? echo __('Database Server');?></label>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('mysql_database_server',array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>

                <div class="form-group" id="mssql_db">
                    <div class="col-md-4">
                        <label for="mssql_database_server" class="strong"><? echo __('Database Server');?></label>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('mssql_database_server',array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label for="database_username" class="strong"><? echo __('Database Username');?></label>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('database_username',array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label for="password" class="strong"> <? echo __('Database Password');?></label>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group input-group-sm" id="input-group-database_password">
                            <? echo $form->render("database_password",array('class' => 'form-control')) ?>
                            <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                            <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <label for="confirm-password"> <? echo __('Confirm Password'); ?> </label>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('database_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control input-sm')); ?>
                    </div>
                </div>
            </div>
            <? if( (isset($remote_connection_status)) && ($remote_connection_status == "on") ) { ?>
                <div class="col-md-5">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="add_remote_host"> <? echo __('Remote Hosts'); ?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('add_remote_host',array('class' => 'form-control input-sm')); ?>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-primary btn-xs" data-toggle="tooltip" title="<? echo __('Add');?>" id="btn-add-host"><i class="fa fa-plus"></i></a>
                            <a class="btn btn-danger btn-xs" data-toggle="tooltip" title="<? echo __('Remove');?>" id="btn-remove-host"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4"></div>
                        <div class="col-md-6">
                            <? echo $form->render('remote_hosts',array('class' => 'form-control input-sm')); ?>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            <? } ?>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                            <? if((isset($domain_id))) {?>
                                <a class="btn btn-info btn-sm" data-target="_blank" data-url="<? echo $this->uri->url_encrypt('database/dbAdmin/'.$id.'/domain/'.$domain_id);?>" ><i class="fa fa-edit"></i> DB Admin</a>
                            <? } else { ?>
                                <a class="btn btn-info btn-sm" data-target="_blank" data-url="<? echo $this->uri->url_encrypt('database/dbAdmin/'.$id);?>" ><i class="fa fa-edit"></i> DB Admin</a>
                            <? } ?>
                            <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    core.inputInits();
    core.setWidth({target: 'edit-domain-database-panel'});
    function load_db() {
        var obj = [ "mysql", "mssql", "pgsql" ];
        var db_type = $('#database_server_type').val();
        $.each(obj, function(key,value){
            $('#' + value + '_db').hide();
        });
        $('#' + db_type + '_db').show();
    }
    $(document).ready(function(){
        load_db();
    });

    function selectAllPages() {
        for (var i = 0; i < document.getElementById("remote_hosts").options.length; i++)
        {
            document.getElementById("remote_hosts").options[i].selected = true;
        }
    }
    $(document).ready(function () {
        $('#btn-add-host').click(function(){
            var host = $('#add_remote_host').val();
            $('#remote_hosts').append("<option value='"+ host +"'>"+ host +"</option>");
            $('#add_remote_host').val("");
        });
        $('#btn-remove-host').click(function(){
            $('#remote_hosts option:selected').each( function() {
                $(this).remove();
            });
        });
    });
</script>
