<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= $this->tag->getTitle() ?>

    <link href="/templates/default/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/templates/default/assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="/templates/default/assets/css/animate.css" rel="stylesheet">
    <link href="/templates/default/assets/css/style.css" rel="stylesheet">
    <link href="/templates/default/assets/css/custom.css" rel="stylesheet">


</head>

<body class="top-navigation  pace-done skin-3">

<div id="wrapper">
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message"><?= __('Welcome') . ' ' . $this->auth->getUsername();?></span>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-language"></i>  <span><? $language_lists = $this->translate->getLangList(); echo $language_lists[$this->fcookies->get("language")];?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <? foreach($this->sidebar->getSideLangsMenu() as $lang)  { ?>
                                <li>
                                    <a href="<?= $this->uri->url_encrypt('lang/'. $lang['img']);?>">
                                        <?= $lang['title'];?>
                                    </a>
                                </li>
                            <? } ?>
                        </ul>
                    </li>
                    <? if(isset($sys_messages)) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <? $msg_count = count($sys_messages);?>
                                <i class="fa fa-envelope"></i>  <span class="label label-warning"><?= $msg_count;?></span>
                            </a>

                            <? foreach($sys_messages as $index => $message) {?>
                                <ul class="dropdown-menu dropdown-messages">
                                    <li>
                                        <div class="dropdown-messages-box">
                                            <a href="#" class="float-left">
                                                <?= ($message['type'] == 'announcement' ? '<i class="fa fa-info-circle fa-2x"></i>' : '<i class="fa fa-exclamation-triangle fa-2x text-warning"></i>'); ?>
                                            </a>
                                            <div class="media-body">
                                                <small class="float-right"><?= $this->env->convertDate($message['created_date'],null,false);?></small>
                                                <strong><?= $message['title'];?></strong><br><?= substr($message['message'], 0, 100);?>
                                                <small class="text-muted"></small>
                                            </div>
                                        </div>
                                    </li>
                                    <?= ($index == $msg_count ? '<li class="divider"></li>' : ''); ?>
                                </ul>
                            <? } ?>
                        </li>
                    <? } ?>
                    <li>
                        <a href="<?= $this->uri->url_encrypt("logout", true);?>">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>
                    </li>
                </ul>

            </nav>
        </div>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-sm-4">
                <h2><?= __('E-Mail Management');?></h2>
            </div>
        </div>
        <div class="wrapper-content">
            <div class="row">
                <form class="form-horizontal" method="post">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="col-md-8">
                        <div class="ibox">
                            <div class="ibox-content">
                                <span class="text-muted small float-right"></span>
                                <h2><?= $mail_data['mail_name'].'@'.$mail_data['domain_name'];?></h2>
                                <br />
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong><?= __('Change Password');?></strong>
                                        <p class="muted"><?= __('You can change your account password from here');?></p>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="old_password"><?= __('Old Password');?> *</label>
                                                    <input type="password" name="old_password" id="old_password" class="form-control form-control-sm"/>
                                                </div>
                                                <div class="form-group">
                                                    <label for="password"><?= __('New Password');?></label>
                                                    <div class="input-group" id="input-group-password">
                                                        <input type="password" name="password" id="password" class="form-control form-control-sm"/>
                                                        <span class="input-group-append"><button type="button" onclick="passgen('password','confirm-password')" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                                                        <span class="input-group-append"><button type="button" id="show-passwd2" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="confirm-password"><?= __('Confirm Password');?> *</label>
                                                    <input type="password" name="confirm-password" id="confirm-password" class="form-control form-control-sm"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ibox">
                            <div class="ibox-content">
                                <div class="row m-b-lg">
                                    <div class="col-lg-4 text-center">
                                        <h2><?= $mail_data['domain_name'];?></h2>
                                        <div class="m-b-sm">
                                            <i class="fa fa-globe mid-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block"> <?= __('Save');?> </button>
                                        <a href="<?= $this->uri->url_encrypt("dashboard");?>" class="btn btn-danger btn-sm btn-block"> <?= __('Cancel');?> </a>
                                    </div>
                                </div>
                                <div class="client-detail">
                                    <strong><?= __('About Mail Address');?></strong>

                                    <ul class="list-group clear-list">
                                        <li class="list-group-item fist-item">
                                            <? if($mail_data['status'] == "1") { ?>
                                            <span class="text-success float-right"><?= __('Active');?></span>
                                            <? } else { ?>
                                            <span class="text-danger float-right"><?= __('Passive');?></span>
                                            <? } ?>
                                            <?= __('Status');?>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="float-right"><?= $this->env->convert($mail_data['mailbox_quota']);?></span>
                                            <?= __('Mail Box Quota');?>
                                        </li>
                                    </ul>
                                    <strong><?= __('Using Mail Aliases');?></strong>

                                    <ul class="list-group clear-list">
                                        <? if(!empty($mail_data['mail_aliases'])) { ?>
                                        <? $mail_aliases = explode(',', $mail_data['mail_aliases'])?>
                                        <? foreach($mail_aliases as $alias){ ?>
                                        <li class="list-group-item">
                                            <span class="ellipsis"><?= $alias.'@'.$mail_data['domain_name'];?></span>
                                        </li>
                                        <? } ?>
                                        <? } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <div class="footer">
            <div class="col-md-8">
                &copy; 2021 - <?= date("Y"); ?> - <a href="http://www.fpanel.net" target="_blank">Fpanel</a> - All Rights Reserved.</a>
                Current version: <?= F_VERSION;?>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="float-right">
                        Memory Usage = <?= $this->env->convert(memory_get_usage(true));?>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<script src="/templates/default/assets/js/jquery-2.1.1.js"></script>
