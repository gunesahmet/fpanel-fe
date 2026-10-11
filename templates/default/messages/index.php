<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row" id="system-messages-list-panel" data-width="70%" data-height="50%">
            <div class="col-md-12 margin-bottom-10">
                <div class="float-left">
                    <a class="btn btn-sm btn-primary" data-id="nested" href="<?= $this->uri->url_encrypt('messages/create');?>" type="button"><i class="fa fa-plus-circle"></i> <?= __('Add New'); ?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt('messages');?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="15%"><?= __('Title')?></th>
                        <th width="10%"><?= __('Description')?></th>
                        <th width="10%"><?= __('Created Date')?></th>
                        <th width="15%"><?= __('Expire Date')?></th>
                        <th width="15%"><?= __('Type')?></th>
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