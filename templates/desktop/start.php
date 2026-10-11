<div id="start_menu" class="hidden-xs">
    <div class="row" id="menu">
        <div class="col-md-3">
            <div class="row left-menu">
                <ul>
                    <li class="first-item">
                        <a class="row" href="#">
                            <span class="icon-signin-user fs1 col-md-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></span>
                            <span class="text col-md-9"><? echo $username;?></span>
                        </a>
                    </li>
                </ul>
                <br />
                <hr />
                <ul>
                    <li>
                        <a class="row" data-type="href" data-url="<? echo $this->uri->url_encrypt('logout');?>">
                            <span class="icon-logout fs1 col-md-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span></span>
                            <span class="text col-md-9"><? echo __('Logout');?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="row">
                            <span class="icon-support fs1 col-md-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>
                            <span class="text arrow col-md-9"><? echo __('Help');?></span>
                        </a>
                        <div class="recent">
                            Recent
                            <hr />
                            <ul>
                                <li><a href="#">Recent 1</a></li>
                                <li><a href="#">Recent 2</a></li>
                                <li><a href="#">Recent 3</a></li>
                                <li><a href="#">Film-Bokep.3gp</a></li>
                                <li><a href="#">Film-Nyokep.3gp</a></li>
                                <li><a href="#">Film-Enyak.3gp</a></li>
                                <li><a href="#">Film-Babeh.3gp</a></li>
                            </ul>
                        </div>
                    </li>
                    <? if($firstboot == false) { ?>
                    <li>
                        <a class="row" href="#">
                            <span class="icon-user-fingerprint fs1 col-md-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>
                            <span class="text col-md-9"><? echo __('Profile Settings');?></span>
                        </a>
                    </li>
                    <? } ?>
                </ul>
            </div>
        </div>
        <div class="col-md-9 right-menu navbox">
            <div class="navbox-tiles">
                <div class="shortcut-menu" id="short-cuts">
                    <ul class="gridster connectedLists" id="shortcuts"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .left-menu li a {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .left-menu .text {
        padding-top: 5px;
    }
    #menu .left-menu li a:active {
        background-color: rgba(75,76,76, 0.8);
        /*border:1px ridge rgba(75,76,76, 0.8);*/
    }
    #menu .left-menu li a:active .text {
        text-shadow:
            -1px -1px 1px #111,
            2px 2px 1px #363636;
    }

</style>