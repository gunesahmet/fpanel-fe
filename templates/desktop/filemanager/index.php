<div class="row" id="file-manager-panel" data-width="60%">
    <div class="col-lg-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="file-manager">
                    <button data-target="#upload_files" onclick="getFolderLists('#upload_folder_lists');" data-toggle="modal" class="btn btn-primary btn-block"><? echo __('Upload Files');?></button>
                    <h5><? echo __('Folders');?></h5>
                    <div class="hr-line-dashed"></div>
                    <div class="row margin-left-10">
                        <div style="overflow: auto; max-height: 500px">
                            <div id="folder_lists">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 animated fadeInRight">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="file-lists"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="file_folder_modal" class="modal fade" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>[title]</h3>
    </div>
    <div class="modal-body form-horizontal">
        <div class="form-group">
            <label class="col-md-4 control-label" for="new_name">[label]</label>
            <div class="col-md-8">
                <input id="new_name" name="new_name" type="text" value="" class="form-control input-sm">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
        <button type="button" class="btn btn-primary btn-sm" id="submit_edit_file_folder"><? echo __('Submit');?></button>
    </div>
</div>

<div id="folder_lists_modal" class="modal fade" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3></h3>
    </div>
    <div class="modal-body">
        <div id="move_folders"></div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
        <button type="button" class="btn btn-primary btn-sm" id="submit_move"><? echo __('Submit');?></button>
    </div>
</div>


<div id="permissions_modal" class="modal fade" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3></h3>
    </div>
    <div class="modal-body">
        <? if(PANEL_OS_TYPE == "linux") {
            $this->partial("filemanager/linuxpermission");
        } else {
            $this->partial("filemanager/windowspermission");
        } ?>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
        <button type="button" class="btn btn-primary btn-sm" id="submit_permission"><? echo __('Submit');?></button>
    </div>
</div>

<div id="file_edit_modal" class="modal modal-lg fade" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3></h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-1">
                <div class="form-group">
                    <div class="form-group">
                        <? echo __('Select Language');?>
                        <select id="mode">
                            <option value="css">css</option>
                            <option value="php">php</option>
                            <option value="htmlmixed">html</option>
                            <option value="javascript">javascript</option>
                            <option value="xml">xml</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-11">
                <div class="row">
                    <div class="file_edit">
                        <? $this->partial("filemanager/fileedit"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
        <button type="button" class="btn btn-primary btn-sm" id="submit_save_file"><? echo __('Submit');?></button>
    </div>
</div>

<? echo $this->partial("filemanager/upload");?>


<style>

    .cm-s-monokai .CodeMirror-cursor {
        border-left: 1px solid #f8f8f0 !important;
    }

    .CodeMirror-hscrollbar {
        pointer-events:auto !important;
    }


</style>
<script>
    core.setWidth({
        target: 'file-manager-panel'
    });

    fileUpload.init('<? echo $upload_url;?>');
    var index_url = '<? echo $index_url;?>';
    var file_manager_url = '<? echo $url_files;?>';
    var rename_url = '<? echo $rename_url;?>';
    var move_url = '<? echo $move_url;?>';
    var paste_url = '<? echo $paste_url;?>';
    var delete_url = '<? echo $delete_url;?>';
    var download_url = '<? echo $download_url;?>';
    var create_folder_url = '<? echo $create_folder_url;?>';
    var permissions_url = '<? echo $permissions_url;?>';
    var get_permissions_url = '<? echo $get_permissions_url;?>';
    var create_file_url = '<? echo $create_file_url;?>';
    var edit_file_url = '<? echo $edit_file_url;?>';
    var save_file_url = '<? echo $save_file_url;?>';
    var server_type = '<? echo $system_domain_type;?>';


    filemanager.folderlist('#folder_lists');
    filemanager.filelist('');
    filemanager.init();

</script>