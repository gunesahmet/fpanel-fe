<form id="create-ssl-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="row">
        <div class="col-md-12">
            <?
            $data = array(
                'certificate_name'          => array('type' => 'text','display_name' => __('Certificate Name')),
                'certificate_keysize'       => array('type' => 'selectbox','display_name' => __('Keysize (Bit Value)')),
//                                    'certificate_use_www'       => array('type' => 'checkbox','display_name' => __('Use www')),
                'certificate_domain_name'   => array('type' => 'text','display_name' => __('Domain Name')),
                'certificate_country'       => array('type' => 'selectbox','display_name' => __('Country')),
                'certificate_state'         => array('type' => 'text','display_name' => __('State')),
                'certificate_city'          => array('type' => 'text','display_name' => __('City')),
                'certificate_company'       => array('type' => 'text','display_name' => __('Company')),
                'certificate_department'    => array('type' => 'text','display_name' => __('Department')),
            );

            foreach ($data as $key => $value) { ?>

                <div class="form-group">
                    <div class="col-md-4">
                        <label for="<? echo $key;?>" class="strong"><? echo $value['display_name'];?></label>
                    </div>

                    <div class="col-md-8">
                        <? if($value['type'] != "checkbox") echo $form->render($key,array('class' => 'form-control input-sm'));
                        else echo $form->render($key,array('class' => 'checkbox-blue')); ?>
                    </div>
                </div>
            <? } ?>

            <div class="form-group">
                <div class="col-md-12">
                    <div class="pull-right">
                        <button class="btn btn-default btn-sm" type="reset"> <? echo __('Cancel');?> </button>
                        <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
