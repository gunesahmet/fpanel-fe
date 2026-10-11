<div class="col-md-6">
    <form id="mailserver-general-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>" data-mail-server="<?= $installed_mail; ?>">
        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
        <div class="form-group row">
            <div class="col-md-4">
                <label for="hostname">  <?= __('Hostname');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("hostname",array('class' => 'form-control form-control-sm input-sm')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="max_message_size">  <?= __('Maximum Message Size');?></label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm" data-autoclose="true">
                    <?= $form->render("max_message_size",array('class' => 'form-control form-control-sm')); ?>
                    <span class="input-group-addon">MB</span>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="queue_life_time">  <?= __('Queue Life-Time');?></label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm" data-autoclose="true">
                    <?= $form->render("queue_life_time",array('class' => 'form-control form-control-sm')); ?>
                    <span class="input-group-addon"><?= __("Hours");?></span>
                </div>
            </div>
        </div>


        <div class="form-group row">
            <div class="col-md-4">
                <label for="bounce_queue_lifetime">  <?= __('Bounce Queue Life-Time');?></label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm" data-autoclose="true">
                    <?= $form->render("bounce_queue_lifetime",array('class' => 'form-control form-control-sm')); ?>
                    <span class="input-group-addon"><?= __("Seconds");?></span>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="policyd_spy_timelimit">  <?= __('Policyd Spf Time Limit');?></label>
            </div>
            <div class="col-md-8">
                <div class="input-group input-group-sm" data-autoclose="true">
                    <?= $form->render("policyd_spy_timelimit",array('class' => 'form-control form-control-sm')); ?>
                    <span class="input-group-addon"><?= __("Seconds");?></span>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="concurrency_in_que">  <?= __('Concurrency Connections');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("concurrency_in_que",array('class' => 'form-control form-control-sm input-sm')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="max_recipients">  <?= __('Maximum Recipients');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("max_recipients",array('class' => 'form-control form-control-sm input-sm')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="submission_port">  <?= __('Enable Submission Port (587)');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("submission_port",array('class' => 'checkbox-blue')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="delete_double_msg">  <?= __('Delete DoubleBounce Messages');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("delete_double_msg",array('class' => 'checkbox-blue')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="imap_auth_with_smtp">  <?= __('Enable Pop3/Imap Authentication with SMTP Auth');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("imap_auth_with_smtp",array('class' => 'checkbox-blue')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="spam_protection">  <?= __('Enable Spam Protection');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("spam_filter_status",array('class' => 'checkbox-blue')); ?>
            </div>
        </div>


        <div class="form-group row">
            <div class="col-md-4">
                <label for="spf_protection">  <?= __('Enable SPF Protection');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("spf_protection",array('class' => 'checkbox-blue')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="rbl_status">  <?= __('RBL Status');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("rbl_status",["class" => "checkbox-blue", "data-sub-elements" => "add_rbl_list,rbl_list"]); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="add_rbl_list">  <?= __('Add RBL list');?></label>
            </div>
            <div class="col-md-7">
                <?= $form->render("add_rbl_list",array('class' => 'form-control form-control-sm input-sm')); ?>
            </div>
            <div class="col-md-1">
                <a id="add_to_rbl_list" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i></a>
                <a id="remove_from_rbl_list" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="rbl_list">  <?= __('RBL list');?></label>
            </div>
            <div class="col-md-8">
                <?= $form->render("rbl_list",array('class' => 'form-control form-control-sm input-sm')); ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <div class="float-right">
                    <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                    <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>




<style>
    .ms-container.single {
        width: 100%;
    }
    .ms-container.single .ms-list {
        border-radius: 0;
    }
    .ms-selected {
        background: #d9edf7 none repeat scroll 0 0;
        color: #ddd;
    }
    .ms-selectable li.ms-elem-selectable.ms-selected {
        color: #fff;
    }
    .ms-container.single .ms-list.ms-focus {
        border-color: #1ab394;
        box-shadow: inherit;
        border-radius: 0;
    }
</style>