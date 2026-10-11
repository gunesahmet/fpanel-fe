
var JQD = (function($, window, document, undefined) {
    return {
        buttons: {},
        search_status: false,
        shortcuts: {},
        connected_users: {},
        shortcut_gridster: null,
        desktop_gridster: null,
        go: function() {
            //console.profile(['Debug Desktop'])
            JQD.init.frameBreaker();
            if(ss == false) {
                JQD.showLogin();
                return false;
            }
            JQD.init.initDesktop();

            //console.profileEnd();

        },
        init: {
            frameBreaker: function() {
                if (window.location !== window.top.location) {
                    window.top.location = window.location;
                }
            },
            initDesktop: function () {
                //startSocket();
                JQD.initButtons();
                JQD.gridsters();
                //JQD.init.wallpaper();
                JQD.desktopContextMenu();
                JQD.startMenu();
            },
            desktop: function() {
                // Alias to document.
                var d = $(document);


                // Cancel mousedown.
                d.mousedown(function(ev) {
                    var tags = [
                        'a',
                        'button',
                        'input',
                        'select',
                        'textarea',
                        'tr',
                        'span',
                        'pre',
                        '.modal',
                    ].join(',');

                    if (!$(ev.target).closest(tags).length) {
                        JQD.util.clearActive();
                        ev.preventDefault();
                        ev.stopPropagation();
                    }
                });

                // Cancel right-click.
                d.on('contextmenu', function() {
                    //return false;
                });

                // Relative or remote links?
                d.on('click', 'a', function(e) {
                    var url = $(this).attr('data-target');
                    this.blur();

                    if(url != null) {
                        if (url.match(/^#/)) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        else {
                            $(this).attr('target', '_blank');
                        }
                    }
                    e.preventDefault();
                    e.stopPropagation();
                });

                $('#wrapper').on('click', function (e) {
                    JQD.closeStartMenu();
                    //$('[data-toggle="popover"]').popover('hide');
                    //e.preventDefault();
                });

                // Make top menus active.
                d.on('mousedown', 'a.menu_trigger', function() {
                    if ($(this).next('ul.menu').is(':hidden')) {
                        JQD.util.clearActive();
                        $(this).addClass('active').next('ul.menu').show();
                    }
                    else {
                        JQD.util.clearActive();
                    }
                });

                // Transfer focus, if already open.
                d.on('mouseenter', 'a.menu_trigger', function() {
                    if ($('ul.menu').is(':visible')) {
                        JQD.util.clearActive();
                        $(this).addClass('active').next('ul.menu').show();
                    }
                });

                // Cancel single-click.
                d.on('mousedown', 'a.icon', function(e) {
                    // Highlight the icon.
                    JQD.util.clearActive();
                    $(this).addClass('active');
                    e.preventDefault();
                    e.stopPropagation();
                });

                d.on('click', '.icon', function() {
                    JQD.util.clearActive();
                    if($(this).hasClass('active'))
                        $(this).removeClass('active');
                    else
                        $(this).addClass('active');
                });

                // Respond to double-click.
                d.on('dblclick', '.icon', function() {
                    if(closed_sock == true) {
                        //core.ajaxRequest({
                        //    url: '/system/startsock',
                        //    method: 'GET'
                        //},false);

                        //startSocket();
                    }
                    var element_len = $('ul#dock li').length;
                    if(win_width > 768) {
                        win_width = win_width - (win_width/9) - 45 - 225;
                        if((win_width/element_len) < 150) {
                            $('#pop_alert .modal-body').html('You cant open more window. Please close another after try again!');
                            $('#pop_alert').modal('show');
                            return false;
                        }
                    }
                    var id = $(this).children('a').attr('data-target');
                    var url = $(this).children('a').attr('data-url');
                    var has_child = $(this).children('a').attr('data-child');
                    if(has_child == "1")
                        has_child = "true";
                    if((has_child == "") || (has_child == null))
                        has_child = "false";
                    var text = $(this).children('a').text();
                    var icon= $(this).children('span[class^="icon-"]');
                    var icon_inner = icon.html();
                    var icon_class = icon.attr('class');

                    icon = '<span class="'+ icon_class +'">' + icon_inner + '</span>';
                    var win = '#'+id;
                    if($(win).length < 1) {

                        JQD.createWindow({
                            id: id,
                            icon: icon,
                            name: text,
                            has_child: has_child
                        });

                        if(has_child == "true") {
                            var childs = JQD.buttons[id]['childs'];
                            for(var i in childs) {
                                childs[i].is_child = true;
                                var btn = JQD.createButton(childs[i]);
                                $(win + ' .window_main ul').append(btn);
                            }
                        }
                    }

                    if ($(win).is(':hidden')) {
                        JQD.util.windowFlat();
                        JQD.showWindow(win);
                        $(win).addClass('window_stack');
                        if(has_child == "false") {
                            if($(win +' .window_main').text() == "") {
                                $(win + ' .window_main').html(loading);
                                $(win + ' .window_main').load(url,function(el){
                                    core.inputInits();

                                    //var status = true;
                                    //var json;
                                    //try {
                                    //    status = false;
                                    //    json = $.parseJSON(el);
                                    //}
                                    //catch(err) {
                                    //    status = true;
                                    //}
                                    //
                                    //if(status == true) {
                                    //    $(win +' a.window_close').attr('id',id);
                                    //}
                                    //else {
                                    //    if(json['login'] == false) {
                                    //        JQD.showLogin();
                                    //        var id = $(win).attr('id');
                                    //        JQD.removeDock({
                                    //            id: id
                                    //        });
                                    //        $(win).remove();
                                    //    }
                                    //}
                                });
                            }
                        }
                        $(win +' a.window_close').attr('id',id);
                        $(win).show('fast');
                    }
                    JQD.util.windowFlat();

                    if($(window).width() < 1024) {
                        JQD.util.windowResize('#'+id);
                        $(win + ' a.window_fullscreen').addClass('window_resize');
                        $(win + ' a.window_resize').removeClass('window_fullscreen');
                        $(win + ' a.window_resize').addClass('disabled');
                    }

                    var win_name = $('#'+id + ' .window_top').text();
                    $(document).attr( 'title', win_name);
                });

                // Make icons draggable.
                d.on('mouseenter', 'a.icon', function(e) {
                    $(this).off('mouseenter').draggable({
                        revert: true,
                        containment: 'parent'
                    });
                    e.preventDefault();
                    e.stopPropagation();
                });

                // Taskbar buttons.
                d.on('click', '#dock a', function() {
                    var x = $(this).attr('data-target');
                    var window = $('#' + x);
                    if ($(window).is(':visible')) {
                        JQD.hideWindow(window);
                    }
                    else {
                        JQD.util.windowFlat();
                        JQD.showWindow(window);
                        $(window).addClass('window_stack');

                        $('li[id^="dock_"] a').removeClass('active');
                        $('#dock_'+x + ' a').addClass('active');

                        var win_name = $('#'+x + ' .window_top').text();
                        $(document).attr( 'title', win_name);
                    }
                });

                // Focus active window.
                d.on('mousedown', 'div.window', function() {
                    // Bring window to front.
                    JQD.util.windowFlat();
                    $(this).addClass('window_stack');
                    var win_id = $(this).attr('id');
                    var win_name = $('#'+win_id + ' .window_top').text();
                    $(document).attr( 'title', win_name);

                    $('li[id^="dock_"] a').removeClass('active');
                    $('#dock_'+win_id + ' a').addClass('active');
                });

                // Make windows draggable.
                d.on('mouseenter', 'div.window', function() {
                    $(this).off('mouseenter').draggable({
                        cancel: 'a',
                        containment: 'parent',
                        handle: 'div.window_top'
                    }).resizable({
                        containment: 'parent',
                        minWidth: 400,
                        minHeight: 200,
                        stop: function (event, ui) {
                        }
                    });
                });

                d.on('mouseenter','div.system-monitoring', function () {
                    $(this).off('mouseenter').draggable({
                        cancel: 'a',
                        containment: 'parent',
                        handle: 'div.widget-panel-heading'
                    });
                });

                // Double-click top bar to resize, ala Windows OS.
                d.on('dblclick', 'div.window_top', function() {
                    var parent = $(this).parent().parent();
                    var id = $(parent).attr('id');
                    var windows = '#'+id + ' .window-controls';
                    if(!$(windows + ' .window_resize').hasClass('disabled')) {
                        JQD.util.windowResize(this);
                        if($(windows + ' a').hasClass('window_resize')) {
                            $(windows + ' a.window_resize').addClass('window_fullscreen');
                            $(windows + ' a.window_resize').removeClass('window_resize');
                        }
                        else if($(windows + ' a').hasClass('window_fullscreen')) {
                            $(windows + ' a.window_fullscreen').addClass('window_resize');
                            $(windows + ' a.window_fullscreen').removeClass('window_fullscreen');
                        }
                    }
                });

                d.on('dblclick', 'div.window_top img', function() {
                    $($(this).closest('div.window_top').find('a.window_close').attr('data-target')).hide('fast');
                    $(this).closest('div.window').hide();
                    return false;
                });

                // Minimize the window.
                d.on('click', 'a.window_min', function() {
                    JQD.hideWindow($(this).closest('div.window'));
                });

                // Maximize or restore the window.
                d.on('click', 'a.window_resize', function() {
                    if(!$(this).hasClass('disabled')) {
                        JQD.util.windowResize(this);
                        $(this).addClass('window_fullscreen');
                        $(this).removeClass('window_resize');
                    }
                });

                d.on('click', 'a.window_refresh', function() {
                    var window_id = $(this).closest('.window').attr('id');
                    var button_content = JQD.searchButton(window_id,JQD.buttons);
                    var win = "#"+window_id;
                    if(button_content.has_child == false) {
                        $(win +' .window_main').html(loading);
                        $(win + ' .window_main').load(button_content.url,function(el){
                            $(win +' a.window_close').attr('id',button_content.id);
                            core.inputInits();
                        });
                        $(win+' .window_top #window_name').text($(win+' .window_top').attr('data-original-title'));
                    }
                });

                // Close the window.
                d.on('click', 'a.window_close', function() {
                    var id = $(this).closest('div.window').attr('id');
                    JQD.removeDock({
                        id: id
                    });
                    $(this).closest('div.window').remove();
                    var grid = $(this).find('ul.gridster');
                    if(grid.length > 0) {
                        var grid_id = grid.attr('id');
                        JQD.destroyGridster('#'+grid_id);
                    }
                });

                d.on('click', 'a.window_fullscreen', function() {
                    JQD.util.windowResize(this);
                    $(this).addClass('window_resize');
                    $(this).removeClass('window_fullscreen');

                });

                // Show desktop button, ala Windows OS.
                d.on('mousedown', '#show_desktop', function() {

                    // If any windows are visible, hide all.
                    if ($('div.window:visible').length) {
                        $('div.window').hide();
                    }
                    else {
                        // Otherwise, reveal hidden windows that are open.
                        var a_elements = "#dock li:visible a";
                        $(a_elements).each(function() {
                            $('#' + $(this).attr('data-target')).show();
                        });
                    }
                });


            },
            wallpaper: function() {
                // Add wallpaper last, to prevent blocking.
                if ($('#desktop').length) {
                    if($('#wallpaper').length < 1)
                        $('body').prepend('<img id="wallpaper" class="abs" src="/templates/default/assets/images/misc/bg1.jpg" />');
                }
            }
        },
        util: {
            clearActive: function() {
                $('.icon').removeClass('active');
                $('ul.menu').hide();
            },
            windowFlat: function() {
                $('div.window').removeClass('window_stack');
            },
            windowResize: function(el) {
                var win = $(el).closest('div.window');
                if (win.hasClass('window_full')) {
                    win.removeClass('window_full').css({
                        'top': win.attr('data-t'),
                        'left': win.attr('data-l'),
                        'right': win.attr('data-r'),
                        'bottom': win.attr('data-b'),
                        'width': win.attr('data-w'),
                        'height': win.attr('data-h')
                    });
                }
                else {
                    var height = $(window).height();
                    win.attr({
                        'data-t': win.css('top'),
                        'data-l': win.css('left'),
                        'data-r': win.css('right'),
                        'data-b': win.css('bottom'),
                        'data-w': win.css('width'),
                        'data-h': win.css('height')
                    }).addClass('window_full').css({
                        'top': '0',
                        'left': '0',
                        'right': '0',
                        'bottom': '0',
                        'width': '100%',
                        'height': '100%'
                    });
                }
                JQD.util.windowFlat();
                win.addClass('window_stack');
            }
        },
        addShortCut: function (element,send) {
            var btn = JQD.createShortCutButton(element);
            JQD.shortcut_gridster.add_widget.apply(JQD.shortcut_gridster, [btn, 1, 1]);
            element['col'] = '1';
            element['row'] = '1';
            //if(send == true)
            //socketSend({type: 'shortcut',action: 'add',element:element});
            // @TODO push yazılacak
        },
        callButtons: function (target) {
            var url;
            if(target != null)
                url = btn_links + target;
            else
                url = btn_links;
            var result =  core.ajaxRequest({
                method: 'GET',
                url : url,
                data: ''
            },false);

            if(result != null) {
                if(typeof result['buttons'] != "undefined")
                    return result;
                else
                    return {};
            }
            else
                return {};
        },
        changeLanguage: function () {
            $('.sys-langs').unbind();
            $('.sys-langs').on('click', function () {
                $('.installed-langs').toggle();
                $('.installed-langs li').unbind();
                $('.installed-langs li').on('click', function () {
                    var selected_lang = $(this).attr('id');
                    core.ajaxRequest({
                        method: 'GET',
                        url: '/lang/'+selected_lang,
                        data: {}
                    },false);
                    location.reload(true);
                });
            });
        },
        closeStartMenu: function () {
            $("#start_menu").hide(200);
        },
        connectedUser: function (data) {
            for (var i in data) {
                if(current_id == data[i].id )
                    continue;
                JQD.connected_users[data[i].id] = data[i];

                var user_element = '<a href="#" id="'+ data[i].id +'" data-conversation-history="#'+ data[i].id +'_history" data-sid="' + data[i].sid+ '">' +
                    '<span class="user-status is-online"></span>' +
                    '<em>'+ data[i].username +'</em>' +
                    '</a>';
                if($("#group-1 a#"+ data[i].id).length == false)
                    $('#group-1').append(user_element);
                else {
                    $('#group-1 #'+data[i].id).attr("data-sid",data[i].sid);
                    for(var k in coreChat.statuses) {
                        $('#group-1 #'+data[i].id).children('span.user-status').removeClass(coreChat.statuses[k]['class']);
                    }
                    $('#group-1 #'+data[i].id).children('span.user-status').addClass('is-online');
                }
                if($('#'+ data[i].id +'.chat-conversation').length > 0) {
                    $('#'+ data[i].id +'.chat-conversation').find('span.user-status').removeAttr('class').attr('class','user-status is-online');
                    $('#'+ data[i].id +'.chat-conversation').find('small').text(lvar['online']);
                }

            }

            $('[data-target="#users_sb"]').find('span.badge').text(Object.keys(JQD.connected_users).length);
        },
        createButton: function (options) {
            var icon;
            if(options.icon != null) {
                if (options.icon.match("^ma-"))
                    icon = "fa ma-3x " + options.icon;
                else
                    icon = "fa fa-3x " + options.icon;
            }
            else
                icon = "fa fa-3x ";

            var btn = "";
            if(options.is_child == true) {
                btn = '<li class="icon col-md-1 col-xs-4">';
            }
            else {
                btn = '<li data-row="'+ options.row +'" data-col="'+ options.col +'" data-sizex="1" data-sizey="1" class="icon col-md-1 col-xs-4">';
            }
            btn = btn +
                options.icon +
                '<a data-url="'+ options.url +'" title="'+options.name + ': ' + options.description+ '" data-target="'+ options.target +'" data-child="'+ options.has_child +'" data-id="'+ options.id +'" data-toggle="tooltip" data-placement="bottom" data-trigger="focus">'+ options.name +'</a>' +
                '</li>';

            return btn;

        },
        createDock: function (options) {
            var dock_element = '<li id="dock_'+ options.id +'">' +
                '<a data-target="'+ options.id +'" data-toggle="tooltip" title="'+ options.name +'">' +
                options.icon +
                '</a>' +
                '</li>';

            $('ul#dock').append(dock_element);
        },
        createFolder: function () {
            var modal_options = {
                id: 'create_folder',
                title: 'Create Folder',
                size: '',
                content: '<form class="form-horizontal" method="post">' +
                '<div class="form-group">' +
                '<label class="col-md-4 control-label">Folder Name</label>' +
                '<div class="col-md-8">' +
                '<input class="form-control" type="text" name="folder_name" id="folder_name"/>' +
                '</div>' +
                '</div>' +
                '</form>'
            };
            var modal_window = createModal(modal_options);
            $('body').append(modal_window);
            var modal_id = $('#'+modal_options.id);
            modal_id.modal('show');
            modal_id.on('shown.bs.modal', function () {
                var save_button = 'button#create_folder';
                $(save_button).unbind();
                $(save_button).on('click', function (e) {
                    var folder_name = $('input#folder_name').val();

                    var target_name = folder_name.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
                    var btn = createButton({
                        'icon' : 'fa fa-3x fa-folder',
                        'name' : folder_name,
                        'url' : 'partial/folder.html',
                        'target' : target_name,
                        'col': 1,
                        'row': 1
                    });
                    var cols;
                    var window_width = $(window).width();
                    cols = Math.round(window_width  / 90);
                    var gridster;
                    $(function() {
                        gridster = $("#desktop").gridster({
                            extra_rows:2,
                            min_cols: 1,
                            max_cols: cols,
                            shift_larger_widgets_down: false,
                            min_rows: 5,
                            widget_base_dimensions: [80, 70],
                            widget_margins: [5, 5],
                            autogrow_cols: true
                        }).data('gridster');
                        gridster.add_widget.apply(gridster, [btn, 1, 1]);
                    });
                    modal_id.modal('hide');
                    e.preventDefault();
                });
            });
        },
        createWindow: function (options) {

            var ul = "";
            if(options.has_child == "true") {
                ul = "<ul></ul>";
            }
            var html = '<div id="'+ options.id +'" class="abs window">' +
                '<div class="abs window_inner">' +
                '<div class="window_top" data-original-title="'+options.name+'">' +
                '<span class="pull-left window-element">' +'</span>' +
                '<span id="window_name">'+ options.name +'</span>' +
                '<span class="pull-right window-controls">' +
                //'<a class="window_refresh"></a>' +
                '<a class="window_min"></a>' +
                '<a class="window_fullscreen"></a>' +
                '<a class="window_close"></a>' +
                '</span>' +
                '</div>' +
                '<div class="abs window_content">' +
                '<div class="window_frame"><div class="window_main">' +
                ul +
                '</div></div>' +
                '</div>' +
                '<div class="abs window_bottom">' +
                '</div>' +
                '</div>' +
                '<span class="abs ui-resizable-handle ui-resizable-se"></span>' +
                '</div>';



            $('.abs#wrapper').append(html);

            var dock = '#dock_'+options.id;
            if($(dock).length < 1) {
                JQD.createDock({
                    id: options.id,
                    icon: options.icon,
                    name: options.name
                });
            }
            $(dock).addClass('window_stack').show();

            $('li[id^="dock_"] a').removeClass('active');
            $('#dock_'+options.id + ' a').addClass('active');
            //$("#created_folder").gridster(JQD.gridsterCreatedFolderOptions()).data('gridster');

            //$('.window').css('max-height',$('#wrapper').height() - 45);

        },
        desktopContextMenu: function () {
            $(function() {
                $.contextMenu({
                    selector: 'ul#desktop .icon',
                    callback: function(key, options) {
                        var a_element = $(options)[0]['$trigger'][0]['lastChild'];
                        var i_element = $(options)[0]['$trigger'][0]['firstChild'];
                        var icon = $(i_element).attr('class');
                        var target_id = $(a_element).attr('data-target');
                        var id = $(a_element).attr('data-id');
                        var target_url = $(a_element).attr('data-url');
                        var has_child = $(a_element).attr('data-child');
                        var content_desc = $(a_element).attr('data-content');
                        var name = $(a_element).text();
                        var element = {
                            id: id,
                            target: target_id,
                            url: target_url,
                            icon: icon,
                            name: name,
                            has_child: has_child,
                            description: content_desc
                        };
                        switch (key) {
                            case "add_shortcut":
                                JQD.addShortCut(JQD.buttons[target_id],true);
                                break;
                            case "help":
                                JQD.help(element);
                                break;
                        }
                    },
                    items: {
                        add_shortcut: {
                            name: lvar['add_shortcut'],
                            icon: function(opt, $itemElement){
                                $itemElement.attr('id','add_shortcut');
                                $itemElement.html('<i class="fa fa-external-link" aria-hidden="true"></i> '+ lvar['add_shortcut']);
                            }
                        },
                        "sep1": "---------",
                        help: {
                            name: lvar['help'],
                            icon: function(opt, $itemElement){
                                $itemElement.html('<i class="fa fa-question-circle" aria-hidden="true"></i> '+ lvar['help']);
                            }
                        }
                    }
                });
                $.contextMenu({
                    selector: '.window_main .icon',
                    callback: function(key, options) {
                        var a_element = $(options)[0]['$trigger'][0]['lastChild'];
                        var i_element = $(options)[0]['$trigger'][0]['firstChild'];
                        var icon = $(i_element).attr('class');

                        var target_id = $(a_element).attr('data-target');
                        var target_url = $(a_element).attr('data-url');
                        var id = $(a_element).attr('data-id');
                        var has_child = $(a_element).attr('data-child');
                        var content_desc = $(a_element).attr('data-content');
                        var name = $(a_element).text();

                        var element = {
                            target: target_id,
                            url: target_url,
                            icon: icon,
                            name: name,
                            id: id,
                            has_child: has_child,
                            description: content_desc,
                        };
                        switch (key) {
                            case "add_shortcut":
                                JQD.addShortCut(JQD.searchButton(target_id,JQD.buttons),true);
                                break;
                            case "help":
                                break;
                        }
                    },
                    items: {
                        add_shortcut: {
                            name: lvar['add_shortcut'],
                            icon: function(opt, $itemElement){
                                $itemElement.attr('id','add_shortcut');
                                $itemElement.html('<i class="fa fa-external-link" aria-hidden="true"></i> '+ lvar['add_shortcut']);
                            }
                        },
                        "sep1": "---------",
                        help: {
                            name: lvar['help'],
                            //icon: function(opt, $itemElement, itemKey, item){
                            icon: function(opt, $itemElement){
                                $itemElement.attr('id','help');
                                $itemElement.html('<i class="fa fa-question-circle" aria-hidden="true"></i> '+ lvar['help']);
                            }
                        }
                    }
                });
                $.contextMenu({
                    selector: 'ul#shortcuts li.tile',
                    callback: function(key, options) {


                        var a_element = $(options)[0]['$trigger'][0]['lastChild'];
                        var i_element = $(options)[0]['$trigger'][0]['firstChild'];

                        switch (key) {
                            case "remove":
                                JQD.removeShortCut(i_element,true);
                                break;
                            case "help":
                                break;
                        }
                    },
                    items: {
                        remove: {
                            name: lvar['remove_from_shortcut'],
                            icon: function(opt, $itemElement){
                                $itemElement.attr('id','remove_from_shortcut');
                                $itemElement.html('<i class="fa fa-external-link" aria-hidden="true"></i> '+ lvar['remove_from_shortcut']);
                            }
                        },
                        "sep1": "---------",
                        help: {
                            name: lvar['help'],
                            //icon: function(opt, $itemElement, itemKey, item){
                            icon: function(opt, $itemElement){
                                $itemElement.attr('id','help');
                                $itemElement.html('<i class="fa fa-question-circle" aria-hidden="true"></i> '+ lvar['help']);
                            }
                        }
                    }
                });
                $.contextMenu({
                    selector: '#wrapper #desktop',
                    callback: function(key, options) {
                        switch (key) {
                            //case "new_folder":
                            //    JQD.createFolder();
                            //    break;
                            case "refresh":
                                JQD.refresh();
                                break;
                            case "customize":
                                JQD.customizeInterface();
                                break;
                            case "help":
                                break;
                        }
                    },
                    items: {
                        refresh: {
                            name: lvar['refresh'],
                            icon: function(opt, $itemElement){
                                $itemElement.html('<i class="fa fa-folder" aria-hidden="true"></i> ' + lvar['refresh']);
                            }
                        },
                        "sep1": "---------",
                        customize: {
                            name: lvar['change_interface'],
                            icon: function(opt, $itemElement){
                                $itemElement.html('<i class="fa fa-edit" aria-hidden="true"></i> '+ lvar['change_interface']);
                            }
                        },
                        //account: {
                        //    name: lvar['account_settings'],
                        //    icon: function(opt, $itemElement){
                        //        $itemElement.html('<i class="fa fa-user-secret" aria-hidden="true"></i> '+ lvar['account_settings']);
                        //    }
                        //},
                        help: {
                            name: lvar['help'],
                            icon: function(opt, $itemElement){
                                $itemElement.html('<i class="fa fa-question-circle" aria-hidden="true"></i> '+ lvar['help']);
                            }
                        }
                    }
                });
            });
        },
        destroyDesktop: function () {
            $('.modal').modal('hide');
            JQD.destroyGridster("#desktop");
            JQD.destroyGridster("#shortcuts");
            core.buttons = {};
            core.shortcuts = {};
            $('ul#desktop').html("");
            $('ul#shortcuts').html("");
            $('body > #wrapper').html('<ul class="gridster connectedLists" id="desktop"></ul>');
            $('[id^="dock_"]').remove();
            $.contextMenu('destroy');
        },
        destroyGridster: function (element) {
            var gridster = $(element).gridster({}).data('gridster');
            gridster.destroy();
        },
        disconnectUser: function (data) {
            var el = $('#group-1').find('[data-sid="'+ data['data']['id']+'"]');
            el.children('span.user-status').removeClass('is-online').addClass('is-offline');
            var id = el.attr('id');
            if(typeof coreChat.chat_history[id] != "undefined") {
                coreChat.chat_history[id].$el = $('#group-1').find('[data-sid="'+ data['data']['id']+'"]');
                coreChat.chat_history[id].status = 'offline';
            }

            if($('#'+ id +'.chat-conversation').length > 0) {
                $('#'+ id +'.chat-conversation').find('span.user-status').removeAttr('class').attr('class','user-status is-offline');
                $('#'+ id +'.chat-conversation').find('small').text(lvar['offline']);
            }
            delete  JQD.connected_users[id];
            $('[data-target="#users_sb"]').find('span.badge').text(Object.keys(JQD.connected_users).length);

        },
        gridsters: function () {
            JQD.desktop_gridster = JQD.gridsterDesktop();
            JQD.shortcut_gridster = JQD.gridsterShortcuts();
        },
        gridsterCreatedFolderOptions: function () {
            var cols;
            var window_width = $('.window').width();
            cols = Math.round(window_width  / 100);
            return {
                namespace: '#opened_folder',
                extra_rows:2,
                min_cols: 1,
                max_cols: cols,
                shift_larger_widgets_down: false,
                min_rows: 5,
                widget_base_dimensions: [80, 70],
                widget_margins: [5, 5],
                autogrow_cols: true,
                draggable: {
                    //start: function(e, data) {
                    start: function() {
                        $('body').addClass('gridster-dragging');
                    },
                    //stop: function(e, data) {
                    stop: function() {
                        var outerGrid = $('.hand-over');
                        if (outerGrid.length) {
                            var clone = this.$player.clone(),
                                sizeX = this.$player.data('sizex'),
                                sizeY = this.$player.data('sizey');
                            this.$el.data('gridster').remove_widget(this.$player, true, function() {
                                outerGrid.find('ul').data('gridster').add_widget(clone, sizeX, sizeY);
                                outerGrid.removeClass('hand-over');
                            });
                        }

                        $('body').removeClass('gridster-dragging');
                    }
                }
            };
        },
        gridsterDesktop: function (e) {
            var cols;
            var window_width = $(window).width();
            cols = Math.round(window_width  / 90);
            var gridster = $(this).find('ul').data('gridster');
            $(function() {
                $(document).on('mouseover', '.gridster', function(e) {
                    if (!$('body').hasClass('gridster-dragging')) {
                        return;
                    }
                    if (gridster.drag_api.is_dragging) {
                        return;
                    }
                    $(this).addClass('hand-over');
                });
                $(document).on('mouseleave', '.gridster', function(e) {
                    $(this).removeClass('hand-over');
                });
                var element;
                return gridster = $("#desktop").gridster({
                    namespace: '#wrapper',
                    min_cols: 1,
                    max_cols: cols,
                    shift_larger_widgets_down: false,
                    min_rows: 5,
                    widget_base_dimensions: [80, 70],
                    widget_margins: [5, 5],
                    autogrow_cols: true,
                    draggable: {
                        start: function(e, data) {
                            $('body').addClass('gridster-dragging');
                            element = $(data.$helper);
                        },
                        stop: function(e, data) {
                            element = $(data.$player);
                            var col = element.attr('data-col');
                            var row = element.attr('data-row');
                            var id = element.children('a').data('id');
                            var grid_height = $('ul#desktop').height();
                            socketSend({type: 'layout',action: 'push','layout':{
                                col: col,
                                row: row,
                                id: id,
                                grid_height: grid_height
                            }});
                            var elements = "ul#desktop li.icon";
                            var layouts = {};
                            $(elements).each(function() {
                                var element = $(this);
                                var col = element.attr('data-col');
                                var row = element.attr('data-row');
                                var id = element.children('a').data('id');
                                layouts[id] = {
                                    col: col,
                                    row: row
                                };
                            });
                            socketSend({type: 'layout',action: 'set','layout':layouts});
                            $('body').removeClass('gridster-dragging');
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    }
                }).data('gridster');
            });
        },
        gridsterShortcuts: function () {
            var gridster_shortcuts = $(this).find('ul#shortcuts').data('gridster');
            $(document).on('mouseover', '.gridster', function(e) {
                if (!$('body').hasClass('gridster-dragging')) {
                    return;
                }
                if (gridster_shortcuts.drag_api.is_dragging) {
                    return;
                }
                $(this).addClass('hand-over');
                e.preventDefault();
            });
            $(document).on('mouseleave', '.gridster', function(e) {
                $(this).removeClass('hand-over');
                e.preventDefault();
            });
            var element;
            return gridster_shortcuts = $("ul#shortcuts").gridster({
                namespace: '#short-cuts',
                extra_rows:1,
                min_cols: 5,
                max_cols: 5,
                shift_larger_widgets_down: false,
                min_rows: 5,
                widget_base_dimensions: [110, 90],
                widget_margins: [2, 2],
                autogrow_cols: true,
                draggable: {
                    start: function(e, data) {
                        $('body').addClass('gridster-dragging');
                        element = $(data.$helper);
                    },
                    stop: function(e, data) {
                        element = $(data.$player);
                        var col = element.attr('data-col');
                        var row = element.attr('data-row');
                        var target = element.children('a').attr('data-target');
                        //socketSend({type: 'shortcut',action: 'push','layout':{
                        //    col: col,
                        //    row: row,
                        //    id: target
                        //}});
                        //JQD.sendShortCutLayout();
                        $('body').removeClass('gridster-dragging');
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }
            }).data('gridster');
        },
        help: function (element) {
            $('[data-id="'+ element.id +'"]').popover('show');
        },
        hideWindow: function (el) {
            $(el).hide();
        },
        initButtons: function () {
            var btn_results = JQD.callButtons(null);
            var shortcuts = btn_results['shortcuts'];
            var buttons = btn_results['buttons'];
            this.buttons = buttons;
            this.shortcuts = shortcuts;
            var small_window = false;
            if($(window).width() < 1024) {
                small_window = true;
            }
            // Desktop
            for(var i in buttons) {
                if(small_window == true) {
                    buttons[i]['col'] = 1;
                    buttons[i]['row'] = 1;
                }
                if(buttons[i]['col'] == null) {
                    buttons[i]['col'] = 1;
                    buttons[i]['row'] = 1;
                }
                var btn = JQD.createButton(buttons[i]);
                $('#wrapper ul#desktop').append(btn);
            }

            // Shortcuts
            for(var i in shortcuts) {
                //console.log(shortcuts[i]['col']);
                //console.log(shortcuts[i]['row']);

                var shortcut_btn = createButton(shortcuts[i]);
                $('ul#shortcuts').append(shortcut_btn);
            }

            //$(document).ready(function(){
            //    $('[data-toggle="tooltip"]').tooltip();
            //});
        },
        loginInit: function () {
            //core.inputInits();
            //$('#login-form select#language').selectpicker('val', selected_lang);
            //$('#login-form select#language').selectpicker('refresh');
            //$('#login-form select#language').change(function () {
            //    core.ajaxRequest({
            //        method: 'GET',
            //        url: '/lang/'+$(this).val(),
            //        data: {}
            //    },false);
            //    location.reload(true);
            //});
            //
            //$("#login-form").submit(function(e) {
            //
            //    var result = core.ajaxRequest({
            //        method: 'POST',
            //        data: {
            //            username: $('input[name="username"]').val(),
            //            password: $('input[name="password"]').val(),
            //            unit: $('select#unit').val(),
            //            language: $('select#language').val(),
            //        },
            //        url: '/login',
            //        form: 'login-form'
            //    },true);
            //    if(result['type'] == "success") {
            //        $('.login-container').remove();
            //        ss = true;
            //        current_id = result['user'];
            //        current_username = result['fullname'];
            //        JQD.go();
            //    }
            //
            //    e.preventDefault(); // avoid to execute the actual submit of the form.
            //});
            window.location.href = "/";

        },
        open: function (element) {

        },
        removeShortCut: function (element,send) {
            JQD.shortcut_gridster.remove_widget($(element).parent());

            //$('ul#shortcuts li a[data-target="'+element.target+'"]').parent().remove();
            //if(send == true)
            //    socketSend({type: 'shortcut',action: 'remove',element:element});
        },
        refresh: function () {
            //if($(window).width() < 1024) {
            //    return false;
            //}
            JQD.destroyGridster("#desktop");
            core.buttons = {};
            $('ul#desktop').html("");
            JQD.initButtons();
            JQD.gridsterDesktop();
        },
        removeDock: function (options) {
            $('#dock_'+options.id).remove();
        },
        reorder: function (layout) {
            if($(window).width() < 1024) {
                return false;
            }
            var btn = $("ul#desktop").find("a[data-id='" + layout.id + "']");
            $('.gridster').height(layout.grid_height);
            $(btn).parent().attr('data-col',layout.col);
            $(btn).parent().attr('data-row',layout.row);
        },
        reorderShortcut: function (layout) {
            var btn = $("ul#shortcuts").find("a[data-target='" + layout.id + "']");
            $(btn).parent().attr('data-col',layout.col);
            $(btn).parent().attr('data-row',layout.row);
        },
        searchButton: function (id,buttons) {
            var result = false;
            for(var i in buttons) {
                if(id == i) {
                    return buttons[i];
                }
                else {
                    if(buttons[i]['has_child'] == true) {
                        result = JQD.searchButton(id,buttons[i]['childs']);
                        if(result != false)
                            return result;
                    }
                }
            }
            return result;
        },
        searchMenu: function(buttons,value,parent) {
            if((value == null) || (value == "")) {
                $('li.icon').show();
                return true;
            }
            for(var i in buttons) {
                if (buttons[i]['name'].toLowerCase().indexOf(value) <= -1) {
                    $('li a[data-id="'+ buttons[i]['id'] +'"]').parent('li').hide();
                    if(buttons[i]['has_child'] == true) {
                        JQD.searchMenu(buttons[i]['childs'],value,buttons[i]['id']);
                    }
                }
                else {
                    $('li a[data-id="'+ buttons[i]['id'] +'"]').parent('li').show();
                    if(parent != null) {
                        $('li a[data-id="'+ parent +'"]').parent('li').show();
                    }
                }
            }
        },
        sendShortCutLayout: function () {
            var elements = "ul#shortcuts li.icon";
            var layouts = {};
            var i = 0;
            $(elements).each(function() {
                var target_id = $(this).children('a').attr('data-target');
                var button = JQD.searchButton(target_id,JQD.buttons);
                button['col'] = $(this).attr('data-col');
                button['row'] = $(this).attr('data-row');
                //console.log(button);
                layouts[i] = button;
                i++;
            });
            socketSend({type: 'shortcut',action: 'set','layout':layouts});
        },
        sideBar: function () {
            var cSide = 'right';
            $(".sidebar." + cSide).sidebar({side: cSide});
            $(".icons a[data-action]").unbind();
            $(".icons a[data-action]").on("click", function () {
                var $this = $(this);
                var action = $this.attr("data-action");
                var side = $this.attr("data-side");
                $(".sidebar." + side).trigger("sidebar:" + action);
                return false;
            });
        },
        showLogin: function () {
            //JQD.closeStartMenu();
            ////JQD.destroyGridster("#desktop");
            //core.buttons = {};
            ////$('ul#desktop').html("");
            //var units = "";
            //var languages = "";
            //
            //var llist = core.jsonParse(language_lists);
            //for(var k in llist) {
            //    languages += '<option value="'+ k +'">'+ llist[k] +'</option>'
            //}
            //var login = '<div id="login-container" class="login-container">' +
            //    '<div class="login-box animated fadeInDown">' +
            //    '<div class="login-logo"></div>' +
            //    '<div class="login-body">' +
            //    '<div class="login-title"><strong>'+ lvar['please_login'] +'</strong></div>' +
            //    '<form class="form-horizontal" id="login-form" method="post">' +
            //
            //    '<div class="form-group">' +
            //    '<div class="col-md-12">' +
            //    '<input class="form-control" placeholder="'+ lvar['username'] +'" type="text" name="username" id="username">' +
            //    '</div>' +
            //    '</div>' +
            //
            //    '<div class="form-group">' +
            //    '<div class="col-md-12">' +
            //    '<input class="form-control" placeholder="'+ lvar['password'] +'" type="password" name="password" id="password">' +
            //    '</div>' +
            //    '</div>' +
            //
            //    '<div class="form-group">' +
            //    '<div class="col-md-12">' +
            //    '<select class="form-control selectpicker" data-toggle="selectpicker" data-style="btn-login" id="language" name="language" title="'+ lvar['select_language'] +'">' +
            //    languages +
            //    '</select>' +
            //    '</div>' +
            //    '</div>' +
            //
            //    '<div class="form-group">' +
            //    '<div class="col-md-6">' +
            //    '<a class="btn btn-link btn-block" href="#">'+ lvar['forgot_password'] +'?</a>' +
            //    '</div>' +
            //    '<div class="col-md-6">' +
            //    '<button class="btn btn-info btn-block" type="submit" id="login">'+ lvar['login'] +'</button>' +
            //    '</div>' +
            //    '</div>' +
            //
            //    '</form>' +
            //    '</div>'+
            //    '</div>'+
            //    '</div>';
            //$('body').prepend(login);
            JQD.loginInit();
        },
        showWindow: function (el) {
            var top = $(el).attr('data-top');
            var left = $(el).attr('data-left');
            var height = $(el).attr('data-height');
            var width = $(el).attr('data-width');
            $(el).show();
        },
        startMenu: function () {
            $(".start_menu").unbind();
            $(".start_menu").on('click',function() {
                var start_menu = '#start_menu';
                if($(start_menu).is(":visible"))
                    $(start_menu).hide(200);
                else
                    $(start_menu).show(200);
            });
        },
        createShortCutButton: function (options) {
            return '<li class="tile" data-tile-id="'+ options.id +'"><a href="'+ options.url +'" data-toggle="tooltip" title="'+ options.name +': '+ options.description +'"><div class="tile-icon">'+ options.icon +'</div><span class="title">'+ options.name +'</span></a></li>';
        },
        customizeInterface: function () {
            $('#customize-screen').modal('show');
            $('#background').on('keyup', function () {
                $('.background-preview').attr('style',$(this).val());
            });
        }
    };
})(jQuery, this, this.document);
jQuery(document).ready(function() {
    JQD.go();
    $('input[name="desktop_search"]').keyup(function(){
        JQD.searchMenu(JQD.buttons,$(this).val(),null);
    });
    JQD.sideBar();
    $('.logout').unbind();
    $('.logout').on('click',function () {
        closeSocket();
        core.ajaxRequest({
            method: 'GET',
            url: $(this).data('href'),
            data : ''
        },false);
        ss = false;
        JQD.destroyDesktop();
        JQD.showLogin();

        //JQD.init.desktop();
    });
    JQD.changeLanguage();
    JQD.init.desktop();

});
function createButton(options) {
    var icon;
    if(options.icon != null) {
        if (options.icon.match("^ma-"))
            icon = "fa ma-3x " + options.icon;
        else
            icon = "fa fa-3x " + options.icon;
    }
    else
        icon = "fa fa-3x ";

    if(options['col'] == null) {
        options['col'] = 1;
        options['row'] = 1;
    }

    return '<li data-row="'+ options.row +'" data-col="'+ options.col +'" data-sizex="1" data-sizey="1" class="icon col-md-1 col-xs-4">' +
        '<i class="'+ icon +'"></i>' +
        '<a data-url="'+ options.url +'" title="' + options.description+ '" data-target="'+ options.target +'" data-child="'+ options.has_child +'" data-id="'+ options.id +'" data-toggle="tooltip" data-placement="bottom">'+ options.name +'</a>' +
        '</li>';

}
function createModal(options) {
    return '<div id="'+ options.id +'" class="modal fade '+ options.size +'" tabindex="-1" data-focus-on="input:first">' +
    '<div class="modal-header">' +
    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>' +
    '<h3>'+ options.title +'</h4>' +
    '</div>' +
    '<div class="modal-body">' +
    options.content +
    '</div>' +
    '<div class="modal-footer">' +
    '<button type="button" data-dismiss="modal" class="btn btn-default btn-sm">'+ lvar['close'] +'</button>' +
    '<button type="button" class="btn btn-primary btn-sm" id="'+ options.id +'">'+ lvar['submit'] +'</button>' +
    '</div>' +
    '</div>';
}
