<form class="form-horizontal" method="post" action="">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fas fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="hostname"> <strong><?= __('Hostname');?></strong></label>
                        </div>
                        <div class="col-md-5">
                            <?= $form->render('hostname',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="admin_email"> <strong><?= __('Admin Contact Email');?></strong></label>
                        </div>
                        <div class="col-md-5">
                            <?= $form->render('admin_email',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="default_ip_address"><strong><?= __('Default Server IP Address');?></strong></label>
                        </div>
                        <div class="col-md-5">
                            <?= $form->render('default_ip_address',array('class' => 'form-control form-control-sm input-sm','disabled' => 'disabled')); ?>
                        </div>
                    </div>

                    <? if($this->session->get("panel_type") == STANDALONE) { ?>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="ipaddress_list"> <strong><?= __('IP Address List');?></strong></label>
                        </div>
                        <div class="col-md-5">
                            <?= $form->render('ipaddress_list',array('class' => 'multiselect')); ?>
                        </div>
                    </div>
                    <br />
                    <? } ?>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="username"> <strong><?= __('Admin Username');?></strong></label>
                        </div>
                        <div class="col-md-5">
                            <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="password"> <strong><?= __('Admin Password');?></strong></label>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="confirm-password"> <strong><?= __('Confirm Password');?></strong></label>
                        </div>
                        <div class="col-md-5">
                            <input id="confirm-password" name="confirm-password" type="password" class="form-control form-control-sm input-sm">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <hr />
                            <div class="float-right">
                                <a class="btn btn-sm btn-danger" href="<?= $this->uri->encode('install');?>"><i class="fa fa-backward"></i> <?= __('Go Back'); ?></a>
                                <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-check-circle"></i> <?= __('Next Step'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
