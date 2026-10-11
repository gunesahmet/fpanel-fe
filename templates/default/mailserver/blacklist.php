<? $multi_url= (isset($server_id) == true ? $this->uri->url_encrypt('mailserver/multiple/black/server/'.$server_id) : $this->uri->url_encrypt('mailserver/multiple/black')); ?>
<? $list_url = (isset($server_id) == true ? $this->uri->url_encrypt('mailserver/blacklist/server/'.$server_id) : $this->uri->url_encrypt('mailserver/blacklist')); ?>
<? $create_url = (isset($server_id) == true ? $this->uri->url_encrypt('mailserver/blacklistcreate/server/'.$server_id) : $this->uri->url_encrypt('mailserver/blacklistcreate')); ?>
<?

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
                    <ul class="nav nav-pills nav-fill" id="mailserver-settings-tabs">
                        <li class="nav-item">
                            <a href="<?= $generalsettings_link;?>"><?= __('General Settings'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $spamsettings_link;?>"><?= __('SpamAssassin Settings'); ?></a>
                        </li>
                        <li class="nav-item active">
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
                        <div class="tab-pane" id="tab-mail-general-settings"></div>
                        <div class="tab-pane" id="tab-spam-settings"></div>
                        <div class="tab-pane in active" id="tab-blacklists-settings">
                            <div class="row">
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="float-left">
                                        <a class="btn btn-sm btn-primary" data-id="nested" data-toggle="modal" data-target="#create_blacklist_modal"><i class="fa fa-plus-circle"></i> <?= __('Create Black List');?></a>
                                    </div>
                                    <div class="float-right table-button-lists">
                                        <a class="btn btn-sm btn-danger remove table-action disabled" data-type="multiple" data-action="delete" data-target="lists" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                                        <thead>
                                        <tr>
                                            <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                                            <th width="75%"><?= __('Syntax'); ?></th>
                                            <th width="20%" data-orderable="false"><?= __('Action'); ?></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-whitelists-settings"></div>
                        <div class="tab-pane" id="tab-smtp-settings"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->partial("mailserver/create_blacklist_modal");?>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
