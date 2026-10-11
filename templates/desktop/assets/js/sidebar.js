(function($) {
    $.fn.sidebar = function(options) {

        var self = this;
        if (self.length > 1) {
            return self.each(function () {
                $(this).sidebar(options);
            });
        }

        // Width, height
        //var width = self.outerWidth();
        //var height = self.outerHeight();

        // Defaults
        var settings = $.extend({

            // Animation speed
            speed: 200,

            // Side: left|right|top|bottom
            side: "left",

            // Is closed
            isClosed: false,

            // Should I close the sidebar?
            close: true

        }, options);

        /*!
         *  Opens the sidebar
         *  $([jQuery selector]).trigger("sidebar:open");
         * */
        self.on("sidebar:open", function(ev, data) {
            var properties = {};
            properties[settings.side] = 0;
            settings.isClosed = null;
            self.stop().animate(properties, $.extend({}, settings, data).speed, function() {
                settings.isClosed = false;
                self.trigger("sidebar:opened");
            });
        });


        /*!
         *  Closes the sidebar
         *  $("[jQuery selector]).trigger("sidebar:close");
         * */
        self.on("sidebar:close", function(ev, data) {
            var properties = {};
            if (settings.side === "left" || settings.side === "right") {
                properties[settings.side] = -self.outerWidth()-10;
            } else {
                properties[settings.side] = -self.outerHeight()-10;
            }
            settings.isClosed = null;
            self.stop().animate(properties, $.extend({}, settings, data).speed, function() {
                settings.isClosed = true;
                self.trigger("sidebar:closed");
            });
        });

        /*!
         *  Toggles the sidebar
         *  $("[jQuery selector]).trigger("sidebar:toggle");
         * */
        self.on("sidebar:toggle", function(ev, data) {
            if (settings.isClosed) {
                self.trigger("sidebar:open", [data]);
            } else {
                self.trigger("sidebar:close", [data]);
            }
        });

        function closeWithNoAnimation() {
            self.trigger("sidebar:close", [{
                speed: 0
            }]);
        }

        // Close the sidebar
        if (!settings.isClosed && settings.close) {
            closeWithNoAnimation();
        }

        $(window).on("resize", function () {
            if (!settings.isClosed) { return; }
            closeWithNoAnimation();
        });

        self.data("sidebar", settings);

        return self;
    };

    // Version
    $.fn.sidebar.version = "3.3.2";
})(jQuery);