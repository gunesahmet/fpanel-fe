<form id="mailserver-general-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>" data-mail-server="<?= $installed_mail; ?>">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <fieldset>
                    <legend>
                        <?= __("General & Limit Settings");?>
                    </legend>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="hostname">  <?= __('Hostname');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("hostname",array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="sub_port_status">  <?= __('Enable Submission Port');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("sub_port_status",['class' => 'checkbox-blue', "data-sub-elements" => "sub_port,sub_port_auth_mode"]); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="sub_port">  <?= __("Listen on alternate port");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("sub_port",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="sub_port_auth_mode">  <?= __("Authentication Mode");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("sub_port_auth_mode",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="max_msg_size_status"><?= __('Limit outbound message size');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("max_msg_size_status",['class' => 'checkbox-blue', "data-sub-elements" => "max_msg_size"]);?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="max_msg_size"> <?= __('Maximum');?></label>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm" data-autoclose="true">
                                <?= $form->render("max_msg_size",array('class' => 'form-control form-control-sm')); ?>
                                <span class="input-group-addon">MB</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="smtpd_recipient_limit">  <?= __("Limit number of recipients per hour");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("smtpd_recipient_limit",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="smtp_in_connection_limit">  <?= __("Maximum number of concurrent connections");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("smtp_in_connection_limit",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="smtp_out_connection_limit">  <?= __("Maximum number of send threads");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("smtp_out_connection_limit",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="max_queue_lifetime"><?= __("Failed message lifetime");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("max_queue_lifetime",['class' => 'form-control form-control-sm input-sm']);?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="spam_protection">  <?= __('Enable Spam Protection');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("spam_filter_status",array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="col-md-4">
                <fieldset>
                    <legend>
                        <?= __("Blacklist (RBL) Settings");?>
                    </legend>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="dns_rbl_status"><?= __('Enable DNS Blacklisting');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("dns_rbl_status",['class' => 'checkbox-blue', "data-sub-elements" => "dns_rbl_mode,me_dns_black_lists"]);?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="dns_rbl_mode"><?= __("Action when detected");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("dns_rbl_mode",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="me_dns_black_lists">  <?= __("DNS Blacklists");?></label>
                        </div>
                        <div class="col-md-12">
                            <?= $form->render('me_dns_black_lists',array('class' => 'multiselect','data-selectable' => __('Available'),'data-selection' => __('Selected'))); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="url_rbl_status">  <?= __('Enable URL Blacklisting');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("url_rbl_status",['class' => 'checkbox-blue', "data-sub-elements" => "url_rbl_mode,me_url_black_lists"]);?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="url_rbl_mode">  <?= __("Action when detected");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("url_rbl_mode",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="me_url_black_lists">  <?= __("URL Blacklists");?></label>
                        </div>
                        <div class="col-md-12">
                            <?= $form->render('me_url_black_lists',array('class' => 'multiselect','data-selectable' => __('Available'),'data-selection' => __('Selected'))); ?>
                        </div>
                    </div>
                </fieldset>

            </div>

            <div class="col-md-4">
                <fieldset>
                    <legend style="position: relative;">
                        SPF (Sender Policy Framework) <span class="label float-right label-primary" style="position: absolute;bottom: 10px;margin-left: 5px;" data-toggle="tooltip" title="<?= __("This feature work only in MailEnable Professional or Enterprise versions");?>">pro</span>
                    </legend>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="spf_protection"> <?= __('Enable SPF (Sender Policy Framework) Checking');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("spf_protection",['class' => 'checkbox-blue', "data-sub-elements" => "spf_best_guess,spf_best_guesses,spf_white_lists_status,spf_white_lists,spf_pass_local"]);?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="spf_best_guess"> <?= __('Apply best-guess policy for domains without SPF records');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("spf_best_guess",['class' => 'checkbox-blue', "data-sub-elements" => "spf_best_guesses"]);?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="spf_best_guesses"> <?= __('This policy will be applied to all domains that do not have a SPF record');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("spf_best_guesses",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="spf_white_lists_status"> <?= __('Enable local SPF whitelist policy');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("spf_white_lists_status",['class' => 'checkbox-blue', "data-sub-elements" => "spf_white_lists"]);?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="spf_white_lists"> <?= __("The whitelist policy is applied after the 'all' keyword is found. Use thi to add your own possible denials to inbound connections");?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("spf_white_lists",['class' => 'form-control form-control-sm input-sm']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="spf_pass_local"> <?= __('Dont check connections from local IP addresses');?></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render("spf_pass_local",['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <div class="float-right">
                    <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                    <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<style>
    .ms-container .ms-list {
        height: 102px;
    }
</style>