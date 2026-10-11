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
                        <li class="nav-item active">
                            <a href="<?= $generalsettings_link;?>"><?= __('General Settings'); ?></a>
                        </li>
                        <li class="nav-item">
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
                        <div class="tab-pane in active" id="tab-mail-general-settings">
                                <? if($installed_mail == MAILENABLE) {
                                    $this->partial('mailserver/mailenable');
                                }
                                else if($installed_mail == HMAIL) {
                                    $this->partial('mailserver/hmail');
                                }
                                else if($installed_mail == POSTFIX) {
                                    $this->partial('mailserver/postfix');
                                }
                                ?>
                        </div>
                        <div class="tab-pane" id="tab-spam-settings"></div>
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

