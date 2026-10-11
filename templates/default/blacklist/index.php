<?
$ftpusers_link = $this->uri->url_encrypt('blacklist/ftpusers');
$domains_link = $this->uri->url_encrypt('blacklist/domains');
$dbusers_link = $this->uri->url_encrypt('blacklist/dbusers');
$emailusers_link = $this->uri->url_encrypt('blacklist/emailusers');
?>
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
            <div class="tabbable-line">
                <ul class="nav nav-pills nav-fill" id="blacklist-settings-tabs" data-tabs="ajax">
                    <li class="nav-item active"><a data-url="<?= $ftpusers_link;?>" href="#tab-blacklist-ftp-users"><?= __('FTP Users'); ?></a></li>
                    <li class="nav-item"><a data-url="<?= $domains_link;?>" href="#tab-blacklist-domain-names"><?= __('Domain Names'); ?></a></li>
                    <li class="nav-item"><a data-url="<?= $dbusers_link;?>" href="#tab-blacklist-database-users"><?= __('Database Users'); ?></a></li>
                    <li class="nav-item"><a data-url="<?= $emailusers_link;?>" href="#tab-blacklist-email-users"><?= __('Email Users'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab-blacklist-ftp-users"></div>
                    <div class="tab-pane fade" id="tab-blacklist-domain-names"></div>
                    <div class="tab-pane fade" id="tab-blacklist-database-users"></div>
                    <div class="tab-pane fade" id="tab-blacklist-email-users"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
