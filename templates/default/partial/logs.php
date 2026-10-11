<div class="small-chat-box">
    <div class="heading chat-heading">
        <small class="chat-date float-right">
            <?= $this->date->datetime(false, "d.m.Y");?>
        </small>
        <?= __("Fpanel Logs");?>
    </div>
    <div class="content">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?= __("Date");?></th>
                    <th><?= __("Type");?></th>
                    <th><?= __("Client");?></th>
                    <th><?= __("Content");?></th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="small-chat">
    <a class="open-small-chat" href="">
        <i class="fp fp-details"></i>
    </a>
</div>
