<? $server_link = (isset($server_id) ? "/server/$server_id" : ""); ?>
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
                    <ul class="nav nav-pills nav-fill" id="ftp-settings-tabs">
                        <li class="nav-item">
                            <a href="<?= $this->uri->url_encrypt("ftp/settings$server_link");?>"><?= __('General Settings'); ?></a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?= $this->uri->url_encrypt("ftp/sessions$server_link");?>"><?= __('Active Sessions'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-ftp-general-settings">
                        </div>
                        <div class="tab-pane active" id="tab-active-sessions">
                            <div class="col-md-12 margin-bottom-10">
                                <div class="table-button-lists">
                                    <a class="btn btn-sm btn-danger remove table-action disabled" data-type="multiple" data-action="delete" data-target="aliases" data-url="<?= $this->uri->url_encrypt("ftp/multiple$server_link");?>"><i class="fa fa-plus-circle"></i> <?= __('Remove Selected');?></a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt("ftp/sessions$server_link");?>" data-select="true" data-buttons="true" data-actions="true">
                                    <thead>
                                    <tr>
                                        <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                                        <th width="35%"><?= __('FTP User')?></th>
                                        <th width="20%"><?= __('Logged Time')?></th>
                                        <th width="10%"><?= __('Idle Time')?></th>
                                        <th width="20%" data-orderable="false"><?= __('Actions')?></th>
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

<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
