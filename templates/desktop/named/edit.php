<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <!-- Record Type BEGIN-->
    <div class="form-group">
        <div class="col-md-4">
            <label for="record_type"> <? echo  __('Record Type');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('record_type',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <!-- Record Type END-->

    <? if($record_data['type'] == "NS") {?>
        <!-- NS Record BEGIN-->
        <div class="form-group" id="ns_record_block_name">
            <div class="col-md-4">
                <label for="domain"> <? echo  __('Domain Name');?> </label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control input-sm" id="domain" name="domain" disabled value="<? echo $domain_name;?>">
            </div>
        </div>
        <div class="form-group" id="ns_record_block_ns">
            <div class="col-md-4">
                <label for="ns_record"> <? echo  __('NS Record');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('ns_record',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <!-- NS Record END-->
    <? } ?>
    <? if($record_data['type'] == "A") {?>

        <!-- A Record BEGIN-->
        <div class="form-group" id="a_record_block_name">
            <div class="col-md-4">
                <label for="a_record"> <? echo  __('A Record');?> </label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <? echo $form->render('a_record',array('class' => 'form-control')); ?>
                    <span class="input-group-addon">.<? echo $domain_name; ?></span>
                </div>
            </div>
        </div>
        <div class="form-group" id="a_record_block_ip">
            <div class="col-md-4">
                <label for="a_record_ip_address"> <? echo  __('IP Address');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('a_record_ip_address',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <!-- A Record END-->
    <? } ?>

    <? if($record_data['type'] == "CNAME") {?>
        <!-- CNAME Record BEGIN-->
        <div class="form-group" id="cname_record_block_name">
            <div class="col-md-4">
                <label for="cname_record_domain_name"> <? echo  __('Domain Name');?> </label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <? echo $form->render('cname_record_domain_name'); ?>
                    <span class="input-group-addon">.<? echo $domain_name; ?></span>
                </div>
            </div>
        </div>
        <div class="form-group" id="cname_record_block_cname">
            <div class="col-md-4">
                <label for="cname_record"> <? echo  __('CNAME');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('cname_record',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <!-- CNAME Record END-->
    <? } ?>

    <? if($record_data['type'] == "MX") {?>
        <!-- MX Record BEGIN-->
        <div class="form-group" id="mx_record_block_name">
            <div class="col-md-4">
                <label for="domain"> <? echo  __('Domain Name');?> </label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control input-sm" id="domain" name="domain" disabled value="<? echo $domain_name;?>">
            </div>
        </div>
        <div class="form-group" id="mx_record_block">
            <div class="col-md-4">
                <label for="mx_record"> <? echo  __('MX Record');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('mx_record',array('class' => 'form-control input-sm')); ?>.
            </div>
        </div>
        <div class="form-group" id="mx_record_block_priority">
            <div class="col-md-4">
                <label for="mx_record_priority"> <? echo  __('Priority');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('mx_record_priority',array('class' => 'form-control input-sm')); ?>.
            </div>
        </div>
        <!-- MX Record BEGIN-->
    <? } ?>


    <? if($record_data['type'] == "PTR") {?>
        <!-- PTR Record BEGIN-->
        <div class="form-group" id="ptr_record_block_name">
            <div class="col-md-4">
                <label><? echo __('IP');?></label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <? echo $form->render('ptr_record_ip_address',array('class' => 'form-control')); ?>
                    <span class="input-group-addon"> / </span>
                    <? echo $form->render('ptr_record_ip_block',array('class' => 'form-control')); ?>
                </div>

            </div>
        </div>

        <div class="form-group" id="ptr_record_host">
            <div class="col-md-4">
                <label for="ptr_record"><? echo  __('PTR Record');?></label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <? echo $form->render('ptr_record',array('class' => 'form-control')); ?>
                    <span class="input-group-addon">.<? echo $domain_name;?></span>
                </div>
            </div>
        </div>
        <!-- PTR Record END-->
    <? } ?>

    <? if($record_data['type'] == "TXT") {?>
        <!-- TXT Record BEGIN-->
        <div class="form-group" id="txt_record_block_name">
            <div class="col-md-4">
                <label for="domain"> <? echo __('Domain Name');?> </label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <? echo $form->render('txt_record_prefix',array('class' => 'form-control')); ?>
                    <span class="input-group-addon">.<? echo $domain_name;?></span>
                </div>
            </div>
        </div>
        <div class="form-group" id="txt_record_block">
            <div class="col-md-4">
                <label for="txt_record"> <? echo  __('TXT Record');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('txt_record',array('class' => 'form-control input-sm')); ?>.
            </div>
        </div>
        <!-- TXT Record END-->
    <? } ?>


    <? if($record_data['type'] == "SRV") {?>

        <!-- SRV Record BEGIN-->
        <div class="form-group srv_record_block">
            <div class="col-md-4">
                <label for="domain"> <? echo __('Service Name');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('srv_service_name',array('class' => 'form-control input-sm'))?>
            </div>
        </div>

        <div class="form-group srv_record_block">
            <div class="col-md-4">
                <label for="txt_record"> <? echo  __('Protocol');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('srv_protocol',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group srv_record_block">
            <div class="col-md-4">
                <label for="txt_record"> <? echo  __('Domain Name');?> </label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <? echo $form->render('srv_domain_name',array('class' => 'form-control')); ?>
                    <span class="input-group-addon">.<? echo $domain_name; ?></span>
                </div>
            </div>
        </div>
        <div class="form-group srv_record_block">
            <div class="col-md-4">
                <label for="txt_record"> <? echo  __('Priority');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('srv_priority',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group srv_record_block">
            <div class="col-md-4">
                <label for="txt_record"> <? echo  __('Relative weight for records with the same priority');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('srv_priority_weight',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group srv_record_block">
            <div class="col-md-4">
                <label for="txt_record"> <? echo  __('Target Host');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('srv_target_host',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group srv_record_block">
            <div class="col-md-4">
                <label for="txt_record"> <? echo  __('Target Port');?> </label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('srv_target_port',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <!-- SRV Record END-->
    <? } ?>
    <div class="form-group">
        <div class="col-md-12">
            <div class=" pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
<script>
    core.changeDnsType();
</script>