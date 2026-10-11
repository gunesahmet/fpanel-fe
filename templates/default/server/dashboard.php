<div class="row">
    <div class="col-md-12">
        <?
        /** @var ArrayObject $dashboard_buttons */
        foreach ($dashboard_buttons as $menu) { ?>
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= $menu['name']; ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="action-nav-normal">
                        <div class="row action-nav-row">
                            <? $i = 1;?>
                            <? foreach($menu['sub'] as $sub_menu)  { ?>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 action-nav-button">
                                    <? $url = ($sub_menu['type'] == "ajax" ? 'data-url="' . $sub_menu['link'] . '"' : 'href="'.$sub_menu['link'].'"');?>
                                    <? $type = ($sub_menu['type'] == "ajax" ? 'data-id="'. $sub_menu['id'] .'"' : 'id="'.$sub_menu['id'].'"');?>
                                    <a class="<?= $sub_menu['class'];?>" <?= $type; ?> <?= $url; ?>>
                                        <?= (isset($sub_menu['icon']) ? $sub_menu['icon'] : "");?>
                                        <span><?= $sub_menu['name'];?></span>
                                    </a>
                                </div>
                                <? if(($i % 6) === 0) { echo '<div class="clearfix"></div>';}?>

                                <? $i++;} ?>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>