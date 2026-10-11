<form class="form-horizontal" method="post">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="hostname"> <strong><? echo __('Hostname');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <? echo $form->render('hostname'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="admin_email"> <strong><? echo __('Admin Contact Email');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <? echo $form->render('admin_email'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="default_ip_address"><strong><? echo __('Default Server IP Address');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <? echo $form->render('default_ip_address',array('disabled' => 'disabled')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="ipaddress_list"> <strong><? echo __('IP Address List');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <? echo $form->render('ipaddress_list',array('class' => 'multiselect-custom')); ?>
                        </div>
                    </div>
                    <br />
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="username"> <strong><? echo __('Admin Username');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <? echo $form->render('username');?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="password"> <strong><? echo __('Admin Password');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <? echo $form->render('password',array('class' => 'form-control'));?>
                                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" onclick="passgen('password','confirm-password')" class="btn btn-primary"><i class="fa fa-refresh"></i></button></span>
                                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd2" class="btn btn-primary"><i class="fa fa-eye"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="confirm-password"> <strong><? echo __('Confirm Password');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <input id="confirm-password" name="confirm-password" type="password" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <hr />
                            <div class="pull-right">
                                <a class="btn btn-danger" href="<? echo $this->uri->url_encrypt('install');?>"><i class="fa fa-backward"></i> <? echo __('Go Back'); ?></a>
                                <button class="btn btn-primary" type="button" onclick="submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Next Step'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
