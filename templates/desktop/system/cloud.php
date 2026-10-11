<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="action-nav-normal">
                    <div class="row action-nav-row">
                        <? foreach($dashboard_buttons as $sub_menu)  { ?>
                            <div class="col-md-2 col-xs-12 action-nav-button">
                                <? if($sub_menu['type'] == "ajax" ) { ?>
                                <? if($sub_menu['id'] != "show_log" ) { ?>
                                <a class="icon-btn <? echo $sub_menu['class']; ?>" id="<? echo $sub_menu['id']; ?>" onclick="confirm('<? echo $sub_menu['link']; ?>','<? echo __('Are you sure?'); ?>')">
                                    <? } else { ?>
                                    <a class="icon-btn <? echo $sub_menu['class']; ?>" id="<? echo $sub_menu['id']; ?>" data-url="<? echo $sub_menu['link']; ?>">
                                        <? } ?>
                                        <? } else { ?>
                                        <a href="<? echo $sub_menu['link']; ?>" class="icon-btn <? echo $sub_menu['class']; ?>">
                                            <? } ?>
                                            <i></i>
                                            <div>
                                                <? echo $sub_menu['name'];?>
                                            </div>
                                            <? if(!empty($sub_menu['count'])) { ?>
                                                <span class="triangle-button red">  <span class="inner"><? echo $sub_menu['count'];?> </span></span>
                                            <? } ?>
                                        </a>
                            </div>
                        <? } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="">
                    <a class="btn btn-primary btn-sm" href="<? echo $this->uri->url_encrypt("server/create");?>"><i class="fa fa-plus-circle"></i> <? echo __('Add New Server');?></a>
                </div>
                <table class="dynamicTable table table-striped table-bordered table-hover dataTable no-footer table dataTables" >
                    <thead>
                    <tr>
                        <th width="5%"><input type="checkbox" id="select_all" class="checkbox"></th>
                        <th width="20%"><? echo __('Server Name');?></th>
                        <th width="10%"><? echo __('OS');?></th>
                        <th width="10%"><? echo __('Type');?></th>
                        <th width="10%"><? echo __('Created Date');?></th>
                        <th width="10%"><? echo __('Status');?></th>
                        <th width="10%"><? echo __('Actions');?></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

</div>
