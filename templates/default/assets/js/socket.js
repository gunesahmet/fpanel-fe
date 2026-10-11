var sock;
var socket = function() {
    return {
        init: function () {
            var currentUrl = new URL(window.location)
            var host = "ws://{0}:{1}".sprintf(currentUrl.hostname, 8890);
            try {
                sock = new WebSocket(host);
                console.log("Sock started");

                sock.onopen = function(msg) {
                    console.log("Sock opened");
                    var data = {};
                    data = $.cookie();
                    sock.send(window.JSON.stringify(data));
                    socket.countMsg();
                    core.getNetworkData(true,0,0);
                };
                sock.onmessage = function(msg) {
                    var message = $.parseJSON(msg['data']);
                    if(message.hasOwnProperty("result")) {
                        if(message.hasOwnProperty("command")) {
                            switch (message.command) {
                                case "sysinfo":
                                    if($("#memory_usage").length) {
                                        var memory_usage = parseFloat(parseInt(message.result["memory"]["used"])/parseInt(message.result["memory"]["total"])*100).toFixed(0);
                                        var disk_usage = parseFloat(parseInt(message.result["disk"]["used"])/parseInt(message.result["disk"]["total"])*100).toFixed(0);
                                        if(memory_usage != $('#memory_usage').attr("data-percent")) {
                                            $('#memory_usage').data('easyPieChart').update(memory_usage);
                                            $('#memory_usage').attr("data-percent", memory_usage);
                                            core.fadeOutIn("#memory_usage .title");
                                            core.fadeOutIn("#memory_usage .percent");
                                        }
                                        if(memory_usage != $('#memory_usage').attr("data-percent")) {
                                            $('#disk_usage').data('easyPieChart').update(disk_usage);
                                            $('#disk_usage').attr("data-percent", disk_usage);
                                            core.fadeOutIn("#disk_usage .title");
                                            core.fadeOutIn("#disk_usage .percent");
                                        }
                                        if(message.result["cpu"]["used"] != $('#cpu_usage').attr("data-percent")) {
                                            $('#cpu_usage').data('easyPieChart').update(message.result["cpu"]["used"]);
                                            $('#cpu_usage').attr("data-percent", message.result["cpu"]["used"]);
                                            core.fadeOutIn("#cpu_usage .title");
                                            core.fadeOutIn("#cpu_usage .percent");
                                        }
                                        if($('#load-avg').text() != message.result["loadavg"]["min1"] + " " + message.result["loadavg"]["min5"] + " " + message.result["loadavg"]["min15"]) {
                                            $('#load-avg').text(message.result["loadavg"]["min1"] + " " + message.result["loadavg"]["min5"] + " " + message.result["loadavg"]["min15"]);
                                            core.fadeOutIn("#load-avg");
                                            core.fadeOutIn("#load-avg");
                                        }
                                    }
                                    break;
                                case "network-status":
                                    if($("#memory_usage").length) {
                                        core.getNetworkData(false, message.result.download, message.result.upload);
                                        core.plot.setData(core.series);
                                        core.plot.draw();
                                    }
                                    break;
                                case "process-list":
                                    if($("table#process-list").length) {
                                        core.setProcesses(message.result);
                                    }
                                    break;
                                case "domain-logs":
                                    if($(".small-chat-box").hasClass("active")) {
                                        core.showDomainLog(message.result);
                                        // console.log(message.result);
                                    }
                                    break;
                            }
                        }
                    }
                };
                sock.onclose = function(msg) {
                    if(this.readyState == 1)
                        console.log("Disconnected from server");
                    setTimeout(function(){
                        socket.init();
                    }, 1000);
                };
            }
            catch(ex){
                console.log(ex);
            }
        },
        sendMessage: function (data) {
            try {
                console.log(data);
                sock.send(window.JSON.stringify(data));
                // socket.countMsg();
            }
            catch(ex) {
                console.log(ex);
            }
        },
        log: function (msg) {
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
            socket.countMsg();

            if(element_len == 3) {
                $(".small-chat-box .content").slimscroll();
            }
        },
        countMsg: function () {
            $('#message_count').html( $(".small-chat-box .content").children().length);
        },
    };
}();
socket.init();