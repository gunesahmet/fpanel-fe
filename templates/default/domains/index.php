<style>
    .progress-bar {
        position: relative;
        background-color: #1ab394;
    }
</style>
<? if(isset($client_id)) {
    $multi_url = $this->uri->url_encrypt('domains/multiple/client/'.$client_id);
    $list_url = $this->uri->url_encrypt('domains/client/'.$client_id);
    $create_url = $this->uri->url_encrypt('domains/create/client/'.$client_id);
}
else {
    $multi_url = $this->uri->url_encrypt('domains/multiple');
    $list_url = $this->uri->url_encrypt('domains');
    $create_url = $this->uri->url_encrypt('domains/create');
} ?>
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
                    <a class="btn btn-sm btn-primary d-block d-md-inline-block <?= $active_class;?>" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New');?></a>
                </div>
                <div class="table-button-lists float-right">
                    <? if ($user_type != CLIENT_USER) { ?>
                        <a class="btn btn-sm btn-default disabled change-parent d-block d-md-inline-block table-action" data-url="<?= $this->uri->url_encrypt("domains/getclients/$user_id/$user_type");?>" data-toggle="modal" id="change_parent" data-target="#parent_modal"><i class="fa fa-exchange"></i> <?= __('Change Parent');?></a>
                    <? } ?>
                    <a class="btn btn-sm btn-success suspend disabled d-block d-md-inline-block table-action" data-type="multiple" data-action="unsuspend" data-target="domains" data-url="<?= $multi_url;?>"><i class="fa fa-check"></i> <?= __('Unsuspend Selected');?></a>
                    <a class="btn btn-sm btn-warning unsuspend disabled d-block d-md-inline-block table-action" data-type="multiple" data-action="suspend" data-target="domains" data-url="<?= $multi_url;?>"><i class="fa fa-ban"></i> <?= __('Suspend Selected');?></a>
                    <a class="btn btn-sm btn-danger remove disabled d-block d-md-inline-block table-action" data-type="multiple" data-action="delete" data-target="domains" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                        <thead>
                        <tr>
                            <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                            <th width="10%"><?= __('Domain Name')?></th>
                            <th width="7%"><?= __('Owner')?></th>
                            <th width="8%"><?= __('Created Date')?></th>
                            <th width="10%"><?= __('Expire Date')?></th>
                            <th width="10%"><?= __('IP Address')?></th>
                            <th width="5%"><?= __('Status')?></th>
                            <th width="10%"><?= __('Disk')?></th>
                            <th width="10%"><?= __('Traffic')?></th>
                            <th width="25%" data-orderable="false"><?= __('Actions')?></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal fade" id="parent_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static" data-url="<?= $multi_url;?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?= __('Change Domain Parent');?></h4>
                <small class="font-bold"><?= __('Please select a client as parent assing to selected domains');?></small>
            </div>
            <div class="modal-body form-horizontal">
                <div class="form-group row">
                    <label class="control-label col-md-4" for="parent"><?= __('Select Parent');?></label>
                    <div class="col-md-8"><select id="parent" name="parent" class="form-control form-control-sm input-sm select-picker"></select></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm" id="close_file_edit"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>