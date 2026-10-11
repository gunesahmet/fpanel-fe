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
            <div class="col-md-12">
                <div class="tabbable-line">
                    <ul class="nav nav-pills nav-fill" id="blacklist-settings-tabs" data-tabs="ajax">
                        <li class="nav-item"><a href="<?= $ftpusers_link;?>" href="#tab-blacklist-ftp-users"><?= __('FTP Users'); ?></a></li>
                        <li class="nav-item"><a href="<?= $domains_link;?>" href="#tab-blacklist-domain-names"><?= __('Domain Names'); ?></a></li>
                        <li class="nav-item"><a href="<?= $dbusers_link;?>" href="#tab-blacklist-database-users"><?= __('Database Users'); ?></a></li>
                        <li class="nav-item active"><a href="<?= $emailusers_link;?>" href="#tab-blacklist-email-users"><?= __('Email Users'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-blacklist-ftp-users"></div>
                        <div class="tab-pane" id="tab-blacklist-domain-names"></div>
                        <div class="tab-pane" id="tab-blacklist-database-users"></div>
                        <div class="tab-pane in active" id="tab-blacklist-email-users">
                            <div class="row">
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="float-left">
                                        <a class="btn btn-sm btn-primary" data-id="nested" href="<?= $this->uri->url_encrypt('blacklist/addemailuser');?>" type="button"><i class="fa fa-plus-circle"></i> <?= __('Add Email User'); ?></a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt('blacklist/emailusers');?>" data-select="false" data-buttons="true" data-actions="false">
                                        <thead>
                                        <tr>
                                            <th width="60%"><?= __('Email Users');?></th>
                                            <th width="40%" data-orderable="false"><?= __('Actions');?></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
