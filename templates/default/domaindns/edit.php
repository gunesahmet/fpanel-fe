<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <form id="edit-dns-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
            <div class="row">
                <div class="col-md-6">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <!-- Record Type BEGIN-->
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="record_type"> <?=  __('Record Type');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('record_type',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <!-- Record Type END-->

                    <? if($record_data['type'] == "NS") {?>
                        <!-- NS Record BEGIN-->
                        <div class="form-group row" id="ns_record_block_name">
                            <div class="col-md-4">
                                <label for="domain"> <?=  __('Domain Name');?> </label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control form-control-sm input-sm" id="domain" name="domain" disabled value="<?= $domain_name;?>">
                            </div>
                        </div>
                        <div class="form-group row" id="ns_record_block_ns">
                            <div class="col-md-4">
                                <label for="ns_record"> <?=  __('NS Record');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('ns_record',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <!-- NS Record END-->
                    <? } ?>
                    <? if($record_data['type'] == "A") {?>

                        <!-- A Record BEGIN-->
                        <div class="form-group row" id="a_record_block_name">
                            <div class="col-md-4">
                                <label for="a_record"> <?=  __('A Record');?> </label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <?= $form->render('a_record',array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">.<?= $domain_name; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="a_record_block_ip">
                            <div class="col-md-4">
                                <label for="a_record_ip_address"> <?=  __('IP Address');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('a_record_ip_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <!-- A Record END-->
                    <? } ?>

                    <? if($record_data['type'] == "CNAME") {?>
                        <!-- CNAME Record BEGIN-->
                        <div class="form-group row" id="cname_record_block_name">
                            <div class="col-md-4">
                                <label for="cname_record_domain_name"> <?=  __('Domain Name');?> </label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <?= $form->render('cname_record_domain_name', ["class" => "form-control form-control-sm"]); ?>
                                    <span class="input-group-addon">.<?= $domain_name; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="cname_record_block_cname">
                            <div class="col-md-4">
                                <label for="cname_record"> <?=  __('CNAME');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('cname_record',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <!-- CNAME Record END-->
                    <? } ?>

                    <? if($record_data['type'] == "MX") {?>
                        <!-- MX Record BEGIN-->
                        <div class="form-group row" id="mx_record_block_name">
                            <div class="col-md-4">
                                <label for="domain"> <?=  __('Domain Name');?> </label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <?= $form->render('mx_domain',array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">.<?= $domain_name; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="mx_record_block">
                            <div class="col-md-4">
                                <label for="mx_record"> <?=  __('MX Record');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('mx_record',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group row" id="mx_record_block_priority">
                            <div class="col-md-4">
                                <label for="mx_record_priority"> <?=  __('Priority');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('mx_record_priority',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <!-- MX Record BEGIN-->
                    <? } ?>


                    <? if($record_data['type'] == "PTR") {?>
                        <!-- PTR Record BEGIN-->
                        <div class="form-group row" id="ptr_record_block_name">
                            <div class="col-md-4">
                                <label><?= __('IP');?></label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <?= $form->render('ptr_record_ip_address',array('class' => 'form-control form-control-sm col-8', "placeholder" => __("Ip Address"))); ?>
                                    <span class="input-group-addon col-1"> / </span>
                                    <?= $form->render('ptr_record_ip_block',array('class' => 'form-control form-control-sm col-3', "placeholder" => __("Block"))); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="ptr_record_host">
                            <div class="col-md-4">
                                <label for="ptr_record"><?=  __('PTR Record');?></label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <?= $form->render('ptr_record',array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">.<?= $domain_name;?></span>
                                </div>
                            </div>
                        </div>
                        <!-- PTR Record END-->
                    <? } ?>

                    <? if($record_data['type'] == "TXT") {?>
                        <!-- TXT Record BEGIN-->
                        <div class="form-group row" id="txt_record_block_name">
                            <div class="col-md-4">
                                <label for="domain"> <?= __('Domain Name');?> </label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <?= $form->render('txt_record_prefix',array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">.<?= $domain_name;?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="txt_record_block">
                            <div class="col-md-4">
                                <label for="txt_record"> <?=  __('TXT Record');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('txt_record',array('class' => 'form-control form-control-sm input-sm')); ?>.
                            </div>
                        </div>
                        <!-- TXT Record END-->
                    <? } ?>


                    <? if($record_data['type'] == "SRV") {?>

                        <!-- SRV Record BEGIN-->
                        <div class="form-group row srv_record_block">
                            <div class="col-md-4">
                                <label for="domain"> <?= __('Service Name');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('srv_service_name',array('class' => 'form-control form-control-sm input-sm'))?>
                            </div>
                        </div>

                        <div class="form-group row srv_record_block">
                            <div class="col-md-4">
                                <label for="txt_record"> <?=  __('Protocol');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('srv_protocol',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group row srv_record_block">
                            <div class="col-md-4">
                                <label for="txt_record"> <?=  __('Domain Name');?> </label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <?= $form->render('srv_domain_name',array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">.<?= $domain_name; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row srv_record_block">
                            <div class="col-md-4">
                                <label for="txt_record"> <?=  __('Priority');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('srv_priority',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group row srv_record_block">
                            <div class="col-md-4">
                                <label for="txt_record"> <?=  __('Relative weight for records with the same priority');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('srv_priority_weight',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group row srv_record_block">
                            <div class="col-md-4">
                                <label for="txt_record"> <?=  __('Target Host');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('srv_target_host',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group row srv_record_block">
                            <div class="col-md-4">
                                <label for="txt_record"> <?=  __('Target Port');?> </label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('srv_target_port',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <!-- SRV Record END-->
                    <? } ?>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class=" float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>