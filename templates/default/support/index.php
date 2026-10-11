<?
$create_url = $this->uri->url_encrypt('support/create');
$class = '';
    if($opened_support == true) {
        $create_url = '#';
        $class = 'disabled';
    }
?>
<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-md-12 margin-bottom-10">
                <div class="float-left">
                    <? if($support_status == false) { ?>
                        <a class="btn btn-sm btn-primary <?= $class;?>" data-id="nested" href="<?= $create_url;?>" type="button"><i class="fa fa-check-circle"></i> <?= __('Request New Support'); ?></a>
                    <? } else { ?>
                        <a class="btn btn-sm btn-primary table-action disabled" data-id="nested" href="#" type="button"><i class="fa fa-check-circle"></i> <?= __('Request New Support'); ?></a>
                    <? } ?>

                    <? if($support_user == true) { ?>
                        <a class="btn btn-sm btn-primary" data-id="nested" href="<?= $this->uri->url_encrypt('support/supportuser');?>" type="button"><i class="fa fa-user"></i> <?= __('Support User Area'); ?></a>
                    <? } ?>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt('support');?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th><?= __('Title');?></th>
                        <th><?= __('Status');?></th>
                        <th><?= __('Created Date');?></th>
                        <th><?= __('Closed/Updated Date');?></th>
                        <th><?= __('Support User Messages');?></th>
                        <th><?= __('Connection Status');?></th>
                        <th data-orderable="false"><?= __('Disable Logins');?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>