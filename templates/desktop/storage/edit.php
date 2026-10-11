<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
<div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
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
                                    <label for="storages"> <? echo __('Storage Type');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('storages',array('class' => 'form-control'));?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="storage_name"> <? echo __('Storage Name');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('storage_name',array('class' => 'form-control'));?>
                                </div>
                            </div>

                            <div class="form-group" id="dropbox">
                                <div class="col-md-4">
                                    <label for="access_token"> <? echo __('Access Token');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('access_token',array('class' => 'form-control'));?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="folder"> <? echo __('Folder');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-addon">/</span>
                                        <? echo $form->render('folder',array('class' => 'form-control'));?>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="status"> <? echo __('Status');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('status',array('class' => 'make-switch','data-size' => 'small'));?>
                                </div>
                            </div>


                            <div class="form-actions pull-right">
                                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                                <? echo $links;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    core.inputInits();
</script>