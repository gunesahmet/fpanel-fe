<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="session_time"><strong><? echo  __('Session Time');?></strong></label>
            <p><? echo __('If you set 0 session timeout will be unlimited');?></p>
        </div>
        <div class="col-md-8">
            <? echo $form->render('session_time',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="add_to_allow_list"><strong><? echo  __('Allow these IP Addresses');?></strong></label>
        </div>
        <div class="col-md-1">
            <? echo $form->render('allow_ip_address',array('class' => 'checkbox-blue')); ?>
        </div>
        <div class="col-md-5">
            <? echo $form->render('add_to_allow_list',array('class' => 'form-control input-sm')); ?>
            <? echo $form->render('allowed_ip_addresses',array('class' => 'form-control input-sm')); ?>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-xs" type="button" id="add-ip-allow"><i class="fa fa-plus-circle"></i></button>
            <button class="btn btn-danger btn-xs" type="button" id="remove-ip-allow"><i class="fa fa-minus-circle"></i></button>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="add_to_allow_list"><strong><? echo  __('Block these IP Addresses');?></strong></label>
        </div>
        <div class="col-md-1">
            <? echo $form->render('block_ip_address',array('class' => 'checkbox-blue')); ?>
        </div>
        <div class="col-md-5">
            <? echo $form->render('add_to_block_list',array('class' => 'form-control input-sm')); ?>
            <? echo $form->render('blocked_ip_addresses',array('class' => 'form-control input-sm')); ?>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-xs" type="button" id="add-ip-block"><i class="fa fa-plus-circle"></i></button>
            <button class="btn btn-danger btn-xs" type="button" id="remove-ip-block"><i class="fa fa-minus-circle"></i></button>
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