<div class="row">
    <div class="col-md-12">
        <? foreach ($dashboard_buttons as $menu) { ?>
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><? echo $menu['name']; ?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="action-nav-normal">
                            <div class="row action-nav-row">
                                <? foreach($menu['sub'] as $sub_menu)  { ?>
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
        <? } ?>
    </div>
</div>
