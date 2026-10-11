<div class="modal inmodal fade" id="create_blacklist_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static" data-url="<?= $create_url;?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?= __('Create Blacklist');?></h4>
                <small class="font-bold"><?= __('Please enter your blacklist rule');?></small>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group row">
                        <label class="control-label col-md-3" for="blacklist_rule"><?= __('Blacklist Rule');?></label>
                        <div class="col-md-9">
                            <input id="blacklist_rule" name="blacklist_rule" class="form-control form-control-sm input-sm" type="text"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>
