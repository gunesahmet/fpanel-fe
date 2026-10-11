$.intervalArr = [];
var breadcrumbs;
var bread_crumb = $('ol.breadcrumb'),
thisDevice = null,
ismobile = (/iphone|ipad|ipod|android|mini|windows\sce/i.test(navigator.userAgent.toLowerCase())),
jsArray = {},
initApp = (function(app) {
    app.addDeviceType = function() {
        if (!ismobile) {
            // Desktop
            $.root_.addClass("desktop-detected");
            thisDevice = "desktop";
            return false;
        }
        else {
            // Mobile
            $.root_.addClass("mobile-detected");
            thisDevice = "mobile";
            if (fastClick) {
                $.root_.addClass("needsclick");
                return false;
            }
        }
    };
    app.domReadyMisc = function() {
        core.resetSessionTimer(time_limit, core.time_left);
    };
    app.mobileCheckActivation = function(){
        if ($(window).width() < 979) {
            $.root_.addClass('mobile-view-activated');
            $.root_.removeClass('minified');
        }
        else if ($.root_.hasClass('mobile-view-activated')) {
            $.root_.removeClass('mobile-view-activated');
        }
    }
    return app;
})({});
initApp.addDeviceType();
jQuery(document).ready(function() {
    initApp.domReadyMisc();
});
$('#main').resize(function() {
    initApp.mobileCheckActivation();
});
// @TODO: delete this function later on - no longer needed
var ie = ( function() {
    var undef, v = 3, div = document.createElement('div'), all = div.getElementsByTagName('i');
    while (div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->', all[0]);
    return v > 4 ? v : undef;
}());
jQuery.fn.doesExist = function() {
    return jQuery(this).length > 0;
};
function loadScript(scriptName, callback) {
    if (!jsArray[scriptName]) {
        var promise = jQuery.Deferred();
        var body = document.getElementsByTagName('body')[0],
            script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = scriptName;
        script.onload = function() {
            promise.resolve();
        };
        body.appendChild(script);
        jsArray[scriptName] = promise.promise();
    }
    else if (debugState)
        root.root.console.log("This script was already loaded %c: " + scriptName, debugStyle_warning);
    jsArray[scriptName].then(function () {
        if(typeof callback === 'function')
            callback();
    });
}

if($.navAsAjax) {
    // fire this on page load if nav exists
    if ($('nav').length) {
        checkURL();
    }
    $(document).on('click', 'nav a[href!="#"]', function(e) {
        e.preventDefault();
        if($(this).attr("href").length > 0) {
            var $this = $(e.currentTarget);
            if ($.root_.hasClass('mobile-view-activated')) {
                $.root_.removeClass('hidden-menu');
                $('html').removeClass("hidden-menu-mobile-lock");
                window.setTimeout(function() {
                    if (window.location.search) {
                        window.location.href =
                            window.location.href.replace(window.location.search, '')
                                .replace(window.location.hash, '') + '#' + $this.attr('href');
                        // console.log("window.location.hash: " + window.location.hash);
                    }
                    else {
                        window.location.hash = $this.attr('href');
                        // console.log("window.location.hash: " + window.location.hash);
                    }
                }, 150);
            }
            else {
                if (window.location.search) {
                    window.location.href =
                        window.location.href.replace(window.location.search, '')
                            .replace(window.location.hash, '') + '#' + $this.attr('href');
                }
                else {
                    if (typeof $this.data('id') !== 'undefined') {
                        window.location.hash = $this.attr('href');
                        // console.log("window.location.hash: " + window.location.hash);
                        window.location = "/";
                    }
                    else {
                        window.location.hash = $this.attr('href');
                        // console.log("window.location.hash: " + window.location.hash);
                    }
                }
            }
            var title = $this.text();
            // change page title from global var
            document.title = (title == null ? document.title : title);
            // debugState
            if (debugState){
                root.console.log("Page title: %c " + document.title, debugStyle_green);
            }
        }
    });
    $(document).on('click', '[data-id="logout"]', function(e) {
        e.preventDefault();
    });
    // fire links with targets on different window
    $(document).on('click', 'nav a[target="_blank"]', function(e) {
        e.preventDefault();
        var $this = $(e.currentTarget);
        window.open($this.attr('href'));
    });
    // fire links with targets on same window
    $(document).on('click', 'nav a[target="_top"]', function(e) {
        e.preventDefault();
        var $this = $(e.currentTarget);
        window.location = ($this.attr('href'));


    });
    // all links with hash tags are ignored
    $(document).on('click', 'a[href="#"]', function(e) {
        // console.log("window.location.hash: " + window.location.hash);
        e.preventDefault();
    });
    // DO on hash change
    $(window).on('hashchange', function() {
        core.resetSessionTimer(time_limit, core.time_left);
        checkURL();
    });
}
function checkURL() {
    //get the url by removing the hash
    //var url = location.hash.replace(/^#/, '');
    var url = location.href.split('#').splice(1).join('#');
    //BEGIN: IE11 Work Around
    if(url.length === 0) {
        return;
    }
    if (!url) {
        try {
            var documentUrl = window.document.URL;
            if (documentUrl) {
                if (documentUrl.indexOf('#', 0) > 0 && documentUrl.indexOf('#', 0) < (documentUrl.length + 1)) {
                    url = documentUrl.substring(documentUrl.indexOf('#', 0) + 1);
                }
            }
        }
        catch (err) {}
    }
    //END: IE11 Work Around
    if(url != "undefined" && url.length > 0) {
        container = $('#main-content');
        // Do this if url exists (for page refresh, etc...)
        if (url) {
            loadURL(url + location.search, container);
        }
        else {
            if(url.length === 0) {
                return;
            }
            // grab the first URL from nav
            var $this = $('nav > ul > li:first-child > a[href!="#"]');
            //update hash
            // console.log("window.location.hash: " + window.location.hash);
            window.location.hash = $this.attr('href');
            //clear dom reference
            $this = null;
        }
    }
}

function loadURL(url, container) {
    // debugState
    if (debugState){
        // root.root.console.log("Loading URL: %c" + url, debugStyle);
    }
    var start_counter, end_counter;
    if(url.length === 0) {
        return;
    }
    $.ajax({
        type : "GET",
        url : "/" + url,
        async : true,
        dataType : 'html',
        cache : false, // (warning: setting it to false will cause a timestamp and will call the request twice)
        beforeSend : function() {
            start_counter = new Date().getTime();
            // destroy all datatable instances
            if ( $.navAsAjax && $('.dataTables_wrapper')[0] && (container[0] == $("#main-content")[0]) ) {
                var tables = $.fn.dataTable.fnTables(true);
                $(tables).each(function () {
                    if($(this).find('.details-control').length != 0) {
                        $(this).find('*').addBack().off().remove();
                        $(this).dataTable().fnDestroy();
                    } else {
                        $(this).dataTable().fnDestroy();
                    }
                });
            }
            // end destroy
            // cluster destroy: destroy other instances that could be on the page
            // this runs a script in the current loaded page before fetching the new page
            if ( $.navAsAjax && (container[0] == $("#main-content")[0]) ) {
                // this function is below the pagefunction for all pages that has instances
                if (typeof pagedestroy == 'function') {
                    try {
                        pagedestroy();
                        if (debugState){
                            // root.console.log("✔ Pagedestroy()");
                        }
                    }
                    catch(err) {
                        pagedestroy = undefined;
                        if (debugState){
                            root.console.log("! Pagedestroy() Catch Error");
                        }
                    }
                }
            }
            // end cluster destroy

            // empty container and var to start garbage collection (frees memory)
            pagefunction = null;
            container.removeData().html("");
            container.html("<div class='spinner-div'>" + spinner + "</div>");
            // Only draw breadcrumb if it is main content material
            if (container[0] == $("#main-content")[0]) {
                // clear everything else except these key DOM elements
                // we do this because sometime plugins will leave dynamic elements behind
                $('body').find('> *').filter(':not(' + ignore_key_elms + ')').empty().remove();
                // draw breadcrumb
                // scroll up
                $("html").animate({
                    scrollTop : 0
                }, "fast");
            }
            // end if
        },
        success : function(data, status, xhr) {
            // dump data to container
            container.css({
                opacity : '0.0'
            }).html(data).delay(50).animate({
                opacity : '1.0'
            }, 100);
            // clear data var
            data = null;
            container = null;
            core.init();
            if((lvar.cname == "domainbackup") || (lvar.cname == "clientbackup") || (lvar.cname == "serverbackup")) {
                if(lvar.aname == "upload") {
                    core.backupFileUpload();
                }
                if((lvar.aname == "addstorage") || (lvar.aname == "editStorage")) {
                    core.initBackupStorage();
                }
            }
            drawBreadCrumb();
            core.checkAuth(xhr);
            document.title = (typeof xhr.getResponseHeader("title") != "undefined" &&  xhr.getResponseHeader("title") != null ? xhr.getResponseHeader("title") : document.title);
        },
        error : function(xhr, status, thrownError, error) {
            if(xhr.status == 401) {
                if (debugState){
                    // root.console.log("✔ User will be logout in here!!!");
                    window.location.replace("/");
                }
            }
            else {
                container.css({
                    opacity : '0.0'
                }).html(xhr.responseText).delay(50).animate({
                    opacity : '1.0'
                }, 200);
                // container.html('<h4 class="ajax-loading-error"><i class="fa fa-warning txt-color-orangeDark"></i> Error requesting <span class="txt-color-red">' + url + '</span>: ' + xhr.status + ' <span style="text-transform: capitalize;">'  + thrownError + '</span></h4>');
            }
            drawBreadCrumb();
        },
        complete: function () {
            end_counter = new Date().getTime();
            let debug_style = debugStyle_success;
            let elapsed = (end_counter - start_counter);
            if(elapsed > 100) {
                debug_style = debugStyle_warning;
            }
            if(elapsed > 300) {
                debug_style = debugStyle_error;
            }

            root.console.log("✔ Page loading completed in %c" +  elapsed, debug_style, "ms");
            $(".load-time").text((end_counter - start_counter) + " ms");
        },
    });
}

function drawBreadCrumb() {
    $("ol.breadcrumb").html("");
    if(Array.isArray(breadcrumbs)) {
        for (var i in breadcrumbs) {
            $("ol.breadcrumb").append(breadcrumbs[i]["html"]);
        }
    }
}
core.sessionCountTimer();

$('body').on('click', function(e) {
    $('[rel="popover"], [data-rel="popover"]').each(function() {
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
        }
    });
});
$('body').on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
});
function getParam(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results == null)
        return "";
    else
        return results[1];
}