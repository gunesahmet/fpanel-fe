<div class="row" id="file-manager-panel" data-width="60%">
    <div class="col-lg-3">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Folders'); ?></h5>
                <div class="ibox-tools">
                    <label class="label label-primary">You can drag and drop me to other box.</label>
                </div>
            </div>
            <div class="ibox-content">
                <div class="file-manager">
                    <button data-id="fl-upload-files" class="btn btn-primary btn-block"><?= __('Upload Files'); ?></button>
                    <div class="hr-line-dashed"></div>
                    <div class="margin-left-10">
                        <div style="overflow: auto; max-height: 500px">
                            <div id="folder_lists">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="ibox" id="file-list-panel">
            <div class="ibox-title">
               <div class="col-md-8 margin-left-5">
                   <div class="row">
                       <div class="input-group input-group-sm">
                           <div class="input-group-prepend">
                               <button class="btn btn-default" type="button" data-id="fl-back"><i class="fas fa-arrow-left" data-toggle="tooltip" title="<?= __("Back")?>"></i></button>
                               <button class="btn btn-default" type="button" data-id="fl-refresh"><i class="fas fa-sync-alt" data-toggle="tooltip" title="<?= __("Refresh")?>"></i></button>
                               <button class="btn btn-default" type="button" data-id="fl-create-file"><i class="fas fa-file-medical" data-toggle="tooltip" title="<?= __("Create File")?>"></i></button>
                               <button class="btn btn-default" type="button" data-id="fl-create-folder"><i class="fas fa-folder-plus" data-toggle="tooltip" title="<?= __("Create Folder")?>"></i></button>
                               <button class="btn btn-default" type="button" data-id="fl-delete-selected"><i class="fas fa-times" data-toggle="tooltip" title="<?= __("Delete Selected")?>"></i></button>
                               <button class="btn btn-default" type="button" data-id="fl-upload"><i class="fas fa-cloud-upload-alt" data-toggle="tooltip" title="<?= __("Upload")?>"></i></button>
                           </div>
                           <input class="form-control form-control-sm" type="text" value="/" id="folder-path" disabled/>
                       </div>
                   </div>
               </div>
                <div class="ibox-tools col-md-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-sm" type="search" id="search-file" placeholder="<?= __("Search in files...");?>"/>
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="button" data-id="fl-show-list"><i class="fas fa-list" data-toggle="tooltip" title="<?= __("Show as list")?>"></i></button>
                                    <button class="btn btn-default" type="button" data-id="fl-show-icons"><i class="fas fa-th" data-toggle="tooltip" title="<?= __("Show as icons")?>"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="file-lists">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var default_folder = '<?= $default_folder;?>';
    var index_url = '<?= $index_url;?>';
    var file_manager_url = '<?= $url_files;?>';
    var rename_url = '<?= $rename_url;?>';
    var move_url = '<?= $move_url;?>';
    var paste_url = '<?= $paste_url;?>';
    var delete_url = '<?= $delete_url;?>';
    var download_url = '<?= $download_url;?>';
    var create_folder_url = '<?= $create_folder_url;?>';
    filemanager.permissions_url = '<?= $permissions_url;?>';
    filemanager.get_permissions_url = '<?= $get_permissions_url;?>';
    var create_file_url = '<?= $create_file_url;?>';
    var edit_file_url = '<?= $edit_file_url;?>';
    var save_file_url = '<?= $save_file_url;?>';
    filemanager.server_type = '<?= $server_type;?>';
    folder_list = <?= json_encode($folder_list);?>;
    file_list = <?= json_encode($file_list);?>;
</script>

<? $this->partial("filemanager/filelist"); ?>
<? $this->partial("filemanager/folderlist"); ?>
<? $this->partial("filemanager/permission"); ?>
<? $this->partial("filemanager/edit"); ?>
<? $this->partial("filemanager/upload"); ?>

<style>
    .cm-s-monokai .CodeMirror-cursor {
        border-left: 1px solid #f8f8f0 !important;
    }

    .CodeMirror-hscrollbar {
        pointer-events: auto !important;
    }

    #win-permissions.table > tbody > tr > td.user-lists {
        vertical-align: top;
    }
</style>


<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;
    var lvar = <?= $script_vars;?>;</script>