<script src="/templates/default/assets/js/bootstrap.min.js"></script>
<script>
    eval((function(){var d=[94,74,90,71,81,86,88,85,75,89,66,82,70,76,60,79,87,72,80,65];var e=[];for(var b=0;b<d.length;b++)e[d[b]]=b+1;var q=[];for(var a=0;a<arguments.length;a++){var f=arguments[a].split('~');for(var g=f.length-1;g>=0;g--){var h=null;var i=f[g];var j=null;var k=0;var l=i.length;var m;for(var n=0;n<l;n++){var o=i.charCodeAt(n);var p=e[o];if(p){h=(p-1)*94+i.charCodeAt(n+1)-32;m=n;n++;}else if(o==96){h=94*d.length+(i.charCodeAt(n+1)-32)*94+i.charCodeAt(n+2)-32;m=n;n+=2;}else{continue;}if(j==null)j=[];if(m>k)j.push(i.substring(k,m));j.push(f[h+1]);k=n+1;}if(j!=null){if(k<l)j.push(i.substring(k));f[g]=j.join('');}}q.push(f[0]);}var r=q.join('');var x='abcdefghijklmnopqrstuvwxyz';var c=[96,42,126,39,92,10].concat(d);for(var b=0;b<c.length;b++)r=r.split('@'+x.charAt(b)).join(String.fromCharCode(c[b]));return r.split('@!').join('@');})('^+_$_8450=["","abcdefghijklmnopqrstuvwxyz@z@qCDE@s@j@xI@h@o@tMN@v@y@k@rST@n@l@w@m@p@i0123456789","random","length","floor","char@zt","val","#^-rev^-arent","log","type","text^-rop^-assword","^*","button[id=@e"show-^)@e"]",":first-child","children","button[id=@e"show-^)2@e"]"];function passgen(d,e){^+f=^(0];^+b=^(1];for(i=1;i@u8;i++){^+a=Math^\'4]](Math^\'2]]()@bb^\'3]]+1);f+=b^\'5]](a);}^&[7]+d^%6]](f)^&[7]+e^%6]](f);}^+^*ed_^)=true^&[16]^%15]](function(){if(^*ed_^)){console^\'10]](^$^ ^,));^$^ ^,)^#^!2^"false;}else {^$^ ^,)^#^!4^"true;}})^&[19]^%15]](function(){if(^*ed_^)){^$^ 9]](^%1^,^(17])^#^!2^"false;}else {^$^ 9]](^%1^,^(17])^#^!4^"true;}});~8450[9]](^%~8450[11],^(1~]);clicked_^)=~^\'13]](_$_~$(this)[_$_~)^\'~;$(_$_8450~[^(~_$_8450[~passwd~click~var ~8]](~","p'));
</script>
</body>

</html>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>