<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="database_name" class="strong"> <? echo __('Database Name');?></label>
        </div>

        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-database_name">
                <span class="input-group-addon"><? echo $prefix;?>_</span>
                <? echo $form->render('database_name',array('class' => 'form-control')); ?>
            </div>
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
            <label for="username"> <? echo __('Confirm Password'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('database_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>


<style>

    #mssql_db {
        display: none;
    }
    #mysql_db {
        display: block;
    }
</style>
<script>
    core.inputInits();
</script>