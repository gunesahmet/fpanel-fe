$.root_ = $('body');
$.navAsAjax = true;
var root = this,
    debugState = true,
    debugStyle_green = 'font-weight: bold; font-style:italic; color: #46C246;',
    debugStyle_warning = 'font-weight: bold; font-style:italic; color: #f8ac59;',
    debugStyle_success = 'font-weight: bold; font-style:italic; color: #1ab394;',
    debugStyle_error = 'font-weight: bold; font-style:italic; color: #ed5565;',
    throttle_delay = 350,
    menu_speed = 235,

    menu_accordion = false,

    /*
     * Turn on fast click for mobile devices
     * Enable this to activate fastclick plugin
     * dependency: js/plugin/fastclick/fastclick.js
     */
    fastClick = false,
    ignore_key_elms = ["#header, #left-panel, #right-panel, #main, #wrapper, .profile-element, .buttons-html5, div.page-footer, #shortcut, #divSmallBoxes, #divMiniIcons, #divbigBoxes, #voiceModal, script"];




