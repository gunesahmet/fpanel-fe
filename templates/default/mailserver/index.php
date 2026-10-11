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
                        <li class="active"><a data-url="<?= $generalsettings_link;?>" href="#tab-mail-general-settings"><?= __('General Settings'); ?></a></li>
                        <li><a data-url="<?= $spamsettings_link;?>" href="#tab-spam-settings"><?= __('SpamAssassin Settings'); ?></a></li>
                        <li><a data-url="<?= $blacklist_link;?>" href="#tab-blacklists-settings"><?= __('Black Lists'); ?></a></li>
                        <li><a data-url="<?= $whitelist_link;?>" href="#tab-whitelists-settings"><?= __('White Lists'); ?></a></li>
                        <li><a data-url="<?= $smtp_link;?>" href="#tab-smtp-settings"><?= __('SMTP Queue'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab-mail-general-settings"></div>
                        <div class="tab-pane fade" id="tab-spam-settings"></div>
                        <div class="tab-pane fade" id="tab-blacklists-settings"></div>
                        <div class="tab-pane fade" id="tab-whitelists-settings"></div>
                        <div class="tab-pane fade" id="tab-smtp-settings"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>