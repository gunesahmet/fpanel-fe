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
                <div class="action-nav-normal">
                    <div class="row action-nav-row">
                        <?
                        /** @var ArrayObject $dashboard_buttons */
                        foreach($dashboard_buttons as $sub_menu)  { ?>
                            <? $url = ($sub_menu['type'] == "ajax" ? 'data-url="' . $sub_menu['link'] . '"' : 'href="'.$sub_menu['link'].'"');?>
                            <? $type = ($sub_menu['type'] == "ajax" ? 'data-id="'. $sub_menu['id'] .'"' : 'id="'.$sub_menu['id'].'"');?>

                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 action-nav-button">

                            <a class="<?= $sub_menu['class'];?>" <?= $type; ?> <?= $url; ?>>
                                <?= (isset($sub_menu['icon']) ? $sub_menu['icon'] : "");?>
                                    <span><?= $sub_menu['name'];?></span>
                                </a>
                            </div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <div class="row">
                    <div class="col-md-12 margin-bottom-10">
                        <div class="float-left">
                            <a class="btn btn-primary btn-sm" href="<?= $this->uri->url_encrypt("server/create");?>"><i class="fa fa-plus-circle"></i> <?= __('Add New Server');?></a>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt('system');?>"  data-buttons="true" data-actions="true" id="server-list">
                                <thead>
                                <tr>
                                    <th width="20%"><?= __('Server Name');?></th>
                                    <th width="10%"><?= __('Operating System');?></th>
                                    <th width="10%"><?= __('Services');?></th>
                                    <th width="10%"><?= __('Created Date');?></th>
                                    <th width="10%" data-orderable="false"><?= __('Actions');?></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
