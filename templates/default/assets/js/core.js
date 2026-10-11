var cookie;
var refreshcon;
var spinner = "<div class=\"sk-spinner sk-spinner-circle\"><div class=\"sk-circle1 sk-circle\"></div><div class=\"sk-circle2 sk-circle\"></div><div class=\"sk-circle3 sk-circle\"></div><div class=\"sk-circle4 sk-circle\"></div><div class=\"sk-circle5 sk-circle\"></div><div class=\"sk-circle6 sk-circle\"></div><div class=\"sk-circle7 sk-circle\"></div><div class=\"sk-circle8 sk-circle\"></div><div class=\"sk-circle9 sk-circle\"></div><div class=\"sk-circle10 sk-circle\"></div><div class=\"sk-circle11 sk-circle\"></div><div class=\"sk-circle12 sk-circle\"></div></div>";

function secondsToStr(temp) {
    function numberEnding(number) {
        return (number > 1) ? 's' : '';
    }

    var years = Math.floor(temp / 31536000);
    if (years) {
        return years + ' year' + numberEnding(years);
    }
    var days = Math.floor((temp %= 31536000) / 86400);
    if (days) {
        return days + ' day' + numberEnding(days);
    }
    var hours = Math.floor((temp %= 86400) / 3600);
    if (hours) {
        return hours + ' hour' + numberEnding(hours);
    }
    var minutes = Math.floor((temp %= 3600) / 60);
    if (minutes) {
        return minutes + ' minute' + numberEnding(minutes);
    }
    var seconds = temp % 60;
    return seconds + ' second' + numberEnding(seconds);
}
String.prototype.sprintf = function() {
    var formatted = this;
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{'+i+'\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};
String.prototype.trimLeft = function (charlist) {
    if (charlist === undefined)
        charlist = "\s";
    return this.replace(new RegExp("^[" + charlist + "]+"), "");
};
var queue = {};
const
    tickRate = 250;

function isInt(value) {
    if (isNaN(value)) {
        return false;
    }
    var x = parseFloat(value);
    return (x | 0) === x;
}
const now = () => new Date().getTime();

let before_time = 0;
function ajaxRequest(options, message) {
    var obj;
    var cache = false;
    if (options.method == "GET")
        cache = true;

    var async = false;
    if (typeof options.async != "undefined") {
        async = true;
    }
    $.ajax({
        type: options.method,
        cache: cache,
        async: async,
        url: options.url,
        data: options.data,
        beforeSend: function () {
            options.before();
        },
        success: function (data) {
            obj = data;
            if ((typeof message != "undefined") && (message == true)) {
                if (typeof obj["type"] != "undefined") {
                    if (obj["type"] == 'success') {
                        core.clearErrorLogs(options.form);
                        core.showToast(obj['type'], "fa-check", obj['title'], obj['messages']);
                        core.addSuccessLabel(obj['title'], obj["messages"]);
                    }
                    else {
                        core.initErrorLogs(obj['errors'], options.form);
                        var err_message = obj['messages'];
                        if (typeof obj['errors']['content'] != "undefined") {
                            err_message = obj['errors']['content'];
                        }
                        core.showToast(obj['type'], "fa-exclamation", obj['title'], err_message);
                    }
                }
                else {
                    core.showToast("error", "fa-exclamation", "System Error", "Please report this error to developers!");
                }
            }
            cookie = obj['cookies'];
            if (obj != null) {
                if (obj.hasOwnProperty('login')) {
                    if (obj['login'] == false) {

                        //if(typeof socket != "undefined")
                        //closeSocket();
                    }
                }
            }
            if (typeof options.table != 'undefined') {
                core.tableRefresh({
                    target: options.table,
                    url: options.table_url
                });
            }
            options.success(data);
        },
        error: function (e) {
            var obj = {
                type: 'error',
                message: 'Request error'
            };
            options.error();

        },
        complete: function (XMLHttpRequest, textStatus) {
            options.complete();
            core.resetSessionTimer(core.timelimit, core.time_left);
        }
    });
    return obj;
}

let core = function () {
    return {
        plot: null,
        select_result: {},
        last_selected: {},
        is_async_step: false,
        reset_timer: false,
        time_left: 0,
        timelimit: 0,
        series: [
            {
                data: [],
                yaxis: 2,
                color: "#999999",
                lines: {
                    fill: true
                },
                label: "Download"
            },
            {
                data: [],
                yaxis: 1,
                color: "#1ab394",
                lines: {
                    fill: true
                },
                label: "Upload"
            },
        ],
        xVal: 0,
        network_upload_data: [],
        network_download_data: [],
        available_db: ["mysql", "mssql", "pgsql"],
        domain_log_started: false,
        intervalId: 0,
        sess_time: 0,
        TIMER_COLOR_CODES: {
            info: {
                color: "green"
            },
            warning: {
                color: "orange",
                threshold: 30
            },
            alert: {
                color: "red",
                threshold: 10
            }
        },
        init: function () {
            core.submitForm();
            core.initMainPage();
            core.inputInits();
            core.initErrorMessages();
            $('[data-toggle="tooltip"]').tooltip();
            $("[data-toggle=popover]").popover({
                container: 'body',
                trigger: "focus"
            });
            $(".img-fluid").click(function () {
                $(this).toggleClass("thumb");
            });

            $(".dropdown-menu > li > a.trigger").on("click", function (e) {
                var current = $(this).next();
                var grandparent = $(this).parent().parent();
                if ($(this).hasClass('left-caret') || $(this).hasClass('right-caret'))
                    $(this).toggleClass('right-caret left-caret');
                grandparent.find('.left-caret').not(this).toggleClass('right-caret left-caret');
                grandparent.find(".sub-menu:visible").not(current).hide();
                current.toggle();
                e.stopPropagation();
            });
            $(".dropdown-menu > li > a:not(.trigger)").on("click", function () {
                var root = $(this).closest('.dropdown');
                root.find('.left-caret').toggleClass('right-caret left-caret');
                root.find('.sub-menu:visible').hide();
            });

            if ($("#folder_lists").length) {
                filemanager.initJstree("#folder_lists");
                $("#folder_lists").jstree(true).settings.core.data = folder_list;
                $("#folder_lists").jstree(true).refresh();
                // filemanager.folderlist('#folder_lists');
                // filemanager.filelist(default_folder);
                filemanager.initFileList(file_list, "/", $('#file-lists'));
                filemanager.init();
            }
            // core.draggableModal(".modal-title", ".modal-content");
            core.logModule();
        },
        checkAuth: function (xhr) {
            if(!isNaN(xhr.getResponseHeader("x-request-auth")) && !xhr.getResponseHeader("x-request-auth")) {
                root.console.log("✖ %c" + "Autharization ended. Redirecting...", debugStyle_red);
                setTimeout(() => {
                    location.href = "/";
                    window.location.reload();
                }, 250);
            }
        },
        showDomainSs: function () {
            $('[data-id="data-show-image"]').mouseover(function () {
                $($(this).attr("data-target")).show();
            });
            $('[data-id="data-show-image"]').mouseout(function () {
                $($(this).attr("data-target")).hide();
            });
        },
        showToast(type, icon, title, message) {
            var current_ts = new Date().valueOf();
            var html = core.toastElement(current_ts, type, icon, title, message);
            $("body").append(html);
            let current_toast = $('.toast-' + current_ts);
            current_toast.toast({
                delay: 5000,
                animation: true
            });
            current_toast.toast("show");
        },
        toastElement: function (current_ts, type, icon, title, message) {
            return '<div style="position: absolute; top: 20px; right: 20px;"><div class="toast toast-' + current_ts + ' toast-bootstrap" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-header ' + type + '"><i class="fa ' + icon + '"> </i><strong class="mr-auto m-l-sm">' + title + '</strong><small></small><button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="toast-body">' + message + '</div></div></div>';
        },
        initMainPage: function () {
            $(document).ready(function () {
                // Add body-small class if window less than 768px
                if ($(this).width() < 769) {
                    $('body').addClass('body-small')
                }
                else {
                    $('body').removeClass('body-small')
                }
                // MetsiMenu
                $('#side-menu').metisMenu({
                    toggle: false
                });
                // Collapse ibox function
                $('.collapse-link').click(function () {
                    var ibox = $(this).closest('div.ibox');
                    var button = $(this).find('i');
                    var content = ibox.find('div.ibox-content');
                    content.slideToggle(200);
                    button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
                    ibox.toggleClass('').toggleClass('border-bottom');
                    setTimeout(function () {
                        ibox.resize();
                        ibox.find('[id^=map-]').resize();
                    }, 50);
                });
                // Close ibox function
                $('.close-link').click(function () {
                    var content = $(this).closest('div.ibox');
                    content.remove();
                });

                // Close menu in canvas mode
                $('.close-canvas-menu').click(function () {
                    $("body").toggleClass("mini-navbar");
                    SmoothlyMenu();
                });
                // Minimalize menu
                $('.navbar-minimalize').click(function () {
                    $("body").toggleClass("mini-navbar");
                    SmoothlyMenu();

                });

                // Full height of sidebar
                function fix_height() {
                    var heightWithoutNavbar = $("body > #wrapper").height() - 61;
                    $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");

                    var navbarHeigh = $('nav.navbar-default').height();
                    var wrapperHeigh = $('#page-wrapper').height();

                    if (navbarHeigh > wrapperHeigh) {
                        $('#page-wrapper').css("min-height", navbarHeigh + "px");
                    }

                    if (navbarHeigh < wrapperHeigh) {
                        $('#page-wrapper').css("min-height", $(window).height() + "px");
                    }

                }

                fix_height();
                $(window).scroll(function () {
                    if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) {
                        $('#right-sidebar').addClass('sidebar-top');
                    }
                    else {
                        $('#right-sidebar').removeClass('sidebar-top');
                    }
                });
                $(document).bind("load resize scroll", function () {
                    if (!$("body").hasClass('body-small')) {
                        fix_height();
                    }
                });
            });
            $(window).bind("resize", function () {
                if ($(this).width() < 769) {
                    $('body').addClass('body-small')
                }
                else {
                    $('body').removeClass('body-small')
                }
            });

            function SmoothlyMenu() {
                if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
                    $('#side-menu').hide();
                    setTimeout(
                        function () {
                            $('#side-menu').fadeIn(500);
                        }, 100);
                }
                else if ($('body').hasClass('fixed-sidebar')) {
                    $('#side-menu').hide();
                    setTimeout(
                        function () {
                            $('#side-menu').fadeIn(500);
                        }, 300);
                }
                else {
                    $('#side-menu').removeAttr('style');
                }
            }
        },
        isExists: function (array, element) {
            for (var i = 0; i < array.length; i++) {
                if (array[i] == element) {
                    return true;
                }
            }
            return false;
        },
        popover: function () {
            $('[rel="popover"]').popover({
                html: true,
                content: function () {
                    var content = $(this).attr("data-popover-content");
                    return $(content).children(".popover-content").html();
                }
            });
        },
        bytesToSize: function (bit) {
            var sizes = ['Bit', 'KBit', 'MBit', 'GBit', 'TBit'];
            if (bit == 0) return '0 Bit';
            var i = parseInt(Math.floor(Math.log(bit) / Math.log(1024)));
            return Math.round(bit / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },
        jsonParse: function (data) {
            var obj;
            try {
                obj = $.parseJSON(data);
            } catch (e) {
                obj = {
                    message: 'error',
                    content: 'System Error'
                };
            }
            return data;
        },
        addSuccessLabel: function (title, message) {
            var html = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' + message + '</div>';
            $(".wrapper-content").prepend(html);
            setTimeout(function () {
                $(".alert").fadeOut("normal", function () {
                    $(this).remove();
                });
            }, 2000);

        },
        ajaxRequest: function (options, message) {
            if (typeof options.data == "undefined")
                options.data = {};

            options.data[lvar.stk] = lvar.stc;
            if (typeof options.before == "undefined") {
                options.before = function () {
                };
            }
            if (typeof options.complete == "undefined") {
                options.complete = function () {
                };
            }
            if (typeof options.success == "undefined") {
                options.success = function () {
                };
            }
            return ajaxRequest(options, message);
        },
        clearErrorLogs: function (form_id) {
            $('form#' + form_id).find('span.help-block').remove();
            $('form#' + form_id).find('input').each(function () {
                //$(this).parent().removeClass('has-error');
                $(this).closest('.form-group').removeClass('has-error');
                $(".ms-selectable").removeClass('has-error');
            });
        },
        initErrorLogs: function (errors, form_id) {
            if (typeof form_id != "undefined") {
                core.clearErrorLogs(form_id);
                for (var i in errors) {
                    if (errors[i].length == 0) {
                        continue;
                    }
                    if (i == "message_summary")
                        continue;
                    if (i == "sql") {
                        var html = "";
                        for (var k in errors['sql']) {
                            html += '<span class="label label-danger">' + k + '</span>: ' + errors['sql'][k] + '<br>'
                        }
                        $('#logs pre').prepend(html);
                        continue;
                    }
                    if (isNaN(i)) {
                        var element = $('form#' + form_id + ' #' + i);
                        var element_type = element.prop('nodeName');
                        if (element_type == "SELECT") {
                            if (element.hasClass("multiselect")) {
                                $("form#" + form_id + " .ms-container#ms-" + i + " .ms-selectable").addClass('has-error');
                                $("form#" + form_id + " .ms-container#ms-" + i).parent().append('<span class="col-md-12 help-block form-error">' + errors[i] + '</span>');
                            }
                            else {
                                element.parents('.form-group').addClass('has-error');
                                $('<span class="col-md-12 help-block form-error">' + errors[i] + '</span>').insertAfter(element.parent());
                            }
                        }
                        else {
                            let input_element = $('form#' + form_id + ' input#' + i);
                            if (input_element.parent().hasClass('input-group')) {
                                input_element.parents('.form-group').addClass('has-error');
                                input_element.parent().append($('<span class="col-md-12 help-block form-error">' + errors[i] + '</span>'));
                            }
                            else {
                                input_element.parents('.form-group').addClass('has-error');
                                $('<span class="col-md-12 help-block form-error">' + errors[i] + '</span>').insertAfter(input_element);
                            }
                        }
                    }
                }
            }
            else {
                if (errors.length > 0) {
                    $(".wrapper-content .ibox-content").find(".alert").remove();
                    var html_template = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
                    for (var i in errors) {
                        html_template += errors[i] + "<br />";
                    }
                    html_template += '</div>';
                    $(".wrapper-content .ibox-content").prepend(html_template);
                }
            }
        },
        summernote: function () {
            $('.summernote').summernote({
                height: 180,
                focus: true,
                tabsize: 2,
                onChange: function (contents, $editable) {
                    $('textarea#content').text(contents);
                }
            });
        },
        tableCheckBoxes: function (options) {
            var init_check_all = true;
            var total_tr_elements = $(options.target + ' tbody tr').length;
            $(options.target + ' tbody tr :checkbox').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $(this).closest("tbody tr").addClass("selected");
                    if (options.actions == true) {
                        if ($(options.target + ' tbody tr.selected').length > 0) {
                            $(options.target).closest('.ibox').find('div.table-button-lists').find("a.btn.table-action").each(function () {
                                $(this).removeClass('disabled');
                            });
                        }
                    }
                    if (total_tr_elements == $(options.target + ' tbody tr.selected').length) {
                        init_check_all = true;
                        // $(options.target + ' #select_all').iCheck("check");
                    }
                }
                else {
                    $(this).closest("tbody tr").removeClass("selected");
                    if (options.actions == true) {
                        if ($(options.target + ' tbody tr.selected').length == 0) {
                            $(options.target).closest('.ibox').find('div.table-button-lists').find("a.btn.table-action").each(function () {
                                $(this).addClass('disabled');
                            });
                        }
                    }
                    if (total_tr_elements > $(options.target + ' tbody tr.selected').length) {
                        init_check_all = false;
                        // $(options.target + ' #select_all').iCheck("uncheck");
                    }
                }
            });

            var checkAll = $(options.target + ' #select_all');
            var checkboxes_table = checkAll.closest('table').parent();
            var checkboxes;
            if (checkboxes_table.hasClass("dataTables_scrollHeadInner")) {
                checkboxes = checkboxes_table.parent().next().find(':checkbox');
            }
            else {
                checkboxes = checkAll.closest('table').children("tbody").find(':checkbox');
            }
            checkAll.on('ifClicked', function (ev) {
                checkAll.on('ifChecked ifUnchecked', function (event) {
                    if (!init_check_all) {
                        return;
                    }
                    if (event.type == 'ifChecked') {
                        checkboxes.iCheck('check');
                        checkboxes.closest("tbody tr").addClass("selected");
                    }
                    else {
                        checkboxes.iCheck('uncheck');
                        checkboxes.closest("tbody tr").removeClass("selected");
                    }
                });
            });
        },
        datatable: function (options) {
            options.select_row = false;
            options.actions = false;
            options.serverside = true;
            if ((typeof $(options.target).attr('data-select') != "undefined") && ($(options.target).attr('data-select') == "true")) {
                options.select_row = true;
            }
            if ((typeof $(options.target).attr('data-actions') != "undefined") && ($(options.target).attr('data-actions') == "true")) {
                options.actions = true;
            }
            if ((typeof $(options.target).attr('data-server') != "undefined") && ($(options.target).attr('data-server') == "false")) {
                options.serverside = false;
            }
            var table_options = {
                dataType: "json",
                bStateSave: true,
                bProcessing: true,
                sAjaxSource: "/" + $(options.target).attr("data-url").trimLeft("#"),
                aLengthMenu: [[10, 20, 100, -1], [10, 20, 100, lvar['all']]],
                iDisplayLength: 10,
                retrieve: true,
                dom: '<"html5buttons"BC>lTfgtipr',
                oLanguage: {
                    sInfo: lvar['dt_showing_info'],
                    sSearch: "_INPUT_",
                    sProcessing: lvar["loading"] + ". " + lvar["please_wait"] + "...",
                    sInfoFiltered: " (" + lvar['filtered'] + ")",
                    sEmptyTable: lvar['empty_table'],
                    sInfoEmpty: lvar['no_entries_to_show'],
                    sZeroRecords: lvar['zero_records'],
                    sLengthMenu: "_MENU_",
                    oPaginate: {
                        "sFirst": "<i class='fa fa-step-backward'></i>",
                        "sLast": "<i class='fa fa-step-forward'></i>",
                        "sNext": "<i class='fa fa-forward'></i>",
                        "sPrevious": "<i class='fa fa-backward'></i>"
                    }
                },
                "fnDrawCallback": function (oSettings) {
                    $(options.target + ' input.checkbox-blue').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        radioClass: 'iradio_minimal-blue'
                    });
                    core.showDomainSs();
                    $('a[data-id="confirm"]').unbind();
                    $('a[data-id="confirm"]').on('click', function () {
                        core.confirm(this);
                    });

                    $('a[data-id="response"]').unbind();
                    $('a[data-id="response"]').on('click', function () {
                        core.response(this);
                    });
                    $('[data-toggle="tooltip"]').tooltip();

                    core.tableCheckBoxes(options);

                },
                "fnInitComplete": function (data, json) {
                    $('.dynamic-table input.checkbox-blue').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        radioClass: 'iradio_minimal-blue'
                    });
                    $('a[data-id="confirm"]').unbind();
                    $('a[data-id="confirm"]').on('click', function () {
                        core.confirm(this);
                    });
                    core.showDomainSs();

                    $(".dataTables_wrapper div.html5buttons .dt-buttons").append('<a class="btn btn-default buttons-refresh btn-sm"><span><i class="fas fa-sync-alt"></i></span></a>');
                    $('.dataTables_wrapper .buttons-refresh').on('click', function () {
                        core.tableRefresh({
                            target: options.target,
                            url: $(options.target).attr("data-url")
                        });
                        $(options.target + ' #select_all').iCheck("uncheck");
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                    core.tableCheckBoxes(options);


                },
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": lvar.stk, "value": lvar.stc});
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: sSource,
                        data: aoData,
                        success: fnCallback,
                        complete: function (XMLHttpRequest, textStatus) {
                        }
                    });
                    if (typeof options.action_buttons != "undefined") {
                        for (var i in options.action_buttons) {
                            $(options.target + '_wrapper')
                            .parent()
                            .parent()
                            .parent()
                            .find('div.table-button-lists .' + options.action_buttons[i])
                            .addClass('disabled');
                        }
                    }

                    $('input.checkbox-blue').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        radioClass: 'iradio_minimal-blue'
                    });
                }
            };


            var founded_checkbox = $('table thead tr :checkbox');
            if (founded_checkbox.length > 0) {
                table_options['colVis'] = {exclude: [0]};
            }

            if (typeof options.serverside != 'undefined')
                table_options["bServerSide"] = options.serverside;
            else
                table_options["bServerSide"] = true;

            $(options.target).DataTable(table_options);


            if (options.select_row == true) {
                table_options["select"] = true;
                $(options.target + ' tbody').on('click', 'tr', function () {
                    if ($(options.target + ' tbody tr td:first-child').hasClass('dataTables_empty') == false) {
                        if ($(this).hasClass('selected')) {
                            $(this).removeClass('selected');
                            $(this).find("input:checkbox").iCheck("uncheck");
                        }
                        else {
                            $(this).addClass('selected');
                            $(this).find("input:checkbox").iCheck("check");
                        }
                    }
                });
            }
            $('.dataTables_wrapper .dataTables_filter input').attr("placeholder", lvar['search']);
            $('.dataTables_wrapper div.html5buttons .buttons-print').remove();
        },
        tableRefresh: function (options) {
            let table = $(options.target).dataTable();
            table.fnReloadAjax(options.url);
            core.resetSessionTimer(core.timelimit, core.time_left);
        },
        removeAllOptions: function (options) {
            $(options.target).html('');
            if ((typeof options.selectpicker == "undefined") && (options.selectpicker != false))
                $(options.target).selectpicker('refresh');
        },
        alertChangeService: function () {
            var dns_service_proc = false;
            var mail_service_proc = false;
            var client_proc = false;
            $('[name="enable_dns"]').on("change", function () {
                var message;
                var message_second;
                if (dns_service_proc == true) {
                }
                else {
                    dns_service_proc = true;
                }

                var url = $(this).attr('data-url');
                var checkbox = $(this).attr('name');
                var data = {};
                var status = "off";
                if ($(this).prop('checked')) {
                    message = lvar["dns_will_enable"];
                    message_second = lvar["dns_service_enabled"];
                    status = "on";
                }
                else {
                    message = lvar["dns_will_disable"];
                    message_second = lvar["dns_service_disabled"];
                }

                data[checkbox] = status;
                data[lvar.stk] = lvar.stc;
                swal({
                    title: lvar["are_you_sure"],
                    text: message,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: lvar["submit"],
                    closeOnConfirm: false
                }, function (confirmed) {
                    if (confirmed) {
                        $.ajax({
                            type: 'POST',
                            url: "/" + url.trimLeft("#"),
                            data: data,
                            success: function (dataRules) {
                                var obj;
                                obj = dataRules;

                                if (obj['message'] == "505") {
                                    toastr["error"]("System Error");
                                    setTimeout(function () {
                                        window.location.assign('/error.php')
                                    }, 1000);
                                }
                                if (obj['message'] == "logout") {
                                    toastr["error"](lvar["session_timeout"]);
                                    setTimeout(function () {
                                        window.location.assign('/')
                                    }, 1000);
                                }

                                if (obj['type'] == 'success') {
                                    toastr["success"](obj['messages'], obj['title']);
                                    swal(lvar["success"] + "!", message_second, "success");
                                }
                                else {
                                    toastr["error"](obj['content'], obj['title']);
                                    dns_service_proc = false;
                                    var error_messages = "";
                                    for (var i in obj['errors']) {
                                        error_messages += obj['errors'][i] + "\n";
                                    }
                                    swal(lvar["canceled"] + "!", error_messages, "error");
                                }
                            }
                        });

                    }
                    else {
                        $('input[name="enable_dns"]').bootstrapToggle("toggle");
                    }
                });
            });
            $('[name="enable_mail"]').on("change", function () {
                var message;
                var message_second;
                if (mail_service_proc == true) {
                }
                else {
                    mail_service_proc = true;
                }

                var url = $(this).attr('data-url');
                var checkbox = $(this).attr('name');
                var data = {};
                var status = "off";
                if ($(this).prop('checked')) {
                    message = lvar["mail_will_enable"];
                    status = "on";
                    message_second = lvar["mail_service_enabled"];
                }
                else {
                    message = lvar["mail_will_enable"];
                    message_second = lvar["mail_service_enabled"];
                }

                data[checkbox] = status;
                data[lvar.stk] = lvar.stc;

                swal({
                    title: lvar["are_you_sure"],
                    text: message,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: lvar["submit"],
                    closeOnConfirm: false
                }, function (confirmed) {
                    if (confirmed) {
                        $.ajax({
                            type: 'POST',
                            url: "/" + url.trimLeft("#"),
                            data: data,
                            success: function (dataRules) {
                                var obj;
                                obj = dataRules;

                                if (obj['message'] == "505") {
                                    toastr["error"]("System Error");
                                    setTimeout(function () {
                                        window.location.assign('/error.php')
                                    }, 1000);
                                }
                                if (obj['message'] == "logout") {
                                    toastr["error"](lvar["session_timeout"]);
                                    setTimeout(function () {
                                        window.location.assign('/')
                                    }, 1000);
                                }

                                if (obj['type'] == 'success') {
                                    toastr["success"](obj['messages'], obj['title']);
                                    swal(lvar["success"] + "!", message_second, "success");
                                }
                                else {
                                    toastr["error"](obj['content'], obj['title']);
                                    mail_service_proc = false;
                                    var error_messages = "";
                                    for (var i in obj['errors']) {
                                        error_messages += obj['errors'][i] + "\n";
                                    }
                                    swal(lvar["canceled"] + "!", error_messages, "error");
                                }
                            }
                        });

                    }
                    else {
                        $('input[name="enable_mail"]').bootstrapToggle("toggle");
                    }
                });
            });
            $('#client_status').on("change", function () {
                var message;
                var message_second;
                if (client_proc == true) {
                }
                else {
                    client_proc = true;
                }

                var url = $(this).attr('data-url');
                var checkbox = $(this).attr('name');
                var data = {};
                var status = "off";
                if ($(this).prop('checked')) {
                    message = lvar["domain_will_enable"];
                    message_second = lvar["domain_status_enabled"];
                    status = "on";
                }
                else {
                    message = lvar["domain_will_disable"];
                    message_second = lvar["domain_status_disabled"];
                }

                data[checkbox] = status;
                data[lvar.stk] = lvar.stc;

                swal({
                    title: lvar["are_you_sure"],
                    text: message,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: lvar["submit"],
                    closeOnConfirm: false
                }, function (confirmed) {
                    if (confirmed) {
                        $.ajax({
                            type: 'POST',
                            url: "/" + url.trimLeft("#"),
                            data: data,
                            success: function (dataRules) {
                                var obj;
                                obj = dataRules;

                                if (obj['message'] == "505") {
                                    toastr["error"]("System Error");
                                    setTimeout(function () {
                                        window.location.assign('/error.php')
                                    }, 1000);
                                }
                                if (obj['message'] == "logout") {
                                    toastr["error"](lvar["session_timeout"]);
                                    setTimeout(function () {
                                        window.location.assign('/')
                                    }, 1000);
                                }

                                if (obj['type'] == 'success') {
                                    toastr["success"](obj['messages'], obj['title']);
                                    swal(lvar["success"] + "!", message_second, "success");
                                }
                                else {
                                    toastr["error"](obj['content'], obj['title']);
                                    dns_service_proc = false;
                                    var error_messages = "";
                                    for (var i in obj['errors']) {
                                        error_messages += obj['errors'][i] + "\n";
                                    }
                                    swal(lvar["canceled"] + "!", error_messages, "error");
                                }
                            }
                        });

                    }
                    else {
                        $('#client_status').bootstrapToggle("toggle");
                    }
                });
            });
        },
        inputInits: function () {
            $('[data-dismiss="modal"]').click(function () {
                var form_id = $(this).closest('.modal').find('form').attr('id');
                if (typeof form_id != "undefined")
                    core.formReset(form_id);
            });
            core.serverCreate();
            core.initProcessTable();

            $('.select-multiple').selectMultiple({
                cssClass: "single"
            });
            $('input.checkbox-blue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $('input.checkbox-aero').iCheck({
                checkboxClass: 'icheckbox_minimal-aero',
                radioClass: 'iradio_minimal-aero'
            });
            $('input[value="unlimited"]').on('ifChanged', function (event) {
                var objchk = $(this).attr('name');
                if ($(this).is(':checked')) {
                    $('#' + objchk).attr('disabled', 'disabled');
                }
                else {
                    $('#' + objchk).removeAttr('disabled');
                }
            });
            core.generatePassword();
            core.showPasswd(this);
            // Session Block Allow List
            core.sessionSettings();
            if ($(".dynamic-table").length) {
                core.datatable({
                    target: '.dynamic-table'
                });
            }
            if ($(".mimetypes-list").length) {
                core.datatable({
                    target: '.mimetypes-list'
                });
            }

            core.multipleProcess();
            core.changeParent();
            $('.file').fileinput({
                showUpload: false,
                dropZoneEnabled: false,
                mainClass: "input-group-sm"
            });

            core.clearUnlimited();
            core.changeServicePlan();
            core.systemInit();
            core.sourceList();
            core.load_db();
            core.initDomainBackup();
            core.subdomains();
            core.changeDnsType();
            core.domainMail();
            core.setSideBarStatus();
            core.crontabs();
            core.spamLists();
            $('[data-id="check"]').unbind();
            $('[data-id="check"]').on('click', function () {
                window.location.assign($(this).attr('data-url'));
            });
            core.windowsSettings();
            core.networkFormat();

            $('#sync_time').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#timezone_addr').prop('disabled', false);
                }
                else {
                    $('#timezone_addr').prop('disabled', true);
                }
            });
            $('#send_information').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#email_address').prop('disabled', false);
                }
                else {
                    $('#email_address').prop('disabled', true);
                }
            });
            $('input[name="unlimited_quota"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#mail_quota').prop('disabled', true);
                }
                else {
                    $('#mail_quota').prop('disabled', false);
                }
            });
            core.mailServer();


            core.databaseServer();
            core.telegram();
            core.webServer();

            core.vdir();
            core.prettyPrint();
            $(".multiselect").multiSelect({
                cssClass: "multi",
                selectableOptgroup: true,
                // selectableHeader: "<div class='ms-header'><strong>"+ $('.multiselect').attr('data-selectable') +"</strong></div>",
                // selectionHeader: "<div class='ms-header'><strong>"+ $('.multiselect').attr('data-selection') +"</strong></div>",
                selectableHeader: "<div class='ms-header'><strong>" + $('.multiselect').attr('data-selectable') + "</strong></div><input type='text' class='search-input form-control form-control-sm' autocomplete='off' placeholder='" + lvar["search"] + "'>",
                selectionHeader: "<div class='ms-header'><strong>" + $('.multiselect').attr('data-selectable') + "</strong></div><input type='text' class='search-input form-control form-control-sm' autocomplete='off' placeholder='" + lvar["search"] + "'>",
                afterInit: function (ms) {
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function (e) {
                        if (e.which === 40) {
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function (e) {
                        if (e.which == 40) {
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
                },
                afterSelect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });
            $('.clipboard').unbind();
            $('.clipboard').on('click', function (e) {
                var clip = new Clipboard('.clipboard');
                core.showToast("success", "fa-check", lvar['success'], lvar['copied']);
                e.preventDefault();
            });
            $('[id="ssl_support"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#ssl_lists").show();
                }
                else {
                    $("#ssl_lists").hide();
                }
                event.preventDefault();
            });
            $('#system-update-form input[name="random_time"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('input[id="update_time"]').prop('disabled', true);
                }
                else {
                    $('input[id="update_time"]').prop('disabled', false);
                }
            });
            $('#system-update-form input[name="statistics_random"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('input[id="statistics_time"]').prop('disabled', true);
                }
                else {
                    $('input[id="statistics_time"]').prop('disabled', false);
                }
            });
            $('.select-picker').selectpicker({
                caretIcon: 'fa fa-angle-down',
                style: "btn-white",
                liveSearch: true
            });
            $(".make-switch").each(function () {
                $("#" + $(this).attr("id")).bootstrapToggle({
                    offstyle: "danger",
                    on: $(this).data('on-text'),
                    off: $(this).data("off-text"),
                });
            });

            $('a[data-id="confirm"]').unbind();
            $('a[data-id="confirm"]').on('click', function () {
                core.confirm(this);
            });
            core.disableEnableElements();
            core.selectDependElements();
        },
        subdomains: function () {
            var is_native = $("#use_native_ftp").parent('[class*="icheckbox"]').hasClass("checked");
            if (is_native) {
                $('#create-subdomain-form input[id="username').prop('disabled', false);
                $('#create-subdomain-form input[id="password').prop('disabled', false);
                $('#create-subdomain-form input[id="confirm-password').prop('disabled', false);
            }
            else {
                $('#create-subdomain-form input[id="username').prop('disabled', true);
                $('#create-subdomain-form input[id="password').prop('disabled', true);
                $('#create-subdomain-form input[id="confirm-password').prop('disabled', true);
            }
            $('input[id="use_native_ftp"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#create-subdomain-form input[id="username').prop('disabled', false);
                    $('#create-subdomain-form input[id="password').prop('disabled', false);
                    $('#create-subdomain-form input[id="confirm-password').prop('disabled', false);
                }
                else {
                    $('#create-subdomain-form input[id="username').prop('disabled', true);
                    $('#create-subdomain-form input[id="password').prop('disabled', true);
                    $('#create-subdomain-form input[id="confirm-password').prop('disabled', true);
                }
            });
        },
        readablizeBytes: function (bytes) {
            var s = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'];
            var e = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, e)).toFixed(2) + " " + s[e];
        },
        backupFileUpload: function () {
            backupUploader.init();
            var query = {'ts': Math.round(Date.now() / 1000)};
            query[lvar.stk] = lvar.stc;
            backupUploader.upload($('form#file-backup-upload').attr('action'), query);
        },
        formReset: function (target) {
            document.getElementById(target).reset();
            $("form#" + target).find('select.selectpicker').each(function () {
                var attr = $(this).attr('data-toggle');
                if (typeof attr != "undefined") {
                    $(this).selectpicker('val', '');
                }
                else {
                    core.removeAllOptions({
                        target: $(this)
                    });
                }
                $(this).selectpicker('refresh');
            });

            $('input.checkbox').iCheck('destroy');
            $('input.checkbox').prop('checked', false);
            $('input.checkbox').iCheck({
                checkboxClass: 'icheckbox_minimal-grey',
                radioClass: 'iradio_minimal-grey'
            });
            core.clearErrorLogs(target);
        },
        multipleSelect: function (target, selector) {
            $(target).multiSelector({
                selector: selector,
                onSelectionEnd: function (list, parent, element) //Callback
                {
                    // console.log("First parameter: object list");
                    //
                    // console.log("Second parameter: parent object");
                    // console.log(parent);
                    //
                    // console.log("Third parameter: clicked object");
                    // console.log(element);
                }
            });
        },
        confirm: function (element) {
            swal({
                title: lvar["confirm"],
                text: lvar['are_you_sure'],
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false,
                buttonsStyling: false,
            }, function (confirmed) {
                var post_data = {};
                post_data[lvar["stk"]] = lvar["src"];
                if (confirmed) {
                    var options = {
                        async: true,
                        method: 'POST',
                        url: "/" + $(element).attr('data-url').trimLeft("#"),
                        data: post_data,
                        form: "server-connect",
                        table: '#' + $(element).closest('.ibox-content').find(".dynamic-table").attr('id'),
                        table_url: $(element).closest('.ibox-content').find(".dynamic-table").attr('data-url'),
                    };
                    options.before = function () {
                        $(".sa-button-container .cancel").attr("disabled", "disabled");
                        $(".sa-button-container .confirm").attr("disabled", "disabled");
                    };
                    options.success = function (data) {
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        // core.is_async_step = true;
                        if (data.type == "success") {

                        }
                    };
                    options.error = function () {
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        return false;
                    };
                    options.complete = function (data) {
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        swal.close();
                    };
                    core.ajaxRequest(options, true);
                }
            });
        },
        response: function (element) {
            swal({
                title: lvar["confirm"],
                text: lvar['are_you_sure'],
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false,
            }, function (confirmed) {
                if (confirmed) {
                    var options = {
                        async: true,
                        method: 'POST',
                        url: "/" + $(element).attr('data-url').trimLeft("#"),
                        data: {},
                        form: "server-connect",
                        table: '#' + $(element).closest('.ibox-content').find("table").attr('id'),
                        table_url: $(element).closest('.ibox-content').find("table").attr('data-url'),
                    };
                    options.before = function () {
                        $(".sa-button-container .cancel").attr("disabled", "disabled");
                        $(".sa-button-container .confirm").attr("disabled", "disabled");
                    };
                    options.success = function (res) {
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        if (res["type"] == "success") {
                            var depence_text = '<div class="modal-table-responsive"><table class="table table-striped table-bordered"><thead>';
                            depence_text += '<tr>';
                            depence_text += '<td colspan="2">';
                            depence_text += "<span class='cm-strong'>" + lvar["dependencies"] + "</span>";
                            depence_text += '</td>';
                            depence_text += '</tr>';
                            depence_text += '<tr>';
                            depence_text += '<td colspan="1">';
                            depence_text += "<span class='cm-strong'>" + lvar["package_name"] + "</span>";
                            depence_text += '</td>';
                            depence_text += '<td colspan="1">';
                            depence_text += "<span class='cm-strong'>" + lvar["status"] + "</span>";
                            depence_text += '</td>';
                            depence_text += '</tr></thead><tbody>';
                            for (var i in res["content"]) {
                                depence_text += '<tr>';
                                depence_text += '<td colspan="1">';
                                depence_text += i;
                                depence_text += '</td>';
                                depence_text += '<td colspan="1">';
                                depence_text += res["content"][i];
                                depence_text += '</td>';
                                depence_text += '</tr>';
                            }
                            depence_text += '</tbody></table></div>';
                            core.showConfirmPost(depence_text, res["url"], options);
                        }
                        else {
                            swal({
                                title: lvar["confirm"],
                                text: res["messages"],
                                type: "error",
                                showCancelButton: true,
                                showConfirmButton: false,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: lvar["submit"],
                                cancelButtonText: lvar["close"],
                                closeOnConfirm: false,
                                customClass: {
                                    ConfirmButton: "btn btn-primary",
                                },
                                html: true,
                            }, function (confirmed) {
                                $(".sa-button-container .confirm").attr("disabled", "disabled");
                                if (confirmed) {
                                    swal.close();
                                }
                            });
                        }
                    };
                    options.error = function () {
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        return false;
                    };
                    options.complete = function () {
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        // core.tableRefresh({
                        //     target : options.table,
                        //     url: options.table_url
                        // });
                        // swal.close();
                    };
                    core.ajaxRequest(options, false);
                }
            });
        },
        showConfirmPost: function (message, url, options) {
            swal({
                title: lvar["confirm"],
                text: message,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false,
                html: true,
            }, function (confirmed) {
                if (confirmed) {
                    options.async = true;
                    options.method = "POST";
                    options.url = "/" + url.trimLeft("#");
                    options.before = function () {
                        $(".sa-button-container .cancel").attr("disabled", "disabled");
                        $(".sa-button-container .confirm").attr("disabled", "disabled");
                    };
                    options.success = function (data) {
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        swal.close();
                    };
                    options.error = function () {
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        return false;
                    };
                    options.complete = function () {
                        $(".sa-button-container .cancel").removeAttr("disabled");
                        $(".sa-button-container .confirm").removeAttr("disabled");
                        if (typeof options.table != 'undefined') {
                            core.tableRefresh({
                                target: options.table,
                                url: options.table_url
                            });
                        }
                    };
                    core.ajaxRequest(options, true);
                }
            });
        },
        disableEnableElements: function () {
            if ($('input[type="checkbox"]').is("[data-sub-elements]")) {
                $('input[data-sub-elements]').each(function () {
                    var element = $(this).attr("id");
                    var checked_max_size = $("#" + element).parent('[class*="icheckbox"]').hasClass("checked");
                    var sub_elements = $(this).attr("data-sub-elements");
                    if (typeof sub_elements !== typeof undefined && sub_elements !== false) {
                        var targets = sub_elements.split(",");
                        if (checked_max_size) {
                            for (var i = 0; i < targets.length; i++) {
                                $('#' + targets[i]).prop('disabled', false);
                                if ($("#" + targets[i]).is(':checkbox')) {
                                    $("#" + targets[i]).next().removeClass("disabled");
                                }
                                if ($('#' + targets[i]).hasClass("select-picker")) {
                                    $('#' + targets[i]).selectpicker('refresh');
                                }
                            }
                        }
                        else {
                            for (var i = 0; i < targets.length; i++) {
                                $('#' + targets[i]).prop('disabled', true);
                                if ($("#" + targets[i]).is(':checkbox')) {
                                    $("#" + targets[i]).next().addClass("disabled");
                                }
                                if ($('#' + targets[i]).hasClass("select-picker")) {
                                    $('#' + targets[i]).selectpicker('refresh');
                                }
                            }
                        }

                        $('#' + element).on('ifChanged', function (event) {
                            if ($('#' + element).is(':checked')) {
                                for (var i = 0; i < targets.length; i++) {
                                    $('#' + targets[i]).prop('disabled', false);
                                    if ($("#" + targets[i]).is(':checkbox')) {
                                        $rz:("#" + targets[i]).next().removeClass("disabled");
                                    }
                                    if ($('#' + targets[i]).hasClass("select-picker")) {
                                        $('#' + targets[i]).selectpicker('refresh');
                                    }
                                }
                            }
                            else {
                                for (var i = 0; i < targets.length; i++) {
                                    $('#' + targets[i]).prop('disabled', true);
                                    if ($("#" + targets[i]).is(':checkbox')) {
                                        $("#" + targets[i]).next().addClass("disabled");
                                    }
                                    if ($('#' + targets[i]).hasClass("select-picker")) {
                                        $('#' + targets[i]).selectpicker('refresh');
                                    }
                                }
                            }

                            for (var i = 0; i < targets.length; i++) {
                                if ($("#" + targets[i]).is(':checkbox')) {
                                    var ssub_elements = $("#" + targets[i]).attr("data-sub-elements");
                                    if (typeof ssub_elements !== typeof undefined && ssub_elements !== false) {
                                        core.triggerDisableEnableElements(targets[i], ssub_elements.split(","));
                                    }
                                }
                            }
                            if (!$('#' + element).is(':checked')) {
                                for (var i = 0; i < targets.length; i++) {
                                    if (!$("#" + targets[i]).is(':checkbox')) {
                                        $('#' + targets[i]).prop('disabled', true);
                                    }
                                    if ($('#' + targets[i]).hasClass("select-picker")) {
                                        $('#' + targets[i]).selectpicker('refresh');
                                    }
                                }
                            }
                            $(".multiselect").multiSelect('refresh');
                        });
                    }
                    else {
                        $(".multiselect").multiSelect('refresh');
                        return true;
                    }
                });
            }
        },
        selectDependElements: function () {
            if ($('input[type="checkbox"]').is("[data-select-elements]")) {
                $('input[data-select-elements]').each(function () {
                    var element = $(this).attr("id");
                    var checked_max_size = $("#" + element).parent('[class*="icheckbox"]').hasClass("checked");
                    var sub_elements = $(this).attr("data-select-elements");
                    if (typeof sub_elements !== typeof undefined && sub_elements !== false) {
                        var targets = sub_elements.split(",");
                        if (checked_max_size) {
                            for (var i = 0; i < targets.length; i++) {
                                $('#' + targets[i]).iCheck('check');
                            }
                        }
                        else {
                            for (var i = 0; i < targets.length; i++) {
                                $('#' + targets[i]).iCheck('uncheck');
                            }
                        }

                        $('#' + element).on('ifChanged', function (event) {
                            if ($('#' + element).is(':checked')) {
                                for (var i = 0; i < targets.length; i++) {
                                    $('#' + targets[i]).iCheck('check');
                                }
                            }
                            else {
                                for (var i = 0; i < targets.length; i++) {
                                    $('#' + targets[i]).iCheck('uncheck');
                                }
                            }

                            for (var i = 0; i < targets.length; i++) {
                                if ($("#" + targets[i]).is(':checkbox')) {
                                    var ssub_elements = $("#" + targets[i]).attr("data-select-elements");
                                    if (typeof ssub_elements !== typeof undefined && ssub_elements !== false) {
                                        core.triggerSelectDependElements(targets[i], ssub_elements.split(","));
                                    }
                                }
                            }
                        });
                    }
                    else {
                        return true;
                    }
                });
            }
        },
        triggerDisableEnableElements: function (element, targets) {
            var element_status = $("#" + element).parent('[class*="icheckbox"]').hasClass("checked");
            if (element_status) {
                for (var i = 0; i < targets.length; i++) {
                    $('#' + targets[i]).prop('disabled', false);
                }
            }
            else {
                for (var i = 0; i < targets.length; i++) {
                    $('#' + targets[i]).prop('disabled', true);
                }
            }
        },
        triggerSelectDependElements: function (element, targets) {
            var element_status = $("#" + element).parent('[class*="icheckbox"]').hasClass("checked");
            if (element_status) {
                for (var i = 0; i < targets.length; i++) {
                    $('#' + targets[i]).iCheck('check');
                }
            }
            else {
                for (var i = 0; i < targets.length; i++) {
                    $('#' + targets[i]).iCheck('uncheck');
                }
            }
        },
        validateIp: function (value) {
            var expression = /((^\s*((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$))/;
            return expression.test(value);
        },
        systemInit: function () {
            if ($('input[name="php_support"]').length) {
                $('input[name="unlimited"]').on('ifChecked ifUnchecked', function (event) {
                    var objchk = $(this).attr('name');
                    if (event.type == 'ifChecked') {
                        $('#' + objchk).attr('disabled', 'disabled');
                    }
                    else {
                        $('#' + objchk).removeAttr('disabled');
                    }
                });
                $('input[name="select-all-limits"]').on('ifChecked ifUnchecked', function (event) {
                    if (event.type == 'ifChecked') {
                        $('input[value="unlimited').iCheck('check');
                        $('.limits').attr('disabled', 'disabled');
                    }
                    else {
                        $('input[value="unlimited').iCheck('uncheck');
                        $('.limits').removeAttr('disabled');
                    }
                });
                $('input[name="select-all-perms"]').on('ifChecked ifUnchecked', function (event) {
                    if (event.type == 'ifChecked') {
                        $('input[data-id="permissions').iCheck('check');
                    }
                    else {
                        $('input[data-id="permissions').iCheck('uncheck');
                    }
                });
            }
        },
        clearUnlimited: function () {
            if ($('.limits').length) {
                $('.limits').each(function () {
                    if (this.value == "unlimited") {
                        this.value = "";
                    }
                });
            }
        },
        submitForm: function () {
            $("button#submit-form").unbind();
            $("button#submit-form").on("click", function (e) {
                $(this).closest('form').find('select').each(function () {
                    if (($(this).hasClass('multiple-select') == false) && (($(this).hasClass('multiselect') == false))) {
                        if (typeof $(this).attr('multiple') != "undefined") {
                            let select_id = $(this).attr('id');
                            for (let i = 0; i < document.getElementById(select_id).options.length; i++) {
                                document.getElementById(select_id).options[i].selected = true;
                            }
                        }
                    }
                });
                let post_data = $(this).closest('form').serialize();
                let options = {
                    async: true,
                    method: 'POST',
                    url: "/" + $(this).closest('form').attr('action').trimLeft("#"),
                    data: post_data,
                    form: $(this).closest('form').attr("id"),
                    elem: this,
                    before: function () {
                        $(options.elem).closest('.ibox-content').prepend(spinner);
                        $(options.elem).closest('.ibox-content').toggleClass('sk-loading');
                        $(options.elem).children("i").attr("class", "fa fa-spinner fa-spin");
                        $(options.elem).attr('disabled', true);
                    },
                    error: function () {
                        return false;
                    },
                    complete: function () {
                        $(options.elem).removeAttr('disabled');
                        $(options.elem).children("i").attr("class", "fa fa-check-circle");
                        $(options.elem).closest('.ibox-content').toggleClass('sk-loading');
                        $(options.elem).closest('.ibox-content').find(".sk-spinner").remove();
                    },
                    success: function (result) {
                        if (result['type'] == "success" || result['type'] == "info") {
                            if (typeof result['redirect_url'] != "undefined") {
                                core.redirect(result['redirect_url']);
                            }
                        }
                    }
                };
                core.ajaxRequest(options, true);
                e.preventDefault();
            });
        },
        changeParent: function () {
            if ($('#change_parent').length) {
                $('#change_parent').on('click', function () {
                    var post_data = {};
                    post_data[lvar.stk] = lvar.stc;
                    $.ajax({
                        type: 'POST',
                        url: "/" + $(this).data("url").trimLeft("#"),
                        data: post_data,
                        success: function (dataRules) {
                            var obj = dataRules;
                            $('select[name="parent"]').html("");
                            for (var i in obj) {
                                var key = i;
                                var value = obj[i];
                                $('select[name="parent"]').append(new Option(value, key));
                            }
                            $('select[name="parent"]').selectpicker('refresh');
                        }
                    });
                });
                $('#parent_modal button#submit').unbind();
                $('#parent_modal button#submit').on('click', function () {
                    var parent = document.getElementById("parent").value;
                    var ids = $('input:checkbox[name="domains[]"]:checked').map(function () {
                        return this.value;
                    }).get();
                    for (var id = 0; id < ids.length; id++) {
                        var data = {};
                        data['action'] = 'changeparent';
                        data['id'] = ids[id];
                        data['parent'] = parent;
                        core.ajaxRequest({
                            method: 'POST',
                            url: "/" + $(this).closest('#parent_modal').attr('data-url').trimLeft("#"),
                            data: data,
                            table: '.dynamic-table',
                            table_url: $('.dynamic-table').attr('data-url')
                        }, true);
                    }
                    $('#parent_modal').modal('hide');
                });
            }
        },
        multipleProcess: function () {
            if ($('a[data-type="multiple"]').length) {
                $('a[data-type="multiple"]').unbind();
                $('a[data-type="multiple"]').on('click', function () {
                    var element = this;
                    swal({
                        title: lvar["confirm"],
                        text: lvar['are_you_sure'],
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: lvar["submit"],
                        closeOnConfirm: false
                    }, function (confirmed) {
                        if (confirmed) {
                            $(element).addClass('disabled');
                            var default_class = $(element).children("i").attr('class');
                            $(element).children("i").removeClass(default_class);
                            $(element).children("i").addClass("fa fa-spinner fa-spin");

                            var ids = $('input:checkbox[name="' + $(element).attr('data-target') + '[]"]:checked').map(function () {
                                return this.value;
                            }).get();
                            var loop = false;
                            if (typeof $(element).attr('data-single') != "undefined")
                                loop = true;
                            var ajaxFn = function (ids, element, total, first) {
                                if (total > 0) {
                                    var timeout = 0;
                                    if (first == false)
                                        timeout = 100;
                                    setTimeout(function () {
                                        var data = {};
                                        data['action'] = $(element).attr('data-action');
                                        data['id'] = ids[total - 1];
                                        core.ajaxRequest({
                                            method: 'POST',
                                            url: "/" + $(element).attr('data-url').trimLeft("#"),
                                            data: data,
                                            table: '#' + $(element).closest('div.row').find('table').attr('id'),
                                            table_url: $(element).closest('div.row').find('table').attr('data-url')
                                        }, true);
                                        ajaxFn(ids, element, total - 1, false);
                                    }, timeout);
                                }
                            };


                            if (loop == false) {
                                ajaxFn(ids, element, ids.length, true);
                                // for(var i=0; i < ids.length; i++) {
                                //
                                // }
                            }
                            else {
                                var data_loop = {};
                                data_loop['id'] = ids;
                                data_loop['action'] = $(element).attr('data-action');
                                core.ajaxRequest({
                                    method: 'POST',
                                    url: "/" + $(element).attr('data-url').trimLeft("#"),
                                    data: data_loop,
                                    table: '#' + $(element).closest('div.row').find('table').attr('id'),
                                    table_url: $(element).closest('div.row').find('table').attr('data-url')
                                }, true);
                            }
                            $(element).children("i").removeClass("fa fa-spinner fa-spin");
                            $(element).children("i").addClass(default_class);
                        }
                        swal.close();
                    });
                });
            }
        },
        passgen: function (element) {
            var passwd = '';
            var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for (var i = 1; i < 8; i++) {
                var c = Math.floor(Math.random() * chars.length + 1);
                passwd += chars.charAt(c)
            }
            $(element).closest("#input-group-password").find('input').val(passwd);
            $("#confirm-password").val(passwd);
        },
        showPasswd: function () {
            if ($('button[data-type="show-passwd"]').length) {
                var clicked_passwd = true;
                $('button[data-type="show-passwd"]').click(function () {
                    if (clicked_passwd) {
                        $(this).parent().prev().prop("type", "text");
                        clicked_passwd = false;
                    }
                    else {
                        $(this).parent().prev().prop("type", "password");
                        clicked_passwd = true;
                    }
                });
            }
        },
        generatePassword: function () {
            if ($('button[data-type="password-generate"]').length) {
                $('button[data-type="password-generate"]').unbind();
                $('button[data-type="password-generate"]').on('click', function () {
                    core.passgen(this);
                });
            }
        },
        changeServicePlan: function () {
            if ($("#template_select").length) {
                $("#template_select").change(function () {
                    if ($("#template_select").val() != 0) {
                        var data = {};
                        data[lvar["stk"]] = lvar["stc"];
                        data['template'] = $("#template_select").val();
                        $.ajax({
                            url: "/" + $("#template_select").attr('data-url').trimLeft("#"),
                            data: data,
                            type: 'POST',
                            'success': function (obj) {
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
                                    expire_time: "expire_time_limit"
                                };
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
                                    access_panel: 'access_panel',
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
                                    man_backup: 'access_backup',
                                    man_database: 'access_database',
                                    man_ssl: 'access_ssl',
                                    man_email: 'access_email',
                                    man_protect_dir: 'access_protect_dir',
                                    man_template: 'access_template'
                                };
                                for (var j in permItems) {
                                    if (obj[j] != "1") {
                                        $('input[name="' + permItems[j] + '"]').iCheck('uncheck');
                                    }
                                    else {
                                        $('input[name="' + permItems[j] + '"]').iCheck('check');
                                    }
                                }
                                var hostSettings = {
                                    php: {
                                        elem: "php_support",
                                        type: "check",
                                    },
                                    php_version: {
                                        elem: "php_version",
                                        type: "select",
                                    },
                                    asp: {
                                        elem: "asp_support",
                                        type: "check",
                                    },
                                    aspnet_core: {
                                        elem: "asp_net_core_support",
                                        type: "check",
                                    },
                                    aspnet: {
                                        elem: "asp_net_support",
                                        type: "check",
                                    },
                                    aspnet_version: {
                                        elem: "asp_net_version",
                                        type: "select",
                                    },
                                    cgi: {
                                        elem: "cgi_support",
                                        type: "check",
                                    },
                                    perl: {
                                        elem: "perl_support",
                                        type: "check",
                                    },
                                    python: {
                                        elem: "python_support",
                                        type: "check",
                                    },
                                    statistic_support: {
                                        elem: "web_statistics_support",
                                        type: "check",
                                    },
                                    statistic_type: {
                                        elem: "web_statistics_type",
                                        type: "select",
                                    },
                                    application_pool_mode: {
                                        elem: "application_pool_mode",
                                        type: "select",
                                    },
                                    dedicated_pool: {
                                        elem: "dedicated_pool",
                                        type: "check",
                                    },
                                    max_cpu: {
                                        elem: "max_cpu_usage",
                                        type: "text",
                                    },
                                    overuse: {
                                        "elem": "overuse",
                                        type: "check",
                                    },
                                };
                                for (var j in hostSettings) {
                                    if (hostSettings[j].type == "check") {
                                        if (obj[j] != "1") {
                                            $('input[name="' + hostSettings[j].elem + '"]').iCheck('uncheck');
                                        }
                                        else {
                                            $('input[name="' + hostSettings[j].elem + '"]').iCheck('check');
                                        }
                                    }
                                    else if (hostSettings[j].type == "select") {
                                        $('#' + hostSettings[j].elem).selectpicker('val', obj[j]);
                                        $('#' + hostSettings[j].elem).selectpicker('refresh');
                                    }
                                    else {
                                        $('input[name="' + hostSettings[j].elem + '"]').val(obj[j]);
                                    }

                                }
                            }
                        });
                    }
                });
            }
        },
        changeDnsType: function () {
            if ($('#record_type').length) {
                $('#record_type').on('change', function () {
                    $("#ns_record_block_name").css('display', (this.value == 'NS') ? 'flex' : 'none');
                    $("#ns_record_block_ns").css('display', (this.value == 'NS') ? 'flex' : 'none');
                    $("#a_record_block_name").css('display', (this.value == 'A') ? 'flex' : 'none');
                    $("#a_record_block_ip").css('display', (this.value == 'A') ? 'flex' : 'none');
                    $("#cname_record_block_name").css('display', (this.value == 'CNAME') ? 'flex' : 'none');
                    $("#cname_record_block_cname").css('display', (this.value == 'CNAME') ? 'flex' : 'none');
                    $("#mx_record_block").css('display', (this.value == 'MX') ? 'flex' : 'none');
                    $("#mx_record_block_name").css('display', (this.value == 'MX') ? 'flex' : 'none');
                    $("#mx_record_block_priority").css('display', (this.value == 'MX') ? 'flex' : 'none');
                    $("#ptr_record_host").css('display', (this.value == 'PTR') ? 'flex' : 'none');
                    $("#ptr_record_block_name").css('display', (this.value == 'PTR') ? 'flex' : 'none');
                    $("#txt_record_block").css('display', (this.value == 'TXT') ? 'flex' : 'none');
                    $("#txt_record_block_name").css('display', (this.value == 'TXT') ? 'flex' : 'none');
                    $(".srv_record_block").css('display', (this.value == 'SRV') ? 'flex' : 'none');
                });
            }
        },
        ajaxFilePost: function (object) {
            var obj;
            $(object).prop('disabled', true);
            $(object).children("i").removeClass("fa fa-check-circle");
            $(object).children("i").addClass("fa fa-spinner fa-spin");

            var form = $(object).closest('form');
            var form_id = $(object).closest('form').attr('id');

            $.ajax({
                url: "/" + $(object).closest('form').attr('action').trimLeft("#"),
                type: "POST",
                data: new FormData(form[0]),
                processData: false,
                contentType: false,
                success: function (obj) {
                    var result = core.jsonParse(obj);
                    if (result['type'] == 'success') {
                        core.clearErrorLogs(form_id);
                        core.showToast(result['type'], "fa-check", result['title'], result['messages']);
                    }
                    else {
                        core.initErrorLogs(result['errors'], form_id);
                        var err_message = result['messages'];
                        if (typeof result['errors']['content'] != "undefined") {
                            err_message = result['errors']['content'];
                        }
                        core.showToast(result['type'], "fa-exclamation", result['title'], err_message);
                    }
                    cookie = result['cookies'];
                    if (result['login'] == false) {
                        if ($('#login-container').length !== 1) {
                            JQD.showLogin();
                        }
                    }
                },
                error: function () {
                    core.showToast("error", "fa-exclamation", "Request error", "An error has occured");
                },
                complete: function (XMLHttpRequest, textStatus) {
                    core.ajaxLog(XMLHttpRequest);
                }
            });


            $(object).children("i").removeClass("fa fa-spinner fa-spin");
            $(object).children("i").addClass("fa fa-check-circle");
            $(object).prop('disabled', false);
        },
        fadeOutIn: function (object) {
            $(object).fadeOut(500);
            $(object).fadeIn(500);
        },
        networkFormat: function () {
            if ($('#network-format').length) {
                var state = false;
                $('#network-format').on('ifChecked ifUnchecked', function (event) {
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
                    document.getElementById('netmask_cidr').value = $("#netmask").val();
                });
                $("#netmask_cidr").change(function () {
                    document.getElementById('netmask').value = $("#netmask_cidr").val();
                });
            }
        },
        chart: function (chart_id, start_val, end_val) {
            $('#' + chart_id).easyPieChart({
                barColor: function (percent) {
                    return (percent < 50 ? '#19aa8d' : percent < 85 ? '#f0ad4e' : '#ef1e25');
                },
                easing: 'easeOutBounce',
                scaleLength: 7,
                trackWidth: 5,
                lineWidth: 5,
                onStep: function (start_val, end_val, start_val) {
                    $(this.el).find('.percent').text(end_val);
                }
            });
        },
        dashboardAdmin: function (usage) {
            if ($("#memory_usage").length) {
                core.chart('memory_usage', 0, usage['memory']['used']);
                core.chart('disk_usage', 0, usage['disk']['used']);
                core.chart('cpu_usage', 0, usage['cpu']['used']);

                var container = $("#network-status");
                var options = {
                    grid: {
                        hoverable: true,
                        clickable: false,
                        // color: "#999999",
                        color: '#d5d5d5',
                        // tickColor: "#ddd",
                        tickColor: "#d5d5d5",
                        borderWidth: 1,
                        minBorderMargin: 20,
                        labelMargin: 10,
                        backgroundColor: {
                            colors: ["#ffffff", "#ffffff"]
                        },
                        margin: {
                            top: 8,
                            bottom: 20,
                            left: 20
                        },

                        markings: function (axes) {
                            var markings = [];
                            var xaxis = axes.xaxis;
                            for (var x = Math.floor(xaxis.min); x < xaxis.max; x += xaxis.tickSize * 2) {
                                markings.push({
                                    xaxis: {
                                        from: x,
                                        to: x + xaxis.tickSize
                                    },
                                    color: "#fff"
                                });
                            }
                            return markings;
                        }
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: function (label, x, y) {
                            var text = "";
                            core.series.forEach(function (series) {
                                text += "<b style='color:" + series.color + "'>" + series.label + '</b> : ' + core.bytesToSize(series.data[x][1]) + "<br/>";
                            });
                            return text;
                        },
                        onHover: function (flotItem, $tooltipEl) {
                        }
                    },
                    colors: ["#1ab394"],
                    xaxis: {
                        min: 0,
                        max: 60,
                        tickSize: 10,
                        tickFormatter: function () {
                            return "";
                        }
                    },
                    yaxes: [
                        {
                            position: "left",
                            axisLabel: "Upload",
                            min: 0,
                            max: 1024 * 1024 * 1024,
                            mode: "byte",
                            minTickSize: 1024 * 1024 * 100,
                            tickSize: 1024 * 1024 * 100,
                            base: 8,
                        },
                        {
                            position: "right",
                            axisLabel: "Download",
                            min: 0,
                            max: 1024 * 1024 * 1024,
                            mode: "byte",
                            minTickSize: 1024 * 1024 * 100,
                            tickSize: 1024 * 1024 * 100,
                            base: 8,
                            tickFormatter: function () {
                                return "";
                            }
                        }
                    ],
                    legend: {
                        show: true
                    },
                };

                core.plot = $.plot(container, core.series, options);
            }
        },
        dashboardClient: function (usage) {
            if ($("#traffic_usage").length) {
                var traffic_value_first = (+usage['traffic']['used']);
                var disk_value_first = (+usage['disk']['used']);
                var dbsize_value_first = (+usage['db']['used']);

                if ((+usage['traffic']['used']) > 100) {
                    traffic_value_first = 100;
                }

                if ((+usage['disk']['used']) > 100) {
                    disk_value_first = 100;
                }
                if ((+usage['db']['used']) > 100) {
                    dbsize_value_first = 100;
                }
                core.chart('traffic_usage', 0, traffic_value_first);
                core.chart('disk_usage', 0, disk_value_first);
                core.chart('dbsize_usage', 0, dbsize_value_first);
            }
        },
        init_load_db: function (db_type) {
            for (var i in core.available_db) {
                $('#' + core.available_db[i] + '_db_create').hide();
                $('#' + core.available_db[i] + '_db_edit').hide();
                $('#' + core.available_db[i] + '_db_create select').prop('disabled', true);
            }

            $('#' + db_type + '_db_edit').show();
            $('#' + db_type + '_db_create').show();
            $('#' + db_type + '_db_create select').prop('disabled', false);
            $('.select-picker').selectpicker("refresh");
        },
        load_db: function () {
            if ($('#database_server_type').length) {
                var db_type = $('#database_server_type').val();
                core.init_load_db(db_type);
                $('#database_server_type').unbind();
                $('#database_server_type').on('change', function () {
                    var selected = $(this).val();
                    core.init_load_db(selected);
                });

                $('#btn-add-host').click(function () {
                    var host = $('#add_remote_host').val();
                    $('#remote_hosts').append("<option value='" + host + "'>" + host + "</option>");
                    $('#add_remote_host').val("");
                });
                $('#btn-remove-host').click(function () {
                    $('#remote_hosts option:selected').each(function () {
                        $(this).remove();
                    });
                });
            }
        },
        initBackupStorage: function () {
            //dropbox
            $('select#storages').on('change', function () {
                $(".gdrive").css('display', 'none');
                $(".gdrive-block").css('display', 'none');
                $(".dropbox").css('display', 'none');
                switch ($(this).val()) {
                    case "dropbox":
                        $(".dropbox").css('display', 'flex');
                        $(".gdrive").css('display', 'none');
                        $(".gdrive-block").css('display', 'none');
                        // $('#repo').attr('disabled',false);
                        break;
                    case "gdrive":
                        $(".gdrive").css('display', 'flex');
                        $(".gdrive-block").css('display', 'block');
                        $(".dropbox").css('display', 'none');
                        // $('#storages').attr('disabled',false);
                        break;
                }
            });
        },
        initDomainBackup: function () {
            $('#backup_repo').on('change', function () {
                $(".ftp_repos").css('display', 'none');
                $(".cloud_storages").css('display', 'none');
                switch ($(this).val()) {
                    case "ftp":
                        $(".ftp_repos").css('display', 'flex');
                        $('#repo').attr('disabled', false);
                        break;
                    case "storage":
                        $(".cloud_storages").css('display', 'flex');
                        $('#storages').attr('disabled', false);
                        break;
                }
            });
        },
        setSideBarStatus: function () {
            $('.navbar-minimalize').unbind();
            $('.navbar-minimalize').on("click", function () {
                if ($('body').hasClass("mini-navbar")) {
                    $.cookie('sidebar', 'false', {path: '/', expires: 86400 * 365});
                }
                else {
                    $.cookie('sidebar', 'true', {path: '/', expires: 86400 * 365});
                }
            });
            if ($.cookie("sidebar") == "true") {
                $('body').addClass("mini-navbar");
            }
            else {
                $('body').removeClass("mini-navbar");
            }
        },
        crontabs: function () {
            $("#minute_select").change(function () {
                document.getElementById('minute').value = $("#minute_select").val();
            });
            $("#hour_select").change(function () {
                document.getElementById('hour').value = $("#hour_select").val();
            });
            $("#day_select").change(function () {
                document.getElementById('day').value = $("#day_select").val();
            });
            $("#week_select").change(function () {
                document.getElementById('week').value = $("#week_select").val();
            });
            $("#month_select").change(function () {
                document.getElementById('month').value = $("#month_select").val();
            });

            var is_checked = $('input[name="php_script"]').parent().hasClass("checked");
            if (is_checked) {
                $("div#php-scripting").show();
                $("div#sys-command").hide();
            }
            else {
                $("div#php-scripting").hide();
                $("div#sys-command").show();
            }
            $('input[name="php_script"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("div#php-scripting").show();
                    $("div#sys-command").hide();
                }
                else {
                    $("div#php-scripting").hide();
                    $("div#sys-command").show();
                }
                event.preventDefault();
            });
        },
        mailServer: function () {
            const POSTFIX = 0;
            const MAILENABLE = 1;
            const HMAIL = 2;
            const MSEXCHANGE = 3;
            const QMAIL = 4;

            var installed_mail_server = $("#mailserver-general-settings-form").data("mail-server");
            if (installed_mail_server == POSTFIX) {
                // core.disableEnableElements();
                $('#add_to_rbl_list').click(function () {
                    var allowed_ip = $('#add_rbl_list').val();
                    $('#rbl_list').append("<option value='" + allowed_ip + "'>" + allowed_ip + "</option>");
                    $('#add_rbl_list').val('');
                });
                $('#remove_from_rbl_list').click(function () {
                    $('#rbl_list option:selected').each(function () {
                        $(this).remove();
                    });
                });
            }

        },
        databaseServer: function () {
            if ($('#slow_query_log').val() == "on") {
                $('#slow_query_log_file').prop('disabled', false);
            }
            else {
                $('#slow_query_log_file').prop('disabled', true);
            }
            $('#slow_query_log').on('click', function () {
                if ($('#slow_query_log').val() == "on") {
                    $('#slow_query_log_file').prop('disabled', false);
                }
                else {
                    $('#slow_query_log_file').prop('disabled', true);
                }
            });
        },
        telegram: function () {
            $('#activation_modal').on('shown.bs.modal', function (e) {
                $('#activate_code').unbind();
                $('#activate_code').on('click', function () {
                    var data = {};
                    data['code'] = $("#activation_code").val();

                    var result = core.ajaxRequest({
                        method: 'POST',
                        url: "/" + $("#activation-code-form").attr("action").trimLeft("#"),
                        data: data
                    }, true);

                    if (result['type'] == "success") {
                        $('#activation_modal').modal('hide');
                        $("#activation_code").val("");
                    }
                });
                e.preventDefault();
            });
        },
        domainMail: function () {
            $('input[id="forward_to_address"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#mail_address').prop('disabled', false);
                    $('#reject_incoming').iCheck("uncheck");
                }
                else {
                    $('#mail_address').prop('disabled', true);
                    $('#reject_incoming').iCheck("check");
                }
            });
            $('input[id="forwarding"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#forward_to_address').prop('disabled', false);
                }
                else {
                    $('#forward_to_address').prop('disabled', true);
                }
            });

            $('input[id="reject_incoming"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#mail_address').prop('disabled', true);
                    $('#forward_to_address').iCheck("uncheck");
                }
                else {
                    $('#mail_address').prop('disabled', false);
                    $('#forward_to_address').iCheck("check");
                }
            });
            $('input[name="unlimited_quota"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('#mail_quota').prop('disabled', true);
                }
                else {
                    $('#mail_quota').prop('disabled', false);
                }
            });

        },
        vdir: function () {
            $('#btn-up').unbind();
            $('#btn-up').on('click', function () {
                $('#default_document_order option:selected').each(function () {
                    var newPos = $('#default_document_order option').index(this) - 1;
                    if (newPos > -1) {
                        $('#default_document_order option').eq(newPos).before("<option value='" + $(this).val() + "' selected='selected'>" + $(this).text() + "</option>");
                        $(this).remove();
                    }
                });
                $('#default_document_order').selectMultiple('refresh');

            });

            $('#btn-down').unbind();
            $('#btn-down').on('click', function () {
                var countOptions = $('#default_document_order option').length;
                $('#default_document_order option:selected').each(function () {
                    var newPos = $('#default_document_order option').index(this) + 1;
                    if (newPos < countOptions) {
                        $('#default_document_order option').eq(newPos).after("<option value='" + $(this).val() + "' selected='selected'>" + $(this).text() + "</option>");
                        $(this).remove();
                    }
                });
                $('#default_document_order').selectMultiple('refresh');

            });

            $('#btn-add').unbind();
            $('#btn-add').on('click', function () {
                var defaultDocument = $('#default_document_add').val();
                $('#default_document_order').append("<option selected>" + defaultDocument + "</option>");
                $('#default_document_order').selectMultiple('refresh');
                $('#default_document_add').val("");
            });

            $('#btn-remove').unbind();
            $('#btn-remove').on('click', function () {
                $('#default_document_order option:selected').each(function () {
                    $(this).remove();
                });
                $('#default_document_order').selectMultiple('refresh');
            });

            $('#segment-add').click(function () {
                var defaultDocument = $('#add_hidden_segments').val();
                $('#hidden_segments').append("<option selected>" + defaultDocument + "</option>");
                $('#add_hidden_segments').val('');
                $('#hidden_segments').selectMultiple('refresh');
            });
            $('#segment-remove').click(function () {
                $('#hidden_segments option:selected').each(function () {
                    $(this).remove();
                });
                $('#hidden_segments').selectMultiple('refresh');
            });
        },
        windowsSettings: function () {
            $('input[id="asp_net_support"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('select[id="asp_net_version').prop('disabled', false);
                    // $('select[id="application_pool_mode').prop('disabled', false);
                }
                else {
                    $('select[id="asp_net_version').prop('disabled', true);
                    // $('select[id="application_pool_mode').prop('disabled', true);
                }
            });
        },
        redirect: function (url) {
            window.setTimeout(function () {
                window.location.href = url;
            }, 1500);
        },
        sessionSettings: function () {
            $('#allow_ip_address').on('ifChecked ifUnchecked', function (event) {
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
            $('#block_ip_address').on('ifChecked ifUnchecked', function (event) {
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
            $('#add-ip-allow').click(function (e) {
                var allowed_ip = $('#add_to_allow_list').val();
                if (core.validateIp(allowed_ip) == true) {
                    $('select#allowed_ip_addresses').append("<option value='" + allowed_ip + "'>" + allowed_ip + "</option>");
                    $('#add_to_allow_list').val('');
                    $('select#allowed_ip_addresses').parent().removeClass('has-error');
                }
                else {
                    $('select#allowed_ip_addresses').parent().addClass('has-error');
                    core.showToast("error", "fa-exclamation", lvar['error'], lvar['enter_valid_ip']);
                }
            });

            $('#remove-ip-allow').click(function (e) {
                $("select#allowed_ip_addresses").find('option:selected').remove().end();
            });
            $('#add-ip-block').click(function (e) {
                var blocked_ip = $('#add_to_block_list').val();
                if (core.validateIp(blocked_ip) == true) {
                    $('select#blocked_ip_addresses').append("<option value='" + blocked_ip + "'>" + blocked_ip + "</option>");
                    $('#add_to_block_list').val('');
                }
                else {
                    $('select#blocked_ip_addresses').parent().addClass('has-error');
                    core.showToast("error", "fa-check", lvar['error'], lvar['enter_valid_ip']);
                }
            });
            $('#remove-ip-block').click(function (e) {
                $("select#blocked_ip_addresses").find('option:selected').remove().end();
            });
            $("#submit-form").click(function (e) {
                let timelimit = parseInt($("input#session_time").val());
                time_limit = (timelimit*1000).toString();
                core.timelimit = timelimit*1000;
                core.time_left = timelimit*1000;
                e.preventDefault();
            });
        },
        webServer: function () {
            //
            $('input[name="perl_support"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('input[id="perl_extension"]').prop('disabled', false);
                }
                else {
                    $('input[id="perl_extension"]').prop('disabled', true);
                }
            });
            $('input[name="python_support"]').on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $('input[id="python_extension"]').prop('disabled', false);
                }
                else {
                    $('input[id="python_extension"]').prop('disabled', true);
                }
            });
        },
        codeMirror: function (id) {
            var cm = CodeMirror.fromTextArea(document.getElementById(id), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "apache",
                keyMap: "sublime",
            });
            cm.on('change', function () {
                cm.save();
            });
        },
        paginate: function (container, selector) {
            new purePajinate({
                containerSelector: container,
                itemSelector: selector,
                navigationSelector: '.page_navigation',
                defaultClass: '',
                activeClass: "active",
                disabledClass: "disabled",
                itemsPerPage: 12,
            });
        },
        sourceList: function () {
            $('.pretty-code').each(function (index, elem) {
                core.codeMirror($(elem).attr("id"));
            });
            $(".save-source-list").unbind();
            $(".save-source-list").on("click", function (e) {
                var url = $(this).attr('data-url');
                var name = $(this).attr('data-name');
                var content = $("textarea#" + $(this).attr('data-name')).val();
                //message, url, table, table_url
                core.showConfirmPost(lvar["are_you_sure"], $(this).attr('data-url'), {
                    data: {
                        name: name,
                        content: content
                    }
                });
                e.preventDefault();
            });

            $(".remove-source-list").unbind();
            $(".remove-source-list").on("click", function (e) {
                var url = $(this).attr('data-url');
                var name = $(this).attr('data-name');

                swal({
                    title: lvar["confirm"],
                    text: lvar['are_you_sure'],
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: lvar["submit"],
                    closeOnConfirm: false
                }, function (confirmed) {
                    if (confirmed) {
                        var result = core.delete({
                            method: 'POST',
                            url: "/" + url.trimLeft("#"),
                            data: {
                                name: name,
                            },
                            message: true
                        });
                        if (typeof result['type'] != "undefined") {
                            if (result['type'] == "success") {
                                core.redirect(result['redirect_url']);
                            }
                        }
                    }
                    swal.close();
                });
                e.preventDefault();
            });

            $("#create-source-list").unbind();
            $("#create-source-list").on("click", function (e) {
                var _url = $(this).attr("data-url");
                $("#new_source_list").modal("show");
                $('#new_source_list').on('shown.bs.modal', function (e) {
                    $('#save_new_source_list').unbind();
                    $('#save_new_source_list').on('click', function () {
                        var data = {};
                        data['content'] = $("#new_source_content").val();
                        data['name'] = $("#new_source_name").val();

                        var result = core.ajaxRequest({
                            method: 'POST',
                            url: "/" + _url.trimLeft("#"),
                            data: data
                        }, true);

                        if (result['type'] == "success") {
                            $('#new_source_list').modal('hide');
                            core.redirect(result["redirect_url"]);
                        }
                        else {
                            core.showToast("error", "fa-exclamation", result['title'], result['content']);
                        }
                    });

                    $('#new_source_list').on('hidden.bs.modal', function (e) {
                        $('#new_source_content').html('');
                        $('#new_source_name').val('');
                        e.preventDefault();
                    });
                    e.preventDefault();
                });
            });
        },
        replaceAll: function (str, find, replace) {
            return str.toString().replace(new RegExp(find, 'g'), replace);
        },
        prettyPrint: function () {
            $("#json_data").on("paste", function (e) {
                setTimeout(function () {
                    var ugly = document.getElementById('json_data').value;
                    var obj = JSON.parse(ugly);
                    var pretty = JSON.stringify(obj, undefined, 4);
                    document.getElementById('json_data').value = pretty;
                }, 0);
            });

        },
        initErrorMessages: function () {
            try {
                var json_obj = JSON.parse(errors);
                if (json_obj.length > 0) {
                    core.initErrorLogs(json_obj, $("#" + Object.keys(json_obj)[0]).closest("form").attr("id"));
                }
            } catch (e) {
            }
        },
        formatTimer: function (time) {
            const minutes = Math.floor(time / 60);
            var seconds = time % 60;
            if (seconds < 10) {
                seconds = `0${seconds}`;
            }
            return `${minutes}:${seconds}`;
        },
        setRemainingPathColor: function (percent) {
            const {alert, warning, info} = core.TIMER_COLOR_CODES;
            let path_remaining = document
                .getElementById("base-timer-path-remaining");
            if (percent <= alert.threshold) {
                path_remaining.classList.remove(warning.color);
                path_remaining.classList.add(alert.color);
                $(".base-timer").removeClass("pulse5-animation");
                $(".base-timer").addClass("pulse-animation");
            }
            else if (percent <= warning.threshold) {
                path_remaining.classList.remove(info.color);
                path_remaining.classList.add(warning.color);
                $(".base-timer").addClass("pulse5-animation");
            } else {
                path_remaining.classList.remove(warning.color);
                path_remaining.classList.remove(alert.color);
                path_remaining.classList.add(info.color);
            }
        },
        calculateTimeFraction: function (time_left, time_limit) {
            const rawTimeFraction = time_left / time_limit;
            return rawTimeFraction - (1 / time_limit / 1000) * (1 - rawTimeFraction);
        },
        setCircleDasharray: function (time_left, time_limit) {
            const circleDasharray = `${(
                core.calculateTimeFraction(time_left, time_limit) * 283
            ).toFixed(0)} 283`;
            document
                .getElementById("base-timer-path-remaining")
                .setAttribute("stroke-dasharray", circleDasharray);
        },
        startTimer: function () {
            core.sess_time = now();
            core.intervalId = setInterval(core.update, tickRate);
        },
        update: function(){
            if(core.reset_timer) {
                before_time = 0;
                core.reset_timer = false;
                $(".base-timer").removeAttr("class");
                $("#countdown-app div").addClass("base-timer");
            }
            else {
                if ((typeof window.localStorage.reset_timer != "reset_timer") && window.localStorage.reset_timer == "1") {
                    before_time = 0;
                    window.localStorage.removeItem("reset_timer");
                    core.sess_time = parseInt(window.localStorage.sess_time);
                    core.timelimit = parseInt(window.localStorage.time_limit);
                    core.time_left = parseInt(window.localStorage.time_left);
                    window.localStorage.removeItem("sess_time");
                    $(".base-timer").removeAttr("class");
                    $("#countdown-app div").addClass("base-timer");
                }
            }

            let time_passed = (now() - core.sess_time + tickRate);
            if(Math.floor(before_time/1000) < Math.floor(time_passed/1000)) {
                before_time = time_passed;
                core.time_left = core.timelimit - time_passed;
                if (Math.floor(core.time_left/1000) <= 0) {
                    setTimeout(() => {
                        core.resetSessionTimer(core.timelimit, core.time_left);
                        location.href = "/";
                        window.location.reload();
                    }, 1000);
                }
                document.getElementById("base-timer-label").innerHTML = core.formatTimer(Math.floor(core.time_left/1000));
                core.setCircleDasharray(core.time_left, core.timelimit);
                core.setRemainingPathColor(core.time_left/core.timelimit*100);
            }
        },
        resetSessionTimer: function (timelimit, timeleft) {
            before_time = 0;
            let sess_time = (now());
            core.timelimit = timelimit;
            core.time_left = timeleft;
            core.sess_time = sess_time;
            window.localStorage.setItem('sess_time', sess_time.toString());
            window.localStorage.setItem('time_limit', core.timelimit);
            window.localStorage.setItem('time_left', core.time_left);
            window.localStorage.setItem('reset_timer', "1");
            core.reset_timer = true;
        },
        isNumeric: function (str) {
            return /^[0-9]*$/.test(str);
        },
        sessionCountTimer: function () {
            if ((isNaN(core.timelimit)) && !core.isNumeric(core.timelimit) && (core.timelimit === 0)) {
                return false;
            }
            if ($("#countdown-app").length === 0)
                return false;

            document.getElementById("countdown-app").innerHTML = `<div class="base-timer"><svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g class="base-timer__circle"><circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle><path id="base-timer-path-remaining" stroke-dasharray="283" class="base-timer__path-remaining ${this.TIMER_COLOR_CODES.info.color}" d="M 50, 50 m -45, 0 a 45,45 0 1,0 90,0 a 45,45 0 1,0 -90,0"></path></g></svg><span id="base-timer-label" class="base-timer__label">0</span></div>`;
            core.startTimer();
        },
        spamLists: function () {
            $('#create_blacklist_modal').on('shown.bs.modal', function (e) {
                $('#create_blacklist_modal #submit').unbind();
                $('#create_blacklist_modal #submit').on('click', function () {
                    var data = {};
                    data[lvar["stk"]] = lvar["stc"];
                    data['blacklist_rule'] = $("#blacklist_rule").val();

                    var result = core.ajaxRequest({
                        method: 'POST',
                        url: "/" + $("#create_blacklist_modal").attr("data-url").trimLeft("#"),
                        data: data,
                        table: '#' + $(".dynamic-table").attr('id'),
                        table_url: $(".dynamic-table").attr('data-url')
                    }, true);

                    if (result['type'] == "success") {
                        $('#create_blacklist_modal').modal('hide');
                        $("#blacklist_rule").val("");
                    }
                });
                e.preventDefault();
            });

            $('#create_whitelist_modal').on('shown.bs.modal', function (e) {
                $('#create_whitelist_modal #submit').unbind();
                $('#create_whitelist_modal #submit').on('click', function () {
                    var data = {};
                    data[lvar["stk"]] = lvar["stc"];
                    data['whitelist_rule'] = $("#whitelist_rule").val();

                    var result = core.ajaxRequest({
                        method: 'POST',
                        url: "/" + $("#create_whitelist_modal").attr("data-url").trimLeft("#"),
                        data: data,
                        table: '#' + $(".dynamic-table").attr('id'),
                        table_url: $(".dynamic-table").attr('data-url')
                    }, true);

                    if (result['type'] == "success") {
                        $('#create_whitelist_modal').modal('hide');
                        $("#whitelist_rule").val("");
                    }
                });
                e.preventDefault();
            });
        },
        generalSelectPkg: function () {
            $("#phpmyadmin").prop('disabled', true).iCheck('update');
            $("#roundcube").prop('disabled', true).iCheck('update');
            var scriptings = ["php56", "php72", "php74", "python", "perl", "phpmyadmin", "mylittleadmin", "awstats", "webalizer", "roundcube"];
            var web_servers = ["iis", "apache", "nginx"];
            for (var i in scriptings) {
                $("#" + scriptings[i]).on('ifChecked ifUnchecked', function (event) {
                    if (event.type == 'ifChecked') {
                        for (var w in web_servers) {
                            if (web_servers[w] == "iis") {
                                $("#" + web_servers[w]).iCheck('check');
                            }
                            else if (web_servers[w] == "apache") {
                                if (!$("#" + web_servers[w]).is(':disabled')) {
                                    if (!$("#nginx").is(':checked')) {
                                        $("#apache").iCheck('check');
                                    }
                                }
                            }
                        }
                    }
                });
            }
            var phps = ["php56", "php72", "php74"];
            for (var p in phps) {
                $("#" + phps[p]).on('ifChecked ifUnchecked', function (event) {
                    if (event.type == 'ifChecked') {
                        $("#phpmyadmin").prop('disabled', false).iCheck('update');
                        $("#roundcube").prop('disabled', false).iCheck('update');
                    }
                    else {
                        core.checkStatusPhpChecked();
                    }
                });
            }
        },
        linuxSelectPkg: function () {
            var scriptings = ["php56", "php72", "php74", "python", "perl", "phpmyadmin", "mylittleadmin", "awstats", "webalizer", "roundcube"];

            $("#apache").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#nginx").iCheck('uncheck');
                    $("#proftpd").iCheck('check');
                }
                else {
                    $("#proftpd").iCheck('uncheck');
                    var apache_status = $("#apache").is(':checked');
                    var nginx_status = $("#nginx").is(':checked');
                    if (!nginx_status && !apache_status) {
                        for (var i in scriptings) {
                            $("#" + scriptings[i]).iCheck('uncheck');
                        }
                    }
                }
            });

            $("#nginx").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#apache").iCheck('uncheck');
                    $("#proftpd").iCheck('check');
                }
                else {
                    $("#proftpd").iCheck('uncheck');
                    var apache_status = $("#apache").is(':checked');
                    var nginx_status = $("#nginx").is(':checked');
                    if (!nginx_status && !apache_status) {
                        for (var i in scriptings) {
                            $("#" + scriptings[i]).iCheck('uncheck');
                        }
                    }
                }
            });


            $("#mysql").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#mariadb").iCheck('uncheck');
                }
            });
            $("#mariadb").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#mysql").iCheck('uncheck');
                }
            });

            $("#postfix").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#dovecot").iCheck('check');
                    $('#spamassassin').prop('disabled', false).iCheck('update');
                }
                else {
                    $("#dovecot").iCheck('uncheck');
                    $('#spamassassin').prop('disabled', true).iCheck('update');
                }
            });
            $("#dovecot").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#postfix").iCheck('check');
                    $('#spamassassin').prop('disabled', false).iCheck('update');
                }
                else {
                    $("#postfix").iCheck('uncheck');
                    $('#spamassassin').prop('disabled', true).iCheck('update');
                }
            });
        },
        windowsSelectPkg: function () {
            $('#spamassassin').prop('disabled', true).iCheck('update');
            var scriptings = ["php56", "php72", "php74", "python", "perl", "phpmyadmin", "mylittleadmin", "awstats", "webalizer", "roundcube"];
            $("#iis").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifUnchecked') {
                    for (var i in scriptings) {
                        $("#" + scriptings[i]).iCheck('uncheck');
                    }
                }
            });
            $("#bind").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#msdns").iCheck('uncheck');
                }
            });
            $("#msdns").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#bind").iCheck('uncheck');
                }
            });
            $("#mssql12").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#mssql16").iCheck('uncheck');
                }
            });
            $("#mssql16").on('ifChecked ifUnchecked', function (event) {
                var mssql12 = $("#mssql12").is(':disabled');
                if (!mssql12) {
                    if (event.type == 'ifChecked') {
                        $("#mssql12").iCheck('uncheck');
                    }
                }
            });

            $("#mysql57").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#mysql80").iCheck('uncheck');
                }
            });
            $("#mysql80").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#mysql57").iCheck('uncheck');
                }
            });
            $("#hmail").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#mailenable").iCheck('uncheck');
                    $('#spamassassin').prop('disabled', false).iCheck('update');
                }
                else {
                    $('#spamassassin').prop('disabled', true).iCheck('update');
                }
            });
            $("#mailenable").on('ifChecked ifUnchecked', function (event) {
                if (event.type == 'ifChecked') {
                    $("#hmail").iCheck('uncheck');
                    $('#spamassassin').prop('disabled', false).iCheck('update');
                }
                else {
                    $('#spamassassin').prop('disabled', true).iCheck('update');
                }
            });

        },
        checkStatusPhpChecked: function () {
            var phps = ["php56", "php72", "php74"];
            var checked = false;
            for (var p in phps) {
                if ($("#" + phps[p]).prop("checked")) {
                    checked = true;
                }
            }
            if (!checked) {
                $("#phpmyadmin").prop('disabled', true).iCheck('update');
                $("#roundcube").prop('disabled', true).iCheck('update');
            }
        },
        serverCreate: function () {
            is_async_step = false;
            const CONNECTION = 0;
            const GET_INFO = 1;
            const INSTALL = 2;
            const FINISH = 3;
            var lin_pkgs = ["apache", "nginx", "mariadb", "proftpd", "mariadb", "postfix", "dovecot", "mysql"];
            var win_pkgs = ["iis", "mylittleadmin", "msdns", "mssql12", "mssql16", "mysql57", "mysql80", "hmail", "mailenable"];
            var post_data, options, server_id;
            $(".wizard-big").steps({
                bodyTag: "fieldset",
                enableContentCache: false,
                onStepChanging: function (event, currentIndex, newIndex) {
                    if (is_async_step) {
                        is_async_step = false;
                        return true;
                    }
                    var server_hostname;
                    if (currentIndex === CONNECTION) {
                        post_data = $("form#server-connect").serialize();
                        options = {
                            async: true,
                            method: 'POST',
                            url: "/" + $("form#server-connect").attr("data-url").trimLeft("#"),
                            data: post_data,
                            form: "server-connect",
                        };
                        options.before = function () {
                            $('#server-connect').closest(".ibox-content").prepend(spinner);
                            $('#server-connect').closest(".ibox-content").toggleClass('sk-loading');

                            $(".wizard-big .actions a[href*=\"#next\"] i").attr("class", "fa fa-spinner fa-spin");
                            $(".wizard-big .actions a[href*=\"#next\"] i").attr('disabled', true);
                        };
                        options.success = function (data) {
                            if (typeof data.sinfo != "undefined") {
                                server_hostname = data["sinfo"]['result']["hostname"];
                                $("span#hostname").text(data["sinfo"]['result']["hostname"]);
                                $("span#ip_address").text(data["sinfo"]['result']["default_ip"]);
                                $("span#operating_system").text(data["sinfo"]['result']["os"]);
                                $("span#cpu_brand").text(data["sinfo"]['result']["cpu"]);
                                $("span#total_memory").text(data["sinfo"]['result']["memory"]["used"] + " / " + data["sinfo"]['result']["memory"]["total"]);
                                $("span#total_disk").text(data["sinfo"]['result']["disk"]["used"] + " / " + data["sinfo"]['result']["disk"]["total"]);
                                $("#description").val(data["sinfo"]['result']["os"]);
                                if ($("select#server_os").val() == "linux") {
                                    $("i#os_icon").attr("class", "fp fp-linux");
                                    for (var i in lin_pkgs) {
                                        $('[data-id="' + lin_pkgs[i] + '"').show();
                                        $('[data-id="' + lin_pkgs[i] + '"').find("input").prop('disabled', false).iCheck('update');
                                    }
                                    for (var i in win_pkgs) {
                                        $('[data-id="' + win_pkgs[i] + '"').hide();
                                        $('[data-id="' + win_pkgs[i] + '"').find("input").prop('disabled', true).iCheck('update');
                                    }
                                }
                                else {
                                    $("i#os_icon").attr("class", "fp fp-windows");
                                    var is_core = data["is_core"];
                                    for (var i in lin_pkgs) {
                                        $('[data-id="' + lin_pkgs[i] + '"').hide();
                                        $('[data-id="' + lin_pkgs[i] + '"').find("input").prop('disabled', true).iCheck('update');
                                    }
                                    for (var i in win_pkgs) {
                                        $('[data-id="' + win_pkgs[i] + '"').show();
                                        $('[data-id="' + win_pkgs[i] + '"').find("input").prop('disabled', false).iCheck('update');
                                    }
                                    if (is_core) {
                                        $("input#mssql12").prop('disabled', true).iCheck('update');
                                        $("input#hmail").prop('disabled', true).iCheck('update');
                                        $("input#mailenable").prop('disabled', true).iCheck('update');
                                    }
                                }
                                is_async_step = true;
                                $(".wizard-big").steps("next");
                                return true;
                            }
                            else {
                                return false;
                            }
                            return false;
                        };
                        options.error = function () {
                            return false;
                        };
                        options.complete = function () {
                            $(".wizard-big .actions a[href*=\"#next\"] i").removeAttr('disabled');
                            $(".wizard-big .actions a[href*=\"#next\"] i").attr("class", "fa fa-step-forward");
                            $('#server-connect').closest(".ibox-content").find(".sk-spinner").remove();
                            $('#server-connect').closest(".ibox-content").toggleClass('sk-loading');
                        };
                        core.ajaxRequest(options, true);
                        return false;
                    }
                    else if (currentIndex === GET_INFO) {
                        return true;
                    }
                    else if (currentIndex === INSTALL) {
                        post_data = $("form#select-services").serialize();
                        options = {
                            async: true,
                            method: 'POST',
                            url: "/" + $("form#select-services").attr("data-url").trimLeft("#"),
                            data: post_data,
                            form: "select-services",
                        };
                        options.before = function () {
                            $('#select-services').closest(".ibox-content").prepend(spinner);
                            $('#select-services').closest(".ibox-content").toggleClass('sk-loading');

                            $(".wizard-big .actions a[href*=\"#next\"] i").attr("class", "fa fa-spinner fa-spin");
                            $(".wizard-big .actions a[href*=\"#next\"] i").attr('disabled', true);
                        };
                        options.success = function (data) {
                            if ((typeof data.type != "undefined") && (data.type == "success")) {
                                $(".sql-pass").html(data.sql_pass);
                                if (!data.server_data.DATABASE) {
                                    $("#database_limit").attr("disabled", "disabled");
                                }
                                if (!data.server_data.MAIL) {
                                    $("#mail_limit").attr("disabled", "disabled");
                                }
                                if (!data.server_data.WEB) {
                                    $("#domain_limit").attr("disabled", "disabled");
                                }
                                $("input#server_name").val(server_hostname);
                                $("input#server_name").focus();
                                $("input#server_name").addClass("error");
                                server_id = data.server_id;
                                is_async_step = true;
                                $(".wizard-big").steps("next");
                                return true;
                            }
                            else {
                                $(".wrapper-content .ibox-content").find(".alert").remove();
                                var html_template = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
                                for (var i in data.errors) {
                                    html_template += data.errors[i] + "<br />";
                                }
                                html_template += '</div>';
                                $(".wrapper-content .ibox-content").prepend(html_template);
                                return false;
                            }
                        };
                        options.error = function () {
                            return false;
                        };
                        options.complete = function () {
                            $(".wizard-big .actions a[href*=\"#next\"] i").removeAttr('disabled');
                            $(".wizard-big .actions a[href*=\"#next\"] i").attr("class", "fa fa-step-forward");
                            $('#select-services').closest(".ibox-content").find(".sk-spinner").remove();
                            $('#select-services').closest(".ibox-content").toggleClass('sk-loading');
                        };
                        core.ajaxRequest(options, true);
                        return false;
                    }
                    var form = $(this);
                    if (currentIndex < newIndex) {
                        // To remove error styles
                        $(".body:eq(" + newIndex + ") label.error", form).remove();
                        $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
                    }
                },
                onStepChanged: function (event, currentIndex, newIndex) {
                    return true;
                },
                onFinishing: function (event, currentIndex) {
                    if (is_async_step) {
                        is_async_step = false;
                        return true;
                    }
                    post_data = $("form#save-server").serialize();
                    post_data = "server_id=" + server_id + "&" + post_data;
                    options = {
                        async: true,
                        method: 'POST',
                        url: "/" + $("form#save-server").attr("data-url").trimLeft("#"),
                        data: post_data,
                        form: "save-server",
                    };
                    options.before = function () {
                        $('#save-server').closest(".ibox-content").prepend(spinner);
                        $('#save-server').closest(".ibox-content").toggleClass('sk-loading');

                        $(".wizard-big .actions a[href*=\"#next\"] i").attr("class", "fa fa-spinner fa-spin");
                        $(".wizard-big .actions a[href*=\"#next\"] i").attr('disabled', true);
                    };
                    options.success = function (data) {
                        is_async_step = true;
                        return true;
                    };
                    options.error = function () {
                        return false;
                    };
                    options.complete = function () {
                        $(".wizard-big .actions a[href*=\"#next\"] i").removeAttr('disabled');
                        $(".wizard-big .actions a[href*=\"#next\"] i").attr("class", "fa fa-step-forward");
                        $('#save-server').closest(".ibox-content").find(".sk-spinner").remove();
                        $('#save-server').closest(".ibox-content").toggleClass('sk-loading');
                    };
                    core.ajaxRequest(options, true);
                },
                onFinished: function (event, currentIndex) {
                    return false;
                }
            });
            core.generalSelectPkg();
            core.linuxSelectPkg();
            core.windowsSelectPkg();
            $("select#server_os").on("change", function (e) {
                if ($(this).val() == "windows") {
                    $("input#username").val("administrator");
                }
                else {
                    $("input#username").val("root");
                }
                e.preventDefault();
            });
            $("select#server_os option").each(function (elem) {
                if ($(this).val() == "windows") {
                    $(this).attr("data-icon", "fp fp-windows");
                }
                if ($(this).val() == "linux") {
                    $(this).attr("data-icon", "fp fp-linux");
                }
            });
        },
        getNetworkData: function (isNew, download_data, upload_data) {
            const maximum = 60
            if (core.network_download_data.length) {
                core.network_download_data = core.network_download_data.slice(1);
                core.network_upload_data = core.network_upload_data.slice(1);
            }

            while (core.network_download_data.length < maximum) {
                if (isNew) {
                    core.network_download_data.push(0);
                    core.network_upload_data.push(0);
                }
                else {
                    core.network_download_data.push(download_data);
                    core.network_upload_data.push(upload_data);
                }
            }
            core.series[0].data = [];
            core.series[1].data = [];
            for (var i = 0; i < core.network_download_data.length; ++i) {
                core.series[0].data.push([i, core.network_download_data[i]])
                core.series[1].data.push([i, core.network_upload_data[i]])
            }
        },
        initProcessTable: function () {
            var table_options = {
                data: {},
                bStateSave: true,
                bProcessing: false,
                aLengthMenu: [[10, 20, 100, -1], [10, 20, 100, lvar['all']]],
                iDisplayLength: 20,
                // retrieve: true,
                // scrollY:        "450px",
                // scrollCollapse: true,
                // scrollX:        true,
                // paging:         false,
                // searching:         false,
                fixedColumns: true,
                order: [[2, 'desc']],
                dom: '<"html5buttons"BC>lTfgtipr',
                oLanguage: {
                    sInfo: lvar['dt_showing_info'],
                    sSearch: "",
                    sProcessing: lvar["loading"] + ". " + lvar["please_wait"] + "...",
                    sInfoFiltered: " (" + lvar['filtered'] + ")",
                    sEmptyTable: lvar['empty_table'],
                    sInfoEmpty: lvar['no_entries_to_show'],
                    sZeroRecords: lvar['zero_records'],
                    sLengthMenu: "_MENU_",
                    oPaginate: {
                        "sFirst": "<i class='fa fa-step-backward'></i>",
                        "sLast": "<i class='fa fa-step-forward'></i>",
                        "sNext": "<i class='fa fa-forward'></i>",
                        "sPrevious": "<i class='fa fa-backward'></i>"
                    }
                },
                "fnDrawCallback": function (oSettings) {
                    $('a[data-id="confirm"]').unbind();
                    $('a[data-id="confirm"]').on('click', function () {
                        core.confirm(this);
                    });
                },
                "fnInitComplete": function (data, json) {
                    $('a[data-id="confirm"]').unbind();
                    $('a[data-id="confirm"]').on('click', function () {
                        core.confirm(this);
                    });
                }
            };
            $("#process-list").removeAttr('width').DataTable(table_options);
        },
        setProcesses: function (data) {
            let table = $("#process-list").DataTable();
            table.clear().draw(false);
            var result = data.map(function (item) {
                var result = [];
                result.push(item.pid);
                result.push(item.exe);
                result.push(item.cpu);
                result.push(item.mem);
                result.push(item.user);
                result.push(item.cmd_line);
                return result;
            });
            table.rows.add(result);
            table.draw(false);
        },
        draggableModal: function (handler, target) {
            // target => .modal-content
            // handler => .modal-title
            (function ($) {
                $.fn.drags = function (opt) {
                    opt = $.extend({
                        handle: "",
                        cursor: "move"
                    }, opt);

                    if (opt.handle === "") {
                        var $el = this;
                    }
                    else {
                        var $el = this.find(opt.handle);
                    }

                    return $el.css('cursor', opt.cursor).on("mousedown", function (e) {
                        if (opt.handle === "") {
                            var $drag = $(this).closest(handler).addClass('draggable');
                        }
                        else {
                            var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
                        }
                        var z_idx = $drag.css('z-index'),
                            drg_h = $drag.outerHeight(),
                            drg_w = $drag.outerWidth(),
                            pos_y = $drag.offset().top + drg_h - e.pageY,
                            pos_x = $drag.offset().left + drg_w - e.pageX;
                        $drag.css('z-index', 2500).parents().on("mousemove", function (e) {
                            $('.draggable').offset({
                                top: e.pageY + pos_y - drg_h,
                                left: e.pageX + pos_x - drg_w
                            }).on("mouseup", function () {
                                $(this).removeClass('draggable').css('z-index', z_idx);
                            });
                        });
                        e.preventDefault(); // disable selection
                    }).on("mouseup", function () {
                        if (opt.handle === "") {
                            $(this).removeClass('draggable');
                        }
                        else {
                            $(this).removeClass('active-handle').parent().removeClass('draggable');
                        }
                    });

                }
            })(jQuery);
            $(target).drags();
        },
        logModule: function () {
            core.draggableModal(".small-chat-box", ".chat-heading");

            $('.open-small-chat').unbind();
            $('.open-small-chat').on('click', function (e) {
                core.openLog($(this));
                e.preventDefault();
            });
        },
        openLog: function (element) {
            var request = {
                command: "domain-logs",
                data: {}
            };
            if(typeof lvar["did"] != "undefined") {
                request.data["did"] = lvar["did"];
            }
            if (!core.domain_log_started) {
                request.data["process"] = "start";
                core.domain_log_started = true;
            }
            else {
                request.data["process"] = "stop";
                core.domain_log_started = false;
            }

            socket.sendMessage(request);

            element.children().toggleClass('fp fp-details').toggleClass('fa fa-times');
            $('.small-chat-box').toggleClass('active');
            core.slimscroll(".small-chat-box .content");
        },
        slimscroll: function (target) {
            var container = $(target);
            container.slimScroll({
                height: '92%',
                railOpacity: 0.4,
                start: "bottom",
            });
        },
        scrollToBottom: function (container) {
            var bottomCoord = container[0].scrollHeight;
            container.slimScroll({scrollTo: bottomCoord});
        },
        showDomainLog: function (result) {
            var matches = Array.from(result.line.matchAll(/\[([^\][]*)]/g), x => x[1])
            var errorContent = result.line;
            for(i = 0; i < matches.length; ++i) {
                // console.log(matches[i])
                errorContent = errorContent.replace("\[{0}\] ".sprintf(matches[i]), "");
            }

            var div_element = '<tr><td>{0}</td><td>{1}</td><td>{2}</td><td>{3}</td></tr>'.
            sprintf(matches[0], matches[1], matches[3], errorContent)
            var container = $(".small-chat-box .content table tbody");
            container.append(div_element);
            core.scrollToBottom($(".small-chat-box .content"));
        },
    };
}();
