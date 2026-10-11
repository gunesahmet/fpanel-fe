<form id="create-network-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="interface"> <? echo __('Interface');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('interface',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="format"> <? echo __('Netmask CIDR Format');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('format',array('id' => 'network-format','class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="ip_address"> <? echo __('IP Address');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-ip_address">
                <? echo $form->render('ip_address',array('class' => 'form-control')); ?>
                <span class="input-group-addon">/</span>
                <? echo $form->render('netmask_cidr',array('class' => 'form-control')); ?>
                <? echo $form->render('netmask',array('class' => 'form-control')); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="gateway"> <? echo __('Gateway');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('gateway',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-4">
            <label for="used_type"> <? echo __('Using Type');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('used_type',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <hr >

    <div class="form-group">
        <div class="col-md-12">
            <div class=" pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>


<script>
    core.inputInits();
    core.networkFormat();
</script>

