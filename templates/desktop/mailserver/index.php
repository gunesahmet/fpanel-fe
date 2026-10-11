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
<div class="row" id="mailserver-settings-panel" data-width="60%">
    <div class="tabbable-line">
        <ul class="nav nav-tabs nav-justified" id="mailserver-settings-tabs">
            <li class="active"><a data-url="<? echo $generalsettings_link;?>" href="#tab-mail-general-settings"><? echo __('General Settings'); ?></a></li>
            <li><a data-url="<? echo $spamsettings_link;?>" href="#tab-spam-settings"><? echo __('Spam Settings'); ?></a></li>
            <li><a data-url="<? echo $blacklist_link;?>" href="#tab-blacklists-settings"><? echo __('Black Lists'); ?></a></li>
            <li><a data-url="<? echo $whitelist_link;?>" href="#tab-whitelists-settings"><? echo __('White Lists'); ?></a></li>
            <li><a data-url="<? echo $smtp_link;?>" href="#tab-smtp-settings"><? echo __('SMTP Queue'); ?></a></li>
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
<script>
    core.setWidth({
        target: 'mailserver-settings-panel'
    });
    core.ajaxTabs("#mailserver-settings-tabs");
</script>
