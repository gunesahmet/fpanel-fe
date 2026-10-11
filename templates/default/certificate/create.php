<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <form id="create-ssl-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
            <div class="row">
                <div class="col-md-6">
                    <?
                    $data = array(
                        'certificate_name'          => array('type' => 'text','display_name' => __('Certificate Name')),
                        'certificate_user_email'    => array('type' => 'text','display_name' => __('Email')),
                        'certificate_keysize'       => array('type' => 'selectbox','display_name' => __('Keysize (Bit Value)')),
                        'certificate_domain_name'   => array('type' => 'text','display_name' => __('Domain Name')),
                        'certificate_country'       => array('type' => 'selectbox','display_name' => __('Country')),
                        'certificate_state'         => array('type' => 'text','display_name' => __('State')),
                        'certificate_city'          => array('type' => 'text','display_name' => __('City')),
                        'certificate_company'       => array('type' => 'text','display_name' => __('Company')),
                        'certificate_department'    => array('type' => 'text','display_name' => __('Department')),
                    );

                    foreach ($data as $key => $value) { ?>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="<?= $key;?>" class="strong"><?= $value['display_name'];?></label>
                            </div>

                            <div class="col-md-8">
                                <?
                                if($value['type'] == "checkbox")
                                    echo $form->render($key,array('class' => 'checkbox-blue'));
                                else if($value['type'] == "selectbox")
                                    echo $form->render($key,array('class' => 'form-control select-picker'));
                                else
                                    echo $form->render($key,array('class' => 'form-control form-control-sm input-sm'));
                                ?>
                            </div>
                        </div>
                    <? } ?>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-default btn-sm" type="reset"> <?= __('Cancel');?> </button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    textarea {
        resize: vertical; /* user can resize vertically, but width is fixed */
    }
</style>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
