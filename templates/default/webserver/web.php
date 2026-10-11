<? $server_link = (isset($server_id) == true ? "/server/$server_id" : ''); ?>
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
                    <ul class="nav nav-pills nav-fill" id="webserver-settings-tabs">
                        <li class="nav-item active">
                            <a href="<?= $this->uri->url_encrypt("webserver/web$server_link");?>"><?= __('Web Server Settings'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("webserver/modules$server_link");?>"><?= __('Modules'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("webserver/mimetypes$server_link");?>"><?= __('Mime Types'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-web-general-settings">
                        </div>
                        <div class="tab-pane in active" id="tab-webserver-settings">
                            <div class="col-md-12">
                                <? $this->partial('webserver/'.$system_os_type.'/web'); ?>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-module-settings">
                        </div>
                        <div class="tab-pane" id="tab-mimetype-settings">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>