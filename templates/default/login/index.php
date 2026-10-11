<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?= $this->tag->getTitle() ?>
        <link rel="icon" type="image/x-icon" href="/favicon.ico" />
        <link href="/templates/default/assets/css/bootstrap.css" rel="stylesheet">
        <link href="/templates/default/assets/css/style.css" rel="stylesheet">
        <link href="/templates/default/assets/css/fp.css" rel="stylesheet">
    </head>
    <body class="gray-bg">
        <div class="middle-box text-center loginscreen">
            <div>
                <span><i class="fp fp-fp-logo img-thumbnail rounded-circle img-circle <?= ($this->panel_installed ? "installed" : "")?> fp-6x"></i></span>
                <h3><?= __('Welcome to Fpanel');?></h3>
                <? if($this->flashSession->has("error")) { ?>
                <div class="alert alert-danger">
                    <? $this->flashSession->output(); ?>
                </div>
                <? } ?>

                <? if(isset($captcha_error)) { ?>
                    <div class="alert alert-danger"><button class="close" data-close="alert"></button><span><?= $captcha_error; ?></span></div>
                <? } ?>


                <form class="m-t" method="post" role="form" autocomplete="on" id="login-form">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group">

                        <?=/** @var $form \Phalcon\Forms\Form */ $form->render("username",array('class' => 'form-control form-control-sm','type' => 'text', 'placeholder' => __('Username'))) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->render("password",array('class' => 'form-control form-control-sm','type' => 'password', 'placeholder' => __('Password'))) ?>
                    </div>

                    <div class="form-group">
                        <?= $form->render("lang_select",array('class' => 'form-control form-control-sm')) ?>
                    </div>

                    <div class="form-group">
                        <?= $form->render("templates",array('class' => 'form-control form-control-sm')) ?>
                    </div>

                    <button type="submit" class="btn btn-primary block full-width m-b"><?= __('Login');?></button>

                </form>
                <p class="m-t"> <small><?= __('Allrights reserved by %kovengo%', ["kovengo" => "<a href='https://kovengo.com' target='_blank'>Kovengo</a>"]);?> &copy; <?= date("Y"); ?></small> </p>
            </div>
        </div>

        <script src="/templates/default/assets/js/jquery.min.js"></script>
    <? if(isset($captcha) && $captcha == true)  { ?>
        <script src="https://www.google.com/recaptcha/api.js?trustedtypes=true&render=<?= $this->config->recaptcha->site_key;?>"></script>
        <script>
            $('#login-form').submit(function(event) {
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?= $this->config->recaptcha->site_key;?>', {action: '<?= md5($this->crypto->bencode($this->session->getId()));?>'}).then(function(token) {
                        $('#login-form').prepend('<input type="hidden" name="token" value="' + token + '">');
                        $('#login-form').unbind('submit').submit();
                    });
                });
                event.preventDefault();
            });
        </script>
    <? } ?>
        <script>
            $("#lang_select").change(function () {
                let lang = $("#lang_select").val();
                if(lang != null) {
                    window.location.href = "/?&language=" + lang;
                }
            });
            $("#templates").change(function () {
                let template = $("#templates").val();
                if(template != null)
                    window.location.href = "/?&template=" + template;
            });
        </script>
    </body>
</html>

<?php  exit(0); ?>