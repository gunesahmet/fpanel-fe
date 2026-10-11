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
                <form id="edit-network-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="interface"> <?= __('Interface');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('interface',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="format"> <?= __('Netmask CIDR Format');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('format',array('id' => 'network-format','class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="ip_address"> <?= __('IP Address');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-ip_address">
                                <?= $form->render('ip_address',array('class' => 'form-control form-control-sm')); ?>
                                <span class="input-group-addon">/</span>
                                <?= $form->render('netmask_cidr',array('class' => 'form-control form-control-sm')); ?>
                                <?= $form->render('netmask',array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="gateway"> <?= __('Gateway');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('gateway',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="used_type"> <?= __('Using Type');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('used_type',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <hr >

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
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>