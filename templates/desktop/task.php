<div class="abs" id="bar_bottom">
    <div class="taskbar hidden-xs">
        <a class="pull-left start_menu" title="<? echo __('Start');?>">
            <span class="fs1 icon-logo"></span>
        </a>
        <ul id="dock">
            <li id="desktop-search" class="desktop-search">
                <input class="form-control" type="text" name="desktop_search" placeholder="<? echo __('Typing search');/*Arama*/?>...">
            </li>
        </ul>
    </div>
    <div class="system-tasks hidden-xs">
        <div class="icons">
            <a style="right:20px" title="13 <? echo __('New notifications');?>" data-action="toggle" data-side="right"><i class="fa fa-stack-exchange"></i> <span class="label label-danger">13</span></a>
            <a style="right:105px" data-toggle="logs"><i class="fa fa-eye"></i> <span class="label label-success">Show/Hide Logs</span></a>
        </div>
        <div class="sys-langs">
            <? echo strtoupper(substr($this->translate->codeListTranslate($this->fcookies->get('language')), 0, 3));?>
        </div>
        <div class="dates">
            <div class="row" id="clock">
                <ul>
                    <li id="hours"></li>
                    <li id="pointh">:</li>
                    <li id="min"></li>
                </ul>
            </div>
            <div class="row" id="date"></div>
        </div>
        <div class="show-desktop" id="show_desktop" title="<? echo __('Show Desktop');/*Masaüstünü Göster*/?>"></div>
    </div>
    <div class="col-md-12 visible-xs" id="mobile">
        <a class="start_menu fs2">
            <span class="icon-logo"></span>
        </a>
    </div>
</div>