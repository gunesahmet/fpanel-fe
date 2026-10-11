<?
$multi_url = $this->uri->url_encrypt("vdir/multiple");
$list_url = $this->uri->url_encrypt("vdir");
$create_url = $this->uri->url_encrypt("vdir/create");
$general_url = $this->uri->url_encrypt("vdir/general");
if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt("vdir/multiple/domain/$domain_id");
    $list_url = $this->uri->url_encrypt("vdir/domain/$domain_id");
    $create_url = $this->uri->url_encrypt("vdir/create/domain/$domain_id");
    $general_url = $this->uri->url_encrypt("vdir/general/domain/".$domain_id);
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
            <div class="col-md-12 margin-bottom-10">
                <div class="float-left">
                    <a class="btn btn-primary btn-sm" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New');?></a>
                    <a class="btn btn-info btn-sm" data-id="nested" href="<?= $general_url;?>"><i class="fa fa-cogs"></i> <?= __('General Settings');?></a>
                </div>
                <div class="float-right table-button-lists">
                    <a class="btn btn-sm btn-danger remove table-action disabled" data-type="multiple" data-action="delete" data-target="vdirs" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                        <th width="20%"><?= __('Virtual Directory'); ?></th>
                        <th width="20%"><?= __('Physical Path'); ?></th>
                        <th width="30%"><?= __('Type'); ?></th>
                        <th width="10%" data-orderable="false"><?= __('Actions'); ?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>