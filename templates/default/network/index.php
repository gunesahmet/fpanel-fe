<? $server_link = (isset($server_id) ? "/server/$server_id" : '');
$create_url = $this->uri->url_encrypt("network/create$server_link");
$reread_url = $this->uri->url_encrypt("network/reread$server_link");
$list_url = $this->uri->url_encrypt("network$server_link");
$hostname_url = $this->uri->url_encrypt("network/hostname$server_link");

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
                    <a class="btn btn-sm btn-primary" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Add New IP Address');?></a>
                    <a class="btn btn-sm btn-info" data-id="nested" href="<?= $hostname_url;?>"><i class="fa fa-edit"></i> <?= __('Hostname');?></a>
                </div>
                <div class="float-right">
                    <a class="btn btn-sm btn-danger" data-id="confirm" data-url="<?= $reread_url;?>"><i class="fa fa-recycle"></i> <?= __('Reread All Interfaces');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="15%"><?= __('IP Address')?></th>
                        <th width="10%"><?= __('Network Interface')?></th>
                        <th width="10%"><?= __('Used Accounts')?></th>
                        <th width="10%"><?= __('Used Domains')?></th>
                        <th width="10%"><?= __('Used Type')?></th>
                        <th width="10%"><?= __('Default')?></th>
                        <th width="15%" data-orderable="false"><?= __('Actions')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>