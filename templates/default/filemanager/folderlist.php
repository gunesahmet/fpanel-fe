<div class="modal inmodal fade" id="folder_lists_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5></h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('Close');?></span></button>
            </div>
            <div class="modal-body">
                <div id="move_folders"></div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm" id="close_file_edit"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_move"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>