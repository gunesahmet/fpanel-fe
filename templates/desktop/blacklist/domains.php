<div class="row">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt('blacklist/adddomain');?>" type="button"><i class="fa fa-plus-circle"></i> <? echo __('Add Domain'); ?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="blacklist-domains-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('blacklist/domains');?>">
                <thead>
                <tr>
                    <th width="60%"><? echo __('Domain Name');?></th>
                    <th width="40%"><? echo __('Actions');?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.inputInits();
    core.datatable({
        target : '#blacklist-domains-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>
