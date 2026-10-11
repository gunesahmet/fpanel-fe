<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <form id="ssl-install-form" method="post" class="form-horizontal row" enctype="multipart/form-data" action="<?= $post_url;?>">
            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><?= __('Upload SSL Certificate');?></div>
                    <div class="panel-body">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label for="crt_name"> <strong><?= __('SSL Name');?></strong></label>
                            </div>
                            <div class="col-md-9">
                                <?= $form->render('crt_name_file',array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="crt"> <strong><?= __('Certificate');?></strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="file" class="file" data-show-preview="false" name="crtFile" id="crtFile">
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="crt"> <strong><?= __('CA Certificate');?></strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="file" class="file" data-show-preview="false" name="cacrtFile" id="cacrtFile">
                                    </div>
                                </div>
                                <br>
                                <div class="form-actions float-right">
                                    <button class="btn btn-primary btn-sm" type="button" onclick="core.ajaxFilePost(this)"><i class="fa fa-upload"></i> <?= __('Send'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-1 col-md-offset-5"><?= __('OR');?></div>
                </div>
                <hr />
                <div class="panel panel-default">
                    <div class="panel-heading"><?= __('Upload SSL Certificate');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label for="crt"> <strong><?= __('Certificate');?></strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <?= $form->render('crt',array('class' => 'form-control form-control-sm', "rows" => "10")); ?>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label for="ca_crt"> <strong><?= __('CA Certificate');?></strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <?= $form->render('ca_crt',array('class' => 'form-control form-control-sm', "rows" => "10")); ?>
                                    </div>
                                </div>

                                <div class="form-actions float-right">
                                    <button type="reset" class="btn btn-default btn-sm"> <?= __('Cancel');?> </button>
                                    <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><?= __('CSR Code');?></div>
                    <div class="panel-body">
                        <pre><?= $csr_code;?></pre>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><?= __('Private Key');?></div>
                    <div class="panel-body">
                        <pre><?= $private_key;?></pre>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    textarea {
        resize: vertical;
    }
</style>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
