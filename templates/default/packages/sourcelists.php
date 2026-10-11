<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-sm btn-xs-block btn-primary d-block d-md-inline-block margin-bottom-5" href="<?= $packages_url;?>" type="button"><i class="fa fa-list-alt"></i> <?= __('Package List');?></a>
                        <a class="btn btn-sm btn-xs-block btn-warning d-block d-md-inline-block margin-bottom-5" data-id="confirm" data-url="<?= $init_source_list_url;?>" type="button"><i class="fa fa-sync-alt"></i>&nbsp; <?= __('Init Source List');?></a>
                        <a class="btn btn-sm btn-sm-block btn-danger d-block d-md-inline-block margin-bottom-5" id="create-source-list" data-url="<?= $create_url;?>" type="button"><i class="fa fa-plus"></i>&nbsp; <?= __('Create Source List');?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="source_lists">
<? foreach ($source_lists_contents as $source_lists_content) { ?>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $source_lists_content["source_list"] ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <? $item_id = $this->crypto->encryptSession($source_lists_content["source_list"]);?>
                        <a class="save-source-list" data-url="<?= $update_url?>" data-name="<?= $item_id;?>">
                            <i class="fa fa-save"></i>
                        </a>
                        <a class="remove-source-list" data-url="<?= $remove_url;?>" data-name="<?= $item_id;?>">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <textarea class="pretty-code" id="<?= $item_id;?>"><?= trim($source_lists_content["content"]); ?></textarea>
                </div>
            </div>
        </div>
<? } ?>
</div>

<div class="modal inmodal fade" id="new_source_list" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= __("Close");?></span></button>
                <h4 class="modal-title"><?= __("Create New Source List");?></h4>
            </div>
            <div class="modal-body">
                <form id="add-server-form" method="post" class="form-horizontal">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group ">
                        <div class="col-md-4">
                            <label for="new_source_name"> <strong><?= __('Source List Path');?></strong></label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" data-autoclose="true">
                                <input id="new_source_name" name="new_source_name" type="text" class="form-control form-control-sm" placeholder="new-source-list-name"/>
                                <? if ((strpos(strtolower($system_items["system_data"]["os"]), "debian") !== false ) || (strpos(strtolower($system_items["system_data"]["os"]), "ubuntu") !== false)) { ?>
                                    <span class="input-group-addon">.list</span>
                                <? } else { ?>
                                    <span class="input-group-addon">.repo</span>
                                <? } ?>
                            </div>

                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="col-md-4">
                            <label for="new_source_content"> <strong><?= __('Source List Path');?></strong></label>
                        </div>
                        <div class="col-md-8">
                            <textarea id="new_source_content" name="new_source_content" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal"><?= __("Close");?></button>
                <button type="button" id="save_new_source_list" class="btn btn-primary"><?= __("Save");?></button>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>