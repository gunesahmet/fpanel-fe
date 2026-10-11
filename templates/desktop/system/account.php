<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <? echo __('Account Management');?>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                <form class="form-horizontal" method="post">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom: 25px">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong><? echo __('Change Password');?></strong>
                                        <p class="muted"><? echo __('You can change your account password from here');?></p>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <label for="inputUsername"><? // echo __('');?></label>
                                                <? echo $form->render('username',array('class' => 'form-control', 'placeholder' => __('Username'))); ?>
                                                <div class="separator"></div>


                                                <label for="inputPasswordOld"><? // echo __('');?></label>
                                                <? echo $form->render('old_password',array('class' => 'form-control', 'placeholder' => __('Old Password'))); ?>
                                                <div class="separator"></div>

                                            </div>
                                            <div class="col-md-6">

                                                <label for="inputFullname"><? // echo __('');?></label>
                                                <? echo $form->render('fullname',array('class' => 'form-control', 'placeholder' => __('Fullname'))); ?>
                                                <div class="separator"></div>


                                                <label for="inputPasswordNew"><? // echo __('');?></label>
                                                <div class="input-group">
                                                    <? echo $form->render('password',array('class' => 'form-control', 'placeholder' => __('New Password'))); ?>
                                                    <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" onclick="passgen('password','confirm-password')" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                                                    <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd2" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                                </div>

                                                <div class="separator"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr />
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong><? echo __('Contact details');?></strong>
                                        <p class="muted"><? echo __('You can change contact informations from here');?></p>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <label for="inputEmail"><? // echo __('');?></label>
                                                <? echo $form->render('email',array('class' => 'form-control', 'placeholder' => __('E-Mail'))); ?>

                                                <div class="separator"></div>

                                                <label for="inputPhone"><? // echo __('');?></label>
                                                <? echo $form->render('telephone',array('class' => 'form-control', 'placeholder' => __('Phone'))); ?>

                                                <div class="separator"></div>

                                                <label for="inputFax"><? // echo __('');?></label>
                                                <? echo $form->render('fax',array('class' => 'form-control', 'placeholder' => __('Fax'))); ?>

                                                <div class="separator"></div>

                                                <label for="inputCity"><? // echo __('');?></label>
                                                <? echo $form->render('city',array('class' => 'form-control', 'placeholder' => __('City'))); ?>

                                                <div class="separator"></div>

                                            </div>
                                            <div class="col-md-6">
                                                <label for="inputAddress"><? // echo __('');?></label>
                                                <? echo $form->render('address',array('class' => 'form-control', 'placeholder' => __('Address'),'rows' => '3')); ?>

                                                <div class="separator"></div>

                                                <label for="inputCompany"><? // echo __('');?></label>
                                                <? echo $form->render('company',array('class' => 'form-control', 'placeholder' => __('Company'))); ?>

                                                <div class="separator"></div>

                                                <label for="inputCountry"><? // echo __('');?></label>
                                                <? echo $form->render('country',array('class' => 'form-control', 'placeholder' => __('Country'))); ?>

                                                <div class="separator"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <hr />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                                            <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                                        </div>
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
