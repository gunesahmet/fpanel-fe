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
                <a class="btn btn-sm btn-default" data-id="nested" href="<?= $this->uri->url_encrypt('telegram/settings');?>" type="button"><i class="fa fa-cogs"></i> <?= __('Telegram Settings'); ?></a>
                <a class="btn btn-sm btn-info float-right" data-target="#activation_modal" data-toggle="modal"><i class="fa fa-plus-circle"></i> <?= __('Activation'); ?></a>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt('telegram');?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="20%"><?= __('Username');?></th>
                        <th width="30%"><?= __('Fullname');?></th>
                        <th width="20%"><?= __('Status');?></th>
                        <th width="20%"><?= __('Created Date');?></th>
                        <th width="10%" data-orderable="false"><?= __('Actions');?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="modal inmodal fade" id="activation_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><?= __("Enter activation code"); ?></h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('Close');?></span></button>
            </div>
            <div class="modal-body">
                <form id="activation-code-form" method="post" class="form-horizontal" action="<?= $this->uri->url_encrypt('telegram/activation');?>">
                    <div class="form-group row">
                        <label for="activation_code" class="col-sm-4 col-form-label"><?= __('Activation Code');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="activation_code" id="activation_code" class="form-control form-control-sm input-sm" autofocus />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="activate_code"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>