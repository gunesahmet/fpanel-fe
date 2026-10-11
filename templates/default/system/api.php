<div class="row" id="api-management-panel" data-width="45%" data-height="50%">
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Api Management');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <form id="api-management-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="row" style="margin-bottom: 25px">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="api_status"><?= __('Api Status');?></label>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->render('api_status',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="api_url"><?= __('Api URL');?></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?= $_SERVER['HTTP_HOST'];?>/</span>
                                        <?= $form->render('api_url',array('class' => 'form-control form-control-sm input-sm')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="username"><?= __('Api Username');?></label>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="password"><?= __('Api Password');?></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <?= $form->render('password',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                        <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label><?= __('Confirm Password');?> *</label>
                                </div>
                                <div class="col-md-6">
                                    <input id="confirm-password" name="confirm-password" type="password" class="form-control form-control-sm input-sm">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-10">
                                    <div class="float-right">
                                        <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                        <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-6" >
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <?= $form->render('user_list',array('class' => 'multiselect')); ?>
                                </div>

                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>