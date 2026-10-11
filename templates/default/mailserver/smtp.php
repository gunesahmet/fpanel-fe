<?
if(isset($server_id))  {
    $multi_url = $this->uri->url_encrypt('mailserver/multiplequeue/server/'.$server_id);
    $clear_url = $this->uri->url_encrypt('mailserver/clear/server/'.$server_id);
    $flush_url = $this->uri->url_encrypt('mailserver/flush/server/'.$server_id);
    $list_url = $this->uri->url_encrypt('mailserver/smtp/server/'.$server_id);
}
else  {
    $multi_url = $this->uri->url_encrypt('mailserver/multiplequeue');
    $clear_url = $this->uri->url_encrypt('mailserver/clear');
    $flush_url = $this->uri->url_encrypt('mailserver/flush');
    $list_url = $this->uri->url_encrypt('mailserver/smtp');
}
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
                        <li class="nav-item">
                            <a href="<?= $blacklist_link;?>"><?= __('Black Lists'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $whitelist_link;?>"><?= __('White Lists'); ?></a>
                        </li>
                        <li class="nav-item active">
                            <a href="<?= $smtp_link;?>"><?= __('SMTP Queue'); ?></a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane" id="tab-mail-general-settings"></div>
                        <div class="tab-pane" id="tab-spam-settings"></div>
                        <div class="tab-pane" id="tab-blacklists-settings"></div>
                        <div class="tab-pane" id="tab-whitelists-settings"></div>
                        <div class="tab-pane in active" id="tab-smtp-settings">
                            <div class="row">
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="float-left">
                                        <? if(PANEL_OS_TYPE == LINUX) { ?>
                                            <a class="btn btn-primary btn-sm" data-id="confirm" data-url="<?= $flush_url;?>"><i class="fa fa-sync-alt"></i> <?= __('Flush Queue');?></a>
                                        <? } ?>
                                        <a class="btn btn-danger btn-sm" data-id="confirm" data-url="<?= $clear_url;?>"><i class="fa fa-trash"></i> <?= __('Clear Queue');?></a>
                                    </div>
                                    <div class="float-right table-button-list">
                                        <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="removequeue" data-target="queue" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover dynamic-table" data-server="false" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                                        <thead>
                                        <tr>
                                            <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero"></th>
                                            <th width="10%"><?= __('Type'); ?></th>
                                            <th width="10%"><?= __('Sender'); ?></th>
                                            <th width="20%"><?= __('Subject'); ?></th>
                                            <th width="10%"><?= __('Date'); ?></th>
                                            <th width="10%"><?= __('Age'); ?></th>
                                            <th width="10%"><?= __('Size'); ?></th>
                                            <th width="20%" data-orderable="false"><?= __('Action'); ?></th>
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
