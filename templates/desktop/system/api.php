<div class="row" id="api-management-panel" data-width="45%" data-height="50%">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Api Management');/*Api Yönetimi*/?></div>
            <div class="panel-body">
                <form id="api-management-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
                    <div class="row" style="margin-bottom: 25px">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="api_status"><? echo __('Api Status');?></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('api_status',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="api_url"><? echo __('Api URL');?></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><? echo $_SERVER['HTTP_HOST'];?>/</span>
                                        <? echo $form->render('api_url',array('class' => 'form-control input-sm')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="username"><? echo __('Api Username');?></label>
                                </div>
                                <div class="col-md-6">
                                    <? echo $form->render('username',array('class' => 'form-control input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="password"><? echo __('Api Password');?></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <? echo $form->render('password',array('class' => 'form-control')); ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label><? echo __('Confirm Password');?> *</label>
                                </div>
                                <div class="col-md-6">
                                    <input id="confirm-password" name="confirm-password" type="password" class="form-control input-sm">
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-6" >
                            <div class="form-group">
                                <div class="col-md-12">
                                    <? echo $form->render('user_list',array('class' => 'multiselect')); ?>
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
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    core.setWidth({
        target: 'api-management-panel'
    });
    core.inputInits();
</script>