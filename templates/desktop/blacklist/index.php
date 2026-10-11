<?
$ftpusers_link = $this->uri->url_encrypt('blacklist/ftpusers');
$domains_link = $this->uri->url_encrypt('blacklist/domains');
$dbusers_link = $this->uri->url_encrypt('blacklist/dbusers');
$emailusers_link = $this->uri->url_encrypt('blacklist/emailusers');
?>
<div class="row" id="blacklist-settings-panel" data-width="60%">
    <div class="tabbable-line">
        <ul class="nav nav-tabs nav-justified" id="blacklist-settings-tabs">
            <li class="active"><a data-url="<? echo $ftpusers_link;?>" href="#tab-blacklist-ftp-users"><? echo __('FTP Users'); ?></a></li>
            <li><a data-url="<? echo $domains_link;?>" href="#tab-blacklist-domain-names"><? echo __('Domain Names'); ?></a></li>
            <li><a data-url="<? echo $dbusers_link;?>" href="#tab-blacklist-database-users"><? echo __('Database Users'); ?></a></li>
            <li><a data-url="<? echo $emailusers_link;?>" href="#tab-blacklist-email-users"><? echo __('Email Users'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-blacklist-ftp-users"></div>
            <div class="tab-pane fade" id="tab-blacklist-domain-names"></div>
            <div class="tab-pane fade" id="tab-blacklist-database-users"></div>
            <div class="tab-pane fade" id="tab-blacklist-email-users"></div>
        </div>
    </div>

</div>
<script>
    core.setWidth({
        target: 'blacklist-settings-panel'
    });
    core.ajaxTabs("#blacklist-settings-tabs");
</script>

