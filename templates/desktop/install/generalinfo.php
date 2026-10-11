<div class="col-md-12 margin-top-10">
    <form id="system-info-settings-form" method="post" class="form-horizontal" action="<? echo $this->uri->url_encrypt("install/finish");?>">
        <div class="form-group">
            <div class="col-md-3">
                <label for="company_name"> <strong><? echo __('Company Name');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('company_name',array('class' => 'form-control input-sm'));?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="fullname"> <strong><? echo __('Name Lastname');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('fullname',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="telephone"> <strong><? echo __('Telephone');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('telephone',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="fax"> <strong><? echo __('Fax');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('fax',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="address"> <strong><? echo __('Address');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('address',array('class' => 'form-control input-sm'));?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="city"> <strong><? echo __('City');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('city',array('class' => 'form-control input-sm'));?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="town"> <strong><? echo __('Town');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('town',array('class' => 'form-control input-sm'));?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="zip_code"> <strong><? echo __('Zip Code');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('zip_code',array('class' => 'form-control input-sm'));?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3">
                <label for="country"> <strong><? echo __('Country');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('country',array('class' => 'form-control input-sm'));?>
            </div>
        </div>


        <div class="form-group">
            <div class="col-md-3">
                <label for="email"> <strong><? echo __('Email Address').' <b>(*)</b>';?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('email',array('class' => 'form-control input-sm'));?>
            </div>
        </div>


        <div class="form-group">
            <div class="col-md-3">
                <label for="themes"> <strong><? echo __('Themes');?></strong></label>
            </div>
            <div class="col-md-4">
                <? echo $form->render('themes',array('class' => 'form-control input-sm'));?>
            </div>
        </div>
    </form>
</div>
