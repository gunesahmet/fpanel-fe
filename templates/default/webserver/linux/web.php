<form id="web-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <? ($web_server_type == "apache" ? $this->partial('webserver/linux/apache') : $this->partial('webserver/linux/nginx'));?>

    <div class="form-group">
        <div class="col-md-5">
            <div class=" float-right">
                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>