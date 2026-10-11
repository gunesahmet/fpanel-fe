<?
$multi_url = $this->uri->url_encrypt("mail/multiple/account");
$list_url = $this->uri->url_encrypt("mail/accounts");
$create_url = $this->uri->url_encrypt("mail/create");
if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt("mail/multiple/account/domain/$domain_id");
    $list_url = $this->uri->url_encrypt("mail/accounts/domain/$domain_id");
    $create_url = $this->uri->url_encrypt("mail/create/domain/$domain_id");
}
if($active_class == "disabled")
    $create_url = "#";
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
            <div class="col-md-12 margin-bottom-10">
                <div class="float-left">
                    <a class="btn btn-sm btn-primary <?= $active_class;?>" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create Mail Account');?></a>
                </div>
                <div class="float-right table-button-lists">
                    <a class="btn btn-sm btn-danger remove table-action disabled" data-type="multiple" data-action="delete" data-target="mails" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                        <th width="15%"><?= __('Email Name')?></th>
                        <th width="10%"><?= __('Email Quota')?></th>
                        <th width="10%"><?= __('Email Forwarding')?></th>
                        <th width="15%"><?= __('Mail Alias Count')?></th>
                        <th width="15%"><?= __('Status')?></th>
                        <th width="15%" data-orderable="false"><?= __('Actions')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>