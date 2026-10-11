<div id="domain-edit-panel" >
    <form id="edit-domain-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
        <div class="row">
            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
            <div class="col-md-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= $client_data['user']['info']['username'];?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-3">
                                <strong><?= __('Change Password');?></strong>
                                <p class="muted"><?= __('You can change your account password from here');?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="">
                                            <label for="fullname"><?= __('Fullname');?></label>
                                            <?= $form->render('fullname',array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </div>
                                        <div class="">
                                            <label for="username"><?= __('Username');?></label>
                                            <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </div>
                                        <div class="">
                                            <label for="parents"><?= __('Parent');?></label>
                                            <?= $form->render('parents',array('class' => 'form-control form-control-sm input-sm select-picker')); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="">
                                            <label for="password"><?= __('New Password');?></label>
                                            <div class="input-group input-group-sm" id="input-group-password">
                                                <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                                            </div>
                                        </div>
                                        <div class="">
                                            <label for="confirm-password"><?= __('Confirm Password');?> *</label>
                                            <?= $form->render("confirm_password",array('id' => 'confirm-password','name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')) ?>
                                        </div>
                                        <div class="">
                                            <label for="templates"><?= __('Apply new Limits from Templates');?></label>
                                            <?= $form->render('templates',array('class' => 'form-control form-control-sm input-sm select-picker')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-3">
                                <strong><?= __('Contact details');?></strong>
                                <p class="muted"><?= __('You can change contact informations from here');?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-body">
                                            <div class="">
                                                <label for="telephone"><?= __('Phone');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                    <?= $form->render('telephone',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="">
                                                <label for="email_addr"><?= __('E-Mail');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                    <?= $form->render('email_addr',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="">
                                                <label for="fax"><?= __('Fax');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-fax"></i></span>
                                                    <?= $form->render('fax',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="">
                                                <label for="company"><?= __('Company');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-tags"></i></span>
                                                    <?= $form->render('company',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div class="">
                                                <label for="address"><?= __('Address');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-university"></i></span>
                                                    <?= $form->render('address',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="">
                                                <label for="city"><?= __('City');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                                    <?= $form->render('city',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="">
                                                <label for="region"><?= __('Region');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-road"></i></span>
                                                    <?= $form->render('region',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="">
                                                <label for="country"><?= __('Country');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                                                    <?= $form->render('country',array('class' => 'form-control form-control-sm')); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><?= __('Features');?></div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div id="contact-1" class="tab-pane active">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="reset" class="btn btn-default btn-sm btn-block"> <?= __('Cancel');?> </button>
                                        <button class="btn btn-sm btn-primary btn-block" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                    </div>

                                </div>
                                <div class="client-detail">
                                    <hr />
                                    <ul class="list-unstyled file-list">
                                        <li class="list-item fist-item">
                                            <span class="float-right"> <?= __('Domain');?> </span>
                                            <?= __('User Type');?>
                                        </li>
                                        <li class="list-item">
                                            <span class="float-right"> <?= $this->env->convert($client_data['user']['statistics']['used_total_disk']);?> </span>
                                            <?= __('Disk Used');?>
                                        </li>
                                        <li class="list-item">
                                            <span class="float-right"> <?= $this->env->convert($client_data['user']['statistics']['used_total_traffic']);?> </span>
                                            <?= __('Traffic Used');?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>