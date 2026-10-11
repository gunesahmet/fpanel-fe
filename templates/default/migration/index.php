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
                <? if(isset($server_id)) { ?>
                    <a class="btn btn-sm btn-primary" href="<?= $this->uri->url_encrypt('migration/create/server/'.$server_id);?>" type="button"><i class="fa fa-plus-circle"></i> <?= __('Add Server for Migration'); ?></a>
                <? } else { ?>
                    <a class="btn btn-sm btn-primary" href="<?= $this->uri->url_encrypt('migration/create');?>" type="button"><i class="fa fa-plus-circle"></i> <?= __('Add Server for Migration'); ?></a>
                <? } ?>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt('migration');?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="15%"><?= __('Type'); ?></th>
                        <th width="10%"><?= __('IP Address'); ?></th>
                        <th width="10%"><?= __('Description'); ?></th>
                        <th width="10%"><?= __('Created Date'); ?></th>
                        <th width="10%"><?= __('Last Migration Date'); ?></th>
                        <th width="10%"><?= __('Status'); ?></th>
                        <th width="20%" data-orderable="false"><?= __('Actions'); ?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>