<script>
    let errors = <?= $errors;?>;
    let time_limit = parseInt('<?= $this->config->session->timeout; ?>')*1000;
</script>
<!--<script src="/templates/default/assets/js/bundle.min.js"></script>-->
<script src="/templates/default/assets/js/jquery.min.js"></script>
<script src="/templates/default/assets/js/bootstrap-switch.min.js"></script>
<script src="/templates/default/assets/js/jquery.easing.js"></script>
<script src="/templates/default/assets/js/popper.min.js"></script>
<script src="/templates/default/assets/js/bootstrap.min.js"></script>
<script src="/templates/default/assets/js/sweetalert.min.js"></script>
<script src="/templates/default/assets/js/metisMenu.min.js"></script>
<script src="/templates/default/assets/js/jquery.cokie.min.js"></script>
<script src="/templates/default/assets/js/jquery.multi-select.js"></script>
<script src="/templates/default/assets/js/bootstrap-select.min.js"></script>
<script src="/templates/default/assets/js/jquery.select-multiple.js"></script>
<script src="/templates/default/assets/js/icheck.min.js"></script>
<script src="/templates/default/assets/js/toastr.min.js"></script>
<script src="/templates/default/assets/js/quicksearch.js"></script>
<script src="/templates/default/assets/js/jstree.min.js"></script>
<script src="/templates/default/assets/js/jquery.slimscroll.min.js"></script>
<script src="/templates/default/assets/js/fileinput.min.js"></script>
<script src="/templates/default/assets/js/copy-clipboard.js"></script>
<script src="/templates/default/assets/js/fileupload.js"></script>
<script src="/templates/default/assets/js/jquery.sortable.min.js"></script>
<script src="/templates/default/assets/js/datatables.min.js"></script>
<script src="/templates/default/assets/js/ajax.reload.js"></script>
<script src="/templates/default/assets/js/jquery.contextMenu.min.js"></script>
<script src="/templates/default/assets/js/jquery.knob.js"></script>
<script src="/templates/default/assets/js/jquery.steps.min.js"></script>
<script src="/templates/default/assets/js/jquery.flot.js"></script>
<script src="/templates/default/assets/js/jquery.flot.tooltip.min.js"></script>
<script src="/templates/default/assets/js/jquery.flot.resize.js"></script>
<script src="/templates/default/assets/js/jquery.flot.time.js"></script>
<script src="/templates/default/assets/js/multiselector.js"></script>
<script src="/templates/default/assets/js/jquery.gridly.js"></script>

<script src="/templates/default/assets/js/codemirror.js"></script>
<script src="/templates/default/assets/js/foldcode.js"></script>
<script src="/templates/default/assets/js/foldgutter.js"></script>
<script src="/templates/default/assets/js/brace-fold.js"></script>
<script src="/templates/default/assets/js/xml-fold.js"></script>
<script src="/templates/default/assets/js/indent-fold.js"></script>
<script src="/templates/default/assets/js/comment-fold.js"></script>

<script src="/templates/default/assets/js/xml.js"></script>
<script src="/templates/default/assets/js/htmlmixed.js"></script>
<script src="/templates/default/assets/js/css.js"></script>
<script src="/templates/default/assets/js/javascript.js"></script>
<script src="/templates/default/assets/js/perl.js"></script>
<script src="/templates/default/assets/js/python.js"></script>
<script src="/templates/default/assets/js/nginx.js"></script>
<script src="/templates/default/assets/js/shell.js"></script>
<script src="/templates/default/assets/js/sublime.js"></script>
<script src="/templates/default/assets/js/mathematica.js"></script>
<script src="/templates/default/assets/js/php.js"></script>
<script src="/templates/default/assets/js/clike.js"></script>
<script src="/templates/default/assets/js/apache.js"></script>
<script src="/templates/default/assets/js/csv.js"></script>
<script src="/templates/default/assets/js/sql.js"></script>
<script src="/templates/default/assets/js/sublime.js"></script>

<script src="/templates/default/assets/js/closebrackets.js"></script>
<script src="/templates/default/assets/js/closetag.js"></script>
<script src="/templates/default/assets/js/active-line.js"></script>
<script src="/templates/default/assets/js/matchbrackets.js"></script>
<script src="/templates/default/assets/js/matchtags.js"></script>
<script src="/templates/default/assets/js/match-highlighter.js"></script>
<script src="/templates/default/assets/js/jplist.core.min.js"></script>

<script src="/templates/default/assets/js/core.js?<?= time();?>"></script>
<? if($controller_name != "install") { ?>
<script src="/templates/default/assets/js/fp.file.uploader.js?<?= time();?>"></script>
<script src="/templates/default/assets/js/fp.filemanager.js?<?= time();?>"></script>
<script src="/templates/default/assets/js/fpanel.config.js?<?= time();?>"></script>
<script src="/templates/default/assets/js/fpanel.js?<?= time();?>"></script>
<script src="/templates/default/assets/js/socket.js?<?= time();?>"></script>
<? } else { ?>
<script>
    var lvar = <?= $script_vars;?>;
    core.init();
</script>
<? } ?>






