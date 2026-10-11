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
            <div class="col-md-6">
                <form id="session-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="session_time"><strong><?=  __('Session Time');?></strong></label>
                            <p><?= __('If you set 0 session timeout will be unlimited');?></p>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('session_time',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="add_to_allow_list"><strong><?=  __('Allow these IP Addresses');?></strong></label>
                        </div>
                        <div class="col-md-1">
                            <?= $form->render('allow_ip_address',array('class' => 'checkbox-blue')); ?>
                        </div>
                        <div class="col-md-5">
                            <?= $form->render('add_to_allow_list',array('class' => 'form-control form-control-sm input-sm')); ?>
                            <?= $form->render('allowed_ip_addresses',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-xs" type="button" id="add-ip-allow"><i class="fa fa-plus-circle"></i></button>
                            <button class="btn btn-danger btn-xs" type="button" id="remove-ip-allow"><i class="fa fa-minus-circle"></i></button>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="add_to_allow_list"><strong><?=  __('Block these IP Addresses');?></strong></label>
                        </div>
                        <div class="col-md-1">
                            <?= $form->render('block_ip_address',array('class' => 'checkbox-blue')); ?>
                        </div>
                        <div class="col-md-5">
                            <?= $form->render('add_to_block_list',array('class' => 'form-control form-control-sm input-sm')); ?>
                            <?= $form->render('blocked_ip_addresses',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-xs" type="button" id="add-ip-block"><i class="fa fa-plus-circle"></i></button>
                            <button class="btn btn-danger btn-xs" type="button" id="remove-ip-block"><i class="fa fa-minus-circle"></i></button>
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
                </form>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>