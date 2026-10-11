<form class="form-horizontal" method="post">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="company_name"> <strong><?= __('Company Name');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('company_name',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="fullname"> <strong><?= __('Name & Lastname');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('fullname',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="telephone"> <strong><?= __('Telephone');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('telephone',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="fax"> <strong><?= __('Fax');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('fax',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="address"> <strong><?= __('Address');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('address',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="city"> <strong><?= __('City');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('city',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="town"> <strong><?= __('Town');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('town',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="zip_code"> <strong><?= __('Zip Code');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('zip_code',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="country"> <strong><?= __('Country');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('country',array('class' => 'form-control form-control-sm select-picker'));?>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="email"> <strong><?= __('Email Address').' <b>(*)</b>';?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('email',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="themes"> <strong><?= __('Themes');?></strong></label>
                        </div>
                        <div class="col-md-4">
                            <?= $form->render('themes',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="float-right">
                                <a class="btn btn-sm btn-danger" href="<?= $this->uri->encode('install/serverinfo');?>"><i class="fa fa-backward"></i> <?= __('Go Back'); ?></a>
                                <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-check-circle"></i> <?= __('Next Step'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
