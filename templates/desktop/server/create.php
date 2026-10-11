<form method="post" autocomplete="off" class="form-horizontal">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><? echo __('Server Add'); ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="status"> <strong><? echo __('Server Status');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('status',array('class' => 'make-switch')); ?>
                                </div>
                            </div>

                            <br />
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="server_os"> <strong><? echo __('Operating System');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('server_os',array('class' => 'form-control')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="server_name"> <strong><? echo __('Server Name');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('server_name',array('class' => 'form-control')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="description"> <strong><? echo __('Description');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('description',array('class' => 'form-control')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="server_type"> <strong><? echo __('Server Type');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('server_type',array('class' => 'multiselect-custom','data-selectable' => __('Selectable Service'),'data-selection' => __('Selected Service'))); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="ip_address"> <strong><? echo __('IP Address');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('ip_address',array('class' => 'form-control')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="username"> <strong><? echo __('Username');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('username',array('class' => 'form-control')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="password"> <strong><? echo __('Password');?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <? echo $form->render('password',array('class' => 'form-control')); ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions col-md-10">
                                <div class="pull-right">
                                    <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                                    <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
</form>
