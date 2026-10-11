<style>
    #mailserver-general-settings-form h3 {
        border-bottom: 3px solid #ccc;
        background: #ddd;
        padding: 5px;
    }
</style>
<form id="mailserver-general-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>" data-mail-server="<?= $installed_mail; ?>">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-2">
                    <a class="btn btn-block btn-outline btn-info" data-id="confirm" data-url="<?= $validate_url;?>"> <?= __('Retrive Again'); ?></a>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-block btn-outline btn-default" type="reset"> <?= __('Cancel'); ?></button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-block btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </div>
    <br />
    <div class="row">

        <div class="col-md-12">
            <div class="tabbable-line">
                <ul class="nav nav-pills nav-fill ">
                    <li class="nav-item active"><a href="#smtp" data-toggle="tab"><?= __('SMTP / POP3 / IMAP'); ?></a></li>
                    <li class="nav-item"><a href="#antispam" data-toggle="tab"><?= __('Antispam');?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="smtp">
                        <div class="row">
                            <div class="col-md-4">
                                <h3><?= __("SMTP Connections");?></h3>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label for="max_smtp_threads"> <?= __('Maximum Number of Simultaneous Connections');?></label>
                                        <?= $form->render("max_smtp_threads",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label for="max_msg_size"> <?= __('Maximum Message Size');?></label>
                                        <div class="input-group input-group-sm" data-autoclose="true">
                                            <?= $form->render("max_msg_size",array('class' => 'form-control form-control-sm')); ?>
                                            <span class="input-group-addon">MB</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="sub_port_status">  <?= __('Enable Submission Port');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("sub_port_status",['class' => 'checkbox-blue', "data-sub-elements" => "sub_port"]); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="sub_port">  <?= __("Listen on alternate port");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("sub_port",['class' => 'form-control form-control-sm input-sm']); ?>
                                    </div>
                                </div>
                                <br />
                                <h3><?= __("POP3/IMAP Connections");?></h3>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="max_pop_threads"> <?= __("Max. Number of POP3 simultaneous connections");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("max_pop_threads",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="max_imap_threads"> <?= __("Max. Number of IMAP simultaneous connections");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("max_imap_threads",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <h3><?= __("Delivery of e-mail");?></h3>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="smtp_no_tries"> <?= __("Number of retries");?></label>
                                        <?= $form->render("smtp_no_tries",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="smtp_tries_min"> <?= __("Minutes between every retry");?></label>
                                        <?= $form->render("smtp_tries_min",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="hostname"> <?= __("Local hostname");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("hostname",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="delivery_bind_to_ip"> <?= __("Bind to local IP Address");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("delivery_bind_to_ip",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="max_rec_batch"> <?= __("Maximum number of recipients in batch");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("max_rec_batch",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="add_del_head"> <?= __("Add Delivered to header");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("add_del_head",array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="rule_loop_limit"> <?= __("Rule Loop Limit");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("rule_loop_limit",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="max_mx_host"> <?= __("Maximum number of recipient hosts");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("max_mx_host",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <h3><?= __("SMTP Relayer");?></h3>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="smtp_relayer"> <?= __("Remote hostname");?></label>
                                        <?= $form->render("smtp_relayer",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="smtp_relayer_port"> <?= __("Remote TCP/IP Port");?></label>
                                        <?= $form->render("smtp_relayer_port",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="smtp_relayer_auth"> <?= __("Server Require Authentication");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("smtp_relayer_auth",array('class' => 'checkbox-blue', "data-sub-elements" => "smtp_relayer_user,smtp_relayer_passwd,smtp_relayer_security")); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="smtp_relayer_user"> <?= __("Username");?></label>
                                        <?= $form->render("smtp_relayer_user",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="smtp_relayer_passwd"> <?= __("Password");?></label>
                                        <?= $form->render("smtp_relayer_passwd",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="smtp_relayer_security"> <?= __("Connection Security");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("smtp_relayer_security",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                                <br />
                                <h3><?= __("RFC Compliance");?></h3>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="smtp_auth_plain"> <?= __("Allow plain text authentication");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("smtp_auth_plain",array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="allow_empty_sender"> <?= __("Allow empty sender address");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("allow_empty_sender",array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="allow_invalid_end"> <?= __("Allow incorrectly formatted line endings");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("allow_invalid_end",array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="dis_invalid_client"> <?= __("Disconnect client after too many invalid commands");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("dis_invalid_client",array('class' => 'checkbox-blue', "data-sub-elements" => "max_inv_command")); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="max_inv_command"> <?= __("Maximum number of invalid commands");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("max_inv_command",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="antispam">
                        <div class="row">
                            <div class="col-md-6">
                                <h3><?= __("General");?></h3>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="spam_protection">  <?= __('Enable Spam Protection');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("spam_filter_status",array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="spam_mark_threshold"> <?= __("Spam Mark Threshold");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("spam_mark_threshold",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="add_header_spam"> <?= __("Add X-hMailServer Spam");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("add_header_spam",array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="add_reason_spam"> <?= __("Add X-hMailServer Reason");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("add_reason_spam",array('class' => 'checkbox-blue')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="add_msg_subj"> <?= __("Add to message Subject");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("add_msg_subj",array('class' => 'checkbox-blue', "data-sub-elements" => "add_msg_subj_txt")); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="add_msg_subj_txt"> <?= __("Add to message Subject Text");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("add_msg_subj_txt",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="spam_del_threshold"> <?= __("Spam Delete Threshold");?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render("spam_del_threshold",array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="spam_max_msg_size"> <?= __('Maximum Message Size to Scan');?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-sm" data-autoclose="true">
                                            <?= $form->render("spam_max_msg_size",array('class' => 'form-control form-control-sm')); ?>
                                            <span class="input-group-addon">MB</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <h3><?= __("SPF Settings");?></h3>
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td></td>
                                        <td>Score</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label for="use_spf"> <?= __("Use SPF");?></label>
                                                </div>
                                                <div class="col-md-6">
                                                    <?= $form->render("use_spf",array('class' => 'checkbox-blue', "data-sub-elements" => "use_spf_score")); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= $form->render("use_spf_score",array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label for="check_host_helo"> <?= __("Check HELO");?></label>
                                                </div>
                                                <div class="col-md-6">
                                                    <?= $form->render("check_host_helo",array('class' => 'checkbox-blue', "data-sub-elements" => "check_host_helo_score")); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= $form->render("check_host_helo_score",array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label for="use_mx_checks"> <?= __("Check DNS-MX");?></label>
                                                </div>
                                                <div class="col-md-6">
                                                    <?= $form->render("use_mx_checks", array('class' => 'checkbox-blue', "data-sub-elements" => "use_mx_checks_score")); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= $form->render("use_mx_checks_score", array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label for="verify_dkim"> <?= __("Verify DKIM");?></label>
                                                </div>
                                                <div class="col-md-6">
                                                    <?= $form->render("verify_dkim",array('class' => 'checkbox-blue', "data-sub-elements" => "verify_dkim_score")); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= $form->render("verify_dkim_score",array('class' => 'form-control form-control-sm input-sm')); ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
