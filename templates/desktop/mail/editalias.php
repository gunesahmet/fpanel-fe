<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4 col-xs-4">
            <label for="mail_alias"> <strong><? echo __('Mail Alias Address');?></strong></label>
        </div>
        <div class="col-md-8 col-xs-8">
            <div class="input-group input-group-sm" id="input-group-mail_alias">
                <? echo $form->render('mail_alias',array('class' => 'form-control input-sm')); ?>
                <span class="input-group-addon">@<? echo $domain_name; ?></span>
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
