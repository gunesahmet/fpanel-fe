<form id="create-vdir-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="domain_name"> <? echo __('Domain Name');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('domain_name',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="directory_path"> <? echo __('Directory Path');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-addon">httpdoc/</span>
                <? echo $form->render('directory_path',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="title_of_header"> <? echo __('Title of Header');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('title_of_header',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
