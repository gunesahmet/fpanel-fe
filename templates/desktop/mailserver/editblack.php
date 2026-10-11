<form id="edit-blacklist-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group" >
        <div class="col-md-4">
            <label for="blacklist_rule"> <? echo __('Blacklist Rule');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('blacklist_rule',array('class' => 'form-control input-sm')); ?>
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