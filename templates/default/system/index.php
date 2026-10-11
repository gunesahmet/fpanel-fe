<?
if($system_type == STANDALONE) {
    $this->partial('system/standalone');
}
else if($system_type == CLOUD) {
    $this->partial('system/cloud');
}
?>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
