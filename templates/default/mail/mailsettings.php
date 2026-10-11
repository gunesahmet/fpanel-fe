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
                <form id="mail-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="forward_to_address"><?= __('Forward to Address');?></label>
                        </div>
                        <div class="col-md-6">
                            <label> <?= $form->render('forward_to_address',array('class' => 'checkbox-blue', "data-sub-elements" => "mail_address")); ?> </label>
                            <div class="input-group input-group-sm" id="input-group-mail_address">
                                <?= $form->render('mail_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                                <span class="input-group-addon">@<?= $domain_name;?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="reject_incoming"><?= __('Reject');?></label>
                        </div>
                        <div class="col-md-6">
                            <?
                            echo '<label>';
                            echo $form->render('reject_incoming',array('class' => 'checkbox-blue'));
                            echo '</label>';
                            ?>
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
                </form>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>