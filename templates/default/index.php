<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex">
        <?= $this->tag->getTitle() ?>
        <link rel="icon" type="image/x-icon" href="/favicon.ico" />
        <link href="/templates/default/assets/css/bootstrap.css" rel="stylesheet">
        <link href="/templates/default/assets/css/font-awesome.css" rel="stylesheet">
        <link href="/templates/default/assets/css/fp.css" rel="stylesheet">
        <link href="/templates/default/assets/css/bootstrap-switch.css" rel="stylesheet">
        <link href="/templates/default/assets/css/datatables.css" rel="stylesheet">
        <link href="/templates/default/assets/css/codemirror.css" rel="stylesheet">
        <link href="/templates/default/assets/css/file-upload.css" rel="stylesheet">
        <link href="/templates/default/assets/css/style.css" rel="stylesheet">
        <link href="/templates/default/assets/css/js.tree.min.css" rel="stylesheet">
        <link href="/templates/default/assets/css/jquery.contextmenu.css" rel="stylesheet">
        <link href="/templates/default/assets/css/bootstrap-select.min.css" rel="stylesheet">
        <link href="/templates/default/assets/css/ms.css" rel="stylesheet">
        <link href="/templates/default/assets/css/monokai.css" rel="stylesheet">
        <link href="/templates/default/assets/css/icheck.css" rel="stylesheet">
        <link href="/templates/default/assets/css/sweetalert.css" rel="stylesheet">
        <link href="/templates/default/assets/css/chart.css" rel="stylesheet">
<!--        <link href="/templates/default/assets/css/paginate.css" rel="stylesheet">-->
        <link href="/templates/default/assets/css/custom.css" rel="stylesheet">
        <link href="/templates/default/assets/css/toastr.min.css" rel="stylesheet">
    </head>
    <body class="">
        <div id="wrapper">
            <? $this->partial('partial/sidebar'); ?>
            <div id="page-wrapper" class="gray-bg" id="main">
                <? $this->partial('partial/header'); ?>
                <div class="wrapper wrapper-content" id="main-content">
                    <?= $this->message->getMessages();?>
                    <? $this->flashSession->clear();?>
                    <?= $this->getContent();?>
                </div>
                <? $this->partial('partial/footer'); ?>
            </div>
            <? $this->partial('partial/logs'); ?>
        </div>
        <? $this->partial('partial/js'); ?>
    </body>
</html>
