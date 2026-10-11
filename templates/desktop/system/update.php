<div class="row" id="system-update-panel" data-width="60%">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Update Lists');/*Güncelleme Listesi*/?></div>
            <div class="panel-body">
                <div class="col-md-12">
                    <a data-id="confirm" class="btn btn-sm btn-primary" data-url="<? echo $this->uri->url_encrypt("update/force");?>" type="button"><i class="fa fa-refresh"></i> <? echo __('Force Update');?></a>
                </div>
                <br />
                <br />
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="system-update-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('update');?>">
                            <thead>
                            <tr>
                                <th width="20%"><? echo __('Version');?></th>
                                <th width="20%"><? echo __('Update Date');?></th>
                                <th width="20%"><? echo __('Publish Date');?></th>
                                <th width="20%"><? echo __('Description');?></th>
                                <th width="20%"><? echo __('Actions');?></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Update Settings');/*Güncelleme Ayarları*/?></div>
            <div class="panel-body">
                <form method="post" autocomplete="off" class="form-horizontal" action="<? echo $this->uri->url_encrypt('update/settings')?>">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="auto_update"><? echo __('Auto Update');?></label>
                        </div>
                        <div class="col-md-8">
                            <? echo $form->render('auto_update',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="random_time"><? echo __('Random Time');?></label>
                        </div>
                        <div class="col-md-8">
                            <? echo $form->render('random_time',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="update_time"><? echo __('Update Time');?></label>
                        </div>
                        <div class="col-md-8">
                            <? echo $form->render('update_time',array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="update_server"><? echo __('Update Server');?></label>
                        </div>
                        <div class="col-md-8">
                            <? echo $form->render('update_server',array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="pull-right">
                            <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                            <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    core.setWidth({
        target: 'system-update-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#system-update-table',
        select_row: false,
        buttons: false,
        checkbox: false,
        actions: false
    });

    $('input[id="random_time"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('input[id="update_time"]').prop('disabled', true);
        }
        else {
            $('input[id="update_time"]').prop('disabled', false);
        }
    });

</script>
