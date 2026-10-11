
<?
switch($user_type) {
    case "4":
        echo $this->partial('dashboard/domain');
        break;
    case "3":
    case "2":
        if((isset($client_type)) && ($client_type < 4)) {
            echo $this->partial('dashboard/client');
        }
        elseif((isset($client_type) && ($client_type == 4))) {
            echo $this->partial('dashboard/domain');
        }
        else {
            echo $this->partial('dashboard/client');
        }
        break;
    case "6":
    case "0":
        if((isset($client_type)) && ($client_type < 4))
            echo $this->partial('dashboard/client');
        elseif((isset($client_type) && ($client_type == 4)))
            echo $this->partial('dashboard/domain');
        else
            echo $this->partial('dashboard/admin');
        break;
}


