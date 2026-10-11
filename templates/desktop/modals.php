<div id="pop_alert" class="modal fade modal-lg" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><? echo __('Alert');?></h3>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
    </div>
</div>
<div id="confirm" class="modal fade" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3></h3>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
        <button type="button" class="btn btn-primary btn-sm" id="submit"><? echo __('Submit');?></button>
    </div>
</div>
<div id="customize-screen" class="modal modal-lg fade" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3></h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <form class="form-horizontal" method="post" action="<? echo $this->uri->url_encrypt("customize");?>">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="background"><? echo __('Color');?></label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="background" id="background"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="background-preview"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
        <button type="button" class="btn btn-primary btn-sm" id="submit"><? echo __('Submit');?></button>
    </div>
</div>
