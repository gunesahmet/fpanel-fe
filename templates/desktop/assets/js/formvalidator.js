(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(["jquery"], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require("jquery"));
    } else {
        factory(jQuery);
    }
}(this, function (jQuery) {


    (function ($, undefined) {

        'use strict';

        /**
         * @deprecated
         * @param language
         * @param conf
         */
        $.fn.validateForm = function (language, conf) {
            $.formUtils.warn('Use of deprecated function $.validateForm, use $.isValid instead');
            return this.isValid(language, conf, true);
        };

        $(window).on('validatorsLoaded formValidationSetup', function(evt, $form, config) {
            if( !$form ) {
                $form = $('form');
            }

            addSupportForCustomErrorMessageCallback(config);
            addSupportForElementReferenceInPositionParam(config);
            addSupportForValidationDependingOnCheckedInput($form);
        });


        function addSupportForCustomErrorMessageCallback(config) {
            if (config &&
                config.errorMessagePosition === 'custom' &&
                typeof config.errorMessageCustom === 'function') {

                $.formUtils.warn('Use of deprecated function errorMessageCustom, use config.submitErrorMessageCallback instead');

                config.submitErrorMessageCallback = function($form, errorMessages) {
                    config.errorMessageCustom(
                        $form,
                        config.language.errorTitle,
                        errorMessages,
                        config
                    );
                };
            }
        }

        function addSupportForElementReferenceInPositionParam(config) {
            if (config.errorMessagePosition && typeof config.errorMessagePosition === 'object') {
                $.formUtils.warn('Deprecated use of config parameter errorMessagePosition, use config.submitErrorMessageCallback instead');
                var $errorMessageContainer = config.errorMessagePosition;
                config.errorMessagePosition = 'top';
                config.submitErrorMessageCallback = function() {
                    return $errorMessageContainer;
                };
            }
        }

        function addSupportForValidationDependingOnCheckedInput($form) {
            var $inputsDependingOnCheckedInputs = $form.find('[data-validation-if-checked]');
            if ($inputsDependingOnCheckedInputs.length) {
                $.formUtils.warn(
                    'Detected use of attribute "data-validation-if-checked" which is '+
                    'deprecated. Use "data-validation-depends-on" provided by module "logic"'
                );
            }

            $inputsDependingOnCheckedInputs
                .on('beforeValidation', function() {

                    var $elem = $(this),
                        nameOfDependingInput = $elem.valAttr('if-checked');

                    // Set the boolean telling us that the validation depends
                    // on another input being checked
                    var $dependingInput = $('input[name="' + nameOfDependingInput + '"]', $form),
                        dependingInputIsChecked = $dependingInput.is(':checked'),
                        valueOfDependingInput = ($.formUtils.getValue($dependingInput) || '').toString(),
                        requiredValueOfDependingInput = $elem.valAttr('if-checked-value');

                    if (!dependingInputIsChecked || !(
                            !requiredValueOfDependingInput ||
                            requiredValueOfDependingInput === valueOfDependingInput
                        )) {
                        $elem.valAttr('skipped', true);
                    }

                });
        }

    })(jQuery);

    /**
     * Utility methods used for displaying error messages (attached to $.formUtils)
     */
    (function ($) {

        'use strict';

        var dialogs = {

            resolveErrorMessage: function($elem, validator, validatorName, conf, language) {
                var errorMsgAttr = conf.validationErrorMsgAttribute + '-' + validatorName.replace('validate_', ''),
                    validationErrorMsg = $elem.attr(errorMsgAttr);

                if (!validationErrorMsg) {
                    validationErrorMsg = $elem.attr(conf.validationErrorMsgAttribute);
                    if (!validationErrorMsg) {
                        if (typeof validator.errorMessageKey !== 'function') {
                            validationErrorMsg = language[validator.errorMessageKey];
                        }
                        else {
                            validationErrorMsg = language[validator.errorMessageKey(conf)];
                        }
                        if (!validationErrorMsg) {
                            validationErrorMsg = validator.errorMessage;
                        }
                    }
                }
                return validationErrorMsg;
            },
            getParentContainer: function ($elem) {
                if($elem.parent().hasClass('input-group')){
                    $elem = $elem.parent();
                }
                if ($elem.valAttr('error-msg-container')) {
                    return $($elem.valAttr('error-msg-container'));
                }
                else {
                    var $parent = $elem.parent();
                    if (!$parent.hasClass('form-group') && !$parent.closest('form').hasClass('form-horizontal')) {
                        var $formGroup = $parent.closest('.form-group');
                        if ($formGroup.length) {
                            return $formGroup.eq(0);
                        }
                    }
                    return $parent;
                }
            },
            applyInputErrorStyling: function ($input, conf) {
                $input
                    .addClass(conf.errorElementClass)
                    .removeClass('valid');

                this.getParentContainer($input)
                    .addClass(conf.inputParentClassOnError)
                    .removeClass(conf.inputParentClassOnSuccess);


                if (conf.borderColorOnError !== '') {
                    $input.css('border-color', conf.borderColorOnError);
                }
            },
            applyInputSuccessStyling: function($input, conf) {
                $input.addClass('valid');
                this.getParentContainer($input)
                    .addClass(conf.inputParentClassOnSuccess);
            },
            removeInputStylingAndMessage: function($input, conf) {

                // Reset input css
                $input
                    .removeClass('valid')
                    .removeClass(conf.errorElementClass)
                    .css('border-color', '');


                var $parentContainer = dialogs.getParentContainer($input);
                var default_class = $parentContainer.attr('class').replace(conf.inputParentClassOnError,'');
                default_class = default_class.replace(conf.inputParentClassOnSuccess,'');
                // Reset parent css
                $parentContainer.attr('class',default_class);

                // Remove possible error message
                if (typeof conf.inlineErrorMessageCallback === 'function') {
                    var $errorMessage = conf.inlineErrorMessageCallback($input, false, conf);
                    if ($errorMessage) {
                        $errorMessage.html('');
                    }
                } else {
                    $parentContainer
                        .find('.' + conf.errorMessageClass)
                        .remove();
                }

            },
            removeAllMessagesAndStyling: function($form, conf) {

                // Remove error messages in top of form
                if (typeof conf.submitErrorMessageCallback === 'function') {
                    var $errorMessagesInTopOfForm = conf.submitErrorMessageCallback($form, conf);
                    if ($errorMessagesInTopOfForm) {
                        $errorMessagesInTopOfForm.html('');
                    }
                } else {
                    $form.find('.' + conf.errorMessageClass + '.alert').remove();
                }

                // Remove input css/messages
                $form.find('.' + conf.errorElementClass + ',.valid').each(function() {
                    dialogs.removeInputStylingAndMessage($(this), conf);
                });
            },
            setInlineMessage: function ($input, errorMsg, conf) {

                this.applyInputErrorStyling($input, conf);



                var custom = document.getElementById($input.attr('name') + '_err_msg'),
                    $messageContainer = false,
                    setErrorMessage = function ($elem) {
                        $.formUtils.$win.trigger('validationErrorDisplay', [$input, $elem]);
                        $elem.html(errorMsg);
                    },
                    addErrorToMessageContainer = function() {
                        var $found = false;
                        $messageContainer.find('.' + conf.errorMessageClass).each(function () {
                            if (this.inputReferer === $input[0]) {
                                $found = $(this);
                                return false;
                            }
                        });
                        if ($found) {
                            if (!errorMsg) {
                                $found.remove();
                            } else {
                                setErrorMessage($found);
                            }
                        } else if(errorMsg !== '') {
                            $message = $('<div class="' + conf.errorMessageClass + ' alert"></div>');
                            setErrorMessage($message);
                            $message[0].inputReferer = $input[0];
                            $messageContainer.prepend($message);
                        }
                    },
                    $message;

                if (custom) {
                    // Todo: remove in 3.0
                    $.formUtils.warn('Using deprecated element reference ' + custom.id);
                    $messageContainer = $(custom);
                    addErrorToMessageContainer();
                } else if (typeof conf.inlineErrorMessageCallback === 'function') {
                    $messageContainer = conf.inlineErrorMessageCallback($input, errorMsg, conf);
                    if (!$messageContainer) {
                        // Error display taken care of by inlineErrorMessageCallback
                        return;
                    }
                    addErrorToMessageContainer();
                } else {
                    var $parent = this.getParentContainer($input);
                    if($parent.hasClass('input-group')) {
                        $message = $parent.parent().find('.' + conf.errorMessageClass + '.help-block');
                    }
                    else {
                        $message = $parent.find('.' + conf.errorMessageClass + '.help-block');
                    }
                    if ($message.length === 0) {
                        $message = $('<span></span>').addClass('help-block').addClass(conf.errorMessageClass);
                        if($parent.hasClass('input-group')) {
                            $message.appendTo($parent.parent());
                        }
                        else {
                            $message.appendTo($parent);
                        }
                    }
                    setErrorMessage($message);
                }
            },
            setMessageInTopOfForm: function ($form, errorMessages, conf, lang) {
                var view = '<div class="{errorMessageClass} alert alert-danger">'+
                        '<strong>{errorTitle}</strong>'+
                        '<ul>{fields}</ul>'+
                        '</div>',
                    $container = false;

                if (typeof conf.submitErrorMessageCallback === 'function') {
                    $container = conf.submitErrorMessageCallback($form, errorMessages, conf);
                    if (!$container) {
                        // message display taken care of by callback
                        return;
                    }
                }

                var viewParams = {
                    errorTitle: lang.errorTitle,
                    fields: '',
                    errorMessageClass: conf.errorMessageClass
                };

                $.each(errorMessages, function (i, msg) {
                    viewParams.fields += '<li>'+msg+'</li>';
                });

                $.each(viewParams, function(param, value) {
                    view = view.replace('{'+param+'}', value);
                });

                if ($container) {
                    $container.html(view);
                } else {
                    $form.children().eq(0).before($(view));
                }
            }
        };

        $.formUtils = $.extend($.formUtils || {}, {
            dialogs: dialogs
        });

    })(jQuery);

    /**
     * File declaring all methods if this plugin which is applied to $.fn.
     */
    (function($, window, undefined) {

        'use strict';

        var _helpers = 0;


        /**
         * Assigns validateInputOnBlur function to elements blur event
         *
         * @param {Object} language Optional, will override $.formUtils.LANG
         * @param {Object} conf Optional, will override the default settings
         * @return {jQuery}
         */
        $.fn.validateOnBlur = function (language, conf) {
            var $form = this,
                $elems = this.find('*[data-validation]');

            $elems.each(function(){
                var $this = $(this);
                if ($this.is('[type=radio]')){
                    var $additionals = $form.find('[type=radio][name="' + $this.attr('name') + '"]');
                    $additionals.bind('blur.validation', function(){
                        $this.validateInputOnBlur(language, conf, true, 'blur');
                    });
                    if (conf.validateCheckboxRadioOnClick) {
                        $additionals.bind('click.validation', function () {
                            $this.validateInputOnBlur(language, conf, true, 'click');
                        });
                    }
                }
            });

            $elems.bind('blur.validation', function () {
                $(this).validateInputOnBlur(language, conf, true, 'blur');
            });

            if (conf.validateCheckboxRadioOnClick) {
                // bind click event to validate on click for radio & checkboxes for nice UX
                this.find('input[type=checkbox][data-validation],input[type=radio][data-validation]')
                    .bind('click.validation', function () {
                        $(this).validateInputOnBlur(language, conf, true, 'click');
                    });
            }

            return this;
        };

        /*
         * Assigns validateInputOnBlur function to elements custom event
         * @param {Object} language Optional, will override $.formUtils.LANG
         * @param {Object} settings Optional, will override the default settings
         * * @return {jQuery}
         */
        $.fn.validateOnEvent = function (language, config) {
            var $elements = this[0].nodeName === 'FORM' ? this.find('*[data-validation-event]') : this;
            $elements
                .each(function () {
                    var $el = $(this),
                        etype = $el.valAttr('event');
                    if (etype) {
                        $el
                            .unbind(etype + '.validation')
                            .bind(etype + '.validation', function (evt) {
                                if( (evt || {}).keyCode !== 9 ) {
                                    $(this).validateInputOnBlur(language, config, true, etype);
                                }
                            });
                    }
                });
            return this;
        };

        /**
         * fade in help message when input gains focus
         * fade out when input loses focus
         * <input data-help="The info that I want to display for the user when input is focused" ... />
         *
         * @param {String} attrName - Optional, default is data-help
         * @return {jQuery}
         */
        $.fn.showHelpOnFocus = function (attrName) {
            if (!attrName) {
                attrName = 'data-validation-help';
            }

            // Remove previously added event listeners
            this.find('.has-help-txt')
                .valAttr('has-keyup-event', false)
                .removeClass('has-help-txt');

            // Add help text listeners
            this.find('textarea,input').each(function () {
                var $elem = $(this),
                    className = 'jquery_form_help_' + (++_helpers),
                    help = $elem.attr(attrName);

                if (help) {
                    $elem
                        .addClass('has-help-txt')
                        .unbind('focus.help')
                        .bind('focus.help', function () {
                            var $help;
                            if($elem.parent().hasClass('input-group')) {
                                $help = $elem.parent().parent().find('.' + className);
                            }
                            else {
                                $help = $elem.parent().find('.' + className);
                            }
                            if ($help.length === 0) {
                                $help = $('<span />')
                                    .addClass(className)
                                    .addClass('help')
                                    .addClass('help-block') // twitter bs
                                    .text(help)
                                    .hide();
                                if($elem.parent().hasClass('input-group')) {
                                    $elem.parent().after($help);
                                }
                                else {
                                    $elem.after($help);
                                }
                            }
                            $help.fadeIn();
                        })
                        .unbind('blur.help')
                        .bind('blur.help', function () {
                            $(this)
                                .parent()
                                .find('.' + className)
                                .fadeOut('slow');
                        });
                }
            });

            return this;
        };

        /**
         * @param {Function} cb
         * @param {Object} [conf]
         * @param {Object} [lang]
         */
        $.fn.validate = function(cb, conf, lang) {
            var language = $.extend({}, $.formUtils.LANG, lang || {});
            this.each(function() {
                var $elem = $(this),
                    formDefaultConfig = $elem.closest('form').get(0).validationConfig || {};

                $elem.one('validation', function(evt, isValid) {
                    if ( typeof cb === 'function' ) {
                        cb(isValid, this, evt);
                    }
                });

                $elem.validateInputOnBlur(
                    language,
                    $.extend({}, formDefaultConfig, conf || {}),
                    true
                );
            });
        };

        /**
         * Tells whether or not validation of this input will have to postpone the form submit ()
         * @returns {Boolean}
         */
        $.fn.willPostponeValidation = function() {
            return (this.valAttr('suggestion-nr') ||
                this.valAttr('postpone') ||
                this.hasClass('hasDatepicker')) &&
                !window.postponedValidation;
        };

        /**
         * Validate single input when it loses focus
         * shows error message in a span element
         * that is appended to the parent element
         *
         * @param {Object} [language] Optional, will override $.formUtils.LANG
         * @param {Object} [conf] Optional, will override the default settings
         * @param {Boolean} attachKeyupEvent Optional
         * @param {String} eventType
         * @return {jQuery}
         */
        $.fn.validateInputOnBlur = function (language, conf, attachKeyupEvent, eventType) {

            //console.log('validating '+this.get(0).name+' on blur');

            $.formUtils.eventType = eventType;

            if ( this.willPostponeValidation() ) {
                // This validation has to be postponed
                var _self = this,
                    postponeTime = this.valAttr('postpone') || 200;

                window.postponedValidation = function () {
                    _self.validateInputOnBlur(language, conf, attachKeyupEvent, eventType);
                    window.postponedValidation = false;
                };

                setTimeout(function () {
                    if (window.postponedValidation) {
                        window.postponedValidation();
                    }
                }, postponeTime);

                return this;
            }

            language = $.extend({}, $.formUtils.LANG, language || {});
            $.formUtils.dialogs.removeInputStylingAndMessage(this, conf);

            var $elem = this,
                $form = $elem.closest('form'),
                result = $.formUtils.validateInput(
                    $elem,
                    language,
                    conf,
                    $form,
                    eventType
                );

            if (attachKeyupEvent) {
                $elem.removeKeyUpValidation();
            }

            if (result.shouldChangeDisplay) {
                if (result.isValid) {
                    $.formUtils.dialogs.applyInputSuccessStyling($elem, conf);
                } else {
                    $.formUtils.dialogs.setInlineMessage($elem, result.errorMsg, conf);
                }
            }

            if (!result.isValid && attachKeyupEvent) {
                //console.log('in here');
                $elem.validateOnKeyUp(language, conf);
            }

            return this;
        };

        /**
         * Validate element on keyup-event
         */
        $.fn.validateOnKeyUp = function(language, conf) {
            this.each(function() {
                var $input = $(this);
                if (!$input.valAttr('has-keyup-event')) {
                    //console.log('Applygin keyup-validation on '+this.name);
                    //console.log((new Error()).stack);
                    $input
                        .valAttr('has-keyup-event', 'true')
                        .bind('keyup.validation', function (evt) {
                            //console.log('key up');
                            if( evt.keyCode !== 9 ) {
                                $input.validateInputOnBlur(language, conf, false, 'keyup');
                            }
                        });
                }
            });
            return this;
        };

        /**
         * Remove validation on keyup
         */
        $.fn.removeKeyUpValidation = function() {
            this.each(function() {
                $(this)
                    .valAttr('has-keyup-event', false)
                    .unbind('keyup.validation');
            });
            return this;
        };

        /**
         * Short hand for fetching/adding/removing element attributes
         * prefixed with 'data-validation-'
         *
         * @param {String} name
         * @param {String|Boolean} [val]
         * @return {String|undefined|jQuery}
         * @protected
         */
        $.fn.valAttr = function (name, val) {
            if (val === undefined) {
                return this.attr('data-validation-' + name);
            } else if (val === false || val === null) {
                return this.removeAttr('data-validation-' + name);
            } else {
                name = ((name.length > 0) ? '-' + name : '');
                return this.attr('data-validation' + name, val);
            }
        };

        /**
         * Function that validates all inputs in active form
         *
         * @param {Object} [language]
         * @param {Object} [conf]
         * @param {Boolean} [displayError] Defaults to true
         */
        $.fn.isValid = function (language, conf, displayError) {

            if ($.formUtils.isLoadingModules) {
                var $self = this;
                setTimeout(function () {
                    $self.isValid(language, conf, displayError);
                }, 200);
                return null;
            }

            conf = $.extend({}, $.formUtils.defaultConfig(), conf || {});
            language = $.extend({}, $.formUtils.LANG, language || {});
            displayError = displayError !== false;

            if ($.formUtils.errorDisplayPreventedWhenHalted) {
                // isValid() was called programmatically with argument displayError set
                // to false when the validation was halted by any of the validators
                delete $.formUtils.errorDisplayPreventedWhenHalted;
                displayError = false;
            }

            $.formUtils.isValidatingEntireForm = true;
            $.formUtils.haltValidation = false;

            /**
             * Adds message to error message stack if not already in the message stack
             *
             * @param {String} mess
             * @para {jQuery} $elem
             */
            var addErrorMessage = function (mess, $elem) {
                    if ($.inArray(mess, errorMessages) < 0) {
                        errorMessages.push(mess);
                    }
                    errorInputs.push($elem);
                    $elem.attr('current-error', mess);
                    if (displayError) {
                        $.formUtils.dialogs.applyInputErrorStyling($elem, conf);
                    }
                },

                /** Holds inputs (of type checkox or radio) already validated, to prevent recheck of mulitple checkboxes & radios */
                checkedInputs = [],

                /** Error messages for this validation */
                errorMessages = [],

                /** Input elements which value was not valid */
                errorInputs = [],

                /** Form instance */
                $form = this,

                /**
                 * Tells whether or not to validate element with this name and of this type
                 *
                 * @param {String} name
                 * @param {String} type
                 * @return {Boolean}
                 */
                ignoreInput = function (name, type) {
                    if (type === 'submit' || type === 'button' || type === 'reset') {
                        return true;
                    }
                    return $.inArray(name, conf.ignore || []) > -1;
                };

            // Reset style and remove error class
            if (displayError) {
                $.formUtils.dialogs.removeAllMessagesAndStyling($form, conf);
            }

            // Validate element values
            $form.find('input,textarea,select').filter(':not([type="submit"],[type="button"])').each(function () {
                var $elem = $(this),
                    elementType = $elem.attr('type'),
                    isCheckboxOrRadioBtn = elementType === 'radio' || elementType === 'checkbox',
                    elementName = $elem.attr('name');

                if (!ignoreInput(elementName, elementType) && (!isCheckboxOrRadioBtn || $.inArray(elementName, checkedInputs) < 0)) {

                    if (isCheckboxOrRadioBtn) {
                        checkedInputs.push(elementName);
                    }

                    var result = $.formUtils.validateInput(
                        $elem,
                        language,
                        conf,
                        $form,
                        'submit'
                    );

                    if (!result.isValid) {
                        addErrorMessage(result.errorMsg, $elem);
                    } else if (result.isValid && result.shouldChangeDisplay) {
                        $elem.valAttr('current-error', false);
                        $.formUtils.dialogs.applyInputSuccessStyling($elem, conf);
                    }
                }

            });

            // Run validation callback
            if (typeof conf.onValidate === 'function') {
                var errors = conf.onValidate($form);
                if ($.isArray(errors)) {
                    $.each(errors, function (i, err) {
                        addErrorMessage(err.message, err.element);
                    });
                }
                else if (errors && errors.element && errors.message) {
                    addErrorMessage(errors.message, errors.element);
                }
            }

            // Reset form validation flag
            $.formUtils.isValidatingEntireForm = false;

            // Validation failed
            if (!$.formUtils.haltValidation && errorInputs.length > 0) {

                if (displayError) {

                    if (conf.errorMessagePosition === 'top') {
                        $.formUtils.dialogs.setMessageInTopOfForm($form, errorMessages, conf, language);
                    } else {
                        $.each(errorInputs, function (i, $input) {
                            $.formUtils.dialogs.setInlineMessage($input, $input.attr('current-error'), conf);
                        });
                    }
                    if (conf.scrollToTopOnError) {
                        $.formUtils.$win.scrollTop($form.offset().top - 20);
                    }

                }

                return false;
            }

            if (!displayError && $.formUtils.haltValidation) {
                $.formUtils.errorDisplayPreventedWhenHalted = true;
            }

            return !$.formUtils.haltValidation;
        };

        /**
         * Plugin for displaying input length restriction
         */
        $.fn.restrictLength = function (maxLengthElement) {
            new $.formUtils.lengthRestriction(this, maxLengthElement);
            return this;
        };

        /**
         * Add suggestion dropdown to inputs having data-suggestions with a comma
         * separated string with suggestions
         * @param {Array} [settings]
         * @returns {jQuery}
         */
        $.fn.addSuggestions = function (settings) {
            var sugs = false;
            this.find('input').each(function () {
                var $field = $(this);

                sugs = $.split($field.attr('data-suggestions'));

                if (sugs.length > 0 && !$field.hasClass('has-suggestions')) {
                    $.formUtils.suggest($field, sugs, settings);
                    $field.addClass('has-suggestions');
                }
            });
            return this;
        };


    })(jQuery, window);

    /**
     * Utility methods used for handling loading of modules (attached to $.formUtils)
     */
    (function($) {

        'use strict';

        $.formUtils = $.extend($.formUtils || {}, {

            /**
             * @var {Boolean}
             */
            isLoadingModules: false,

            /**
             * @var {Object}
             */
            loadedModules: {},

            /**
             * @example
             *  $.formUtils.loadModules('date, security.dev');
             *
             * Will load the scripts date.js and security.dev.js from the
             * directory where this script resides. If you want to load
             * the modules from another directory you can use the
             * path argument.
             *
             * The script will be cached by the browser unless the module
             * name ends with .dev
             *
             * @param {String} modules - Comma separated string with module file names (no directory nor file extension)
             * @param {String} [path] - Optional, path where the module files is located if their not in the same directory as the core modules
             * @param {function} [callback] - Optional, whether or not to fire event 'load' when modules finished loading
             */
            loadModules: function (modules, path, callback) {

                if ($.formUtils.isLoadingModules) {
                    setTimeout(function () {
                        $.formUtils.loadModules(modules, path, callback);
                    }, 10);
                    return;
                }

                var hasLoadedAnyModule = false,
                    loadModuleScripts = function (modules, path) {

                        var moduleList = $.split(modules),
                            numModules = moduleList.length,
                            moduleLoadedCallback = function () {
                                numModules--;
                                if (numModules === 0) {
                                    $.formUtils.isLoadingModules = false;
                                    if (callback && hasLoadedAnyModule) {
                                        if( typeof callback === 'function' ) {
                                            callback();
                                        }
                                    }
                                }
                            };


                        if (numModules > 0) {
                            $.formUtils.isLoadingModules = true;
                        }

                        var cacheSuffix = '?_=' + ( new Date().getTime() ),
                            appendToElement = document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0];

                        $.each(moduleList, function (i, modName) {
                            modName = $.trim(modName);
                            if (modName.length === 0) {
                                moduleLoadedCallback();
                            }
                            else {
                                var scriptUrl = path + modName + (modName.slice(-3) === '.js' ? '' : '.js'),
                                    script = document.createElement('SCRIPT');

                                if (scriptUrl in $.formUtils.loadedModules) {
                                    // already loaded
                                    moduleLoadedCallback();
                                }
                                else {

                                    // Remember that this script is loaded
                                    $.formUtils.loadedModules[scriptUrl] = 1;
                                    hasLoadedAnyModule = true;

                                    // Load the script
                                    script.type = 'text/javascript';
                                    script.onload = moduleLoadedCallback;
                                    script.src = scriptUrl + ( scriptUrl.slice(-7) === '.dev.js' ? cacheSuffix : '' );
                                    script.onerror = function() {
                                        $.formUtils.warn('Unable to load form validation module '+scriptUrl);
                                    };
                                    script.onreadystatechange = function () {
                                        // IE 7 fix
                                        if (this.readyState === 'complete' || this.readyState === 'loaded') {
                                            moduleLoadedCallback();
                                            // Handle memory leak in IE
                                            this.onload = null;
                                            this.onreadystatechange = null;
                                        }
                                    };
                                    appendToElement.appendChild(script);
                                }
                            }
                        });
                    };

                if (path) {
                    loadModuleScripts(modules, path);
                } else {
                    var findScriptPathAndLoadModules = function () {
                        var foundPath = false;
                        $('script[src*="form-validator"]').each(function () {
                            foundPath = this.src.substr(0, this.src.lastIndexOf('/')) + '/';
                            if (foundPath === '/') {
                                foundPath = '';
                            }
                            return false;
                        });

                        if (foundPath !== false) {
                            loadModuleScripts(modules, foundPath);
                            return true;
                        }
                        return false;
                    };

                    if (!findScriptPathAndLoadModules()) {
                        $(findScriptPathAndLoadModules);
                    }
                }
            }

        });

    })(jQuery);

    /**
     * Setup function for the plugin
     */
    (function ($) {

        'use strict';


        /**
         * A bit smarter split function
         * delimiter can be space, comma, dash or pipe
         * @param {String} val
         * @param {Function|String} [callback]
         * @returns {Array|void}
         */
        $.split = function (val, callback) {
            if (typeof callback !== 'function') {
                // return array
                if (!val) {
                    return [];
                }
                var values = [];
                $.each(val.split(callback ? callback : /[,|\-\s]\s*/g),
                    function (i, str) {
                        str = $.trim(str);
                        if (str.length) {
                            values.push(str);
                        }
                    }
                );
                return values;
            } else if (val) {
                // exec callback func on each
                $.each(val.split(/[,|\-\s]\s*/g),
                    function (i, str) {
                        str = $.trim(str);
                        if (str.length) {
                            return callback(str, i);
                        }
                    }
                );
            }
        };

        /**
         * Short hand function that makes the validation setup require less code
         * @param conf
         */
        $.validate = function (conf) {

            var defaultConf = $.extend($.formUtils.defaultConfig(), {
                form: 'form',
                validateOnEvent: false,
                validateOnBlur: true,
                validateCheckboxRadioOnClick: true,
                showHelpOnFocus: true,
                addSuggestions: true,
                modules: '',
                onModulesLoaded: null,
                language: false,
                onSuccess: false,
                onError: false,
                onElementValidate: false
            });

            conf = $.extend(defaultConf, conf || {});

            if( conf.lang && conf.lang !== 'en' ) {
                var langModule = 'lang/'+conf.lang+'.js';
                conf.modules += conf.modules.length ? ','+langModule : langModule;
            }

            // Add validation to forms
            $(conf.form).each(function (i, form) {

                // Make a reference to the config for this form
                form.validationConfig = conf;

                // Trigger jQuery event that we're about to setup validation
                var $form = $(form);
                // $.formUtils.$win.trigger('formValidationSetup', [$form, conf]);
                $form.trigger('formValidationSetup', [$form, conf]);

                // Remove classes and event handlers that might have been
                // added by a previous call to $.validate
                $form.find('.has-help-txt')
                    .unbind('focus.validation')
                    .unbind('blur.validation');

                $form
                    .removeClass('has-validation-callback')
                    .unbind('submit.validation')
                    .unbind('reset.validation')
                    .find('input[data-validation],textarea[data-validation]')
                    .unbind('blur.validation');

                // Validate when submitted
                $form.bind('submit.validation', function (evt) {

                        var $form = $(this),
                            stop = function() {
                                evt.stopImmediatePropagation();
                                return false;
                            };

                        if ($.formUtils.haltValidation) {
                            // pressing several times on submit button while validation is halted
                            return stop();
                        }

                        if ($.formUtils.isLoadingModules) {
                            setTimeout(function () {
                                $form.trigger('submit.validation');
                            }, 200);
                            return stop();
                        }

                        var valid = $form.isValid(conf.language, conf);

                        if ($.formUtils.haltValidation) {
                            // Validation got halted by one of the validators
                            return stop();
                        } else {
                            if (valid && typeof conf.onSuccess === 'function') {
                                var callbackResponse = conf.onSuccess($form);
                                if (callbackResponse === false) {
                                    return stop();
                                }
                            } else if (!valid && typeof conf.onError === 'function') {
                                conf.onError($form);
                                return stop();
                            } else {
                                return valid ? true : stop();
                            }
                        }
                    })
                    .bind('reset.validation', function () {
                        $.formUtils.dialogs.removeAllMessagesAndStyling($form, conf);
                    })
                    .addClass('has-validation-callback');

                if (conf.showHelpOnFocus) {
                    $form.showHelpOnFocus();
                }
                if (conf.addSuggestions) {
                    $form.addSuggestions();
                }
                if (conf.validateOnBlur) {
                    $form.validateOnBlur(conf.language, conf);
                    $form.bind('html5ValidationAttrsFound', function () {
                        $form.validateOnBlur(conf.language, conf);
                    });
                }
                if (conf.validateOnEvent) {
                    $form.validateOnEvent(conf.language, conf);
                }
            });

            if (conf.modules !== '') {
                $.formUtils.loadModules(conf.modules, false, function() {
                    if (typeof conf.onModulesLoaded === 'function') {
                        conf.onModulesLoaded();
                    }
                    var $form = typeof conf.form === 'string' ? $(conf.form) : conf.form;
                    $.formUtils.$win.trigger('validatorsLoaded', [$form, conf]);
                });
            }
        };

    })(jQuery);

    /**
     * Utility methods and properties attached to $.formUtils
     */
    (function($, window) {

        'use strict';

        var $win = $(window);

        $.formUtils = $.extend($.formUtils || {}, {

            $win: $win,

            /**
             * Default config for $(...).isValid();
             */
            defaultConfig: function () {
                return {
                    ignore: [], // Names of inputs not to be validated even though `validationRuleAttribute` containing the validation rules tells us to
                    errorElementClass: 'error', // Class that will be put on elements which value is invalid
                    borderColorOnError: '#b94a48', // Border color of elements which value is invalid, empty string to not change border color
                    errorMessageClass: 'form-error', // class name of div containing error messages when validation fails
                    validationRuleAttribute: 'data-validation', // name of the attribute holding the validation rules
                    validationErrorMsgAttribute: 'data-validation-error-msg', // define custom err msg inline with element
                    errorMessagePosition: 'inline', // Can be either "top" or "inline"
                    errorMessageTemplate: {
                        container: '<div class="{errorMessageClass} alert alert-danger">{messages}</div>',
                        messages: '<strong>{errorTitle}</strong><ul>{fields}</ul>',
                        field: '<li>{msg}</li>'
                    },
                    scrollToTopOnError: true,
                    dateFormat: 'dd-mm-yyyy',
                    addValidClassOnAll: false, // whether or not to apply class="valid" even if the input wasn't validated
                    decimalSeparator: '.',
                    inputParentClassOnError: 'has-error', // twitter-bootstrap default class name
                    inputParentClassOnSuccess: 'has-success', // twitter-bootstrap default class name
                    validateHiddenInputs: false, // whether or not hidden inputs should be validated
                    inlineErrorMessageCallback: false,
                    submitErrorMessageCallback: false
                };
            },

            /**
             * Available validators
             */
            validators: {},

            /**
             * Events triggered by form validator
             */
            _events: {load: [], valid: [], invalid: []},

            /**
             * Setting this property to true during validation will
             * stop further validation from taking place and form will
             * not be sent
             */
            haltValidation: false,

            /**
             * This variable will be true $.fn.isValid() is called
             * and false when $.fn.validateOnBlur is called
             */
            isValidatingEntireForm: false,

            /**
             * Function for adding a validator
             * @param {Object} validator
             */
            addValidator: function (validator) {
                // prefix with "validate_" for backward compatibility reasons
                var name = validator.name.indexOf('validate_') === 0 ? validator.name : 'validate_' + validator.name;
                if (validator.validateOnKeyUp === undefined) {
                    validator.validateOnKeyUp = true;
                }
                this.validators[name] = validator;
            },

            /**
             * Warn user via the console if available
             */
            warn: function(msg) {
                if( 'console' in window ) {
                    if( typeof window.console.warn === 'function' ) {
                        window.console.warn(msg);
                    } else if( typeof window.console.log === 'function' ) {
                        window.console.log(msg);
                    }
                } else {
                    alert(msg);
                }
            },

            /**
             * Same as input $.fn.val() but also supporting input of typ radio or checkbox
             * @example
             *
             *  $.formUtils.getValue('.myRadioButtons', $('#some-form'));
             *  $.formUtils.getValue($('#some-form').find('.check-boxes'));
             *
             * @param query
             * @param $parent
             * @returns {String|Boolean}
             */
            getValue: function(query, $parent) {
                var $inputs = $parent ? $parent.find(query) : query;
                if ($inputs.length > 0 ) {
                    var type = $inputs.eq(0).attr('type');
                    if (type === 'radio' || type === 'checkbox') {
                        return $inputs.filter(':checked').val();
                    } else {
                        return $inputs.val();
                    }
                }
                return false;
            },

            /**
             * Validate the value of given element according to the validation rules
             * found in the attribute data-validation. Will return an object representing
             * a validation result, having the props shouldChangeDisplay, isValid and errorMsg
             * @param {jQuery} $elem
             * @param {Object} language ($.formUtils.LANG)
             * @param {Object} conf
             * @param {jQuery} $form
             * @param {String} [eventContext]
             * @return {Object}
             */
            validateInput: function ($elem, language, conf, $form, eventContext) {

                conf = conf || $.formUtils.defaultConfig();
                language = language || $.formUtils.LANG;

                var value = this.getValue($elem);

                $elem
                    .valAttr('skipped', false)
                    .one('beforeValidation', function() {
                        // Skip input because its hidden or disabled
                        // Doing this in a callback makes it possible for others to prevent the default
                        // behaviour by binding to the same event and call evt.stopImmediatePropagation()
                        if ($elem.attr('disabled') || (!$elem.is(':visible') && !conf.validateHiddenInputs)) {
                            $elem.valAttr('skipped', 1);
                        }
                    })
                    .trigger('beforeValidation', [value, conf, language]);


                var inputIsOptional = $elem.valAttr('optional') === 'true',
                    skipBecauseItsEmpty = !value && inputIsOptional,
                    validationRules = $elem.attr(conf.validationRuleAttribute),
                    isValid = true,
                    errorMsg = '',
                    result = {isValid: true, shouldChangeDisplay:true, errorMsg:''};

                // For input type="number", browsers attempt to parse the entered value into a number.
                // If the input is not numeric, browsers handle the situation differently:
                // Chrome 48 simply disallows non-numeric input; FF 44 clears out the input box on blur;
                // Safari 5 parses the entered string to find a leading number.
                // If the input fails browser validation, the browser sets the input value equal to an empty string.
                // Therefore, we cannot distinguish (apart from hacks) between an empty input type="text" and one with a
                // value that can't be parsed by the browser.

                if (!validationRules || skipBecauseItsEmpty || $elem.valAttr('skipped')) {
                    result.shouldChangeDisplay = conf.addValidClassOnAll;
                    return result;
                }

                // Filter out specified characters
                var ignore = $elem.valAttr('ignore');
                if (ignore) {
                    $.each(ignore.split(''), function(i, char) {
                        value = value.replace(new RegExp('\\'+char), '');
                    });
                }

                $.split(validationRules, function (rule) {

                    if (rule.indexOf('validate_') !== 0) {
                        rule = 'validate_' + rule;
                    }

                    var validator = $.formUtils.validators[rule];

                    if (validator) {

                        // special change of element for checkbox_group rule
                        if (rule === 'validate_checkbox_group') {
                            // set element to first in group, so error msg attr doesn't need to be set on all elements in group
                            $elem = $form.find('[name="' + $elem.attr('name') + '"]:eq(0)');
                        }

                        if (eventContext !== 'keyup' || validator.validateOnKeyUp) {
                            // A validator can prevent itself from getting triggered on keyup
                            isValid = validator.validatorFunction(value, $elem, conf, language, $form);
                        }

                        if (!isValid) {
                            if (conf.validateOnBlur) {
                                $elem.validateOnKeyUp(language, conf);
                            }
                            errorMsg = $.formUtils.dialogs.resolveErrorMessage($elem, validator, rule, conf, language);
                            return false; // break iteration
                        }

                    } else {

                        // todo: Add some validator lookup function and tell immediately which module is missing
                        throw new Error('Using undefined validator "' + rule +
                            '". Maybe you have forgotten to load the module that "' + rule +'" belongs to?');

                    }

                }, ' ');


                if (isValid === false) {
                    $elem.trigger('validation', false);
                    result.errorMsg = errorMsg;
                    result.isValid = false;
                    result.shouldChangeDisplay = true;
                } else if (isValid === null) {
                    // A validatorFunction returning null means that it's not able to validate
                    // the input at this time. Most probably some async stuff need to gets finished
                    // first and then the validator will re-trigger the validation.
                    result.shouldChangeDisplay = false;
                } else {
                    $elem.trigger('validation', true);
                    result.shouldChangeDisplay = true;
                }

                // Run element validation callback
                if (typeof conf.onElementValidate === 'function' && errorMsg !== null) {
                    conf.onElementValidate(result.isValid, $elem, $form, errorMsg);
                }

                $elem.trigger('afterValidation', [result, eventContext]);

                return result;
            },

            /**
             * Is it a correct date according to given dateFormat. Will return false if not, otherwise
             * an array 0=>year 1=>month 2=>day
             *
             * @param {String} val
             * @param {String} dateFormat
             * @param {Boolean} [addMissingLeadingZeros]
             * @return {Array}|{Boolean}
             */
            parseDate: function (val, dateFormat, addMissingLeadingZeros) {
                var divider = dateFormat.replace(/[a-zA-Z]/gi, '').substring(0, 1),
                    regexp = '^',
                    formatParts = dateFormat.split(divider || null),
                    matches, day, month, year;

                $.each(formatParts, function (i, part) {
                    regexp += (i > 0 ? '\\' + divider : '') + '(\\d{' + part.length + '})';
                });

                regexp += '$';

                if (addMissingLeadingZeros) {
                    var newValueParts = [];
                    $.each(val.split(divider), function(i, part) {
                        if(part.length === 1) {
                            part = '0'+part;
                        }
                        newValueParts.push(part);
                    });
                    val = newValueParts.join(divider);
                }

                matches = val.match(new RegExp(regexp));
                if (matches === null) {
                    return false;
                }

                var findDateUnit = function (unit, formatParts, matches) {
                    for (var i = 0; i < formatParts.length; i++) {
                        if (formatParts[i].substring(0, 1) === unit) {
                            return $.formUtils.parseDateInt(matches[i + 1]);
                        }
                    }
                    return -1;
                };

                month = findDateUnit('m', formatParts, matches);
                day = findDateUnit('d', formatParts, matches);
                year = findDateUnit('y', formatParts, matches);

                if ((month === 2 && day > 28 && (year % 4 !== 0 || year % 100 === 0 && year % 400 !== 0)) ||
                    (month === 2 && day > 29 && (year % 4 === 0 || year % 100 !== 0 && year % 400 === 0)) ||
                    month > 12 || month === 0) {
                    return false;
                }
                if ((this.isShortMonth(month) && day > 30) || (!this.isShortMonth(month) && day > 31) || day === 0) {
                    return false;
                }

                return [year, month, day];
            },

            /**
             * skum fix. är talet 05 eller lägre ger parseInt rätt int annars får man 0 när man kör parseInt?
             *
             * @param {String} val
             * @return {Number}
             */
            parseDateInt: function (val) {
                if (val.indexOf('0') === 0) {
                    val = val.replace('0', '');
                }
                return parseInt(val, 10);
            },

            /**
             * Has month only 30 days?
             *
             * @param {Number} m
             * @return {Boolean}
             */
            isShortMonth: function (m) {
                return (m % 2 === 0 && m < 7) || (m % 2 !== 0 && m > 7);
            },

            /**
             * Restrict input length
             *
             * @param {jQuery} $inputElement Jquery Html object
             * @param {jQuery} $maxLengthElement jQuery Html Object
             * @return void
             */
            lengthRestriction: function ($inputElement, $maxLengthElement) {
                // read maxChars from counter display initial text value
                var maxChars = parseInt($maxLengthElement.text(), 10),
                    charsLeft = 0,

                // internal function does the counting and sets display value
                    countCharacters = function () {
                        var numChars = $inputElement.val().length;
                        if (numChars > maxChars) {
                            // get current scroll bar position
                            var currScrollTopPos = $inputElement.scrollTop();
                            // trim value to max length
                            $inputElement.val($inputElement.val().substring(0, maxChars));
                            $inputElement.scrollTop(currScrollTopPos);
                        }
                        charsLeft = maxChars - numChars;
                        if (charsLeft < 0) {
                            charsLeft = 0;
                        }

                        // set counter text
                        $maxLengthElement.text(charsLeft);
                    };

                // bind events to this element
                // setTimeout is needed, cut or paste fires before val is available
                $($inputElement).bind('keydown keyup keypress focus blur', countCharacters)
                    .bind('cut paste', function () {
                        setTimeout(countCharacters, 100);
                    });

                // count chars on pageload, if there are prefilled input-values
                $(document).bind('ready', countCharacters);
            },

            /**
             * Test numeric against allowed range
             *
             * @param $value int
             * @param $rangeAllowed str; (1-2, min1, max2, 10)
             * @return array
             */
            numericRangeCheck: function (value, rangeAllowed) {
                // split by dash
                var range = $.split(rangeAllowed),
                // min or max
                    minmax = parseInt(rangeAllowed.substr(3), 10);

                if( range.length === 1 && rangeAllowed.indexOf('min') === -1 && rangeAllowed.indexOf('max') === -1 ) {
                    range = [rangeAllowed, rangeAllowed]; // only a number, checking agains an exact number of characters
                }

                // range ?
                if (range.length === 2 && (value < parseInt(range[0], 10) || value > parseInt(range[1], 10) )) {
                    return [ 'out', range[0], range[1] ];
                } // value is out of range
                else if (rangeAllowed.indexOf('min') === 0 && (value < minmax )) // min
                {
                    return ['min', minmax];
                } // value is below min
                else if (rangeAllowed.indexOf('max') === 0 && (value > minmax )) // max
                {
                    return ['max', minmax];
                } // value is above max
                // since no other returns executed, value is in allowed range
                return [ 'ok' ];
            },


            _numSuggestionElements: 0,
            _selectedSuggestion: null,
            _previousTypedVal: null,

            /**
             * Utility function that can be used to create plugins that gives
             * suggestions when inputs is typed into
             * @param {jQuery} $elem
             * @param {Array} suggestions
             * @param {Object} settings - Optional
             * @return {jQuery}
             */
            suggest: function ($elem, suggestions, settings) {
                var conf = {
                        css: {
                            maxHeight: '150px',
                            background: '#FFF',
                            lineHeight: '150%',
                            textDecoration: 'underline',
                            overflowX: 'hidden',
                            overflowY: 'auto',
                            border: '#CCC solid 1px',
                            borderTop: 'none',
                            cursor: 'pointer'
                        },
                        activeSuggestionCSS: {
                            background: '#E9E9E9'
                        }
                    },
                    setSuggsetionPosition = function ($suggestionContainer, $input) {
                        var offset = $input.offset();
                        $suggestionContainer.css({
                            width: $input.outerWidth(),
                            left: offset.left + 'px',
                            top: (offset.top + $input.outerHeight()) + 'px'
                        });
                    };

                if (settings) {
                    $.extend(conf, settings);
                }

                conf.css.position = 'absolute';
                conf.css['z-index'] = 9999;
                $elem.attr('autocomplete', 'off');

                if (this._numSuggestionElements === 0) {
                    // Re-position suggestion container if window size changes
                    $win.bind('resize', function () {
                        $('.jquery-form-suggestions').each(function () {
                            var $container = $(this),
                                suggestID = $container.attr('data-suggest-container');
                            setSuggsetionPosition($container, $('.suggestions-' + suggestID).eq(0));
                        });
                    });
                }

                this._numSuggestionElements++;

                var onSelectSuggestion = function ($el) {
                    var suggestionId = $el.valAttr('suggestion-nr');
                    $.formUtils._selectedSuggestion = null;
                    $.formUtils._previousTypedVal = null;
                    $('.jquery-form-suggestion-' + suggestionId).fadeOut('fast');
                };

                $elem
                    .data('suggestions', suggestions)
                    .valAttr('suggestion-nr', this._numSuggestionElements)
                    .unbind('focus.suggest')
                    .bind('focus.suggest', function () {
                        $(this).trigger('keyup');
                        $.formUtils._selectedSuggestion = null;
                    })
                    .unbind('keyup.suggest')
                    .bind('keyup.suggest', function () {
                        var $input = $(this),
                            foundSuggestions = [],
                            val = $.trim($input.val()).toLocaleLowerCase();

                        if (val === $.formUtils._previousTypedVal) {
                            return;
                        }
                        else {
                            $.formUtils._previousTypedVal = val;
                        }

                        var hasTypedSuggestion = false,
                            suggestionId = $input.valAttr('suggestion-nr'),
                            $suggestionContainer = $('.jquery-form-suggestion-' + suggestionId);

                        $suggestionContainer.scrollTop(0);

                        // Find the right suggestions
                        if (val !== '') {
                            var findPartial = val.length > 2;
                            $.each($input.data('suggestions'), function (i, suggestion) {
                                var lowerCaseVal = suggestion.toLocaleLowerCase();
                                if (lowerCaseVal === val) {
                                    foundSuggestions.push('<strong>' + suggestion + '</strong>');
                                    hasTypedSuggestion = true;
                                    return false;
                                } else if (lowerCaseVal.indexOf(val) === 0 || (findPartial && lowerCaseVal.indexOf(val) > -1)) {
                                    foundSuggestions.push(suggestion.replace(new RegExp(val, 'gi'), '<strong>$&</strong>'));
                                }
                            });
                        }

                        // Hide suggestion container
                        if (hasTypedSuggestion || (foundSuggestions.length === 0 && $suggestionContainer.length > 0)) {
                            $suggestionContainer.hide();
                        }

                        // Create suggestion container if not already exists
                        else if (foundSuggestions.length > 0 && $suggestionContainer.length === 0) {
                            $suggestionContainer = $('<div></div>').css(conf.css).appendTo('body');
                            $elem.addClass('suggestions-' + suggestionId);
                            $suggestionContainer
                                .attr('data-suggest-container', suggestionId)
                                .addClass('jquery-form-suggestions')
                                .addClass('jquery-form-suggestion-' + suggestionId);
                        }

                        // Show hidden container
                        else if (foundSuggestions.length > 0 && !$suggestionContainer.is(':visible')) {
                            $suggestionContainer.show();
                        }

                        // add suggestions
                        if (foundSuggestions.length > 0 && val.length !== foundSuggestions[0].length) {

                            // put container in place every time, just in case
                            setSuggsetionPosition($suggestionContainer, $input);

                            // Add suggestions HTML to container
                            $suggestionContainer.html('');
                            $.each(foundSuggestions, function (i, text) {
                                $('<div></div>')
                                    .append(text)
                                    .css({
                                        overflow: 'hidden',
                                        textOverflow: 'ellipsis',
                                        whiteSpace: 'nowrap',
                                        padding: '5px'
                                    })
                                    .addClass('form-suggest-element')
                                    .appendTo($suggestionContainer)
                                    .click(function () {
                                        $input.focus();
                                        $input.val($(this).text());
                                        $input.trigger('change');
                                        onSelectSuggestion($input);
                                    });
                            });
                        }
                    })
                    .unbind('keydown.validation')
                    .bind('keydown.validation', function (e) {
                        var code = (e.keyCode ? e.keyCode : e.which),
                            suggestionId,
                            $suggestionContainer,
                            $input = $(this);

                        if (code === 13 && $.formUtils._selectedSuggestion !== null) {
                            suggestionId = $input.valAttr('suggestion-nr');
                            $suggestionContainer = $('.jquery-form-suggestion-' + suggestionId);
                            if ($suggestionContainer.length > 0) {
                                var newText = $suggestionContainer.find('div').eq($.formUtils._selectedSuggestion).text();
                                $input.val(newText);
                                $input.trigger('change');
                                onSelectSuggestion($input);
                                e.preventDefault();
                            }
                        }
                        else {
                            suggestionId = $input.valAttr('suggestion-nr');
                            $suggestionContainer = $('.jquery-form-suggestion-' + suggestionId);
                            var $suggestions = $suggestionContainer.children();
                            if ($suggestions.length > 0 && $.inArray(code, [38, 40]) > -1) {
                                if (code === 38) { // key up
                                    if ($.formUtils._selectedSuggestion === null) {
                                        $.formUtils._selectedSuggestion = $suggestions.length - 1;
                                    }
                                    else{
                                        $.formUtils._selectedSuggestion--;
                                    }
                                    if ($.formUtils._selectedSuggestion < 0) {
                                        $.formUtils._selectedSuggestion = $suggestions.length - 1;
                                    }
                                }
                                else if (code === 40) { // key down
                                    if ($.formUtils._selectedSuggestion === null) {
                                        $.formUtils._selectedSuggestion = 0;
                                    }
                                    else {
                                        $.formUtils._selectedSuggestion++;
                                    }
                                    if ($.formUtils._selectedSuggestion > ($suggestions.length - 1)) {
                                        $.formUtils._selectedSuggestion = 0;
                                    }
                                }

                                // Scroll in suggestion window
                                var containerInnerHeight = $suggestionContainer.innerHeight(),
                                    containerScrollTop = $suggestionContainer.scrollTop(),
                                    suggestionHeight = $suggestionContainer.children().eq(0).outerHeight(),
                                    activeSuggestionPosY = suggestionHeight * ($.formUtils._selectedSuggestion);

                                if (activeSuggestionPosY < containerScrollTop || activeSuggestionPosY > (containerScrollTop + containerInnerHeight)) {
                                    $suggestionContainer.scrollTop(activeSuggestionPosY);
                                }

                                $suggestions
                                    .removeClass('active-suggestion')
                                    .css('background', 'none')
                                    .eq($.formUtils._selectedSuggestion)
                                    .addClass('active-suggestion')
                                    .css(conf.activeSuggestionCSS);

                                e.preventDefault();
                                return false;
                            }
                        }
                    })
                    .unbind('blur.suggest')
                    .bind('blur.suggest', function () {
                        onSelectSuggestion($(this));
                    });

                return $elem;
            },

            /**
             * Error dialogs
             *
             * @var {Object}
             */
            LANG: {
                errorTitle: 'Form submission failed!',
                requiredField: 'This is a required field',
                requiredFields: 'You have not answered all required fields',
                badTime: 'You have not given a correct time',
                badEmail: 'You have not given a correct e-mail address',
                badTelephone: 'You have not given a correct phone number',
                badSecurityAnswer: 'You have not given a correct answer to the security question',
                badDate: 'You have not given a correct date',
                lengthBadStart: 'The input value must be between ',
                lengthBadEnd: ' characters',
                lengthTooLongStart: 'The input value is longer than ',
                lengthTooShortStart: 'The input value is shorter than ',
                notConfirmed: 'Input values could not be confirmed',
                badDomain: 'Incorrect domain value',
                badUrl: 'The input value is not a correct URL',
                badCustomVal: 'The input value is incorrect',
                andSpaces: ' and spaces ',
                badInt: 'The input value was not a correct number',
                badSecurityNumber: 'Your social security number was incorrect',
                badUKVatAnswer: 'Incorrect UK VAT Number',
                badUKNin: 'Incorrect UK NIN',
                badUKUtr: 'Incorrect UK UTR Number',
                badStrength: 'The password isn\'t strong enough',
                badNumberOfSelectedOptionsStart: 'You have to choose at least ',
                badNumberOfSelectedOptionsEnd: ' answers',
                badAlphaNumeric: 'The input value can only contain alphanumeric characters ',
                badAlphaNumericExtra: ' and ',
                wrongFileSize: 'The file you are trying to upload is too large (max %s)',
                wrongFileType: 'Only files of type %s is allowed',
                groupCheckedRangeStart: 'Please choose between ',
                groupCheckedTooFewStart: 'Please choose at least ',
                groupCheckedTooManyStart: 'Please choose a maximum of ',
                groupCheckedEnd: ' item(s)',
                badCreditCard: 'The credit card number is not correct',
                badCVV: 'The CVV number was not correct',
                wrongFileDim : 'Incorrect image dimensions,',
                imageTooTall : 'the image can not be taller than',
                imageTooWide : 'the image can not be wider than',
                imageTooSmall : 'the image was too small',
                min : 'min',
                max : 'max',
                imageRatioNotAccepted : 'Image ratio is not be accepted',
                badBrazilTelephoneAnswer: 'The phone number entered is invalid',
                badBrazilCEPAnswer: 'The CEP entered is invalid',
                badBrazilCPFAnswer: 'The CPF entered is invalid',
                badPlPesel: 'The PESEL entered is invalid',
                badPlNip: 'The NIP entered is invalid',
                badPlRegon: 'The REGON entered is invalid',
                badreCaptcha: 'Please confirm that you are not a bot'
            }
        });

    })(jQuery, window);

    /**
     * File declaring all default validators.
     */
    (function($) {

        /*
         * Validate email
         */
        $.formUtils.addValidator({
            name: 'email',
            validatorFunction: function (email) {

                var emailParts = email.toLowerCase().split('@'),
                    localPart = emailParts[0],
                    domain = emailParts[1];

                if (localPart && domain) {

                    if( localPart.indexOf('"') === 0 ) {
                        var len = localPart.length;
                        localPart = localPart.replace(/\"/g, '');
                        if( localPart.length !== (len-2) ) {
                            return false; // It was not allowed to have more than two apostrophes
                        }
                    }

                    return $.formUtils.validators.validate_domain.validatorFunction(emailParts[1]) &&
                        localPart.indexOf('.') !== 0 &&
                        localPart.substring(localPart.length-1, localPart.length) !== '.' &&
                        localPart.indexOf('..') === -1 &&
                        !(/[^\w\+\.\-\#\-\_\~\!\$\&\'\(\)\*\+\,\;\=\:]/.test(localPart));
                }

                return false;
            },
            errorMessage: '',
            errorMessageKey: 'badEmail'
        });

        /*
         * Validate domain name
         */
        $.formUtils.addValidator({
            name: 'domain',
            validatorFunction: function (val) {
                return val.length > 0 &&
                    val.length <= 253 && // Including sub domains
                    !(/[^a-zA-Z0-9]/.test(val.slice(-2))) && !(/[^a-zA-Z0-9]/.test(val.substr(0, 1))) && !(/[^a-zA-Z0-9\.\-]/.test(val)) &&
                    val.split('..').length === 1 &&
                    val.split('.').length > 1;
            },
            errorMessage: '',
            errorMessageKey: 'badDomain'
        });

        /*
         * Validate required
         */
        $.formUtils.addValidator({
            name: 'required',
            validatorFunction: function (val, $el, config, language, $form) {
                switch ($el.attr('type')) {
                    case 'checkbox':
                        return $el.is(':checked');
                    case 'radio':
                        return $form.find('input[name="' + $el.attr('name') + '"]').filter(':checked').length > 0;
                    default:
                        return $.trim(val) !== '';
                }
            },
            errorMessage: '',
            errorMessageKey: function(config) {
                if (config.errorMessagePosition === 'top' || typeof config.errorMessagePosition === 'function') {
                    return 'requiredFields';
                }
                else {
                    return 'requiredField';
                }
            }
        });

        /*
         * Validate length range
         */
        $.formUtils.addValidator({
            name: 'length',
            validatorFunction: function (val, $el, conf, lang) {
                var lengthAllowed = $el.valAttr('length'),
                    type = $el.attr('type');

                if (lengthAllowed === undefined) {
                    alert('Please add attribute "data-validation-length" to ' + $el[0].nodeName + ' named ' + $el.attr('name'));
                    return true;
                }

                // check if length is above min, below max or within range.
                var len = type === 'file' && $el.get(0).files !== undefined ? $el.get(0).files.length : val.length,
                    lengthCheckResults = $.formUtils.numericRangeCheck(len, lengthAllowed),
                    checkResult;

                switch (lengthCheckResults[0]) {   // outside of allowed range
                    case 'out':
                        this.errorMessage = lang.lengthBadStart + lengthAllowed + lang.lengthBadEnd;
                        checkResult = false;
                        break;
                    // too short
                    case 'min':
                        this.errorMessage = lang.lengthTooShortStart + lengthCheckResults[1] + lang.lengthBadEnd;
                        checkResult = false;
                        break;
                    // too long
                    case 'max':
                        this.errorMessage = lang.lengthTooLongStart + lengthCheckResults[1] + lang.lengthBadEnd;
                        checkResult = false;
                        break;
                    // ok
                    default:
                        checkResult = true;
                }

                return checkResult;
            },
            errorMessage: '',
            errorMessageKey: ''
        });

        /*
         * Validate url
         */
        $.formUtils.addValidator({
            name: 'url',
            validatorFunction: function (url) {

                var urlFilter = /^(https?|ftp):\/\/((((\w|-|\.|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])(\w|-|\.|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])(\w|-|\.|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/(((\w|-|\.|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/((\w|-|\.|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|\[|\]|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#(((\w|-|\.|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
                if (urlFilter.test(url)) {
                    var domain = url.split('://')[1],
                        domainSlashPos = domain.indexOf('/');

                    if (domainSlashPos > -1) {
                        domain = domain.substr(0, domainSlashPos);
                    }

                    return $.formUtils.validators.validate_domain.validatorFunction(domain); // todo: add support for IP-addresses
                }
                return false;
            },
            errorMessage: '',
            errorMessageKey: 'badUrl'
        });

        /*
         * Validate number (floating or integer)
         */
        $.formUtils.addValidator({
            name: 'number',
            validatorFunction: function (val, $el, conf) {
                if (val !== '') {
                    var allowing = $el.valAttr('allowing') || '',
                        decimalSeparator = $el.valAttr('decimal-separator') || conf.decimalSeparator,
                        allowsRange = false,
                        begin, end,
                        steps = $el.valAttr('step') || '',
                        allowsSteps = false,
                        sanitize = $el.attr('data-sanitize') || '',
                        isFormattedWithNumeral = sanitize.match(/(^|[\s])numberFormat([\s]|$)/i);

                    if (isFormattedWithNumeral) {
                        if (!window.numeral) {
                            throw new ReferenceError('The data-sanitize value numberFormat cannot be used without the numeral');
                        }
                        //Unformat input first, then convert back to String
                        if (val.length) {
                            val = String(numeral().unformat(val));
                        }
                    }

                    if (allowing.indexOf('number') === -1) {
                        allowing += ',number';
                    }

                    if (allowing.indexOf('negative') === -1 && val.indexOf('-') === 0) {
                        return false;
                    }

                    if (allowing.indexOf('range') > -1) {
                        begin = parseFloat(allowing.substring(allowing.indexOf('[') + 1, allowing.indexOf(';')));
                        end = parseFloat(allowing.substring(allowing.indexOf(';') + 1, allowing.indexOf(']')));
                        allowsRange = true;
                    }

                    if (steps !== '') {
                        allowsSteps = true;
                    }

                    if (decimalSeparator === ',') {
                        if (val.indexOf('.') > -1) {
                            return false;
                        }
                        // Fix for checking range with floats using ,
                        val = val.replace(',', '.');
                    }
                    if (val.replace(/[0-9-]/g, '') === '' && (!allowsRange || (val >= begin && val <= end)) && (!allowsSteps || (val % steps === 0))) {
                        return true;
                    }

                    if (allowing.indexOf('float') > -1 && val.match(new RegExp('^([0-9-]+)\\.([0-9]+)$')) !== null && (!allowsRange || (val >= begin && val <= end)) && (!allowsSteps || (val % steps === 0))) {
                        return true;
                    }
                }
                return false;
            },
            errorMessage: '',
            errorMessageKey: 'badInt'
        });

        /*
         * Validate alpha numeric
         */
        $.formUtils.addValidator({
            name: 'alphanumeric',
            validatorFunction: function (val, $el, conf, language) {
                var patternStart = '^([a-zA-Z0-9',
                    patternEnd = ']+)$',
                    additionalChars = $el.valAttr('allowing'),
                    pattern = '';

                if (additionalChars) {
                    pattern = patternStart + additionalChars + patternEnd;
                    var extra = additionalChars.replace(/\\/g, '');
                    if (extra.indexOf(' ') > -1) {
                        extra = extra.replace(' ', '');
                        extra += language.andSpaces || $.formUtils.LANG.andSpaces;
                    }
                    this.errorMessage = language.badAlphaNumeric + language.badAlphaNumericExtra + extra;
                } else {
                    pattern = patternStart + patternEnd;
                    this.errorMessage = language.badAlphaNumeric;
                }

                return new RegExp(pattern).test(val);
            },
            errorMessage: '',
            errorMessageKey: ''
        });

        /*
         * Validate against regexp
         */
        $.formUtils.addValidator({
            name: 'custom',
            validatorFunction: function (val, $el) {
                var regexp = new RegExp($el.valAttr('regexp'));
                return regexp.test(val);
            },
            errorMessage: '',
            errorMessageKey: 'badCustomVal'
        });

        /*
         * Validate date
         */
        $.formUtils.addValidator({
            name: 'date',
            validatorFunction: function (date, $el, conf) {
                var dateFormat = $el.valAttr('format') || conf.dateFormat || 'yyyy-mm-dd',
                    addMissingLeadingZeros = $el.valAttr('require-leading-zero') === 'false';
                return $.formUtils.parseDate(date, dateFormat, addMissingLeadingZeros) !== false;
            },
            errorMessage: '',
            errorMessageKey: 'badDate'
        });


        /*
         * Validate group of checkboxes, validate qty required is checked
         * written by Steve Wasiura : http://stevewasiura.waztech.com
         * element attrs
         *    data-validation="checkbox_group"
         *    data-validation-qty="1-2"  // min 1 max 2
         *    data-validation-error-msg="chose min 1, max of 2 checkboxes"
         */
        $.formUtils.addValidator({
            name: 'checkbox_group',
            validatorFunction: function (val, $el, conf, lang, $form) {
                // preset return var
                var isValid = true,
                // get name of element. since it is a checkbox group, all checkboxes will have same name
                    elname = $el.attr('name'),
                // get checkboxes and count the checked ones
                    $checkBoxes = $('input[type=checkbox][name^="' + elname + '"]', $form),
                    checkedCount = $checkBoxes.filter(':checked').length,
                // get el attr that specs qty required / allowed
                    qtyAllowed = $el.valAttr('qty');

                if (qtyAllowed === undefined) {
                    var elementType = $el.get(0).nodeName;
                    alert('Attribute "data-validation-qty" is missing from ' + elementType + ' named ' + $el.attr('name'));
                }

                // call Utility function to check if count is above min, below max, within range etc.
                var qtyCheckResults = $.formUtils.numericRangeCheck(checkedCount, qtyAllowed);

                // results will be array, [0]=result str, [1]=qty int
                switch (qtyCheckResults[0]) {
                    // outside allowed range
                    case 'out':
                        this.errorMessage = lang.groupCheckedRangeStart + qtyAllowed + lang.groupCheckedEnd;
                        isValid = false;
                        break;
                    // below min qty
                    case 'min':
                        this.errorMessage = lang.groupCheckedTooFewStart + qtyCheckResults[1] + lang.groupCheckedEnd;
                        isValid = false;
                        break;
                    // above max qty
                    case 'max':
                        this.errorMessage = lang.groupCheckedTooManyStart + qtyCheckResults[1] + lang.groupCheckedEnd;
                        isValid = false;
                        break;
                    // ok
                    default:
                        isValid = true;
                }

                if( !isValid ) {
                    var _triggerOnBlur = function() {
                        $checkBoxes.unbind('click', _triggerOnBlur);
                        $checkBoxes.filter('*[data-validation]').validateInputOnBlur(lang, conf, false, 'blur');
                    };
                    $checkBoxes.bind('click', _triggerOnBlur);
                }

                return isValid;
            }
            //   errorMessage : '', // set above in switch statement
            //   errorMessageKey: '' // not used
        });

    })(jQuery);


}));

!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){!function(a){a.formUtils.addValidator({name:"time",validatorFunction:function(a){if(null===a.match(/^(\d{2}):(\d{2})$/))return!1;var b=parseInt(a.split(":")[0],10),c=parseInt(a.split(":")[1],10);return!(b>23||c>59)},errorMessage:"",errorMessageKey:"badTime"}),a.formUtils.addValidator({name:"birthdate",validatorFunction:function(b,c,d){var e="yyyy-mm-dd";c.valAttr("format")?e=c.valAttr("format"):"undefined"!=typeof d.dateFormat&&(e=d.dateFormat);var f=a.formUtils.parseDate(b,e);if(!f)return!1;var g=new Date,h=g.getFullYear(),i=f[0],j=f[1],k=f[2];if(i===h){var l=g.getMonth()+1;if(j===l){var m=g.getDate();return m>=k}return l>j}return h>i&&i>h-124},errorMessage:"",errorMessageKey:"badDate"})}(a)});

!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){!function(a,b){"use strict";a.formUtils.addValidator({name:"spamcheck",validatorFunction:function(a,b){var c=b.valAttr("captcha");return c===a},errorMessage:"",errorMessageKey:"badSecurityAnswer"}),a.formUtils.addValidator({name:"confirmation",validatorFunction:function(b,c,d,e,f){var g,h=c.valAttr("confirm")||c.attr("name")+"_confirmation",i=f.find('[name="'+h+'"]');if(!i.length)return a.formUtils.warn('Password confirmation validator: could not find an input with name "'+h+'"'),!1;if(g=i.val(),d.validateOnBlur&&!i[0].hasValidationCallback){i[0].hasValidationCallback=!0;var j=function(){c.validate()};i.on("keyup",j),f.one("formValidationSetup",function(){i[0].hasValidationCallback=!1,i.off("keyup",j)})}return b===g},errorMessage:"",errorMessageKey:"notConfirmed"});var c={amex:[15,15],diners_club:[14,14],cjb:[16,16],laser:[16,19],visa:[16,16],mastercard:[16,16],maestro:[12,19],discover:[16,16]},d=!1,e=!1;a.formUtils.addValidator({name:"creditcard",validatorFunction:function(b,f){var g=a.split(f.valAttr("allowing")||"");if(e=a.inArray("amex",g)>-1,d=e&&1===g.length,g.length>0){var h=!1;if(a.each(g,function(d,e){if(e in c){if(b.length>=c[e][0]&&b.length<=c[e][1])return h=!0,!1}else a.formUtils.warn('Use of unknown credit card "'+e+'"')}),!h)return!1}if(""!==b.replace(new RegExp("[0-9]","g"),""))return!1;var i=0;return a.each(b.split("").reverse(),function(a,b){b=parseInt(b,10),a%2===0?i+=b:(b*=2,i+=10>b?b:b-9)}),i%10===0},errorMessage:"",errorMessageKey:"badCreditCard"}),a.formUtils.addValidator({name:"cvv",validatorFunction:function(a){return""===a.replace(/[0-9]/g,"")?(a+="",d?4===a.length:e?3===a.length||4===a.length:3===a.length):!1},errorMessage:"",errorMessageKey:"badCVV"}),a.formUtils.addValidator({name:"strength",validatorFunction:function(b,c){var d=c.valAttr("strength")||2;return d&&d>3&&(d=3),a.formUtils.validators.validate_strength.calculatePasswordStrength(b)>=d},errorMessage:"",errorMessageKey:"badStrength",calculatePasswordStrength:function(a){if(a.length<4)return 0;var b=0,c=function(a,b){for(var c="",d=0;d<b.length;d++){for(var e=!0,f=0;a>f&&f+d+a<b.length;f++)e=e&&b.charAt(f+d)===b.charAt(f+d+a);a>f&&(e=!1),e?(d+=a-1,e=!1):c+=b.charAt(d)}return c};return b+=4*a.length,b+=1*(c(1,a).length-a.length),b+=1*(c(2,a).length-a.length),b+=1*(c(3,a).length-a.length),b+=1*(c(4,a).length-a.length),a.match(/(.*[0-9].*[0-9].*[0-9])/)&&(b+=5),a.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)&&(b+=5),a.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)&&(b+=10),a.match(/([a-zA-Z])/)&&a.match(/([0-9])/)&&(b+=15),a.match(/([!,@,#,$,%,^,&,*,?,_,~])/)&&a.match(/([0-9])/)&&(b+=15),a.match(/([!,@,#,$,%,^,&,*,?,_,~])/)&&a.match(/([a-zA-Z])/)&&(b+=15),(a.match(/^\w+$/)||a.match(/^\d+$/))&&(b-=10),0>b&&(b=0),b>100&&(b=100),20>b?0:40>b?1:60>=b?2:3},strengthDisplay:function(b,c){var d={fontSize:"12pt",padding:"4px",bad:"Very bad",weak:"Weak",good:"Good",strong:"Strong"};c&&a.extend(d,c),b.bind("keyup",function(){var b=a(this).val(),c="undefined"==typeof d.parent?a(this).parent():a(d.parent),e=c.find(".strength-meter"),f=a.formUtils.validators.validate_strength.calculatePasswordStrength(b),g={background:"pink",color:"#FF0000",fontWeight:"bold",border:"red solid 1px",borderWidth:"0px 0px 4px",display:"inline-block",fontSize:d.fontSize,padding:d.padding},h=d.bad;0===e.length&&(e=a("<span></span>"),e.addClass("strength-meter").appendTo(c)),b?e.show():e.hide(),1===f?h=d.weak:2===f?(g.background="lightyellow",g.borderColor="yellow",g.color="goldenrod",h=d.good):f>=3&&(g.background="lightgreen",g.borderColor="darkgreen",g.color="darkgreen",h=d.strong),e.css(g).text(h)})}});var f=function(b,c,d,e,f){var g=c.valAttr("req-params")||c.data("validation-req-params")||{},h=function(b,d){b.valid?c.valAttr("backend-valid","true"):(c.valAttr("backend-invalid","true"),b.message&&c.attr(e.validationErrorMsgAttribute,b.message)),c.valAttr("has-keyup-event")||c.valAttr("has-keyup-event","1").bind("keyup change",function(b){9!==b.keyCode&&16!==b.keyCode&&a(this).valAttr("backend-valid",!1).valAttr("backend-invalid",!1)}),d()};g||(g={}),"string"==typeof g&&(g=a.parseJSON(g)),g[c.valAttr("param-name")||c.attr("name")]=d,a.ajax({url:b,type:"POST",cache:!1,data:g,dataType:"json",error:function(a){return h({valid:!1,message:"Connection failed with status: "+a.statusText},f),!1},success:function(a){h(a,f)}})},g=0,h=function(){return!1};a.formUtils.addValidator({name:"server",validatorFunction:function(b,c,d,e,i){var j=c.valAttr("backend-valid"),k=c.valAttr("backend-invalid"),l=document.location.href;return c.valAttr("url")?l=c.valAttr("url"):"serverURL"in d&&(l=d.backendUrl),j?!0:k?!1:"keyup"!==a.formUtils.eventType||a.formUtils.isValidatingEntireForm?a.formUtils.isValidatingEntireForm?(i.bind("submit",h).addClass("validating-server-side").addClass("on-blur"),c.addClass("validating-server-side"),a.formUtils.haltValidation=!0,g++,f(l,c,b,d,function(){g--,i.removeClass("validating-server-side").removeClass("on-blur").get(0).onsubmit=function(){},i.unbind("submit",h),c.removeClass("validating-server-side"),c.valAttr("value-length",b.length),0===g&&(a.formUtils.haltValidation=!1,i.trigger("submit"))}),null):(i.addClass("validating-server-side"),c.addClass("validating-server-side"),f(l,c,b,d,function(){i.removeClass("validating-server-side"),c.removeClass("validating-server-side"),c.trigger("blur")}),null):null},errorMessage:"",errorMessageKey:"badBackend"}),a.formUtils.addValidator({name:"letternumeric",validatorFunction:function(b,c,d,e){var f="^([a-zA-Z0-9ªµºÀ-ÖØ-öø-ˁˆ-ˑˠ-ˤˬˮͰ-ʹͶͷͺ-ͽΆΈ-ΊΌΎ-ΡΣ-ϵϷ-ҁҊ-ԧԱ-Ֆՙա-ևא-תװ-ײؠ-يٮٯٱ-ۓەۥۦۮۯۺ-ۼۿܐܒ-ܯݍ-ޥޱߊ-ߪߴߵߺࠀ-ࠕࠚࠤࠨࡀ-ࡘࢠࢢ-ࢬऄ-हऽॐक़-ॡॱ-ॷॹ-ॿঅ-ঌএঐও-নপ-রলশ-হঽৎড়ঢ়য়-ৡৰৱਅ-ਊਏਐਓ-ਨਪ-ਰਲਲ਼ਵਸ਼ਸਹਖ਼-ੜਫ਼ੲ-ੴઅ-ઍએ-ઑઓ-નપ-રલળવ-હઽૐૠૡଅ-ଌଏଐଓ-ନପ-ରଲଳଵ-ହଽଡ଼ଢ଼ୟ-ୡୱஃஅ-ஊஎ-ஐஒ-கஙசஜஞடணதந-பம-ஹௐఅ-ఌఎ-ఐఒ-నప-ళవ-హఽౘౙౠౡಅ-ಌಎ-ಐಒ-ನಪ-ಳವ-ಹಽೞೠೡೱೲഅ-ഌഎ-ഐഒ-ഺഽൎൠൡൺ-ൿඅ-ඖක-නඳ-රලව-ෆก-ะาำเ-ๆກຂຄງຈຊຍດ-ທນ-ຟມ-ຣລວສຫອ-ະາຳຽເ-ໄໆໜ-ໟༀཀ-ཇཉ-ཬྈ-ྌက-ဪဿၐ-ၕၚ-ၝၡၥၦၮ-ၰၵ-ႁႎႠ-ჅჇჍა-ჺჼ-ቈቊ-ቍቐ-ቖቘቚ-ቝበ-ኈኊ-ኍነ-ኰኲ-ኵኸ-ኾዀዂ-ዅወ-ዖዘ-ጐጒ-ጕጘ-ፚᎀ-ᎏᎠ-Ᏼᐁ-ᙬᙯ-ᙿᚁ-ᚚᚠ-ᛪᜀ-ᜌᜎ-ᜑᜠ-ᜱᝀ-ᝑᝠ-ᝬᝮ-ᝰក-ឳៗៜᠠ-ᡷᢀ-ᢨᢪᢰ-ᣵᤀ-ᤜᥐ-ᥭᥰ-ᥴᦀ-ᦫᧁ-ᧇᨀ-ᨖᨠ-ᩔᪧᬅ-ᬳᭅ-ᭋᮃ-ᮠᮮᮯᮺ-ᯥᰀ-ᰣᱍ-ᱏᱚ-ᱽᳩ-ᳬᳮ-ᳱᳵᳶᴀ-ᶿḀ-ἕἘ-Ἕἠ-ὅὈ-Ὅὐ-ὗὙὛὝὟ-ώᾀ-ᾴᾶ-ᾼιῂ-ῄῆ-ῌῐ-ΐῖ-Ίῠ-Ῥῲ-ῴῶ-ῼⁱⁿₐ-ₜℂℇℊ-ℓℕℙ-ℝℤΩℨK-ℭℯ-ℹℼ-ℿⅅ-ⅉⅎↃↄⰀ-Ⱞⰰ-ⱞⱠ-ⳤⳫ-ⳮⳲⳳⴀ-ⴥⴧⴭⴰ-ⵧⵯⶀ-ⶖⶠ-ⶦⶨ-ⶮⶰ-ⶶⶸ-ⶾⷀ-ⷆⷈ-ⷎⷐ-ⷖⷘ-ⷞⸯ々〆〱-〵〻〼ぁ-ゖゝ-ゟァ-ヺー-ヿㄅ-ㄭㄱ-ㆎㆠ-ㆺㇰ-ㇿ㐀-䶵一-鿌ꀀ-ꒌꓐ-ꓽꔀ-ꘌꘐ-ꘟꘪꘫꙀ-ꙮꙿ-ꚗꚠ-ꛥꜗ-ꜟꜢ-ꞈꞋ-ꞎꞐ-ꞓꞠ-Ɦꟸ-ꠁꠃ-ꠅꠇ-ꠊꠌ-ꠢꡀ-ꡳꢂ-ꢳꣲ-ꣷꣻꤊ-ꤥꤰ-ꥆꥠ-ꥼꦄ-ꦲꧏꨀ-ꨨꩀ-ꩂꩄ-ꩋꩠ-ꩶꩺꪀ-ꪯꪱꪵꪶꪹ-ꪽꫀꫂꫛ-ꫝꫠ-ꫪꫲ-ꫴꬁ-ꬆꬉ-ꬎꬑ-ꬖꬠ-ꬦꬨ-ꬮꯀ-ꯢ가-힣ힰ-ퟆퟋ-ퟻ豈-舘並-龎ﬀ-ﬆﬓ-ﬗיִײַ-ﬨשׁ-זּטּ-לּמּנּסּףּפּצּ-ﮱﯓ-ﴽﵐ-ﶏﶒ-ﷇﷰ-ﷻﹰ-ﹴﹶ-ﻼＡ-Ｚａ-ｚｦ-ﾾￂ-ￇￊ-ￏￒ-ￗￚ-ￜ",g="]+)$",h=c.valAttr("allowing"),i="";if(h){i=f+h+g;var j=h.replace(/\\/g,"");j.indexOf(" ")>-1&&(j=j.replace(" ",""),j+=e.andSpaces||a.formUtils.LANG.andSpaces),this.errorMessage=e.badAlphaNumeric+e.badAlphaNumericExtra+j}else i=f+g,this.errorMessage=e.badAlphaNumeric;return new RegExp(i).test(b)},errorMessage:"",errorMessageKey:"requiredFields"}),a.formUtils.addValidator({name:"recaptcha",validatorFunction:function(a,b){return""!==grecaptcha.getResponse(b.valAttr("recaptcha-widget-id"))},errorMessage:"",errorMessageKey:"badreCaptcha"}),a.fn.displayPasswordStrength=function(b){return new a.formUtils.validators.validate_strength.strengthDisplay(this,b),this};var i=function(b,c,d){if(c||(c=a("form")),"undefined"!=typeof grecaptcha&&!a.formUtils.hasLoadedGrecaptcha)throw new Error("reCaptcha API can not be loaded by hand, delete reCaptcha API snippet.");if(!a.formUtils.hasLoadedGrecaptcha&&a('[data-validation~="recaptcha"]',c).length){a.formUtils.hasLoadedGrecaptcha=!0;var e="//www.google.com/recaptcha/api.js?onload=reCaptchaLoaded&render=explicit"+(d.lang?"&hl="+d.lang:""),f=document.createElement("script");f.type="text/javascript",f.async=!0,f.defer=!0,f.src=e,document.getElementsByTagName("body")[0].appendChild(f)}};b.reCaptchaLoaded=function(b){b&&"object"==typeof b&&b.length||(b=a("form")),b.each(function(){var b=a(this),c=b.context.validationConfig;a('[data-validation~="recaptcha"]',b).each(function(){var b=a(this),d=document.createElement("DIV"),e=c.reCaptchaSiteKey||b.valAttr("recaptcha-sitekey"),f=c.reCaptchaTheme||b.valAttr("recaptcha-theme")||"light";if(!e)throw new Error("Google reCaptcha site key is required.");var g=function(b){a("form").each(function(){a('[data-validation~="recaptcha"]',a(this)).each(function(){a(this).trigger("validation",b&&""!==b)})})},h=grecaptcha.render(d,{sitekey:e,theme:f,callback:g,"expired-callback":g});b.valAttr("recaptcha-widget-id",h).hide().on("beforeValidation",function(a){a.stopImmediatePropagation()}).parent().append(d)})})},a(b).on("validatorsLoaded formValidationSetup",i)}(a,window)});

!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){!function(a){"use strict";var b=[["AD",24,"F04F04A12"],["AE",23,"F03F16"],["AL",28,"F08A16"],["AT",20,"F05F11"],["AZ",28,"U04A20"],["BA",20,"F03F03F08F02"],["BE",16,"F03F07F02"],["BG",22,"U04F04F02A08"],["BH",22,"U04A14"],["BR",29,"F08F05F10U01A01"],["CH",21,"F05A12"],["CR",21,"F03F14"],["CY",28,"F03F05A16"],["CZ",24,"F04F06F10"],["DE",22,"F08F10"],["DK",18,"F04F09F01"],["DO",28,"U04F20"],["EE",20,"F02F02F11F01"],["ES",24,"F04F04F01F01F10"],["FI",18,"F06F07F01"],["FO",18,"F04F09F01"],["FR",27,"F05F05A11F02"],["GB",22,"U04F06F08"],["GE",22,"U02F16"],["GI",23,"U04A15"],["GL",18,"F04F09F01"],["GR",27,"F03F04A16"],["GT",28,"A04A20"],["HR",21,"F07F10"],["HU",28,"F03F04F01F15F01"],["IE",22,"U04F06F08"],["IL",23,"F03F03F13"],["IS",26,"F04F02F06F10"],["IT",27,"U01F05F05A12"],["KW",30,"U04A22"],["KZ",20,"F03A13"],["LB",28,"F04A20"],["LC",32,"U04F24"],["LI",21,"F05A12"],["LT",20,"F05F11"],["LU",20,"F03A13"],["LV",21,"U04A13"],["MC",27,"F05F05A11F02"],["MD",24,"U02A18"],["ME",22,"F03F13F02"],["MK",19,"F03A10F02"],["MR",27,"F05F05F11F02"],["MT",31,"U04F05A18"],["MU",30,"U04F02F02F12F03U03"],["NL",18,"U04F10"],["NO",15,"F04F06F01"],["PK",24,"U04A16"],["PL",28,"F08F16"],["PS",29,"U04A21"],["PT",25,"F04F04F11F02"],["RO",24,"U04A16"],["RS",22,"F03F13F02"],["SA",24,"F02A18"],["SE",24,"F03F16F01"],["SI",19,"F05F08F02"],["SK",24,"F04F06F10"],["SM",27,"U01F05F05A12"],["ST",25,"F08F11F02"],["TL",23,"F03F14F02"],["TN",24,"F02F03F13F02"],["TR",26,"F05F01A16"],["VG",24,"U04F16"],["XK",20,"F04F10F02"],["AO",25,"F21"],["BF",27,"F23"],["BI",16,"F12"],["BJ",28,"F24"],["CI",28,"U01F23"],["CM",27,"F23"],["CV",25,"F21"],["DZ",24,"F20"],["IR",26,"F22"],["JO",30,"A04F22"],["MG",27,"F23"],["ML",28,"U01F23"],["MZ",25,"F21"],["QA",29,"U04A21"],["SN",28,"U01F23"],["UA",29,"F25"]],c=function(a){a=a.toUpperCase(),a=a.replace(/\s+/g,""),a=a.substr(4)+a.substr(0,4),a=a.split("").map(function(a){var b=a.charCodeAt(0);return b>="A".charCodeAt(0)&&b<="Z".charCodeAt(0)?b-"A".charCodeAt(0)+10:a}).join("");for(var b,c=a;c.length>2;)b=c.slice(0,9),c=(parseInt(b,10)%97).toString()+c.slice(b.length);return parseInt(c,10)%97===1},d=function(a){a=a.toUpperCase(),a=a.replace(/\s+/g,"");var c=a.slice(0,2),d=0,e="";for(d=0;d<b.length;++d)if(b[d][0]===c){e=b[d][2];break}if(""===e)return!1;var f=e.match(/(.{3})/g).map(function(a){var b,c=a.slice(0,1),d=parseInt(a.slice(1),10);switch(c){case"A":b="0-9A-Za-z";break;case"B":b="0-9A-Z";break;case"C":b="A-Za-z";break;case"F":b="0-9";break;case"L":b="a-z";break;case"U":b="A-Z";break;case"W":b="0-9a-z"}return"(["+b+"]{"+d+"})"}),g=new RegExp("^"+f.join("")+"$");return a.length===b[d][1]&&g.test(a.slice(4))};a.formUtils.addValidator({name:"sepa",validatorFunction:c,errorMessage:"",errorMessageKey:"badSepa"}),a.formUtils.addValidator({name:"iban",validatorFunction:function(a){return d(a)&&c(a)},errorMessage:"",errorMessageKey:"badIban"}),a.formUtils.addValidator({name:"bic",validatorFunction:function(a){var b=new RegExp("^[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}$");return b.test(a)},errorMessage:"",errorMessageKey:"badBic"})}(a)});

!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){!function(a,b){"use strict";var c='[type="button"], [type="submit"], [type="radio"], [type="checkbox"], [type="reset"], [type="search"]',d={upper:function(a){return a.toLocaleUpperCase()},lower:function(a){return a.toLocaleLowerCase()},trim:function(b){return a.trim(b)},trimLeft:function(a){return a.replace(/^\s+/,"")},trimRight:function(a){return a.replace(/\s+$/,"")},capitalize:function(b){var c=b.split(" ");return a.each(c,function(a,b){c[a]=b.substr(0,1).toUpperCase()+b.substr(1,b.length)}),c.join(" ")},insert:function(a,b,c){var d=(b.attr("data-sanitize-insert-"+c)||"").replace(/\[SPACE\]/g," ");return"left"===c&&0===a.indexOf(d)||"right"===c&&a.substring(a.length-d.length)===d?a:("left"===c?d:"")+a+("right"===c?d:"")},insertRight:function(a,b){return this.insert(a,b,"right")},insertLeft:function(a,b){return this.insert(a,b,"left")},numberFormat:function(a,c){if(0===a.length)return a;if(!("numeral"in b))throw new ReferenceError('Using sanitation function "numberFormat" requires that you include numeral.js (http://numeraljs.com/)');return a=numeral().unformat(a),a=numeral(a).format(c.attr("data-sanitize-number-format"))},strip:function(b,c){var d=c.attr("data-sanitize-strip")||"";return a.split(d,function(a){b=b.replace(new RegExp("\\"+a,"g"),"")}),b},escape:function(b,c){var d=c.valAttr("is-escaped"),e={"<":"__%AMP%__lt;",">":"__%AMP%__gt;","&":"__%AMP%__amp;","'":"__%AMP%__#8217;",'"':"__%AMP%__quot;"};return"yes"===d?b:(c.valAttr("is-escaped","yes"),c.one("keyup",function(a){9!==a.keyCode&&c.valAttr("is-escaped","no")}),a.each(e,function(a,c){b=b.replace(new RegExp(a,"g"),c)}),b.replace(new RegExp("__%AMP%__","g"),"&"))}},e=function(b,e,f){e||(e=a("form")),e.each||(e=a(e));var g=function(){var b=a(this),c=b.val();a.split(b.attr("data-sanitize"),function(a){if(!(a in d))throw new Error('Use of unknown sanitize command "'+a+'"');c=d[a](c,b,f)}),b.val(c).trigger("keyup.validation")};e.each(function(){var b=a(this);f.sanitizeAll&&b.find("input,textarea").not(c).each(function(){var b=a(this),c=b.attr("data-sanitize")||"";b.attr("data-sanitize",f.sanitizeAll+" "+c)}),b.find("[data-sanitize]").unbind("blur.sanitation",g).bind("blur.sanitation",g),a(function(){b.trigger("blur.sanitation")})})};a(b).on("validatorsLoaded formValidationSetup",e),a.formUtils.setupSanitation=e}(a,window)});

!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){!function(a){"use strict";var b="placeholder"in document.createElement("INPUT"),c="options"in document.createElement("DATALIST"),d=!1,e=function(e){e.each(function(){var e=a(this),f=e.find("input,textarea,select"),g=!1;f.each(function(){var b=[],e=a(this),f=e.attr("required"),h={};switch(f&&b.push("required"),(e.attr("type")||"").toLowerCase()){case"time":b.push("time"),a.formUtils.validators.validate_date||d||(d=!0,a.formUtils.loadModules("date"));break;case"url":b.push("url");break;case"email":b.push("email");break;case"date":b.push("date");break;case"number":b.push("number");var i=e.attr("max"),j=e.attr("min");(j||i)&&(j||(j="0"),i||(i="9007199254740992"),h["data-validation-allowing"]="range["+j+";"+i+"]",0!==j.indexOf("-")&&0!==i.indexOf("-")||(h["data-validation-allowing"]+=",negative"),(j.indexOf(".")>-1||i.indexOf(".")>-1)&&(h["data-validation-allowing"]+=",float"))}if(e.attr("pattern")&&(b.push("custom"),h["data-validation-regexp"]=e.attr("pattern")),e.attr("maxlength")&&(b.push("length"),h["data-validation-length"]="max"+e.attr("maxlength")),!c&&e.attr("list")){var k=[],l=a("#"+e.attr("list"));if(l.find("option").each(function(){k.push(a(this).text())}),0===k.length){var m=a.trim(a("#"+e.attr("list")).text()).split("\n");a.each(m,function(b,c){k.push(a.trim(c))})}l.remove(),a.formUtils.suggest(e,k)}if(b.length){f||(h["data-validation-optional"]="true"),g=!0;var n=(e.attr("data-validation")||"")+" "+b.join(" ");e.attr("data-validation",a.trim(n)),a.each(h,function(a,b){e.attr(a,b)})}}),g&&e.trigger("html5ValidationAttrsFound"),b||f.filter("input[placeholder]").each(function(){this.defaultValue=this.getAttribute("placeholder"),a(this).bind("focus",function(){this.value===this.defaultValue&&(this.value="",a(this).removeClass("showing-placeholder"))}).bind("blur",function(){""===a.trim(this.value)&&(this.value=this.defaultValue,a(this).addClass("showing-placeholder"))})})})};a.formUtils.$win.bind("validatorsLoaded formValidationSetup",function(b,c){c||(c=a("form")),e(c)}),a.formUtils.setupValidationUsingHTML5Attr=e}(a,window)});