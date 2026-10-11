<div class="modal inmodal fade" id="file_folder_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5><?= __("Create File");?></h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __('Close');?></span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <label class="col-md-4 control-label" for="new_name"><?= __("Name");?></label>
                    <div class="col-md-8">
                        <input id="new_name" name="new_name" type="text" value="" class="form-control form-control-sm input-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm" id="close_file_edit"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_edit_file_folder"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>