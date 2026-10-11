<div class="row" id="support-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <? if($support_status == "false") { ?>
                <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt('support/create');?>" type="button"><i class="fa fa-check-circle"></i> <? echo __('Request New Support'); ?></a>
            <? } else { ?>
                <a class="btn btn-sm btn-primary disabled" data-id="nested" href="#" type="button"><i class="fa fa-check-circle"></i> <? echo __('Request New Support'); ?></a>
            <? } ?>

            <? if($support_user == "true") { ?>
                <a class="btn btn-sm btn-primary disabled" data-id="nested" href="<? echo $this->uri->url_encrypt('support/supportuser');?>" type="button"><i class="fa fa-user"></i> <? echo __('Support User Area'); ?></a>
            <? } ?>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="supports-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('support');?>">
                <thead>
                <tr>
                    <th><? echo __('Title');?></th>
                    <th><? echo __('Status');?></th>
                    <th><? echo __('Created Date');?></th>
                    <th><? echo __('Closed/Updated Date');?></th>
                    <th><? echo __('Support User Messages');?></th>
                    <th><? echo __('Connection Status');?></th>
                    <th><? echo __('Disable Logins');?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'support-list-panel'
    });
    core.datatable({
        target : '#supports-table',
        select_row: false,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>
