<div class="modal inmodal fade modal-danger" id="file_edit_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="true" data-backdrop="static">
    <div class="modal-dialog modal-xlg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= __('Edit File');?></h4>
            </div>
            <div class="modal-body" style="max-height: 50%">
                <div class="file_edit">
                    <textarea id="file_editor_textarea"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm" id="close_file_edit"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_save_file"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>