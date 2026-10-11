<? if(isset($client_id)) {
    $multi_url = $this->uri->url_encrypt('users/multiple/client/'.$client_id);
    $list_url = $this->uri->url_encrypt('users/client/'.$client_id, true);
    $url = $this->uri->url_encrypt("users/create/client/".$client_id);
}
else {
    $multi_url = $this->uri->url_encrypt('users/multiple');
    $list_url = $this->uri->url_encrypt('users');
    $url = $this->uri->url_encrypt("users/create");
}
if($active_class == "disabled") $url = "#";
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
            <div class="col-md-12 col-xs-12 margin-bottom-10">
                <div class="float-left">
                    <a data-id="nested" class="btn btn-primary btn-sm margin-bottom-5 visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block <?= $active_class;?>" href="<?= $url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New User'); ?></a>
                </div>
                <div class="float-right table-button-lists">
                    <a class="btn btn-sm btn-primary unsuspend table-action disabled margin-bottom-5 visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" data-type="multiple" data-action="unsuspend" data-target="clients" data-url="<?= $multi_url;?>"><i class="fa fa-check"></i> <?= __('Unsuspend Selected');?></a>
                    <a class="btn btn-sm btn-warning suspend table-action disabled margin-bottom-5 visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" data-type="multiple" data-action="suspend" data-target="clients" data-url="<?= $multi_url;?>"><i class="fa fa-ban"></i> <?= __('Suspend Selected');?></a>
                    <a class="btn btn-sm btn-danger remove table-action disabled margin-bottom-5 visible-xs-block visible-sm-inline-block visible-md-inline visible-lg-inline-block" data-type="multiple" data-action="delete" data-target="clients" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                        <th width="15%"><?= __('Username')?></th>
                        <th width="10%"><?= __('Parent')?></th>
                        <th width="10%"><?= __('Expire Date')?></th>
                        <th width="10%"><?= __('Domains')?></th>
                        <th width="5%"><?= __('Clients')?></th>
                        <th width="10%"><?= __('User Type')?></th>
                        <th width="10%"><?= __('Status')?></th>
                        <th width="15%" data-orderable="false"><?= __('Actions')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>