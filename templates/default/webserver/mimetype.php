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
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("webserver/web$server_link");?>"><?= __('Web Server Settings'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("webserver/modules$server_link");?>"><?= __('Modules'); ?></a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?= $this->uri->url_encrypt("webserver/mimetypes$server_link");?>"><?= __('Mime Types'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-web-general-settings">
                        </div>
                        <div class="tab-pane" id="tab-webserver-settings">
                        </div>
                        <div class="tab-pane" id="tab-module-settings">
                        </div>
                        <div class="tab-pane in active" id="tab-mimetype-settings">
                            <div class="row">
                                <div class="col-md-12 margin-bottom-10">
                                    <a class="btn btn-primary btn-sm" data-id="nested" href="<?= $this->uri->url_encrypt("webserver/addmime$server_link");?>"><i class="fa fa-plus-circle"></i> <?= __('Add Mime Type');?></a>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover mimetypes-list" data-url="<?= $this->uri->url_encrypt('webserver/mimetypes'.$server_link);?>" data-select="false" data-server="false" data-buttons="true" data-actions="true">
                                        <thead>
                                        <tr>
                                            <th><?= __('Extension')?></th>
                                            <th><?= __('Mime Type')?></th>
                                            <th><?= __('Action')?></th>
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