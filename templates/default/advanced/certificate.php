<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="tabbable-line">
            <ul class="nav nav-pills nav-fill" id="advanced-settings-tabs">
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/passwordsettings');?>">
                        <?= __('Password Rules'); ?>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?= $this->uri->url_encrypt('system/certificate');?>">
                        <?= __('System Certificate'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/recaptchasettings');?>">
                        <?= __('Recaptcha Settings'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= $this->uri->url_encrypt('system/addnewadmin');?>">
                        <?= __('Additional System Administrator'); ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane in active" id="tab-system-certificate">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="system-notify-form" class="form-horizontal" method="post" enctype="multipart/form-data" action="<?= $this->uri->url_encrypt('system/certificate')?>">
                                <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="activate_certificate"><?= __('Activate Uploaded Certificate');?></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('activate_certificate',array('class' => 'checkbox-blue')); ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="hostname"><?= __('Hostname');?></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('hostname',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="pri_key"><?= __('Private Key');?></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('pri_key',array('id' => 'file_input_csr', 'class' => 'file', "data-show-preview" => "false")); ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="crt"><?= __('Certificate');?></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('crt',array('id' => 'file_input_crt', 'class' => 'file', "data-show-preview" => "false")); ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="ca_crt"><?= __('CA Bundle Certificate');?></label>
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->render('ca_crt',array('id' => 'file_input_ca_crt', 'class' => 'file', "data-show-preview" => "false")); ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <div class="float-right">
                                                    <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                                    <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <? if($uploaded_certificate != false) { ?>
                                            <h2><?= __('Uploaded Certificate');?></h2>
                                            <pre class="well"><?= $uploaded_certificate['pri_key'];?></pre>
                                            <pre class="well"><?= $uploaded_certificate['crt'];?></pre>
                                            <pre class="well"><?= $uploaded_certificate['ca_crt'];?></pre>
                                        <? } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
