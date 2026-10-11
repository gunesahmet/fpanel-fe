<div class="middle-box text-center">
    <h1>503</h1>
    <h3 class="font-bold text-danger"><?= (isset($error_content) ? $error_content : '');?></h3>

    <div class="error-desc">
        <?= __("Unfortunately we're having trouble loading the page you are looking for. Please wait a moment and try again or use action below.");?><br/>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>