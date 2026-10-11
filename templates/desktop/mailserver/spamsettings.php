<form id="mailserver-spam-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <? $elements = array(
        'spam_filter_status' => array('type' => 'checkbox', 'display_name' => __('Enable Spam Filter Service')),
        'spam_scores' => array('type' => 'text', 'display_name' => __('Spam Scores')),
        'pyzor_support' => array('type' => 'checkbox', 'display_name' => __('Pyzor Support')),
        'razor_support' => array('type' => 'checkbox', 'display_name' => __('Razor Support')),
        'skip_rbl' => array('type' => 'checkbox', 'display_name' => __('Skip RBL Checking')),
        'bayes_support' => array('type' => 'checkbox', 'display_name' => __('Bayes Support')),
        'spam_report' => array('type' => 'checkbox', 'display_name' => __('Enable Spam Report')),
    );
    foreach($elements as $key => $value) {
        ?>
        <div class="form-group" >
            <div class="col-md-3">
                <label for="<? echo $key;?>"> <? echo $value['display_name'];?></label>
            </div>
            <div class="col-md-4">
                <? if($value['type'] == "checkbox") {?>
                    <? echo $form->render($key,array('class' => 'checkbox-blue')); ?>
                <? } else { ?>
                    <? echo $form->render($key,array('class' => 'form-control input-sm')); ?>
                <? } ?>
            </div>
        </div>
    <? } ?>

    <div class="form-group">
        <div class="col-md-7">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>

<script>
    core.inputInits();
</script>