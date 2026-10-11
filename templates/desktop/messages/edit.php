<div class="row" id="system-messages-panel" data-width="45%" data-height="50%">
    <form id="edit-blacklist-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="form-group">
            <div class="col-md-4">
                <label for="status"> <? echo __('Status');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('status',array('class' => 'checkbox-blue')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-4">
                <label for="title"> <? echo __('Message Title');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('title',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-4">
                <label for="description"> <? echo __('Description');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('description',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-4">
                <label for="expire_date"> <? echo __('Expire After Day');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('expire_date',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-4">
                <label for="type"> <? echo __('Message Type');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('type',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-4">
                <label for="message_content"> <? echo __('Message');?> </label>
            </div>
            <div class="col-md-8">
                <div class="widget-body">
                    <? echo $form->render('message_content',array('class' => 'form-control input-sm')); ?>
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
    </form>
</div>
<script>
    core.setWidth({
        target: 'system-messages-panel'
    });
    core.inputInits();
</script>