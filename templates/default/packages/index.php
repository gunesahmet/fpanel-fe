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
                <a data-id="confirm" class="btn btn-sm btn-primary" data-url="<?= $update_source_url;?>" type="button"><i class="fa fa-sync-alt"></i> <?= __('Update List');?></a>
                <? if($server_os == LINUX) { ?>
                <a class="btn btn-sm btn-primary" href="<?= $server_os_type == WINDOWS ? "#" : $source_lists_url;?>" <?= PANEL_OS_TYPE == WINDOWS ? "disabled" : "";?> type="button"><i class="fa fa-list-alt"></i> <?= __('Source List');?></a>
                <? } ?>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $current_url;?>" data-select="false" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="25%"><?= __('Package Name')?></th>
                        <th width="40%"><?= __('Description')?></th>
                        <th width="15%"><?= __('Version')?></th>
                        <th width="10%"><?= __('Status')?></th>
                        <th width="10%" data-orderable="false"><?= __('Action')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>