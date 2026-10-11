<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <form id="file-backup-upload" method="POST" enctype="multipart/form-data" action="/<?= $backup_url;?>">
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="fpb-drop" ondragenter="$(this).addClass('fpb-dragover');" ondragend="$(this).removeClass('fpb-dragover');" ondrop="$(this).removeClass('fpb-dragover');">
                                <?= __("Drop files here to upload or %select_files%", ["select_files" => "<a class=\"fpb-browse-file\"><u>" .  __("select files") . "</u></a>"]);?>
                            </div>
                            <div class="fpb-progress">
                                <table>
                                    <tr>
                                        <td width="100%">
                                            <div class="progress-container"><div class="progress-bar"></div></div>
                                        </td>
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
                            <ul class="uploading-backup-file-list list-unstyled"></ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
