<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <form id="create-subdomain-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
            <div class="row">
                <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                <div class="col-md-6">
                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="subdomain_name"> <?= __('Subdomain Name'); ?> </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-lg-8">
                            <div class="input-group input-group-sm" id="input-group-subdomain_name">
                                <?= $form->render('subdomain_name',array('class' => 'form-control form-control-sm')); ?>
                                <span class="input-group-addon">.<?= $domain_name; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="use_native_ftp"> <?= __('Use Different FTP User'); ?> </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-lg-8">
                            <?= $form->render('use_native_ftp',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="username"> <?= __('FTP Username'); ?> </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-lg-8">
                            <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="password"> <?= __('FTP Password'); ?> </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-lg-8">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="username"> <?= __('Confirm Password'); ?> </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-lg-8">
                            <?= $form->render('password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="status"> <?= __('Status'); ?> </label>
                        </div>

                        <div class="col-md-6 col-xs-6 col-lg-6">
                            <?= $form->render('status', ['class' => 'make-switch', "data-toggle" => "toggle", "data-size" => "mini" , "data-on-text" => __("Active"), "data-off-text" => __('Passive')]); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="php_support"> <?= __('PHP Support'); ?> </label>
                        </div>

                        <div class="col-md-6 col-xs-6 col-lg-6">
                            <?= $form->render('php_support', array('class' => 'checkbox-blue', "data-sub-elements" => "php_version")); ?>
                        </div>
                    </div>

                    <? if($server_os == WINDOWS) { ?>
                        <div class="form-group row">
                            <div class="col-md-4 col-xs-4 col-lg-4">
                                <label for="php_version"> <?= __('PHP Version'); ?> </label>
                            </div>
                            <div class="col-md-8 col-xs-8 col-lg-8">
                                <?= $form->render('php_version',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4 col-xs-4 col-lg-4">
                                <label for="asp_support"> <?= __('ASP Support'); ?> </label>
                            </div>
                            <div class="col-md-8 col-xs-8 col-lg-8">
                                <?= $form->render('asp_support',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4 col-xs-4 col-lg-4">
                                <label for="asp_net_support"> <?= __('ASP.NET Support'); ?> </label>
                            </div>
                            <div class="col-md-8 col-xs-8 col-lg-8">
                                <?= $form->render('asp_net_support', array('class' => 'checkbox-blue', "data-sub-elements" => "asp_net_version")); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4 col-xs-4 col-lg-4">
                                <label for="asp_net_version"> <?= __('ASP.NET Version'); ?> </label>
                            </div>
                            <div class="col-md-8 col-xs-8 col-lg-8">
                                <?= $form->render('asp_net_version',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                    <? } ?>

                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="perl_support"> <?= __('Perl Support'); ?> </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-lg-8">
                            <?= $form->render('perl_support', array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4 col-xs-4 col-lg-4">
                            <label for="python_support"> <?= __('Python Support'); ?> </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-lg-8">
                            <?= $form->render('python_support',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>