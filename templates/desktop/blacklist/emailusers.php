<div class="row">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt('blacklist/addemailuser');?>" type="button"><i class="fa fa-plus-circle"></i> <? echo __('Add Email User'); ?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="blacklist-emailusers-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('blacklist/emailusers');?>">
                <thead>
                <tr>
                    <th width="60%"><? echo __('Email Users');?></th>
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
        target : '#blacklist-emailusers-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>
