<form id="create-vdir-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <h4 class="form-section"><? echo __('Virtual Directory');?></h4>
    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Directory Name');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('dir_name',array('class' => 'form-control input-sm'));?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Physical Path');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('physical_path',array('class' => 'form-control input-sm'));?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Access Policy');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('accessPolicy',array('class' => 'form-control multiple-select'));?>
        </div>
    </div>

    <h4 class="form-section"><? echo __('Application Settings');?></h4>

    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Create as Application');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('create_application',array('class' => 'checkbox-blue'));?>

        </div>
    </div>

    <h4 class="form-section"><? echo __('ASP Settings');?></h4>

    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Max Size of ASP Buffer (Byte)');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('buffer_limit',array('class' => 'form-control input-sm'));?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Max Size of Entity Bod of an ASP Request (Byte)');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('entity_size',array('class' => 'form-control input-sm'));?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Enable ASP Server Side Script Debugging');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('server_debugging',array('class' => 'checkbox-blue'));?>

        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <? echo __('Enable ASP Client Side Script Debugging');?>
        </div>
        <div class="col-md-8">
            <? echo $form->render('client_debugging',array('class' => 'checkbox-blue'));?>

        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
<script>
    core.inputInits();
</script>