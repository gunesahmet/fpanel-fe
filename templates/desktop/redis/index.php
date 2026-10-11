<div class="row" id="redis-manager-panel" data-width="70%">
    <div class="col-lg-3">
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Keys');?></div>
            <div class="panel-body form-horizontal">
                <div class="form-group">
                    <div class="col-md-12">
                        <a class="btn btn-primary btn-sm btn-block"><i class="fa fa-info-circle"></i> <? echo __('Info');?></a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4"><? echo __('Databases');?></label>
                    <div class="col-md-8">
                        <select id="databases" name="databases" class="form-control input-sm">
                            <option value="0"><? echo __('Database');?> (0)</option>
                            <option value="1"><? echo __('Database');?> (1)</option>
                            <option value="2"><? echo __('Database');?> (2)</option>
                            <option value="3"><? echo __('Database');?> (3)</option>
                            <option value="4"><? echo __('Database');?> (4)</option>
                            <option value="5"><? echo __('Database');?> (5)</option>
                        </select>
                    </div>
                </div>
                <div class="file-manager">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="redis_keys"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 animated fadeInRight">
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Details');?></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="key_details">
                            <div class="col-md-12 margin-bottom-10">
                                <div class="row">
                                    <div class="col-md-1"><? echo __('Key');?></div>
                                    <div class="col-md-4"><input class="form-control input-sm" name="redis_key" id="redis_key"></div>
                                    <div class="col-md-7">
                                        <div class="pull-right">
                                            <a class="btn btn-primary btn-sm" id="rename_key"><? echo __('Rename'); ?></a>
                                            <a class="btn btn-danger btn-sm" id="delete_key"><? echo __('Delete'); ?></a>
                                            <a class="btn btn-default btn-sm" id="reload_key"><? echo __('Reload'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="redis_hash">
                                <div class="row margin-bottom-10">
                                    <div class="col-md-10">
                                        <div class="table-responsive">
                                            <table id="hash_table" class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th><? echo __('Key');?></th>
                                                    <th><? echo __('Value');?></th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <a class="btn btn-sm btn-block btn-default" id="add_row"><? echo __('Add Row'); ?></a>
                                        <a class="btn btn-sm btn-block btn-default" id="delete_row"><? echo __('Delete Row'); ?></a>
                                        <a class="btn btn-sm btn-block btn-default" id="reload_row"><? echo __('Reload Row'); ?></a>
                                    </div>
                                </div>
                                <div class="row margin-bottom-10">
                                    <div class="col-md-12 margin-bottom-5">
                                        <div class="pull-left"><? echo __('Key');?></div>
                                        <div class="pull-right"><select class="form-control input-sm">
                                                <option class="text"><? echo __('Plain Text');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 margin-bottom-10"><textarea id="hash_key_text" class="form-control"></textarea></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 margin-bottom-5">
                                        <div class="pull-left"><? echo __('Value');?></div>
                                        <div class="pull-right"><select class="form-control input-sm">
                                                <option class="text"><? echo __('Plain Text');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12"><textarea id="hash_value_text" class="form-control"></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var redis = function () {
        return {
            init: function (target) {
                $(target)
                    .on('changed.jstree', function (e, data) {

                        if(data.selected[0] != null) {
                            if(data.node['li_attr']['data-type'] == "single") {
                                var result = core.ajaxRequest({
                                    method: 'POST',
                                    url: '<? echo $this->uri->url_encrypt('redis/get');?>',
                                    data: {key:data.selected[0]}
                                });
//                                if(result['type'] == "success")
//                                    console.log(result);
                            }
                        }
                    })
                    .jstree({
                        "core" : {
                            "themes" : {
                                "responsive": true
                            },
                            'check_callback' : true,
                            'data' : ''

                        },
                        "contextmenu":{
                            "items": function($node) {
                                var tree = $("#tree").jstree(true);
                                return {
                                    "Create": {
                                        "separator_before": false,
                                        "separator_after": false,
                                        "label": "Create",
                                        "action": function (obj) {
//                                        $node = tree.create_node($node);
//                                        tree.edit($node);
                                        }
                                    },
                                    "Rename": {
                                        "separator_before": false,
                                        "separator_after": false,
                                        "label": "Rename",
                                        "action": function (obj) {
//                                        tree.edit($node);
                                        }
                                    },
                                    "Remove": {
                                        "separator_before": false,
                                        "separator_after": false,
                                        "label": "Remove",
                                        "action": function (obj) {
//                                        tree.delete_node($node);
                                        }
                                    }
                                };
                            }
                        },
                        'plugins' : ['wholerow','contextmenu']
                    });

                var result = core.ajaxRequest({
                    method: 'POST',
                    url: '<? echo $this->uri->url_encrypt('redis');?>',
                    data: {
                        selected_db: $('select#databases').val()
                    }
                });
                var data;
                if(result['type'] == "success")
                    data = result['result'];

                $(target).jstree(true).settings.core.data = data;
                $(target).jstree(true).refresh();
            }
        };
    }();

    redis.init('#redis_keys');
    core.setWidth({target:'redis-manager-panel'});

</script>