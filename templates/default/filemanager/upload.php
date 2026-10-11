<div class="modal inmodal fade" id="upload_files" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <i class="fa fa-upload modal-icon"></i>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form id="fileupload" method="post" enctype="multipart/form-data" action="/<?= $upload_url;?>" data-url="/<?= $extract_url;?>" data-cancel="/<?= $cancel_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="row justify-content-center">
                        <div class="col-6">
                            <input type="hidden" name="upload_folder" id="upload_folder" value="">
                            <div class="fp-drop" ondragenter="jQuery(this).addClass('fp-dragover');" ondragend="jQuery(this).removeClass('fp-dragover');" ondrop="jQuery(this).removeClass('fp-dragover');">
                                <?= __("Drop files here to upload or %select_files%", ["select_files" => "<a class=\"fp-browse-file\"><u>" .  __("select files") . "</u></a>"]);?>
                            </div>
                            <div class="fp-progress">
                                <table>
                                    <tr>
                                        <td width="100%"><div class="progress-container"><div class="progress-bar"></div></div></td>
                                        <td class="progress-text" nowrap="nowrap"></td>
                                        <td class="progress-pause" nowrap="nowrap">
                                            <a href="#" class="progress-start-link btn btn-sm btn-primary" style="display:inline"><?= __('Start');?></a>
                                            <a href="#" class="progress-resume-link btn btn-sm btn-primary" style="display:none"><?= __('Resume');?></a>
                                            <a href="#" class="progress-pause-link btn btn-sm btn-warning" style="display:none"><?= __('Pause');?></a>
                                            <a href="#" class="progress-cancel-link btn btn-sm btn-danger" style="display:inline"><?= __('Cancel');?></a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <ul class="uploading-file-list list-unstyled"></ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm" id="file-upload-close"><?= __('Close');?></button>
            </div>
        </div>
    </div>
</div>
