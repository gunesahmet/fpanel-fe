<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-md-12">
                <div class="tabbable-line">
                    <ul class="nav nav-pills nav-fill" id="mailserver-settings-tabs">
                        <li class="nav-item">
                            <a href="<?= $generalsettings_link;?>"><?= __('General Settings'); ?></a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?= $spamsettings_link;?>"><?= __('SpamAssassin Settings'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $blacklist_link;?>"><?= __('Black Lists'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $whitelist_link;?>"><?= __('White Lists'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $smtp_link;?>"><?= __('SMTP Queue'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-mail-general-settings">
                        </div>
                        <div class="tab-pane in active" id="tab-spam-settings">

                            <form id="mailserver-spam-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                                <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                <? $elements = array(
                                    'spam_scores' => array('type' => 'text', 'display_name' => __('Spam Scores'), "tooltip" => __("Set the score required before a mail is considered spam. n.nn can be an integer or a real number. 5.0 is the default setting, and is quite aggressive; it would be suitable for a single-user setup, but if you are an ISP installing SpamAssassin, you should probably set the default to be more conservative, like 8.0 or 10.0. <br /><br /> It is not recommended to automatically delete or discard messages marked as spam, as your users will complain, but if you choose to do so, only delete messages with an exceptionally high score such as 15.0 or higher. This option was previously known as required_hits and that name is still accepted, but is deprecated.")),
                                    'pyzor_support' => array('type' => 'checkbox', 'display_name' => __('Pyzor Support'), "tooltip" => __("Whether to use Pyzor, if it is available.")),
                                    'razor_support' => array('type' => 'checkbox', 'display_name' => __('Razor Support'), "tooltip" => __("Whether to use Razor version 2, if it is available.")),
                                    'skip_rbl' => array('type' => 'checkbox', 'display_name' => __('Skip RBL Checking'), "tooltip" => __("By default, SpamAssassin will run RBL checks. If your ISP already does this for you, select this option.")),
                                    'bayes_support' => array('type' => 'checkbox', 'display_name' => __('Bayes Support'), "tooltip" => __("Whether to use the naive-Bayesian-style classifier built into SpamAssassin. This is a master on/off switch for all Bayes-related operations.")),
                                    'spam_report' => array('type' => 'checkbox', 'display_name' => __('Enable Spam Report'), "tooltip" => __("if this option is checked, if an incoming message is tagged as spam, instead of modifying the original message, SpamAssassin will create a new report message and attach the original message as a message/rfc822 MIME part (ensuring the original message is completely preserved, not easily opened, and easier to recover). <br /><br />If this option is unchecked, incoming spam is only modified by adding some X-Spam- headers and no changes will be made to the body. In addition, a header named X-Spam-Report will be added to spam. You can use the remove_header option to remove that header after setting report to unchecked.")),

                                    'use_bayes_rules' => array('type' => 'checkbox', 'display_name' => __('Use Bayes Rules') , "tooltip" => __("Whether to use rules using the naive-Bayesian-style classifier built into SpamAssassin. This allows you to disable the rules while leaving auto and manual learning enabled.")),
                                    'bayes_auto_learn' => array('type' => 'checkbox', 'display_name' => __('Bayes Auto Learn'), "tooltip" => __("Whether SpamAssassin should automatically feed high-scoring mails (or low-scoring mails, for non-spam) into its learning systems. The only learning system supported currently is a naive-Bayesian-style classifier.")),
                                    'bayes_auto_learn_on_error' => array('type' => 'checkbox', 'display_name' => __('Bayes Auto Learn on error'), "tooltip" => __("With bayes_auto_learn_on_error off, autolearning will be performed even if bayes classifier already agrees with the new classification (i.e. yielded BAYES_00 for what we are now trying to teach it as ham, or yielded BAYES_99 for spam). This is a traditional setting, the default was chosen to retain backwards compatibility. <br /><br />With bayes_auto_learn_on_error turned on, autolearning will be performed only when a bayes classifier had a different opinion from what the autolearner is now trying to teach it (i.e. it made an error in judgement). This strategy may or may not produce better future classifications, but usually works very well, while also preventing unnecessary overlearning and slows down database growth.")),
                                    'bayes_auto_learn_threshold_nonspam' => array('type' => 'text', 'display_name' => __('Bayes Auto Learn Threshold Non-Spam'), "tooltip" => __("The score threshold below which a mail has to score, to be fed into SpamAssassin is learning systems automatically as a non-spam message.  (default: 0.1)")),
                                    'bayes_auto_learn_threshold_spam' => array('type' => 'text', 'display_name' => __('Bayes Auto Learn Threshold Spam'), "tooltip" => __("The score threshold above which a mail has to score, to be fed into SpamAssassin is learning systems automatically as a spam message.  (default: 12.0)")),
                                    'rewrite_header' => array('type' => 'text', 'display_name' => __('Rewrite Header')),
                                );
                                foreach($elements as $key => $value) {
                                    ?>
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <label for="<?= $key;?>"> <?= $value['display_name'];?> <?= (isset($value["tooltip"]) ? " <a class='text-danger text_underline' data-toggle='popover' data-placement='right' data-trigger='focus' tabindex='0' data-html='true' data-content='$value[tooltip]' title='' data-original-title=''>?</a>" : ""); ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <? if($value['type'] == "checkbox") {?>
                                                <?= $form->render($key,array('class' => 'checkbox-blue')); ?>
                                            <? } else { ?>
                                                <?= $form->render($key,array('class' => 'form-control form-control-sm input-sm')); ?>
                                            <? } ?>
                                        </div>
                                    </div>
                                <? } ?>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <div class="float-right">
                                            <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                            <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tab-blacklists-settings"></div>
                        <div class="tab-pane" id="tab-whitelists-settings"></div>
                        <div class="tab-pane" id="tab-smtp-settings"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>