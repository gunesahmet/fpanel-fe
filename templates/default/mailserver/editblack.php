<?
$generalsettings_link = $this->uri->url_encrypt('mailserver/generalsettings');
$spamsettings_link = $this->uri->url_encrypt('mailserver/spamsettings');
$blacklist_link = $this->uri->url_encrypt('mailserver/blacklist');
$whitelist_link = $this->uri->url_encrypt('mailserver/whitelist');
$antivirus_link = $this->uri->url_encrypt('mailserver/antivirus');
$smtp_link = $this->uri->url_encrypt('mailserver/smtp');

if(isset($server_id)) {
    $generalsettings_link = $generalsettings_link."/server/$server_id";
    $spamsettings_link = $spamsettings_link."/server/$server_id";
    $blacklist_link = $blacklist_link."/server/$server_id";
    $whitelist_link = $whitelist_link."/server/$server_id";
    $antivirus_link = $antivirus_link."/server/$server_id";
    $smtp_link = $smtp_link."/server/$server_id";
} ?>
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
                    <ul class="nav nav-tabs nav-justified" id="mailserver-settings-tabs">
                        <li><a href="<?= $generalsettings_link;?>"><?= __('General Settings'); ?></a></li>
                        <li><a href="<?= $spamsettings_link;?>"><?= __('SpamAssassin Settings'); ?></a></li>
                        <li class="active"><a href="<?= $blacklist_link;?>"><?= __('Black Lists'); ?></a></li>
                        <li><a href="<?= $whitelist_link;?>"><?= __('White Lists'); ?></a></li>
                        <li><a href="<?= $smtp_link;?>"><?= __('SMTP Queue'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-mail-general-settings"></div>
                        <div class="tab-pane" id="tab-spam-settings"></div>
                        <div class="tab-pane in active" id="tab-blacklists-settings">
                            <div class="row">
                                <div class="col-md-6">
                                    <form id="create-blacklist-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                                        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                        <div class="form-group" >
                                            <div class="col-md-4">
                                                <label for="blacklist_rule"> <?= __('Blacklist Rule');?> </label>
                                            </div>
                                            <div class="col-md-8">
                                                <?= $form->render('blacklist_rule',array('class' => 'form-control form-control-sm input-sm')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="float-right">
                                                    <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                                    <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-whitelists-settings"></div>
                        <div class="tab-pane" id="tab-smtp-settings"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>