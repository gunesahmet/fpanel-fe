var cookie;
var closed_sock = true;
var loading = '<div class="sk-circle"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div><span>'+ lvar['loading'] +'</span></div>';
var win_width = $(window).width();
var win_height = $(window).height();
function scrollToBottom($el) {
    var $ = jQuery;

    if(typeof $el == 'string')
        $el = $($el);

    $el.get(0).scrollTop = $el.get(0).scrollHeight;
}
function startSocket() {
    var host = "ws://sbys.birim.com.tr:9000";
    try {
        socket = new WebSocket(host);
        if(socket.readyState == 1) {
            console.log('WebSocket ready');
        }
        socket.onopen = function(msg) {
            if(this.readyState == 1) {
                closed_sock = false;
                console.log("Welcome");
                socketSend({
                    type: 'connected'
                });
            }
        };
        socket.onmessage = function(msg) {
            var data = core.jsonParse(msg.data);
            switch(data['type']) {
                case "layout":
                    JQD.reorder(data.data);
                    break;
                case "shortcut":
                    if(data['action'] == "push") {
                        JQD.reorderShortcut(data['element']);
                    }
                    if(data['action'] == "add") {
                        JQD.addShortCut(JQD.buttons[data['target']],false);
                    }
                    if(data['action'] == "remove") {
                        JQD.removeShortCut(JQD.buttons[data['target']],false);
                    }
                    break;
                case "users":
                    JQD.connectedUser(data.data);
                    break;
                case "chat":
                    var obj = data.data;
                    $(".sidebar.right").trigger("sidebar:open");
                    coreChat.open($('a#'+obj['from']));
                    coreChat.chat_history[obj['from']].messages.push({
                        message: obj['message'],
                        from: obj['username'],
                        time: new Date(),
                        fromOpponent: '',
                        unread: true
                    });
                    coreChat.renderMessages(obj['from'],true);
                    break;
                case "disconnected":
                    JQD.disconnectUser(data);
                    break;
            }
        };
        socket.onclose   = function(msg) {
            closed_sock = true;
            if(this.readyState == 3)
                console.log("Disconnected from server");
        };
    }
    catch(ex){
        console.log(ex);
    }

}
function socketSend(data){
    //try {
    //    if(cookie == null)
    //        cookie = $.cookie();
    //    data['userdata'] = cookie;
    //    socket.send(window.JSON.stringify(data));
    //}
    //catch(ex) {
    //    console.log(ex);
    //}
}
function closeSocket() {
    socket.onclose = function () {};
    socket.close();
    closed_sock = true;
}
function isJSON (object) {
    if (typeof object != 'string')
        object = JSON.stringify(object);

    try {
        JSON.parse(object);
        return true;
    } catch (e) {
        return false;
    }
}
$("#template_select").change(function () {
    if($("#template_select").val() != 0) {
        $.ajax({
            url: $("#template_select").attr('data-url'),
            data: "template="+$("#template_select").val(),
            type: 'POST',
            'success': function (result) {
                var obj = JSON.parse(result);
                var limitItems = {
                    client_limit: "clients",
                    domain_limit: "domain",
                    disk_limit: "disk",
                    traffic_limit: "traffic",
                    subdomain_limit: "subdomain",
                    domain_alias_limit: "domain_alias",
                    email_box_limit: "email_box",
                    email_box_size_limit: "email_box_size",
                    email_forward_limit: "email_forward",
                    email_groups_limit: "email_groups",
                    email_autoresponder_limit: "email_autoresponder",
                    email_lists_limit: "email_list",
                    template_limit: "template",
                    postgresql_limit: "postgresql",
                    mssql_limit: "mssql",
                    mysql_limit: "mysql",
                    database_limit: "database",
                    database_size_limit: "database_size",
                    expire_time_limit: "expire_time_limit"};
                for (var i in limitItems) {
                    if (obj[i] != "unlimited") {
                        $('input[name="' + limitItems[i] + '"]').prop('checked', false);
                        $.uniform.update('input[type="checkbox"]');
                        $('#' + limitItems[i] + '').removeAttr('disabled').prop('value', obj[i]);
                    }
                    else {
                        $('input[name="' + limitItems[i] + '"]').prop('checked', true);
                        $.uniform.update('input[type="checkbox"]');
                        $('#' + limitItems[i] + '').attr('disabled', 'disabled').prop('value', '');
                    }
                }
                var permItems = {
                    access_panel_perm: 'access_panel',
                    man_client: 'access_client',
                    man_domain: 'access_domain',
                    man_subdomain: 'access_subdomain',
                    man_email_alias: 'access_email_alias',
                    man_domain_alias: 'access_domain_alias',
                    man_hosting_settings: 'access_hosting_settings',
                    man_task: 'access_task',
                    man_dns: 'access_dns',
                    man_statistics: 'access_statistics',
                    man_mailing_lists: 'access_mailing_lists',
                    man_spam_filters: 'access_spam_filters',
                    man_backup_to_server: 'access_backup_to_server',
                    man_backup_to_ftp: 'access_backup_to_ftp',
                    man_database: 'access_database',
                    man_ssl: 'access_ssl',
                    man_ftp_user: 'access_ftp_user',
                    man_email: 'access_email',
                    man_protect_dir: 'access_protect_dir',
                    man_template: 'access_template'};
                for (var j in permItems) {
                    if (obj[j] != "allowed") {
                        $('input[name="' + permItems[j] + '"]').prop('checked', false);
                        $.uniform.update('input[type="checkbox"]');
                    }
                    else {
                        $('input[name="' + permItems[j] + '"]').prop('checked', true);
                        $.uniform.update('input[type="checkbox"]');
                    }
                }
                if (obj.dom_hosting_type != null) {
                    var val = obj.dom_hosting_type;
                    $('#hosting_settings').val(val);
                }
                if (obj.dom_php != "false") {
                    $('input[name="php_support"]').prop('checked', true);
                    $('#php_version').removeAttr('disabled').val(obj.dom_php_version);

                    $('input[name="php_safe_mode"]').removeAttr('disabled');
                    $.uniform.update('input[type="checkbox"]');
                    if (obj.dom_php_safe_mode != "false") {
                        $('input[name="php_safe_mode"]').prop('checked', true);
                    }
                }
                else {
                    $('input[name="php_support"]').prop('checked', false);
                    $('#php_version').removeAttr('disabled').val(obj['dom_php_version']);
                    $('input[name="php_safe_mode"]').prop('disabled', true);
                }
                if (obj.dom_asp != "false") {
                    $('input[name="asp_support"]').prop('checked', true);
                }
                else {
                    $('input[name="asp_support"]').prop('checked', false);
                }
                if (obj.dom_aspnet != "false") {
                    $('input[name="asp_net_support"]').prop('checked', true);
                    $('#asp_net_version').removeAttr('disabled').val(obj.dom_aspnet_version);
                }
                else {
                    $('input[name="asp_net_support"]').prop('checked', false);
                }
                if (obj.dom_cgi != "false") {
                    $('input[name="cgi_support"]').prop('checked', true);
                }
                else {
                    $('input[name="cgi_support"]').prop('checked', false);

                }
                if (obj.dom_perl != "false") {
                    $('input[name="perl_support"]').prop('checked', true);
                }
                else {
                    $('input[name="perl_support"]').prop('checked', false);
                }
                if (obj.dom_python != "false") {
                    $('input[name="python_support"]').prop('checked', true);
                }
                else {
                    $('input[name="python_support"]').prop('checked', false);

                }
                if (obj.dom_web_statistics != "false") {
                    $('input[name="web_statistics_support"]').prop('checked', true);
                    $('#web_statistics_type').removeAttr('disabled').val(obj.dom_web_statistics_type);
                }
                else {
                    $('input[name="web_statistics_support"]').prop('checked', false);

                }
                if (obj.dom_custom_error != "false") {
                    $('input[name="custom_error_pages"]').prop('checked', true);
                }
                else {
                    $('input[name="custom_error_pages"]').prop('checked', false);
                }
                if (obj.dom_dedicated_pools != "false") {
                    $('input[name="dedicated_pool"]').prop('checked', true);
                    $('#max_cpu_usage').removeAttr('disabled').prop('value', obj.dom_max_cpu_usage);
                }
                else {
                    $('input[name="dedicated_pool"]').prop('checked', false);
                }
                $.uniform.update('input[type="checkbox"]');
                if(obj.overuse == "true") {
                    $('#overuse').bootstrapSwitch('state', true);
                }
                else {
                    $('#overuse').bootstrapSwitch('state',false);
                }
            }
        });
    }
});
Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
};
String.prototype.lowerCase = function(){
    var string = this;
    var letters = { "İ": "i", "Ş": "s", "Ğ": "g", "Ü": "u", "Ö": "o", "Ç": "c", "I": "i" };
    string = string.replace(/(([İIŞĞÜÇÖ]))/g, function(letter){ return letters[letter]; });
    return string.toLowerCase();
};
String.prototype.upperCase = function(){
    var string = this;
    var letters = { "i": "I", "ş": "S", "ğ": "G", "ü": "U", "ö": "O", "ç": "C", "ı": "I" };
    string = string.replace(/(([iışğüçö]))/g, function(letter){ return letters[letter]; });
    return string.toUpperCase();
};
(function($) {
    $.fn.serializefiles = function() {
        var obj = $(this);
        /* ADD FILE TO PARAM AJAX */
        var formData = new FormData();
        $.each($(obj).find("input[type='file']"), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        var params = $(obj).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        return formData;
    };
})(jQuery);
var core = function() {
    return {
        select_result: {},
        last_selected: {},
        init: function () {
            $('.disk_usage').peity('donut');
            $('.traffic_usage').peity('donut');
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 5000
            };

            $('[data-type="href"]').unbind();
            $('[data-type="href"]').on('click', function () {
                if(typeof $(this).attr('target') != "undefined")
                    window.open($(this).attr('data-url'), '_blank');
                else
                    window.location.replace($(this).attr('data-url'));
            });

        },
        checkAuth: function (xhr) {
            console.log("checkAuth: " + xhr.getResponseHeader("X-Request-Auth"))
        },
        installation: function () {
            $('.window').show();
            $('#installation-wizard').bootstrapWizard({
                'tabClass': 'form-wizard',
                'nextSelector': '.next',
                'previousSelector': '.previous',
                onTabClick: function(tab, navigation, index) {
                    return false;
                },
                'onTabShow':  function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index+1;

                    if(current >= total) {
                        $('#installation-wizard').find('.nav-buttons .next').hide();
                        $('#installation-wizard').find('.nav-buttons .finish').show();
                    }
                    else {
                        $('#installation-wizard').find('.nav-buttons .next').show();
                        $('#installation-wizard').find('.nav-buttons .finish').hide();
                    }
                    $('#installation-wizard').find('.nav-buttons .finish').removeClass('disabled');
                    $('#installation-wizard').find('.nav-buttons .finish').click(function () {
                        window.location.href = "/";
                    });
                },
                'onNext': function (tab, navigation, index) {


                    var current_url = $('#installation-wizard').find('.form-wizard').children('li').eq(index -1).children("a").attr('data-url');
                    var next_url = $('#installation-wizard').find('.form-wizard').children('li').eq(index).children("a").attr('data-url');
                    var next_tab_href = $('#installation-wizard').find('.form-wizard').children('li').eq(index).children("a").attr('href');
                    var nex_tab_content = $(next_tab_href);
                    var form = $('#installation-wizard').find(".tab-content .tab-pane.active form");

                    nex_tab_content.html(loading);


                    var data = {};
                    var form_id = "";
                    var result;
                    if(typeof form != "undefined") {
                        if(form != "") {
                            data = form.serialize();
                            form_id = form.attr('id');
                            result = core.ajaxRequest({
                                form: form_id,
                                method: 'POST',
                                url: current_url,
                                data: data
                            },true);
                        }
                    }
                    else {
                        result = core.ajaxRequest({
                            method: 'POST',
                            url: current_url,
                            data: data
                        },true);
                    }

                    //result = {
                    //    type: 'success'
                    //};
                    if(result["type"] == "success") {
                        $('#installation-wizard').find('.form-wizard').children('li').eq(index - 1).addClass('complete');
                        $('#installation-wizard').find('.form-wizard').children('li').eq(index - 1).find('.step').html('<i class="fa fa-check"></i>');
                        nex_tab_content.load(next_url);
                        return true;
                    }
                    return false;
                }
            });
            $('.btn-logout').click(function () {
                core.ajaxRequest({
                    method: 'GET',
                    url: $(this).attr('href')
                });
                window.location.href = "/";
            });
        },
        isExists: function (array,element) {
            for(var i = 0; i < array.length; i++) {
                if(array[i] == element) {
                    return true;
                }
            }
            return false;
        },
        log: function(msg){
            var element_len = $(".small-chat-box .content").children().length;

            var element_class = "left";
            var element_class_act = "";
            if(element_len%2 == 0) {
                element_class = "right";
                element_class_act = " active";
            }

            var el = '<div class="'+ element_class +'">' +
                '<div class="author-name">' +
                'System ' +
                '<small class="chat-date">' +
                '08:45 pm' +
                '</small>' +
                '</div>' +
                '<div class="chat-message'+ element_class_act +'">' +
                msg +
                ' </div>' +
                ' </div>';
            $(".small-chat-box .content").prepend(el);
            core.countMsg();
            //
            //if(element_len == 3) {
            //    $(".small-chat-box .content").slimscroll();
            //}
        },
        countMsg: function() {
            $('#message_count').html( $(".small-chat-box .content").children().length);
        },
        ajaxFileUpload: function (options) {
            $(options['selector']).fileinput({
                uploadUrl: $(options['selector']).attr("data-url"),
                uploadAsync: false,
                minFileCount: 1,
                maxFileCount: 5,
                overwriteInitial: false,
                initialPreview: [],
                initialPreviewConfig: [],
                uploadExtraData: {}
            });
        },
        popover: function () {
            $('[rel="popover"]').popover({
                html : true,
                content: function() {
                    var content = $(this).attr("data-popover-content");
                    return $(content).children(".popover-content").html();
                }
            });
        },
        jsonParse: function (data) {
            var obj;
            try {
                obj = $.parseJSON(data);
            }
            catch(e) {
                obj = {
                    message:'error',
                    content:'System Error'
                };
            }
            return obj;
        },
        ajaxRequest: function (options,message) {
            var data = core.request(options);
            var obj = core.jsonParse(data);
            if((typeof message != "undefined") && (message == true)) {
                if(typeof obj['type'] != "undefined") {
                    if (obj['type'] == 'success') {
                        core.clearErrorLogs(options.form);
                        toastr[obj['type']](obj['messages'],obj['title']);
                    }
                    else {
                        core.initErrorLogs(obj['errors'],options.form);
                        var err_message = obj['messages'];
                        if(typeof obj['errors']['content'] != "undefined") {
                            err_message = obj['errors']['content'];
                        }
                        toastr[obj['type']](err_message,obj['title']);
                    }
                }
                else {
                    toastr['error']('Please report this error to developers!','System Error');
                }
            }
            cookie = data['cookies'];
            if(obj != null) {
                if (obj.hasOwnProperty('login')) {
                    if(obj['login'] == false) {
                        if($('#login-container').length !== 1) {
                            JQD.showLogin();
                        }
                        //if(typeof socket != "undefined")
                        //closeSocket();
                    }
                }
            }


            if(typeof options.table != 'undefined') {
                core.tableRefresh({
                    target : options.table,
                    url: options.table_url
                });
            }
            return obj;
        },
        request: function (options) {
            var obj;
            var cache = false;
            if(options.method == "GET")
                cache = true;

            if(typeof options.data == "undefined")
                options.data= {};
            $.ajax({
                type: options.method,
                async: false,
                cache:cache,
                url: options.url,
                data: options.data,
                success: function (data) {
                    obj = data;

                },
                error: function () {
                    obj = {
                        type: 'error',
                        message: 'Request error'
                    };

                },
                complete: function (XMLHttpRequest, textStatus) {
                    core.ajaxLog(XMLHttpRequest);
                }
            });
            return obj;
        },
        ajaxLog: function (XMLHttpRequest) {
            var headers = XMLHttpRequest.getAllResponseHeaders().split("\n");
            var founded = {};
            var status = false;
            //for(var i in headers) {
            //    if (headers[i].search("X-Wf-1-1-1-") >= 0) {
            //        founded[i] = headers[i];
            //        status = true;
            //    }
            //}

            if(status != false) {
                //$('#logs').show();
                var data;
                for(var k in founded) {
                    data = core.parseFirePHP(founded[k]);
                    var result = core.jsonParse(data);

                    if(typeof result[0] != 'undefined') {
                        $('#logs pre').prepend('<span class="label label-'+ result[0]['Type'].toLowerCase() +'">' + result[0]['Label'] + '</span>: ' + result[1] + '<br>');
                    }

                }

            }
            else
                $('#logs').hide();

        },
        parseFirePHP: function (data) {
            var start_pos = data.indexOf('|') + 1;
            var end_pos = data.indexOf('|',start_pos);
            return data.substring(start_pos,end_pos);
        },
        clearErrorLogs: function (form_id) {
            $('form#'+form_id).find('span.help-block').remove();
            $('form#'+ form_id).find('input').each(function () {
                //$(this).parent().removeClass('has-error');
                $(this).closest('.form-group').removeClass('has-error');
            });
        },
        initErrorLogs: function (errors,form) {
            core.clearErrorLogs(form);
            for(var i in errors) {
                if(i == "message_summary")
                    continue;
                if(i == "sql") {
                    var html = "";
                    for(var k in errors['sql']) {
                        html += '<span class="label label-danger">' + k + '</span>: ' + errors['sql'][k] + '<br>'
                    }
                    $('#logs pre').prepend(html);
                    continue;
                }
                if(isNaN(i)) {
                    var element = $('form#'+form+' #'+i);
                    var element_type = element.prop('nodeName');
                    if(element_type == "SELECT") {
                        //element.parent().parent().addClass('has-error');
                        element.parents('.form-group').addClass('has-error');
                        $('<span class="col-md-12 help-block form-error">'+ errors[i] +'</spanall>').insertAfter(element.parent());
                    }
                    else {
                        if($('form#'+form+' input#'+i).parent().hasClass('input-group')) {
                            //$('form#'+form+' input#'+i).parent().parent().addClass('has-error');
                            $('form#'+form+' input#'+i).parents('.form-group').addClass('has-error');
                            $('<span class="col-md-12 help-block form-error">'+ errors[i] +'</span>').insertAfter('form#'+form+' #input-group-'+i+".input-group");
                        }
                        else {
                            //$('form#'+form+' input#'+i).parent().addClass('has-error');
                            $('form#'+form+' input#'+i).parents('.form-group').addClass('has-error');
                            $('<span class="col-md-12 help-block form-error">'+ errors[i] +'</span>').insertAfter('form#'+form+' input#'+i);
                        }
                    }

                }
            }
        },
        summernote: function () {
            $('.summernote').summernote({
                height : 180,
                focus : true,
                tabsize : 2,
                onChange: function(contents, $editable) {
                    $('textarea#content').text(contents);
                }
            });
        },
        datatable: function(options){
            var buttonCommon = {
                exportOptions: {
                    format: {
                        body: function ( data, column, row ) {
                            return core.removeFromString(column,data);
                        }
                    }
                }
            };
            var table_options = {
                dataType: "json",
                bStateSave: true,
                "bProcessing": true,
                sAjaxSource: $(options.target).attr("data-url"),
                //dom: 'lTfgtip',
                "aLengthMenu": [ [5, 10, 20, -1], [5, 10, 20, lvar['all']] ],
                "iDisplayLength" : 5,
                retrieve: true,
                select: true,
                colReorder: true,
                oLanguage: {
                    "sInfo": lvar['dt_showing_info'],
                    sSearch: "",
                    "sInfoFiltered": " ("+ lvar['filtered'] +")",
                    sEmptyTable: lvar['empty_table'],
                    sInfoEmpty: lvar['no_entries_to_show'],
                    sZeroRecords: lvar['zero_records'],
                    sLengthMenu: "_MENU_",
                    "sProcessing": "loading data...",
                    "oPaginate": {
                        "sFirst":      "<i class='fa fa-step-backward'></i>",
                        "sLast":      "<i class='fa fa-step-forward'></i>",
                        "sNext":      "<i class='fa fa-forward'></i>",
                        "sPrevious":      "<i class='fa fa-backward'></i>"
                    }
                },
                "fnDrawCallback": function () {

                    $(options.target + ' input.checkbox-blue').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        radioClass: 'iradio_minimal-blue'
                    });

                    $('a[data-id="confirm"]').unbind();
                    core.nested();
                },
                "fnInitComplete": function(data,json) {
                    $(options.target + ' input.checkbox-blue').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        radioClass: 'iradio_minimal-blue'
                    });
                    if(options.checkbox === true) {
                        var checkAll = $(options.target + ' thead input:checkbox');
                        var checkboxes_table = checkAll.closest('table').parent();
                        var checkboxes;
                        if(checkboxes_table.hasClass("dataTables_scrollHeadInner")) {
                            checkboxes = checkboxes_table.parent().next().find(':checkbox');
                        }
                        else {
                            checkboxes = checkAll.closest('table').children("tbody").find(':checkbox');
                        }

                        checkAll.on('ifChecked ifUnchecked', function(event) {
                            if (event.type == 'ifChecked') {
                                checkboxes.iCheck('check');
                                checkboxes.closest("tbody tr").addClass("selected");
                            }
                            else {
                                checkboxes.iCheck('uncheck');
                                checkboxes.closest("tbody tr").removeClass("selected");
                            }
                        });

                        $('a[data-id="confirm"]').unbind();
                        $('a[data-id="confirm"]').on('click',function () {
                            core.confirm(this);
                        });


                        //checkboxes.on('ifChanged', function(event){
                        //    if(checkboxes.filter(':checked').length == checkboxes.length) {
                        //        checkAll.iCheck('check');
                        //    }
                        //    else {
                        //        checkAll.iCheck('uncheck');
                        //    }
                        //});
                    }

                    core.nested();
                },
                "fnServerData": function (sSource, aoData, fnCallback) {
                    if(typeof options.extra != 'undefined') {
                        if(options.extra == true) {
                            for(var i in options.fields) {
                                aoData.push( { "name": $(options.fields[i]).attr('name'), "value": $(options.fields[i]).val() } );
                            }
                        }
                    }
                    //$(options.target + "_filter").prepend('<div id='+ (options.target).replace('#','') +'"_processing" class="dataTables_processing" style="display: none;">Processing...</div>');
                    //$(options.target + "_processing").show();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: sSource,
                        data: aoData,
                        success: fnCallback,
                        complete: function (XMLHttpRequest, textStatus) {
                            core.ajaxLog(XMLHttpRequest);
                        }
                    });

                    if(typeof options.action_buttons != "undefined") {
                        for(var i in options.action_buttons) {
                            $(options.target + '_wrapper')
                                .parent()
                                .parent()
                                .parent()
                                .find('div.table-button-lists .'+options.action_buttons[i])
                                .addClass('disabled');
                        }
                    }
                    $(options.target + ' input.checkbox-blue').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        radioClass: 'iradio_minimal-blue'
                    });
                    if(detail_table == true) {
                        var detail_options = options.details;
                        for(var i in detail_options) {
                            core.emptyTable(detail_options[i]['target'],true);
                            core.datatable({
                                target : detail_options[i]['target'],
                                select_row: detail_options[i]['select_row'],
                                serverside: false,
                                buttons: detail_options[i]['buttons'],
                                actions: detail_options[i]['actions'],
                                action_buttons: detail_options[i]['action_buttons'],
                                scrollY: detail_options[i]['scrollY'],
                                scrollCollapse: detail_options[i]['scrollCollapse'],
                                paging: detail_options[i]['paging'],
                                checkbox: detail_options[i]['checkbox'],
                                chk_target: detail_options[i]['chk_target'],
                                sub: detail_options[i]['sub'],
                                sub_index: detail_options[i]['sub_index']
                            });
                            $(detail_options[i]['target'] + '_wrapper')
                                .parent()
                                .parent()
                                .find('div.table-button-lists .add')
                                .addClass('disabled');
                        }
                    }
                }
            };
            var detail_table = false;
            if(typeof options.detail != 'undefined') {
                detail_table = options.detail;
            }
            var paging = false;
            if(typeof options.paging != "undefined") {
                table_options['paging'] = options.paging;
                paging = options.paging;
            }

            if(paging == true) {
                if(typeof options.pagination != 'undefined')
                    table_options['sPaginationType'] = options.pagination;
            }

            if(typeof options.serverside != 'undefined')
                table_options["bServerSide"] = options.serverside;
            else
                table_options["bServerSide"] = true;

            if(options.buttons == true) {
                table_options['dom'] = '<"html5buttons"B>lTfgtip';
                table_options['buttons'] = [
                    $.extend( true, {}, buttonCommon, {
                        extend: 'colvis',
                        text:      '<i class="fa fa-eye" title="'+ lvar['visible_columns'] +'"></i>',
                        postfixButtons: [ 'colvisRestore' ]
                    }),
                    $.extend( true, {}, buttonCommon, {
                        extend: 'copyHtml5',
                        text:      '<i class="fa fa-files-o" title="'+ lvar['copy'] +'"></i>'
                    }),
                    $.extend( true, {}, buttonCommon, {
                        extend: 'excel',
                        download: 'open',
                        text:      '<i class="fa fa-file-excel-o" title="'+ lvar['export_excel'] +'"></i>',
                        extension: '.xlsx',
                        //title: $(options.target).closest('.window_top').text()
                    }),
                    $.extend( true, {}, buttonCommon, {
                        extend: 'pdf',
                        text:      '<i class="fa fa-file-pdf-o" title="'+ lvar['export_pdf'] +'"></i>',
                        download: 'open',
                        //title: $(options.target).closest('.window_top').text()
                    }),
                    $.extend( true, {}, buttonCommon, {
                        extend: 'print',
                        text:      '<i class="fa fa-print" title="'+ lvar['print'] +'"></i>',
                        autoPrint: false,
                        customize: function (win){
                            $(win.document.body).css('background','#fff');
                            $(win.document.body).css('font-size', '10px');
                        }
                    })
                ];
            }

            if(typeof options.scrollY != "undefined") {
                table_options['scrollY'] = options.scrollY;
            }
            if(typeof options.scrollCollapse != "undefined")
                table_options['scrollCollapse'] = options.scrollCollapse;

            var table = $(options.target).dataTable(table_options);

            if(detail_table == true) {
                var detail_options = options.details;
                for(var i in detail_options) {
                    core.datatable({
                        target : detail_options[i]['target'],
                        select_row: detail_options[i]['select_row'],
                        serverside: false,
                        buttons: detail_options[i]['buttons'],
                        actions: detail_options[i]['actions'],
                        action_buttons: detail_options[i]['action_buttons'],
                        scrollY: detail_options[i]['scrollY'],
                        scrollCollapse: detail_options[i]['scrollCollapse'],
                        paging: detail_options[i]['paging'],
                        checkbox: detail_options[i]['checkbox'],
                        chk_target: detail_options[i]['chk_target']
                    });
                }
            }

            if(options.select_row == true) {
                $(options.target+' tbody').unbind();
                $(options.target+' tbody').on( 'click', 'tr', function () {
                    if($(options.target+' tbody tr td:first-child').hasClass('dataTables_empty') == false) {
                        if ($(this).hasClass('selected')) {
                            $(this).removeClass('selected');
                            if(options.checkbox === true) {
                                $(this).find("input:checkbox").iCheck("uncheck");
                            }
                            if(options.actions == true) {
                                for(var i in options.action_buttons) {
                                    if(table.$('tr.selected').length == 0) {

                                        $(options.target + '_wrapper')
                                            .closest('.window_main')
                                            //.parent()
                                            //.parent()
                                            //.parent()
                                            .find('div.table-button-lists .'+options.action_buttons[i])
                                            .addClass('disabled');
                                    }
                                }
                            }
                            if(detail_table == true) {
                                for(var i in detail_options) {
                                    $(detail_options[i]['target'] + '_wrapper')
                                        .parent()
                                        .parent()
                                        .find('div.table-button-lists .add')
                                        .addClass('disabled');

                                    core.emptyTable(detail_options[i]['target'],true);
                                    core.datatable({
                                        target : detail_options[i]['target'],
                                        select_row: detail_options[i]['select_row'],
                                        serverside: false,
                                        buttons: detail_options[i]['buttons'],
                                        actions: detail_options[i]['actions'],
                                        action_buttons: detail_options[i]['action_buttons'],
                                        scrollY: detail_options[i]['scrollY'],
                                        scrollCollapse: detail_options[i]['scrollCollapse'],
                                        paging: detail_options[i]['paging'],
                                        checkbox: detail_options[i]['checkbox'],
                                        chk_target: detail_options[i]['chk_target'],
                                        sub: detail_options[i]['sub'],
                                        sub_index: detail_options[i]['sub_index']
                                    });
                                }
                            }
                        }
                        else {
                            //table.$('tr.selected').removeClass('selected');
                            $(this).addClass('selected');
                            if(options.checkbox === true) {
                                $(this).find("input:checkbox").iCheck("check");
                            }
                            if(options.actions == true) {
                                for(var i in options.action_buttons) {

                                    $(options.target + '_wrapper')
                                        .closest('.window_main')
                                        .find('div.table-button-lists .'+options.action_buttons[i])
                                        .removeClass('disabled');
                                }
                            }
                            if(detail_table == true) {
                                var selected_id = $(options.target + ' tbody tr.selected td:first-child span:first').text();
                            }
                            if(detail_table == true) {
                                if(selected_id != "") {
                                    for(var i in detail_options) {
                                        $(detail_options[i]['target'] + '_wrapper')
                                            .parent()
                                            .parent()
                                            .find('div.table-button-lists .add')
                                            .removeClass('disabled');
                                        core.emptyTable(detail_options[i]['target'],true);
                                        core.datatable({
                                            target : detail_options[i]['target'],
                                            url : detail_options[i]['url'] + '/' + selected_id,
                                            select_row: detail_options[i]['select_row'],
                                            serverside: detail_options[i]['serverside'],
                                            buttons: detail_options[i]['buttons'],
                                            actions: detail_options[i]['actions'],
                                            action_buttons: detail_options[i]['action_buttons'],
                                            scrollY: detail_options[i]['scrollY'],
                                            scrollCollapse: detail_options[i]['scrollCollapse'],
                                            paging: detail_options[i]['paging'],
                                            checkbox: detail_options[i]['checkbox'],
                                            chk_target: detail_options[i]['chk_target'],
                                            sub: detail_options[i]['sub'],
                                            sub_index: detail_options[i]['sub_index']
                                        });
                                    }
                                }
                            }
                        }
                    }
                } );
            }
            $('.dataTables_filter input').attr("placeholder", lvar['search']);

        },
        /**
         * @param target => table id
         * @param destroy => true|false clear data and destroy datatable
         */
        emptyTable: function (target,destroy) {
            var table = $(target).DataTable();
            table.clear();

            if(typeof destroy == 'undefined') {
                destroy = false;
            }

            if(destroy == true) {
                table.destroy();
            }
        },
        addRow: function (options) {
            var table = $(options.target).DataTable();
            if(options.clear == true) {
                table.clear().draw();
            }
            for(var i in options.data) {
                var pushed = {};
                for(var k in options.data[i]) {
                    pushed[k] = options.data[i][k];
                }
                table.row.add(pushed).draw( false );
            }
        },
        /**
         * @param options
         * target => table id
         * url => table get data from remote url
         */
        tableRefresh: function (options) {
            var table = $(options.target).dataTable();
            table.fnReloadAjax(options.url);
        },
        select: function (options) {
            core.select_result = {};

            var ajax_options = {
                ajax : {
                    url     : options.url,
                    type    : 'POST',
                    dataType: 'json',
                    cache   : false,
                    data    : {
                        q: '{{{q}}}'
                    },
                    beforeSend: function (xhr, opts) {
                        var element = $('[data-id="'+ options.id.split("#").pop() +'"]').next().children('.status');
                        if($(options.id).is(':disabled')) {
                            xhr.abort ();
                        }
                        if (this.options.data.q.length < 3) {
                            element.html(lvar['enter_3_character']);
                            xhr.abort ();
                        }
                        else {
                            element.html(lvar['searching']);
                        }
                    },
                    complete: function (XMLHttpRequest, textStatus) {
                        core.ajaxLog(XMLHttpRequest);
                    }
                },
                log: 1,
                locale: {
                    statusSearching: lvar['searching'],
                    emptyTitle: 'Ara',
                    errorText: 'Hata'
                },
                requestDelay : 750,
                cache : false,
                preprocessData: function (data) {
                    var i, l = data.results.length, array = [];
                    if (l) {
                        for (i = 0; i < l; i++) {
                            array.push($.extend(true, data[i], {
                                text : data.results[i].text,
                                value: data.results[i].id
                            }));
                            core.select_result[data.results[i].id] = data.results[i];
                        }
                    }
                    return array;
                }
                //preserveSelected: false
            };

            var selectpicker_options = {
                showTick: true,
                tickIcon:'glyphicon-ok',
                liveSearch: true,
                size: 10,
                selectOnTab: true
            };

            $(options.id).selectpicker(selectpicker_options).ajaxSelectPicker(ajax_options);

            $(options.id).trigger('change');

            $(options.id).selectpicker('setStyle', 'btn-sm', 'add');
            if(typeof options.autoclose != "undefined") {
                if(options.autoclose == true) {
                    $(options.id).change(function () {
                        if(typeof $(options.id).attr('multiple') == "undefined") {
                            if(($(this).val() != "") && ($(this).val() != "0")) {
                                if(core.last_selected[options.id] != $(this).val()) {
                                    core.last_selected[options.id] = $(this).val();
                                    $(this).selectpicker('toggle');
                                }
                            }
                        }
                    });
                }
            }
        },
        addOption: function (options) {
            //@TODO: Varmı yokmu kontrol et
            var select_picker = true;
            if(typeof options['selectpicker'] != "undefined") {
                select_picker = options.selectpicker;
            }

            $(options.target).append($('<option>', {
                value: options.value,
                text: options.text
            }));
            if(typeof options.selected != 'undefined') {

                if(select_picker == true)
                    $(options.target).selectpicker('val',options.selected);
                else
                    $(options.target).val(options.selected);

                $(options.target).change();
                //if(select_picker == true)
                //    $(options.target).selectpicker('toggle');
            }
            if(select_picker == true)
                $(options.target).selectpicker('refresh');
        },
        removeOption: function (options) {
            $(options.target).find('[value='+ options.value +']').remove();
            $(options.target).selectpicker('refresh');
        },
        removeAllOptions: function (options) {
            $(options.target).html('');
            if((typeof options.selectpicker == "undefined") && (options.selectpicker != false))
                $(options.target).selectpicker('refresh');
        },
        datetimepicker: function (target) {
            $(target).datetimepicker({
                format: "DD-MM-YYYY HH:mm:ss",
                useCurrent: true,
                sideBySide: true
            });
        },
        datepicker: function (target) {
            $(target).datetimepicker({
                format: "DD-MM-YYYY",
                useCurrent: true
            });
        },
        inputInits: function () {
            $('[data-dismiss="modal"]').click(function () {
                var form_id = $(this).closest('.modal').find('form').attr('id');
                if(typeof form_id != "undefined")
                    core.formReset(form_id);
            });
            $(".modal").draggable({
                handle: ".modal-header"
            });
            $(".make-switch").bootstrapSwitch();
            $('.multiselect').multiSelect({
                cssClass: "col-md-12",
                selectableOptgroup: true,
                selectableHeader: "<div class='ms-header'><strong>"+ $('.multiselect').attr('data-selectable') +"</strong></div>",
                selectionHeader: "<div class='ms-header'><strong>"+ $('.multiselect').attr('data-selection') +"</strong></div>"
            });
            $('input.checkbox-blue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $('input.checkbox-aero').iCheck({
                checkboxClass: 'icheckbox_minimal-aero',
                radioClass: 'iradio_minimal-aero'
            });
            $('input[value="unlimited"]').on('ifChanged', function(event){
                var objchk = $(this).attr('name');
                if ($(this).is(':checked')) {
                    $('#' + objchk).attr('disabled', 'disabled');
                }
                else {
                    $('#' + objchk).removeAttr('disabled');
                }
            });
            $('button[data-type="password-generate"]').unbind();
            $('button[data-type="password-generate"]').on('click', function () {
                core.passgen(this);
            });
            core.showPasswd(this);
            $('.multiple-select').selectMultiple();
            $('#segment-add').click(function(){
                var defaultDocument = $('#add_hidden_segments').val();
                $('#hidden_segments').append("<option>"+ defaultDocument +"</option>");
                $('#add_hidden_segments').val('');

            });
            $('#segment-remove').click(function(){
                $('#hidden_segments option:selected').each( function() {
                    $(this).remove();
                });
            });
            $('#allow_ip_address').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('input[id="add_to_allow_list"]').prop('disabled', false);
                    $('select[id="allowed_ip_addresses"]').prop('disabled', false);

                    $('input[id="add_to_block_list"]').prop('disabled', true);
                    $('select[id="blocked_ip_addresses"]').prop('disabled', true);
                    $('input[id="block_ip_address"]').iCheck('uncheck');
                }
                else {
                    $('#add_to_allow_list').prop('disabled', true);
                    $('#allowed_ip_addresses').prop('disabled', true);
                }
            });
            $('#block_ip_address').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('input[id="add_to_allow_list"]').prop('disabled', true);
                    $('select[id="allowed_ip_addresses"]').prop('disabled', true);

                    $('input[id="add_to_block_list"]').prop('disabled', false);
                    $('select[id="blocked_ip_addresses"]').prop('disabled', false);
                    $('input[id="allow_ip_address"]').iCheck('uncheck');
                }
                else {
                    $('#add_to_block_list').prop('disabled', true);
                    $('#blocked_ip_addresses').prop('disabled', true);
                }
            });
            $('#add-ip-allow').unbind();
            $('#add-ip-allow').click(function(e){
                var allowed_ip = $('#add_to_allow_list').val();
                if(core.validateIp(allowed_ip) == true) {
                    $('select#allowed_ip_addresses').append("<option value='"+ allowed_ip +"'>"+ allowed_ip +"</option>");
                    $('#add_to_allow_list').val('');
                    $('select#allowed_ip_addresses').parent().removeClass('has-error');
                }
                else {
                    $('select#allowed_ip_addresses').parent().addClass('has-error');
                    toastr["error"](lvar['enter_valid_ip'],lvar['error']);
                }
            });

            $('#remove-ip-allow').click(function(e){
                $("select#allowed_ip_addresses").find('option:selected').remove().end();
            });

            $('#add-ip-block').unbind();
            $('#add-ip-block').click(function(e){
                var blocked_ip = $('#add_to_block_list').val();
                if(core.validateIp(blocked_ip) == true) {
                    $('select#blocked_ip_addresses').append("<option value='"+ blocked_ip +"'>"+ blocked_ip +"</option>");
                    $('#add_to_block_list').val('');
                }
                else {
                    $('select#blocked_ip_addresses').parent().addClass('has-error');
                    toastr["error"](lvar['enter_valid_ip'],lvar['error']);
                }

            });

            $('#remove-ip-block').click(function(e){
                $("select#blocked_ip_addresses").find('option:selected').remove().end();
            });

            $("form").submit(function(e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                //return true;
            });
            //core.validate('form');
        },
        selectpicker: function (element) {
            $(element).selectpicker({
                showTick: true,
                tickIcon:'glyphicon-ok',
                size: 10
            });
            $(element).selectpicker('setStyle', 'btn-sm', 'add');
            $(element).change(function () {
                if(($(this).val() != "") && ($(this).val() != null)) {
                    if(typeof $(this).attr('multiple') == "undefined") {
                        $(this).selectpicker('toggle');
                    }
                }
            });
        },
        selectAll: function (element) {
            var checkAll = $(element);
            var checkboxes_table = $(element).closest('table').parent();
            var checkboxes;
            if(checkboxes_table.hasClass("dataTables_scrollHeadInner")) {
                checkboxes = checkboxes_table.parent().next().find(':checkbox');
            }
            else {
                checkboxes = $(element).closest('table').find(':checkbox');
            }

            checkAll.on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    checkboxes.iCheck('check');
                    checkboxes.closest("tbody tr").addClass("selected");
                }
                else {
                    checkboxes.iCheck('uncheck');
                    checkboxes.closest("tbody tr").removeClass("selected");
                }
            });

            checkboxes.on('ifChanged', function(event){
                if(checkboxes.filter(':checked').length == checkboxes.length) {
                    checkAll.prop('checked', 'checked');
                } else {
                    checkAll.removeProp('checked');
                }
                checkAll.iCheck('update');
            });

        },
        setWidth: function (options) {
            var target = '.window_main > div#'+options.target;
            if($(document).width() > 1024) {
                $(target).closest('.window_inner').parent().css("min-width",$(target).attr('data-width'));
                $(target).closest('.window_inner').parent().css("min-height",$(target).attr('data-height'));
            }
            else {
                $(target).closest('.window_inner').parent().css('min-width','100%');
                $(target).closest('.window_inner').parent().css('min-height','100%');
            }
        },
        ajaxTabs: function (target) {
            $(target + ' a').unbind();
            $(target + ' a').on('click',function (e) {
                e.preventDefault();

                var parent = $(href).closest(".tabbable-panel").attr('id');
                $("#"+parent + " .tab-pane").each(function() {
                    if($(this).attr('id') != href)
                        $(this).html('');
                });
                var url = $(this).attr("data-url");
                var href = this.hash;
                var pane = $(this);
                $(this).tab('show');
                $(href).html(loading);

                setTimeout(function(){
                    $(href).load(url,function(result){
                        pane.tab('show');
                    });

                }, 100);
            });

            var first_tab = $(target + ' li:first-child a:first-child');
            $(first_tab.attr('href')).html(loading);
            $(first_tab.attr('href')).load(first_tab.attr('data-url'),function(result){
                $(first_tab).tab('show');
            });
        },
        setDefault: function (options) {
            var select_picker = true;
            if(typeof options.selectpicker != "undefined") {
                if(options.selectpicker == false)
                    select_picker = false
            }
            if(select_picker == true) {
                $(options.target).selectpicker('val',options.value);
                $(options.target).selectpicker('refresh');
            }
            else {
                $(options.target).val(options.value).change();
                //$(options.target+' option[value="' + options.value + '"]').prop('selected',true);
            }
        },
        formReset: function (target) {
            document.getElementById(target).reset();
            $("form#"+target).find('select.selectpicker').each(function() {
                var attr = $(this).attr('data-toggle');
                if(typeof attr != "undefined") {
                    $(this).selectpicker('val','');
                }
                else {
                    core.removeAllOptions({
                        target: $(this)
                    });
                }
                $(this).selectpicker('refresh');
            });

            $('input.checkbox').iCheck('destroy');
            $('input.checkbox').prop('checked',false);
            $('input.checkbox').iCheck({
                checkboxClass: 'icheckbox_minimal-grey',
                radioClass: 'iradio_minimal-grey'
            });
            core.clearErrorLogs(target);
        },
        removeFromString: function (column,element) {
            var result = "";
            if(column === 0) {
                result = element.replace(/<(\w+)[^>]*>.*<\/\1>/gi,'');
            }
            else {
                if((result != null) || (result != "") || (typeof result != "undefined")) {
                    result = $.parseHTML(element);
                    result = $(result).text();
                }
            }
            return result;
        },
        confirm: function (element) {
            bootbox.dialog({
                title: lvar['confirm'],
                message: lvar['are_you_sure'],
                buttons: {
                    cancel: {
                        label: lvar['cancel'],
                        className: "btn btn-default btn-sm",
                        callback: function() {
                        }
                    },
                    submit: {
                        label: lvar['submit'],
                        className: "btn btn-primary btn-sm",
                        callback: function() {
                            core.delete({
                                method: 'POST',
                                url: $(element).attr('data-url'),
                                data: {},
                                table: '#'+$(element).closest('table').attr('id'),
                                table_url: $(element).attr('data-url')
                            });
                        }
                    }
                }
            });
        },
        delete: function (options) {
            var method = 'GET';
            if(typeof options.method != 'undefined') {
                method = options.method;
            }
            var data = {};
            if(typeof options.data != 'undefined') {
                data = options.data;
            }
            var result = core.ajaxRequest({
                method: method,
                url: options.url,
                data: data
            },false);

            toastr[result['type']](result['messages'],result['title']);
            if(typeof options.table != 'undefined') {
                core.tableRefresh({
                    target : options.table,
                    url: options.table_url
                });
            }
        },
        deleteItem: function (options) {
            if(typeof options.parameters != 'undefined') {
                for(var i in options.parameters) {
                    options.url = options.url.replace('?'+i,options.parameters[i]);
                }
            }
            bootbox.dialog({
                title: lvar['confirm'],
                message: lvar['are_you_sure'],
                buttons: {
                    cancel: {
                        label: lvar['cancel'],
                        className: "btn btn-default btn-sm",
                        callback: function() {
                        }
                    },
                    submit: {
                        label: lvar['submit'],
                        className: "btn btn-primary btn-sm",
                        callback: function() {
                            core.delete(options);
                        }
                    }
                }
            });

        },
        formPost: function (options) {
            var url = options.url;
            if(typeof options.parameters != 'undefined') {
                for(var i in options.parameters) {
                    url = url.replace('?'+i,options.parameters[i]);
                }
            }
            var result = core.ajaxRequest({
                method: 'POST',
                url: url,
                data: options.data,
                form: options.form
            },options.message);
            if(result['type'] == "success") {
                if(typeof options.table != 'undefined') {
                    core.tableRefresh({
                        target : options.table,
                        url: options.table_url
                    });
                }

                if(typeof options.form != 'undefined') {
                    core.formReset(options.form);
                }
                if(typeof options.modal != 'undefined') {
                    $(options.modal).modal('hide');
                }
            }
            return result;
        },
        formSerialize: function (form) {
            return $(form).serializeArray();
        },
        disableEnableElements: function (options) {
            $('#'+ options.form + ' input').each(function () {
                $(this).prop('disabled',options.disable);
            });
            $('#'+ options.form + ' select').each(function () {
                $(this).prop('disabled',options.disable);
                if($(this).hasClass('selectpicker'))
                    $(this).selectpicker('refresh');
            });
        },
        identity: function (options) {
            core.ajaxRequest({
                method: 'GET',
                url:'/system/identity/'+options.user,
            },false);
            return result;
        },
        createModal: function (options) {
            var modal_size = '';
            if(typeof options.size != "undefined")
                modal_size = options.size;

            var html = '<div id="'+ options.id +'" class="modal fade '+ modal_size +'" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">'+
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
                '<h3>'+ options.title +'</h3>'+
                '</div>'+
                '<div class="modal-body">'+
                '</div>'+
                '<div class="modal-footer">'+
                '<button type="button" data-dismiss="modal" class="btn btn-default btn-sm">'+ lvar['close'] +'</button>'+
                '<button type="button" class="btn btn-primary btn-sm" id="submit">'+ lvar['submit'] +'</button>'+
                '</div>'+
                '</div>';

            $(options.target).append(html);
        },
        validateIp: function (value) {
            var expression = /((^\s*((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$))/;
            return expression.test(value);
        },
        validate: function () {
            $.formUtils.addValidator({
                name : 'ipv4',
                validatorFunction : function(value, $el, config, language, $form) {
                    if (typeof value == "undefined" || value.length == 0 || value.length != 11) {
                        return false;
                    }
                    return core.ipv4(value);

                },
                errorMessage : lvar['enter_valid_ip'],
                errorMessageKey: 'enter_valid_ipv4'
            });
            $.formUtils.addValidator({
                name : 'ipv6',
                validatorFunction : function(value, $el, config, language, $form) {
                    if (typeof value == "undefined" || value.length == 0 || value.length != 11) {
                        return false;
                    }
                    return core.ipv6(value);
                },
                errorMessage : lvar['enter_valid_ip'],
                errorMessageKey: 'enter_valid_ipv6'
            });

            $.validate({
                modules : 'date,sepa,security'
            });
        },
        nested: function () {
            $('a[data-id="nested"]').unbind();
            $('a[data-id="nested"]').on('click', function () {
                core.createWindow(this);
            });
            $('a[data-id="confirm"]').unbind();
            $('a[data-id="confirm"]').on('click',function () {
                core.confirm(this);
            });
        },
        createWindow: function (element) {
            var win_id = $(element).attr('href');
            var win = '#' + win_id;
            var name = $(element).closest('tr').children("td:nth-child(2)").text();
            if(name != "")
                name = ' (' + name + ')';
            if($(win).length < 1) {
                JQD.createWindow({
                    id: win_id,
                    name: $(element).text() + name,
                    icon: '',
                    has_child: "false"
                });
            }
            var url = $(element).attr('href');

            if ($(win).is(':hidden')) {
                JQD.util.windowFlat();
                JQD.showWindow(win);
                $(win).addClass('window_stack');
                if($(win +' .window_main').text() == "") {
                    $(win + ' .window_main').html(loading);
                    $(win + ' .window_main').load(url,function(el){
                        core.inputInits();
                    });
                }
                $(win +' a.window_close').attr('id',$(this).attr('href'));
                $(win).show('fast');
            }

            $(win).on('click', 'a.window_refresh', function() {
                $(win +' .window_main').html(loading);
                $(win + ' .window_main').load(win_id,function(el){
                    core.inputInits();
                    $(win +' a.window_close').attr('id',win_id);
                });
                $(win+' .window_top #window_name').text($(win+' .window_top').attr('data-original-title'));
            });
        },
        systemInit: function () {
            $('input[name="php_support"]').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('#php_version').prop('disabled', false);
                    $('input[id="php_safe_mod').iCheck('enable');
                }
                else {
                    $('#php_version').prop('disabled', true);
                    $('input[id="php_safe_mod').iCheck('disable');
                }
            });
            $('input[name="asp_net_support"]').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('#aspnet_version').prop('disabled', false);
                    $('#asp_net_version').prop('disabled', false);
                }
                else {
                    $('#aspnet_version').prop('disabled', true);
                    $('#asp_net_version').prop('disabled', true);
                }
            });
            $('input[name="web_statistics_support"]').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('select[name="web_statistics_type"]').prop('disabled', false);
                }
                else {
                    $('select[name="web_statistics_type"]').prop('disabled', true);
                }
            });
            $('input[name="dedicated_pool"]').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('input[id="max_cpu_usage').prop('disabled', false);
                }
                else {
                    $('input[id="max_cpu_usage').prop('disabled', true);
                }
            });
            $('input[name="unlimited"]').on('ifChecked ifUnchecked', function(event) {
                var objchk = $(this).attr('name');
                if (event.type == 'ifChecked') {
                    $('#' + objchk).attr('disabled','disabled');
                }
                else {
                    $('#' + objchk).removeAttr('disabled');
                }
            });
            $('input[name="select-all-limits"]').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('input[value="unlimited').iCheck('check');
                    $('.limits').attr('disabled', 'disabled');
                }
                else {
                    $('input[value="unlimited').iCheck('uncheck');
                    $('.limits').removeAttr('disabled');
                }
            });
            $('input[name="select-all-perms"]').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('input[value="allowed').iCheck('check');
                }
                else {
                    $('input[value="allowed').iCheck('uncheck');
                }
            });

        },
        clearUnlimited: function () {
            $('.limits').each( function() {
                if(this.value == "unlimited") {
                    this.value = "";
                }
            });
        },
        submitForm: function (object) {
            $(object).prop('disabled',true);
            $(object).children("i").removeClass("fa fa-check-circle");
            $(object).children("i").addClass("fa fa-spinner fa-spin");
            $(object).closest('form').find('select').each(function () {
                if(($(this).hasClass('multiple-select') == false) && (($(this).hasClass('multiselect') == false))) {
                    if(typeof $(this).attr('multiple') != "undefined") {
                        var select_id = $(this).attr('id');
                        for (var i = 0; i < document.getElementById(select_id).options.length; i++) {
                            document.getElementById(select_id).options[i].selected = true;
                        }
                    }

                }
            });

            var result = core.ajaxRequest({
                form: $(object).closest('form').attr('id'),
                method: $(object).closest('form').attr('method'),
                url: $(object).closest('form').attr('action'),
                data: $(object).closest('form').serialize()
            },true);
            $(object).children("i").removeClass("fa fa-spinner fa-spin");
            $(object).children("i").addClass("fa fa-check-circle");
            $(object).prop('disabled',false);
        },
        multipleProcess: function () {
            $('a[data-type="multiple"]').unbind();
            $('a[data-type="multiple"]').on('click', function () {
                var element = this;
                bootbox.dialog({
                    title: lvar['confirm'],
                    message: lvar['are_you_sure'],
                    buttons: {
                        cancel: {
                            label: lvar['cancel'],
                            className: "btn btn-default btn-sm",
                            callback: function() {
                            }
                        },
                        submit: {
                            label: lvar['submit'],
                            className: "btn btn-primary btn-sm",
                            callback: function() {
                                $(element).addClass('disabled');
                                var default_class =  $(element).children("i").attr('class');
                                $(element).children("i").removeClass(default_class);
                                $(element).children("i").addClass("fa fa-spinner fa-spin");

                                var ids = $('input:checkbox[name="'+ $(element).attr('data-target') +'[]"]:checked').map(function () {
                                    return this.value;
                                }).get();
                                var loop = false;
                                if(typeof $(element).attr('data-single') != "undefined")
                                    loop = true;

                                if(loop == false) {
                                    for(var i=0; i < ids.length; i++) {
                                        var data = {};
                                        data['action'] = $(element).attr('data-action');
                                        data['id'] = ids[i];
                                        core.ajaxRequest({
                                            method: 'POST',
                                            url: $(element).attr('data-url'),
                                            data: data,
                                            table: '#'+$(element).closest('div.row').find('table').attr('id'),
                                            table_url: $(element).closest('div.row').find('table').attr('data-url')
                                        },true);
                                    }
                                }
                                else {
                                    var data_loop = {};
                                    data_loop['id'] = ids;
                                    data_loop['action'] = $(element).attr('data-action');
                                    core.ajaxRequest({
                                        method: 'POST',
                                        url: $(element).attr('data-url'),
                                        data: data_loop,
                                        table: '#'+$(element).closest('div.row').find('table').attr('id'),
                                        table_url: $(element).closest('div.row').find('table').attr('data-url')
                                    },true);
                                }
                                $(element).children("i").removeClass("fa fa-spinner fa-spin");
                                $(element).children("i").addClass(default_class);
                            }
                        }
                    }
                });
            });
        },
        passgen: function (element) {
            var passwd = '';
            var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for (var i=1; i<8; i++) {
                var c = Math.floor(Math.random()*chars.length + 1);
                passwd += chars.charAt(c)
            }
            $(element).closest(".form-group").find('input').val(passwd);
            $(element).closest(".form-group").next().find("input#confirm-password").val(passwd);
            //$('#' + input2).val(passwd);
        },
        showPasswd: function () {
            var clicked_passwd = true;
            $('button[data-type="show-passwd"]').click(function (){
                if (clicked_passwd) {
                    $(this).parent().prev().prop("type", "text");
                    clicked_passwd = false;
                }
                else {
                    $(this).parent().prev().prop("type", "password");
                    clicked_passwd = true;
                }
            });
        },
        changeServicePlan: function () {
            $("#template_select").change(function () {
                if($("#template_select").val() != 0) {
                    $.ajax({
                        url: $("#template_select").attr('data-url'),
                        data: "template="+$("#template_select").val(),
                        type: 'POST',
                        'success': function (result) {
                            var obj = JSON.parse(result);
                            var limitItems = {
                                client_limit: "clients",
                                domain_limit: "domain",
                                disk_limit: "disk",
                                traffic_limit: "traffic",
                                subdomain_limit: "subdomain",
                                domain_alias_limit: "domain_alias",
                                email_box_limit: "email_box",
                                email_box_size_limit: "email_box_size",
                                email_forward_limit: "email_forward",
                                email_groups_limit: "email_groups",
                                email_autoresponder_limit: "email_autoresponder",
                                email_lists_limit: "email_list",
                                template_limit: "template",
                                postgresql_limit: "postgresql",
                                mssql_limit: "mssql",
                                mysql_limit: "mysql",
                                database_limit: "database",
                                database_size_limit: "database_size",
                                expire_time_limit: "expire_time_limit"};
                            for (var i in limitItems) {
                                if (obj[i] != "unlimited") {
                                    $('input[name="' + limitItems[i] + '"]').iCheck('uncheck');
                                    $('#' + limitItems[i] + '').removeAttr('disabled').prop('value', obj[i]);
                                }
                                else {
                                    $('input[name="' + limitItems[i] + '"]').iCheck('check');
                                    $('#' + limitItems[i] + '').attr('disabled', 'disabled').prop('value', '');
                                }
                            }
                            var permItems = {
                                access_panel_perm: 'access_panel',
                                man_client: 'access_client',
                                man_domain: 'access_domain',
                                man_subdomain: 'access_subdomain',
                                man_email_alias: 'access_email_alias',
                                man_domain_alias: 'access_domain_alias',
                                man_hosting_settings: 'access_hosting_settings',
                                man_task: 'access_task',
                                man_dns: 'access_dns',
                                man_statistics: 'access_statistics',
                                man_mailing_lists: 'access_mailing_lists',
                                man_spam_filters: 'access_spam_filters',
                                man_backup_to_server: 'access_backup_to_server',
                                man_backup_to_ftp: 'access_backup_to_ftp',
                                man_database: 'access_database',
                                man_ssl: 'access_ssl',
                                man_ftp_user: 'access_ftp_user',
                                man_email: 'access_email',
                                man_protect_dir: 'access_protect_dir',
                                man_template: 'access_template'};
                            for (var j in permItems) {
                                if (obj[j] != "allowed") {
                                    $('input[name="' + permItems[j] + '"]').iCheck('uncheck');
                                }
                                else {
                                    $('input[name="' + permItems[j] + '"]').iCheck('check');
                                }
                            }
                            if (obj.dom_hosting_type != null) {
                                var val = obj.dom_hosting_type;
                                $('#hosting_settings').val(val);
                            }
                            if (obj.dom_php != "false") {
                                $('input[name="php_support"]').iCheck('check');
                                $('#php_version').removeAttr('disabled').val(obj.dom_php_version);

                                $('input[name="php_safe_mode"]').iCheck('enable');

                                if (obj.dom_php_safe_mode != "false") {
                                    $('input[name="php_safe_mode"]').iCheck('check');
                                }
                            }
                            else {
                                $('input[name="php_support"]').iCheck('uncheck');
                                $('#php_version').removeAttr('disabled').val(obj['dom_php_version']);
                                $('input[name="php_safe_mode"]').iCheck('disable');
                            }
                            if (obj.dom_asp != "false") {
                                $('input[name="asp_support"]').iCheck('check');
                            }
                            else {
                                $('input[name="asp_support"]').iCheck('uncheck');
                            }
                            if (obj.dom_aspnet != "false") {
                                $('input[name="asp_net_support"]').iCheck('check');
                                $('#asp_net_version').removeAttr('disabled').val(obj.dom_aspnet_version);
                            }
                            else {
                                $('input[name="asp_net_support"]').iCheck('uncheck');
                            }
                            if (obj.dom_cgi != "false") {
                                $('input[name="cgi_support"]').iCheck('check');
                            }
                            else {
                                $('input[name="cgi_support"]').iCheck('uncheck');
                            }
                            if (obj.dom_perl != "false") {
                                $('input[name="perl_support"]').iCheck('check');
                            }
                            else {
                                $('input[name="perl_support"]').iCheck('uncheck');
                            }
                            if (obj.dom_python != "false") {
                                $('input[name="python_support"]').iCheck('check');
                            }
                            else {
                                $('input[name="python_support"]').iCheck('uncheck');

                            }
                            if (obj.dom_web_statistics != "false") {
                                $('input[name="web_statistics_support"]').iCheck('check');
                                $('#web_statistics_type').removeAttr('disabled').val(obj.dom_web_statistics_type);
                            }
                            else {
                                $('input[name="web_statistics_support"]').iCheck('uncheck');

                            }
                            if (obj.dom_custom_error != "false") {
                                $('input[name="custom_error_pages"]').iCheck('check');
                            }
                            else {
                                $('input[name="custom_error_pages"]').iCheck('uncheck');
                            }
                            if (obj.dom_dedicated_pools != "false") {
                                $('input[name="dedicated_pool"]').iCheck('check');
                                $('#max_cpu_usage').removeAttr('disabled').prop('value', obj.dom_max_cpu_usage);
                            }
                            else {
                                $('input[name="dedicated_pool"]').iCheck('uncheck');
                            }
                            if(obj.overuse == "true") {
                                $('#overuse').iCheck('check');
                            }
                            else {
                                $('#overuse').iCheck('uncheck');
                            }
                        }
                    });
                }
            });
        },
        changeDnsType: function () {
            $('#record_type').on('change', function () {
                $("#ns_record_block_name").css('display', (this.value == 'NS') ? 'block' : 'none');
                $("#ns_record_block_ns").css('display', (this.value == 'NS') ? 'block' : 'none');
                $("#a_record_block_name").css('display', (this.value == 'A') ? 'block' : 'none');
                $("#a_record_block_ip").css('display', (this.value == 'A') ? 'block' : 'none');
                $("#cname_record_block_name").css('display', (this.value == 'CNAME') ? 'block' : 'none');
                $("#cname_record_block_cname").css('display', (this.value == 'CNAME') ? 'block' : 'none');
                $("#mx_record_block").css('display', (this.value == 'MX') ? 'block' : 'none');
                $("#mx_record_block_name").css('display', (this.value == 'MX') ? 'block' : 'none');
                $("#mx_record_block_priority").css('display', (this.value == 'MX') ? 'block' : 'none');
                $("#ptr_record_host").css('display', (this.value == 'PTR') ? 'block' : 'none');
                $("#ptr_record_block_name").css('display', (this.value == 'PTR') ? 'block' : 'none');
                $("#txt_record_block").css('display', (this.value == 'TXT') ? 'block' : 'none');
                $("#txt_record_block_name").css('display', (this.value == 'TXT') ? 'block' : 'none');
                $(".srv_record_block").css('display', (this.value == 'SRV') ? 'block' : 'none');
            });
        },
        ajaxFilePost: function (object) {
            var obj;
            $(object).prop('disabled',true);
            $(object).children("i").removeClass("fa fa-check-circle");
            $(object).children("i").addClass("fa fa-spinner fa-spin");

            var form = $(object).closest('form');
            var form_id = $(object).closest('form').attr('id');

            $.ajax({
                url: $(object).closest('form').attr('action'),
                type: "POST",
                data: new FormData(form[0]),
                processData: false,
                contentType: false,
                success: function (obj) {
                    var result = core.jsonParse(obj);
                    if (result['type'] == 'success') {
                        core.clearErrorLogs(form_id);
                        toastr[result['type']](result['messages'],result['title']);
                    }
                    else {
                        core.initErrorLogs(result['errors'],form_id);
                        var err_message = result['messages'];
                        if(typeof result['errors']['content'] != "undefined") {
                            err_message = result['errors']['content'];
                        }
                        toastr[result['type']](err_message,result['title']);
                    }
                    cookie = result['cookies'];
                    if(result['login'] == false) {
                        if($('#login-container').length !== 1) {
                            JQD.showLogin();
                        }
                    }
                },
                error: function () {
                    toastr['error']('An error has occured','Request error');
                },
                complete: function (XMLHttpRequest, textStatus) {
                    core.ajaxLog(XMLHttpRequest);
                }
            });


            $(object).children("i").removeClass("fa fa-spinner fa-spin");
            $(object).children("i").addClass("fa fa-check-circle");
            $(object).prop('disabled',false);
        },
        networkFormat: function () {
            var state = false;
            $('#network-format').on('ifChecked ifUnchecked', function(event) {
                if (event.type == 'ifChecked') {
                    $('#netmask_cidr').prop('disabled', false);
                    $('#netmask_cidr').show();
                    $('#netmask').prop('disabled', true);
                    $('#netmask').hide();
                    state = false;
                }
                else {
                    $('#netmask_cidr').prop('disabled', true);
                    $('#netmask_cidr').hide();
                    $('#netmask').prop('disabled', false);
                    $('#netmask').show();
                    state = true;
                }
            });

            $("#netmask").change(function () {
                document.getElementById('netmask_cidr').value=$("#netmask").val() ;
            });
            $("#netmask_cidr").change(function () {
                document.getElementById('netmask').value=$("#netmask_cidr").val() ;
            });
        }
    };
}();
core.init();