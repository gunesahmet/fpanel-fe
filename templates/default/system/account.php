<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <?= __('Account Management');?>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                <form class="form-horizontal" method="post">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong><?= __('Change Password');?></strong>
                                    <p class="muted"><?= __('You can change your account password from here');?></p>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label for="inputUsername"><? // echo __('');?></label>
                                            <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Username'))); ?>
                                            <div class="separator"></div>
                                            <label for="inputFullname"><?= __('');?></label>
                                            <?= $form->render('fullname',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Fullname'))); ?>
                                            <div class="separator"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="inputPasswordOld"><? // echo __('');?></label>
                                            <?= $form->render('old_password',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Old Password'))); ?>
                                            <div class="separator"></div>

                                            <label for="inputPasswordNew"><? // echo __('');?></label>
                                            <div class="input-group input-group-sm" id="input-group-password">
                                                <?= $form->render("password",array('class' => 'form-control form-control-sm','placeholder' => __('New Password')));?>
                                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                            </div>
                                            <div class="separator"></div>
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
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label for="inputEmail"><? // echo __('');?></label>
                                            <?= $form->render('email',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('E-Mail'))); ?>

                                            <div class="separator"></div>

                                            <label for="inputPhone"><? // echo __('');?></label>
                                            <?= $form->render('telephone',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Phone'))); ?>

                                            <div class="separator"></div>

                                            <label for="inputFax"><? // echo __('');?></label>
                                            <?= $form->render('fax',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Fax'))); ?>

                                            <div class="separator"></div>

                                            <label for="inputCity"><? // echo __('');?></label>
                                            <?= $form->render('city',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('City'))); ?>

                                            <div class="separator"></div>

                                        </div>
                                        <div class="col-md-6">
                                            <label for="inputAddress"><? // echo __('');?></label>
                                            <?= $form->render('address',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Address'),'rows' => '3')); ?>

                                            <div class="separator"></div>

                                            <label for="inputCompany"><? // echo __('');?></label>
                                            <?= $form->render('company',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Company'))); ?>

                                            <div class="separator"></div>

                                            <label for="inputCountry"><? // echo __('');?></label>
                                            <?= $form->render('country',array('class' => 'form-control form-control-sm input-sm', 'placeholder' => __('Country'))); ?>

                                            <div class="separator"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                        <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                    </div>
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