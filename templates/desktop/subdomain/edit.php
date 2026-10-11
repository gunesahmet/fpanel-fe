<div class="row" id="edit-subdomain-panel" data-width="50%">
    <form id="edit-subdomain-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="subdomain_name"> <? echo __('Subdomain Name'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-8 col-lg-6">
                    <div class="input-group input-group-sm" id="input-group-subdomain_name">
                        <? echo $form->render('subdomain_name',array('class' => 'form-control')); ?>
                        <span class="input-group-addon">.<? echo $domain_name; ?></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="use_native_ftp"> <? echo __('Use Different FTP User'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('use_native_ftp',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="username"> <? echo __('FTP Username'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('username',array('class' => 'form-control input-sm')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="password"> <? echo __('FTP Password'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <div class="input-group input-group-sm" id="input-group-password">
                        <? echo $form->render("password",array('class' => 'form-control')) ?>
                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="username"> <? echo __('Confirm Password'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control input-sm')); ?>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="php_support"> <? echo __('PHP Support'); ?> </label>
                </div>

                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('php_support',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>

            <? if(PANEL_OS_TYPE == "windows") { ?>
                <div class="form-group">
                    <div class="col-md-4 col-xs-4 col-lg-4">
                        <label for="php_version"> <? echo __('PHP Version'); ?> </label>
                    </div>
                    <div class="col-md-6 col-xs-6 col-lg-6">
                        <? echo $form->render('php_version',array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4 col-xs-4 col-lg-4">
                        <label for="asp_support"> <? echo __('ASP Support'); ?> </label>
                    </div>
                    <div class="col-md-6 col-xs-6 col-lg-6">
                        <? echo $form->render('asp_support',array('class' => 'checkbox-blue')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4 col-xs-4 col-lg-4">
                        <label for="asp_net_support"> <? echo __('ASP.NET Support'); ?> </label>
                    </div>
                    <div class="col-md-6 col-xs-6 col-lg-6">
                        <? echo $form->render('asp_net_support',array('class' => 'checkbox-blue')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4 col-xs-4 col-lg-4">
                        <label for="asp_net_version"> <? echo __('ASP.NET Version'); ?> </label>
                    </div>
                    <div class="col-md-6 col-xs-6 col-lg-6">
                        <? echo $form->render('asp_net_version',array('class' => 'form-control input-sm')); ?>
                    </div>
                </div>
            <? } ?>

            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="cgi_support"> <? echo __('CGI Support'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('cgi_support',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="perl_support"> <? echo __('Perl Support'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('perl_support',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="python_support"> <? echo __('Python Support'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('python_support',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-xs-4 col-lg-4">
                    <label for="custom_error_pages"> <? echo __('Custom Error Pages'); ?> </label>
                </div>
                <div class="col-md-6 col-xs-6 col-lg-6">
                    <? echo $form->render('custom_error_pages',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <button class="btn btn-sm btn-block btn-default" type="reset"><? echo __('Cancel'); ?></button>
            <button class="btn btn-sm btn-block btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
        </div>
    </form>
</div>

<script>
    core.inputInits();
    core.setWidth({target:'edit-subdomain-panel'});
    $('input[id="use_native_ftp"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('input[id="username').prop('disabled',false);
            $('input[id="password').prop('disabled',false);
            $('input[id="confirm-password').prop('disabled',false);
        }
        else {
            $('input[id="username').prop('disabled',true);
            $('input[id="password').prop('disabled',true);
            $('input[id="confirm-password').prop('disabled',true);
        }
    });
    $('input[id="php_support"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('select[id="php_version').prop('disabled',false);
        }
        else {
            $('select[id="php_version').prop('disabled',true);
        }
    });
    $('input[id="asp_net_support"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('select[id="asp_net_version').prop('disabled',false);
        }
        else {
            $('select[id="asp_net_version').prop('disabled',true);
        }
    });
</script>