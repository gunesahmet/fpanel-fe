<?

switch($user_type) {
        case DOMAIN_USER:
            echo $this->partial('dashboard/domain');
            break;
        case CLIENT_USER:
        case RESELLER_USER:
            if((isset($client_type)) && ($client_type == CLIENT_USER)) {
                echo $this->partial('dashboard/client');
            }
            else if((isset($client_type) && ($client_type == DOMAIN_USER))) {
                echo $this->partial('dashboard/domain');
            }
            else {
                echo $this->partial('dashboard/client');
            }
            break;
        case SUPPORT_USER:
        case ADMIN_USER:
        case ROOT_USER:
            if((isset($client_type)) && ($client_type < DOMAIN_USER))
                echo $this->partial('dashboard/client');
            else if((isset($client_type) && ($client_type == DOMAIN_USER)))
                echo $this->partial('dashboard/domain');
            else
                echo $this->partial('dashboard/admin');
            break;
    }
?>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>