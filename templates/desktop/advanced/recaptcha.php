<form id="system-notify-form" class="form-horizontal" method="post" action="<? echo $this->uri->url_encrypt('system/recaptchasettings')?>">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-6">
                    <label for="activate_recaptcha"><? echo __('Activate Recaptcha');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('activate_recaptcha',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6">
                    <label for="attempts"><? echo __('Show after X attempts');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('attempts',array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6">
                    <label for="site_key"><? echo __('Site Key');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('site_key',array('class' => 'form-control input-sm')); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6">
                    <label for="site_secret"><? echo __('Site Secret');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('site_secret',array('class' => 'form-control input-sm')); ?>
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
        <div class="col-md-5">
            <? if($recaptcha_activate != false) { ?>
                <h2><? echo __('Recaptcha Preview');?></h2>
                <div class="g-recaptcha" data-sitekey="<? echo $site_key;?>"></div>
            <? } else { ?>
                <div class="row margin-top-10">
                    <? echo __('Generate your own Google Captcha keys and fill them in').' <a href="http://www.google.com/recaptcha" target="_blank">'.__('here').'</a>';?>
                </div>
            <? } ?>
        </div>
    </div>
</form>

<script>
    core.inputInits();
</script>
<? if($recaptcha_activate != false) { ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
<? } ?>