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
                        <li class="nav-item active">
                            <a href="<?= $this->uri->url_encrypt("webserver/modules$server_link");?>"><?= __('Modules'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("webserver/mimetypes$server_link");?>"><?= __('Mime Types'); ?></a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane" id="tab-web-general-settings">

                        </div>
                        <div class="tab-pane" id="tab-webserver-settings">
                        </div>
                        <div class="tab-pane in active" id="tab-module-settings">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $post_url;?>" data-select="false" data-server="false" data-buttons="true" data-actions="false">
                                        <thead>
                                        <tr>
                                            <? if($system_os_type == LINUX) { ?>
                                                <th><?= __('Module Name')?></th>
                                                <th><?= __('Status')?></th>
                                                <th data-orderable="false"><?= __('Action')?></th>
                                            <? } else { ?>
                                            <th><?= __('Module Name')?></th>
                                            <th><?= __('Image')?></th>
                                            <th><?= __('Precondition')?></th>
                                            <? } ?>

                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
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