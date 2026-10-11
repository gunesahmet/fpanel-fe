<style>
    #php-settings-form h3 {
        border-bottom: 3px solid #ccc;
        background: #ddd;
        padding: 5px;
    }
    .popover{
        max-width:600px;
    }
    code{
        letter-spacing:-3px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <form id="php-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">

            <div class="col-md-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="float-right">
                                <button class="btn btn-sm btn-outline btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
            <div class="col-md-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Logs & Extensions');?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Display Errors');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content=" This directive controls whether or not and where PHP will output errors,
 notices and warnings too. Error output is very useful during development, but
 it could be very dangerous in production environments. Depending on the code
 which is triggering the error, sensitive information could potentially leak
 out of your application such as database usernames and passwords or worse.
 For production environments, we recommend logging errors rather than
 sending them to STDOUT.<br />
 Possible Values:<br />
   Off = Do not display any errors<br />
   stderr = Display errors to STDERR (affects only CGI/CLI binaries!)<br />
   On or stdout = Display errors to STDOUT<br />
 Default Value: On<br />
 Development Value: On<br />
 Production Value: Off<br />
 <br /> <a href='http://php.net/display-errors' target='_blank'>http://php.net/display-errors</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("display_errors",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Display Startup Errors');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content=" The display of errors which occur during PHP's startup sequence are handled
 separately from display_errors. PHP's default behavior is to suppress those
 errors from clients. Turning the display of startup errors on can be useful in
 debugging configuration problems. We strongly recommend you
 set this to 'off' for production servers.<br />
 Default Value: Off<br />
 Development Value: On<br />
 Production Value: Off <br /> <a href='http://php.net/display-startup-errors' target='_blank'>http://php.net/display-startup-errors</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("display_startup_errors",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Error Reporting');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content=" This directive informs PHP of which errors, warnings and notices you would like
 it to take action for. The recommended way of setting values for this
 directive is through the use of the error level constants and bitwise
 operators. The error level constants are below here for convenience as well as
 some common settings and their meanings.
 By default, PHP is set to take action on all errors, notices and warnings EXCEPT
 those related to E_NOTICE and E_STRICT, which together cover best practices and
 recommended coding standards in PHP. For performance reasons, this is the
 recommend error reporting setting. Your production server shouldn't be wasting
 resources complaining about best practices and coding standards. That's what
 development servers and development settings are for.
 Note: The php.ini-development file has this setting as E_ALL. This
 means it pretty much reports everything which is exactly what you want during
 development and early testing.<br />
<br />
 Error Level Constants:<br />
 E_ALL             - All errors and warnings (includes E_STRICT as of PHP 5.4.0)<br />
 E_ERROR           - fatal run-time errors<br />
 E_RECOVERABLE_ERROR  - almost fatal run-time errors<br />
 E_WARNING         - run-time warnings (non-fatal errors)<br />
 E_PARSE           - compile-time parse errors<br />
 E_NOTICE          - run-time notices (these are warnings which often result<br />
                     from a bug in your code, but it's possible that it was<br />
                     intentional (e.g., using an uninitialized variable and<br />
                     relying on the fact it is automatically initialized to an<br />
                     empty string)<br />
 E_STRICT          - run-time notices, enable to have PHP suggest changes<br />
                     to your code which will ensure the best interoperability<br />
                     and forward compatibility of your code<br />
 E_CORE_ERROR      - fatal errors that occur during PHP's initial startup<br />
 E_CORE_WARNING    - warnings (non-fatal errors) that occur during PHP's<br />
                     initial startup<br />
 E_COMPILE_ERROR   - fatal compile-time errors<br />
 E_COMPILE_WARNING - compile-time warnings (non-fatal errors)<br />
 E_USER_ERROR      - user-generated error message<br />
 E_USER_WARNING    - user-generated warning message<br />
 E_USER_NOTICE     - user-generated notice message<br />
 E_DEPRECATED      - warn about code that will not work in future versions<br />
                     of PHP<br />
 E_USER_DEPRECATED - user-generated deprecation warnings<br />
<br />
 Common Values:<br />
   E_ALL (Show all errors, warnings and notices including coding standards.)<br />
   E_ALL & ~E_NOTICE  (Show all errors, except for notices)<br />
   E_ALL & ~E_NOTICE & ~E_STRICT  (Show all errors, except for notices and coding standards warnings.)<br />
   E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR  (Show only errors)<br />
 Default Value: E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED<br />
 Development Value: E_ALL<br />
 Production Value: E_ALL & ~E_DEPRECATED & ~E_STRICT
 <br /> <a href='http://php.net/error-reporting' target='_blank'>http://php.net/error-reporting</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("error_reporting",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Log Errors');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content=" Besides displaying errors, PHP can also log errors to locations such as a<br />
 server-specific log, STDERR, or a location specified by the error_log<br />
 directive found below. While errors should not be displayed on productions<br />
 servers they should still be monitored and logging is a great way to do that.<br />
 Default Value: Off<br />
 Development Value: On<br />
 Production Value: On<br />
<a href='http://php.net/log-errors' target='_blank'>http://php.net/log-errors</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("log_errors",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Log Errors Max Length');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Set maximum length of log_errors. In error_log information about the source is<br />
added. The default is 1024 and 0 allows to not apply any maximum length at all.<br />
                               <br /> <a href='http://php.net/log-errors-max-len' target='_blank'>http://php.net/log-errors-max-len</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("log_errors_max_len",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Ignore Repeated Errors');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Do not log repeated messages. Repeated errors must occur in same file on same<br />
                               line unless ignore_repeated_source is set true.<br />
                                <a href='http://php.net/ignore-repeated-errors' target='_blank'>http://php.net/ignore-repeated-errors</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("ignore_repeated_errors",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('HTML Errors');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content=" When PHP displays or logs an error, it has the capability of formatting the<br />
 error message as HTML for easier reading. This directive controls whether<br />
 the error message is formatted as HTML or not.<br />
 Note: This directive is hardcoded to Off for the CLI SAPI<br />
 <a href='http://php.net/html-errors' target='_blank'>http://php.net/html-errors</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("html_errors",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Error Log');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content=" Log errors to specified file. PHP's default behavior is to leave this value empty. <br />
 Example: <br />
error_log = php_errors.log <br />
<a href='http://php.net/error-log ' target='_blank'>http://php.net/error-log </a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("error_log",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Extensions');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content=" If you wish to have an extension loaded automatically, use the following<br />
 syntax:<br />
   extension=modulename<br />
 For example:<br />
   extension=mysqli<br />
 When the extension library to load is not located in the default extension<br />
 directory, You may specify an absolute path to the library file:<br />
   extension=/path/to/extension/mysqli.so<br />
 Note : The syntax used in previous PHP versions ('extension=<ext>.so' and<br />
 'extension='php_<ext>.dll') is supported for legacy reasons and may be<br />
 deprecated in a future PHP major version. So, when it is possible, please<br />
 move to the new ('extension=<ext>) syntax.<br />
 Notes for Windows environments :<br />
 - Many DLL files are located in the extensions/ (PHP 4) or ext/ (PHP 5+)<br />
   extension folders as well as the separate PECL DLL download (PHP 5+).<br />
   Be sure to appropriately set the extension_dir directive.<br />" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("extensions",array('class' => 'select-multiple')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Performance & Miscellaneous');?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Short Open Tag');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="This directive determines whether or not PHP will recognize code between<br />
<code>&lt; ?</code>  and <code>? ></code> tags as PHP source which should be processed as such. It is<br />
generally recommended that <code>&lt; ? p h p</code> and <code>? ></code> should be used and that this feature<br />
should be disabled, as enabling it may result in issues when generating XML<br />
documents, however this remains supported for backward compatibility reasons.<br />
Note that this directive does not control the <code>&lt; ? =</code> shorthand tag, which can be<br />
used regardless of this directive.<br />
Default Value: On<br />
Development Value: Off<br />
Production Value: Off<br />
<a href='http://php.net/short-open-tag' target='_blank'>http://php.net/short-open-tag</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("short_open_tag",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Output Buffering');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Output buffering is a mechanism for controlling how much output data <br />
(excluding headers and cookies) PHP should keep internally before pushing that <br />
data to the client. If your application's output exceeds this setting, PHP <br />
will send that data in chunks of roughly the size you specify. <br />
Turning on this setting and managing its maximum buffer size can yield some <br />
interesting side-effects depending on your application and web server. <br />
You may be able to send headers and cookies after you've already sent output <br />
through print or echo. You also may see performance benefits if your server is <br />
emitting less packets due to buffered output versus PHP streaming the output <br />
as it gets it. On production servers, 4096 bytes is a good setting for performance <br />
reasons. <br />
Note: Output buffering can also be controlled via Output Buffering Control <br />
  functions. <br />
Possible Values: <br />
  On = Enabled and buffer is unlimited. (Use with caution) <br />
  Off = Disabled <br />
  Integer = Enables the buffer and sets its maximum size in bytes. <br />
Note: This directive is hardcoded to Off for the CLI SAPI <br />
Default Value: Off <br />
Development Value: 4096 <br />
Production Value: 4096 <br />
                               <a href='http://php.net/output-buffering' target='_blank'>http://php.net/output-buffering</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("output_buffering",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Precision');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="The number of significant digits displayed in floating point numbers.
                               <br /> <a href='http://php.net/precision' target='_blank'>http://php.net/precision</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("precision",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Ignore User Abort');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="If enabled, the request will be allowed to complete even if the user aborts<br />
 the request. Consider enabling it if executing long requests, which may end up<br />
 being interrupted by the user or a browser timing out. PHP's default behavior<br />
 is to disable this feature.<br />
<a href='http://php.net/ignore-user-abort' target='_blank'>http://php.net/ignore-user-abort</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("ignore_user_abort",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Default Charset');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="PHP's default character set is set to UTF-8.<br />
                               <a href='http://php.net/default-charset' target='_blank'>http://php.net/default-charset</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("default_charset",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Date Timezone');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Defines the default timezone used by the date functions<br />
                               <a href='http://php.net/date.timezone' target='_blank'>http://php.net/date.timezone</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("date_timezone",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('BcMath Scale');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Number of decimal digits for all bcmath functions.<br />
                               <a href='http://php.net/bcmath.scale' target='_blank'>http://php.net/bcmath.scale</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("bcmath_scale",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Allow URL Fopen');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Whether to allow the treatment of URLs (like http:// or ftp://) as files.<br />
                               <a href='http://php.net/allow-url-fopen' target='_blank'>http://php.net/allow-url-fopen</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("allow_url_fopen",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Auto Globals JIT');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="When enabled, the ENV, REQUEST and SERVER variables are created when they're<br />
first used (Just In Time) instead of when the script starts. If these<br />
variables are not used within a script, having this directive on will result<br />
in a performance gain. The PHP directive register_argc_argv must be disabled<br />
for this directive to have any effect.<br />
                               <a href='http://php.net/auto-globals-jit' target='_blank'>http://php.net/auto-globals-jit</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("auto_globals_jit",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Memory Limit');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Maximum input variable nesting level<br />
http://php.net/max-input-nesting-level<br />
max_input_nesting_level = 64<br />
<br />
How many GET/POST/COOKIE input variables may be accepted<br />
max_input_vars = 1000<br />
<br />
Maximum amount of memory a script may consume<br />
                               <a href='http://php.net/memory-limit' target='_blank'>http://php.net/memory-limit</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <?= $form->render("memory_limit",array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">MB</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Max Input Time');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Maximum amount of time each script may spend parsing request data. It's a good<br />
idea to limit this time on productions servers in order to eliminate unexpectedly<br />
long running scripts.<br />
Note: This directive is hardcoded to -1 for the CLI SAPI<br />
Default Value: -1 (Unlimited)<br />
Development Value: 60 (60 seconds)<br />
Production Value: 60 (60 seconds)<br />
                               <a href='http://php.net/max-input-time' target='_blank'>http://php.net/max-input-time</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("max_input_time",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Max Execution Time');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Maximum execution time of each script, in seconds<br />
Note: This directive is hardcoded to 0 for the CLI SAPI<br />
                               <a href='http://php.net/max-execution-time' target='_blank'>http://php.net/max-execution-time</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("max_execution_time",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Post Max Size');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Maximum size of POST data that PHP will accept.<br />
Its value may be 0 to disable the limit. It is ignored if POST data reading<br />
is disabled through enable_post_data_reading.<br />
                               <a href='http://php.net/post-max-size' target='_blank'>http://php.net/post-max-size</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <?= $form->render("post_max_size",array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">MB</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Upload Max File Size');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Maximum allowed size for uploaded files.<br />
                               <a href='http://php.net/upload-max-filesize' target='_blank'>http://php.net/upload-max-filesize</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <?= $form->render("upload_max_filesize",array('class' => 'form-control form-control-sm')); ?>
                                    <span class="input-group-addon">MB</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Max File Uploads');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Maximum number of files that can be uploaded via a single request" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("max_file_uploads",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="col-md-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Session & Cookie');?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Save Handler');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Handler used to store/retrieve data.<br />
                                       <a href='http://php.net/session.save-handler' target='_blank'>http://php.net/session.save-handler</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_save_handler",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Save Path');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Argument passed to save_handler.  In the case of files, this is the path<br />
        where data files are stored. Note: Windows users have to change this<br />
        variable in order to use PHP's session functions.<br />
        <br />
        The path can be defined as:<br />
            <pre>session.save_path = &quot;N;/path&quot;</pre><br />
                                    where N is an integer.  Instead of storing all the session files in<br />
                                    /path, what this will do is use subdirectories N-levels deep, and<br />
                                    store the session data in those directories.  This is useful if<br />
                                    your OS has problems with many files in one directory, and is<br />
                                    a more efficient layout for servers that handle many sessions.<br />
                                    <br />
                                    NOTE 1: PHP will not create this directory structure automatically.<br />
                                    You can use the script in the ext/session dir for that purpose.<br />
                                    NOTE 2: See the section on garbage collection below if you choose to<br />
                                    use subdirectories for session storage<br />
                                    <br />
                                    The file storage module creates files using mode 600 by default.<br />
                                    You can change that by using<br />
                                    <br />
                                    <pre>session.save_path = &quot;N;MODE;/path&quot;<br /></pre>
                                    <br />
                                    where MODE is the octal representation of the mode. Note that this<br />
                                    does not overwrite the process's umask.<br />
                                       <a href='http://php.net/session.save-path' target='_blank'>http://php.net/session.save-path</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_save_path",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Use Cookies');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Whether to use cookies.<br />
                                       <a href='http://php.net/session.use-cookies' target='_blank'>http://php.net/session.use-cookies</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_use_cookies",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Name');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Name of the session (used as cookie name).<br />
                                       <a href='http://php.net/session.name' target='_blank'>http://php.net/session.name</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_name",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Auto Start');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Initialize session on request startup.<br />
                                       <a href='http://php.net/session.auto-start' target='_blank'>http://php.net/session.auto-start</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_auto_start",array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Cookie Lifetime');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Lifetime in seconds of cookie or, if 0, until browser is restarted.<br />
                                       <a href='http://php.net/session.cookie-lifetime' target='_blank'>http://php.net/session.cookie-lifetime</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_cookie_lifetime",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Cookie Path');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="The path for which the cookie is valid.<br />
                                       <a href='http://php.net/session.cookie-path' target='_blank'>http://php.net/session.cookie-path</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_cookie_path",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Cookie Domain');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="The domain for which the cookie is valid.<br />
                                       <a href='http://php.net/session.cookie-domain' target='_blank'>http://php.net/session.cookie-domain</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_cookie_domain",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-6">
                                <label>
                                    <?= __('Session Cache Expire');?>
                                    <a class="text-danger text_underline" data-toggle="popover" data-placement="right"
                                       data-html="true" data-trigger="focus" tabindex="0"
                                       data-content="Document expires after n minutes.<br />
                                       <a href='http://php.net/session.cache-expire' target='_blank'>http://php.net/session.cache-expire</a>" data-original-title="" title="">
                                        ?
                                    </a>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render("session_cache_expire",array('class' => 'form-control form-control-sm')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<br />
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>