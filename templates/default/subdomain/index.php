<? if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt('subdomain/multiple/domain/'.$domain_id);
    $list_url = $this->uri->url_encrypt('subdomain/domain/'.$domain_id);
    $create_url = $this->uri->url_encrypt("subdomain/create/domain/".$domain_id);
}
else {
    $multi_url = $this->uri->url_encrypt('subdomain/multiple');
    $list_url = $this->uri->url_encrypt('subdomain');
    $create_url = $this->uri->url_encrypt("subdomain/create");
}
$create_url = ($active_class == "disabled" ? '#' : $create_url);
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
        <div class="row" id="subdomains-list-panel" data-width="50%">
            <div class="col-md-12 margin-bottom-10">
                <div class="float-left">
                    <a class="btn btn-primary btn-sm  <?= $active_class;?>" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create Subdomain');?></a>
                </div>
                <div id="multiple_process" class="float-right table-button-lists">
                    <a class="btn btn-sm btn-danger remove table-action disabled" data-type="multiple" data-action="delete" data-target="subdomains" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="5%" data-orderable="false"><input type="checkbox" class="checkbox-aero" id="select_all" /></th>
                        <th width="15%"><?= __('Subdomain Name')?></th>
                        <th width="10%"><?= __('Created Date')?></th>
                        <th width="10%"><?= __('Ftp User Type')?></th>
                        <th width="10%"><?= __('FTP Username')?></th>
                        <th width="5%"><?= __('Status')?></th>
                        <th width="15%" data-orderable="false"><?= __('Actions')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>