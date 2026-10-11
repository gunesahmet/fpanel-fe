<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="db_host"> <? echo __('Server Name'). ' (Api)';?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('db_server_name',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="db_host"> <? echo __('Database Server IP Address');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('db_host',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="db_port"> <? echo __('Database Server Port');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('db_port',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="db_type"> <? echo __('Database Server Type');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('db_type',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="username"> <? echo __('Database Server Username');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('username',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="password"> <? echo __('Database Server Password');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-password">
                <? echo $form->render('password',array('class' => 'form-control')); ?>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
            </div>
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
<script>
    core.inputInits();
</script>