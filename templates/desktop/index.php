<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <? echo $this->tag->getTitle() ?>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <!--[if lt IE 7]><script>window.top.location = 'ie.html';</script><![endif]-->
    <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap-modal-bs3patch.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap-modal.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/datatables.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/jquery.gridster.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/jquery-ui.min.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/datetimepicker.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/reset.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/bootstrap-switch.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/jquery.fileupload.css" />

    <link rel="stylesheet" href="/templates/desktop/assets/css/desktop.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/windows.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/login.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/jquery.contextmenu.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/toastr.min.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/sidebar.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/jstree.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/codemirror.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/styles.css" />
    <link rel="stylesheet" href="/templates/desktop/assets/css/component.css" />
    <!--[if lt IE 9]><link rel="stylesheet" href="/templates/desktop/assets/css/ie.css" /><![endif]-->
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
</head>
<body>
<? if($firstboot == false) $this->partial('sidebar'); ?>
<div class="abs" id="wrapper">
    <?= $this->message->getMessages();?>
    <? $this->flashSession->clear();?>
    <?= $this->getContent();?>
</div>
<? $this->partial('start'); ?>
<? $this->partial('task'); ?>
<div class="installed-langs">
    <ul>
    <? foreach($this->translate->getLangList() as $id => $language) { ?>
        <li id="<? echo $id; ?>" class="<? echo (strpos($language,$this->fcookies->get("language")) !== false ? 'active' : '');?>">
            <span class="col-md-4"><? echo strtoupper(substr(strstr($language, '(', true), 0, 3));?></span>
            <span class="col-md-8"><? echo strstr($language, '(', true);?></span>
        </li>
    <? } ?>
    </ul>
</div>
<? $this->partial('modals'); ?>
<div class="logs" id="logs">
    <pre></pre>
</div>
<? $this->partial('js'); ?>
</body>
</html>