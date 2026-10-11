<? $server_link = (isset($server_id) == true ? "/server/$server_id" : ''); ?>
<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-md-12">
                <div class="tabbable-line">
                    <ul class="nav nav-pills nav-fill" id="webserver-settings-tabs">
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("webserver/web$server_link");?>"><?= __('Web Server Settings'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("webserver/modules$server_link");?>"><?= __('Modules'); ?></a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?= $this->uri->url_encrypt("webserver/mimetypes$server_link");?>"><?= __('Mime Types'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-web-general-settings">
                        </div>
                        <div class="tab-pane" id="tab-webserver-settings">

                        </div>
                        <div class="tab-pane" id="tab-module-settings">
                        </div>
                        <div class="tab-pane in active" id="tab-mimetype-settings">
                            <div class="row">
                                <div class="col-md-6">
                                    <form id="webserver-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                                        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="file_extension"> <?= __('Mime Type File Extension');?> </label>
                                            </div>
                                            <div class="col-md-8">
                                                <?=
                                                /**
                                                 * @var $form \Phalcon\Forms\Form
                                                 */
                                                $form->render('file_extension',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="mimetype_octet"> <?= __('Mime Type Application Type');?> </label>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('mimetype_octet',array('class' => 'form-control form-control-sm input-sm'));?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <div class=" float-right">
                                                    <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                                    <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                                </div>
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
    </div>
</div>


<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>