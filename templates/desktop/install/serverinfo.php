<div class="col-md-12 margin-top-10">
    <form id="server-info-settings-form" method="post" class="form-horizontal" action="<? echo $this->uri->url_encrypt("install/serverinfo");?>">
        <div class="form-group">
            <div class="col-md-4">
                <strong><? echo __('System Type');?></strong>
            </div>
            <div class="col-md-4">
                <label for="select_cloud"> Cloud</label> <? echo $form->render('select_cloud',array('class' => 'checkbox-blue')); ?>
            </div>
            <div class="col-md-4">
                <label for="select_standalone"> Standalone</label> <? echo $form->render('select_standalone',array('class' => 'checkbox-blue')); ?>
            </div>
        </div>

        <!-- System Type Select END -->
        <div class="form-group">
            <label class="control-label col-md-4" for="hostname"> <strong><? echo __('Hostname');?></strong></label>
            <div class="col-md-8">
                <? echo $form->render('hostname',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-4" for="admin_email"> <strong><? echo __('Admin Contact Email');?></strong></label>
            <div class="col-md-8">
                <? echo $form->render('admin_email',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-4" for="default_ip_address"><strong><? echo __('Default Server IP Address');?></strong></label>
            <div class="col-md-3">
                <? echo $form->render('default_ip_address',array('class' => 'form-control input-sm','disabled' => 'disabled')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-4" for="ipaddress_list"> <strong><? echo __('IP Address List');?></strong></label>
            <div class="col-md-8">
                <? echo $form->render('ipaddress_list',array('class' => 'multiselect')); ?>
            </div>
        </div>
        <br />
        <div class="form-group">
            <label class="control-label col-md-4" for="username"> <strong><? echo __('Admin Username');?></strong></label>
            <div class="col-md-8">
                <? echo $form->render('username',array('class' => 'form-control input-sm'));?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-4" for="password"> <strong><? echo __('Admin Password');?></strong></label>
            <div class="col-md-8">
                <div class="input-group input-group-sm" id="input-group-password">
                    <? echo $form->render("password",array('class' => 'form-control')) ?>
                    <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                    <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-4" for="confirm-password"> <strong><? echo __('Confirm Password');?></strong></label>
            <div class="col-md-8">
                <input id="confirm-password" name="confirm-password" type="password" class="form-control input-sm">
            </div>
        </div>
    </form>
</div>
<script>
    core.inputInits();
</script>