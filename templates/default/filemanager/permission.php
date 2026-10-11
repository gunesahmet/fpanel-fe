<div class="modal inmodal fade" id="permissions_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-11"><h5></h5></div>
                    <div class="col-md-1"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('Close');?></span></button></div>
                </div>
            </div>
            <div class="modal-body">
                <? if($server_type == LINUX) {
                    $this->partial("filemanager/linuxpermission");
                } else {
                    $this->partial("filemanager/windowspermission");
                } ?>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm" id="close_file_edit"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_permission"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>