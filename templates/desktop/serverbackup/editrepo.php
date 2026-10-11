<form id="edit-server-repo-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="ftp_server"> <? echo __('FTP Server Host');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('ftp_server',array('class' => 'form-control input-sm'));?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="ftp_user"> <? echo __('FTP User');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('ftp_user',array('class' => 'form-control input-sm'));?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="ftp_folder"> <? echo __('FTP Folder');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-ftp_folder">
                <span class="input-group-addon">/</span>
                <? echo $form->render('ftp_folder',array('class' => 'form-control'));?>
            </div>
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-4">
            <label for="ftp_pass"> <? echo __('FTP Password');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-ftp_pass">
                <? echo $form->render("ftp_pass",array('id' => 'password','class' => 'form-control')) ?>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="status"> <? echo __('Enabled');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('status',array('class' => 'checkbox-blue'));?>
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
