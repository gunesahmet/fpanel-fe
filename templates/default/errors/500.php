<div class="middle-box text-center">
    <h1>500</h1>
    <h3 class="font-bold">Internal Server Error</h3>

    <div class="error-desc">
        Unfortunately we're having trouble loading the page you are looking for. Please wait a moment and try again or use action below.<br/>
        <b class="text-danger"><?= (isset($error_content) ? $error_content : '');?></b>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>