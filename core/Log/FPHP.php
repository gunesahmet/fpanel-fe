<?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */namespace FPanel\Log;
use ErrorException;
use Phalcon\Mvc\User\Component;
use ReflectionClass;

if (!defined('E_STRICT')) {
    define('E_STRICT', 2048);
}
if (!defined('E_RECOVERABLE_ERROR')) {
    define('E_RECOVERABLE_ERROR', 4096);
}
if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED', 8192);
}
if (!defined('E_USER_DEPRECATED')) {
    define('E_USER_DEPRECATED', 16384);
}

class FPHP extends Component
{
    const VERSION = '0.3';

    const LOG = 'LOG';

    const INFO = 'INFO';

    const WARN = 'WARN';

    const ERROR = 'ERROR';

    const DUMP = 'DUMP';

    const TRACE = 'TRACE';

    const EXCEPTION = 'EXCEPTION';

    const TABLE = 'TABLE';

    const GROUP_START = 'GROUP_START';

    const GROUP_END = 'GROUP_END';

    protected static $instance = null;

    protected $inExceptionHandler = false;

    protected $throwErrorExceptions = true;

    protected $convertAssertionErrorsToExceptions = true;

    protected $throwAssertionExceptions = false;

    protected $messageIndex = 1;

    protected $options = array(
        'maxDepth' => 10,
        'maxObjectDepth' => 5,
        'maxArrayDepth' => 5,
        'useNativeJsonEncode' => true,
        'includeLineNumbers' => true,
        'lineNumberOffset' => 0
    );

    protected $objectFilters = array(
        'firephp' => array('objectStack', 'instance', 'json_objectStack')
//        'firephp_test_class' => array('objectStack', 'instance', 'json_objectStack')
    );

    protected $ignoredInTraces = array(
        'classes' => array(
            'FirePHP' => true,
            'FB' => true
        ),
        'paths' => array()
    );

    protected $objectStack = array();

    protected $enabled = true;

    protected $logToInsightConsole = null;

    function __construct ()
    {
        $this->ignoredInTraces['paths'][__DIR__] = true;
        $this->ignoredInTraces['paths'][__FILE__] = true;
    }

    public function __sleep()
    {
        return array('options', 'objectFilters', 'enabled');
    }

    public static function getInstance($autoCreate = false)
    {
        if ($autoCreate === true && !self::$instance) {
            self::init();
        }
        return self::$instance;
    }

    public static function init()
    {
        return self::setInstance(new self());
    }

    public static function setInstance($instance)
    {
        return self::$instance = $instance;
    }

    /**
     * @throws \Exception
     */
    public function setLogToInsightConsole($console)
    {
        if (is_string($console)) {
            if (get_class($this) != 'FirePHP_Insight' && !is_subclass_of($this, 'FirePHP_Insight')) {
                throw $this->newException('FirePHP instance not an instance or subclass of FirePHP_Insight!');
            }
            $this->logToInsightConsole = $this->to('request')->console($console);
        } else {
            $this->logToInsightConsole = $console;
        }
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setObjectFilter($class, $filter = true)
    {
        $this->objectFilters[strtolower($class)] = $filter;
    }

    public function ignoreClassInTraces ($class)
    {
        $this->ignoredInTraces['classes'][$class] = true;
    }

    public function ignorePathInTraces ($path)
    {
        $this->ignoredInTraces['paths'][$path] = true;
    }

    protected function _isClassPropertyInObjectFilter($class, $name)
    {
        $classes = class_parents($class);
        array_unshift($classes, $class);
        $hidden = false;
        foreach ($classes as $class) {
            if ($hidden) {
                break;
            }
            $classLower = strtolower($class);
            if (
                isset($this->objectFilters[$classLower]) &&
                (
                    (
                        is_bool($this->objectFilters[$classLower]) &&
                        $this->objectFilters[$classLower] == true
                    ) ||
                    (
                        is_array($this->objectFilters[$classLower]) &&
                        in_array($name, $this->objectFilters[$classLower])
                    )
                )
            ) {
                $hidden = true;
            }
        }
        return $hidden;
    }

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOption($name, $value)
    {
        if (!isset($this->options[$name])) {
            throw $this->newException('Unknown option: ' . $name);
        }
        $this->options[$name] = $value;
    }

    public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            throw $this->newException('Unknown option: ' . $name);
        }
        return $this->options[$name];
    }

    public function registerErrorHandler($throwErrorExceptions = false)
    {
        //NOTE: The following errors will not be caught by this error handler:
        //      E_ERROR, E_PARSE, E_CORE_ERROR,
        //      E_CORE_WARNING, E_COMPILE_ERROR,
        //      E_COMPILE_WARNING, E_STRICT

        $this->throwErrorExceptions = $throwErrorExceptions;

        return set_error_handler(array($this, 'errorHandler'));
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        // Don't throw exception if error reporting is switched off
        if (error_reporting() == 0) {
            return;
        }
        // Only throw exceptions for errors we are asking for
        if (error_reporting() & $errno) {

            $exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);
            if ($this->throwErrorExceptions) {
                throw $exception;
            } else {
                $this->fb($exception);
            }
        }
    }

    public function registerExceptionHandler()
    {
        return set_exception_handler(array($this, 'exceptionHandler'));
    }

    function exceptionHandler($exception)
    {
        $this->inExceptionHandler = true;

        header('HTTP/1.1 500 Internal Server Error');

        try {
            $this->fb($exception);
        }
        catch (ErrorException | \Exception $e) {
        }

        $this->inExceptionHandler = false;
    }

    public function registerAssertionHandler($convertAssertionErrorsToExceptions = true, $throwAssertionExceptions = false)
    {
        $this->convertAssertionErrorsToExceptions = $convertAssertionErrorsToExceptions;
        $this->throwAssertionExceptions = $throwAssertionExceptions;

        if ($throwAssertionExceptions && !$convertAssertionErrorsToExceptions) {
            throw $this->newException('Cannot throw assertion exceptions as assertion errors are not being converted to exceptions!');
        }

        return assert_options(ASSERT_CALLBACK, array($this, 'assertionHandler'));
    }

    public function assertionHandler($file, $line, $code)
    {
        if ($this->convertAssertionErrorsToExceptions) {

            $exception = new ErrorException('Assertion Failed - Code[ ' . $code . ' ]', 0, null, $file, $line);

            if ($this->throwAssertionExceptions) {
                throw $exception;
            } else {
                $this->fb($exception);
            }

        } else {
            $this->fb($code, 'Assertion Failed', FPHP::ERROR, array('File' => $file, 'Line' => $line));
        }
    }

    public function group($name, $options = null)
    {
        if (
            is_null($name) ||
            !isset($name)
        ) {
            throw $this->newException('You must specify a label for the group!');
        }

        if ($options) {
            if (!is_array($options)) {
                throw $this->newException('Options must be defined as an array!');
            }
            if (array_key_exists('Collapsed', $options)) {
                $options['Collapsed'] = ($options['Collapsed']) ? 'true' : 'false';
            }
        }

        return $this->fb(null, $name, FPHP::GROUP_START, $options);
    }

    public function groupEnd()
    {
        return $this->fb(null, null, FPHP::GROUP_END);
    }

    public function log($object, $label = null, $options = array())
    {
        return $this->fb($object, $label, FPHP::LOG, $options);
    }

    public function info($object, $label = null, $options = array())
    {
        return $this->fb($object, $label, FPHP::INFO, $options);
    }

    public function warn($object, $label = null, $options = array())
    {
        return $this->fb($object, $label, FPHP::WARN, $options);
    }

    public function error($object, $label = null, $options = array())
    {
        return $this->fb($object, $label, FPHP::ERROR, $options);
    }

    public function dump($key, $variable, $options = array())
    {
        if (!is_string($key)) {
            throw $this->newException('Key passed to dump() is not a string');
        }
        if (strlen($key) > 100) {
            throw $this->newException('Key passed to dump() is longer than 100 characters');
        }
        if (!preg_match_all('/^[a-zA-Z0-9-_\.:]*$/', $key, $m)) {
            throw $this->newException('Key passed to dump() contains invalid characters [a-zA-Z0-9-_\.:]');
        }
        return $this->fb($variable, $key, FPHP::DUMP, $options);
    }

    public function trace($label)
    {
        return $this->fb($label, null, FPHP::TRACE, array(
            'trace' => debug_backtrace()
        ));
    }

    public function table($label, $table, $options = array())
    {
        return $this->fb($table, $label, FPHP::TABLE, $options);
    }

    /**
     * @throws \Exception
     */
    public static function to()
    {
        $instance = self::getInstance();
        if (!method_exists($instance, '_to')) {
            throw (new FPHP)->newException('FPHP::to() implementation not loaded');
        }
        $args = func_get_args();
        return call_user_func_array(array($instance, '_to'), $args);
    }

    /**
     * @throws \Exception
     */
    public static function plugin()
    {
        $instance = self::getInstance();
        if (!method_exists($instance, '_plugin')) {
            throw (new FPHP)->newException('FPHP::plugin() implementation not loaded');
        }
        $args = func_get_args();
        return call_user_func_array(array($instance, '_plugin'), $args);
    }

    public function detectClientExtension()
    {
        // Check if FirePHP is installed on client via User-Agent header
        if (@preg_match_all('/\sFirePHP\/([\.\d]*)\s?/si', $this->getUserAgent(), $m) &&
            version_compare($m[1][0], '0.0.6', '>=')) {
            return true;
        } else
            // Check if FirePHP is installed on client via X-FirePHP-Version header
            if (@preg_match_all('/^([\.\d]*)$/si', $this->getRequestHeader('X-FirePHP-Version'), $m) &&
                version_compare($m[1][0], '0.0.6', '>=')) {
                return true;
            }
        return false;
    }

    protected function filterDebugBacktrace ($trace, $offset = 0) {
        $discardedTrace = array();
        $filteredTrace = array();
        for ($i = 0; $i < sizeof($trace); $i++) {
            if (
                count($this->ignoredInTraces['classes']) &&
                isset($trace[$i]['class']) &&
                (
                    isset($this->ignoredInTraces['classes'][$trace[$i]['class']]) ||
                    array_reduce(
                        array_keys($this->ignoredInTraces['classes']),
                        function ($carry, $class) use ($trace, $i)
                        {
                            if (strpos($trace[$i]['class'], $class) === 0) {
                                $carry += 1;
                            }
                            return $carry;
                        },
                        0
                    ) > 0
                )
            ) {
                array_push($discardedTrace, $trace[$i]);
                continue;
            }
            if (
                count($this->ignoredInTraces['paths']) &&
                isset($trace[$i]['file']) &&
                (
                    isset($this->ignoredInTraces['paths'][$trace[$i]['file']]) ||
                    array_reduce(
                        array_keys($this->ignoredInTraces['paths']),
                        function ($carry, $path) use ($trace, $i)
                        {
                            if (strpos($trace[$i]['file'], $path) === 0) {
                                $carry += 1;
                            }
                            return $carry;
                        },
                        0
                    ) > 0
                )
            ) {
                array_push($discardedTrace, $trace[$i]);
                continue;
            }
            array_push($filteredTrace, $trace[$i]);
        }
        if ($offset < 0) {
            for ($i = sizeof($discardedTrace) -1; $i >= ($offset * -1); $i--) {
                array_unshift($filteredTrace, $discardedTrace[$i]);
            }
        }
        return $filteredTrace;
    }

    public function fb($object)
    {
        static $insightGroupStack = array();

        if (!$this->getEnabled()) {
            return false;
        }

        if ($this->headersSent($filename, $linenum)) {
            // If we are logging from within the exception handler we cannot throw another exception
            if ($this->inExceptionHandler) {
                // Simply echo the error out to the page
                echo '<div style="border: 2px solid red; font-family: Arial; font-size: 12px; background-color: lightgray; padding: 5px;"><span style="color: red; font-weight: bold;">FirePHP ERROR:</span> Headers already sent in <b>' . $filename . '</b> on line <b>' . $linenum . '</b>. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.</div>';
            } else {
                throw $this->newException('Headers already sent in ' . $filename . ' on line ' . $linenum . '. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.');
            }
        }

        $type = null;
        $label = null;
        $options = array();

        if (func_num_args() == 1) {
        } else if (func_num_args() == 2) {
            switch (func_get_arg(1)) {
                case self::LOG:
                case self::INFO:
                case self::WARN:
                case self::ERROR:
                case self::DUMP:
                case self::TRACE:
                case self::EXCEPTION:
                case self::TABLE:
                case self::GROUP_START:
                case self::GROUP_END:
                    $type = func_get_arg(1);
                    break;
                default:
                    $label = func_get_arg(1);
                    break;
            }
        } else if (func_num_args() == 3) {
            $type = func_get_arg(2);
            $label = func_get_arg(1);
        } else if (func_num_args() == 4) {
            $type = func_get_arg(2);
            $label = func_get_arg(1);
            $options = func_get_arg(3);
        } else {
            throw $this->newException('Wrong number of arguments to fb() function!');
        }

        // Get folder name where firephp is located.
        $parentFolder = basename(dirname(__FILE__));
        $parentFolderLength = strlen( $parentFolder );
        $fbLength = 7 + $parentFolderLength;
        $fireClassLength = 18 + $parentFolderLength;

        if ($this->logToInsightConsole !== null && (get_class($this) == 'FirePHP_Insight' || is_subclass_of($this, 'FirePHP_Insight'))) {
            $trace = debug_backtrace();
            if (!$trace) return false;
            for ($i = 0; $i < sizeof($trace); $i++) {
                if (isset($trace[$i]['class'])) {
                    if ($trace[$i]['class'] == 'FirePHP' || $trace[$i]['class'] == 'FB') {
                        continue;
                    }
                }
                if (isset($trace[$i]['file'])) {
                    $path = $this->_standardizePath($trace[$i]['file']);
                    if (substr($path, -1*$fbLength, $fbLength) == $parentFolder.'/fb.php' || substr($path, -1*$fireClassLength, $fireClassLength) == $parentFolder.'/FirePHP.class.php') {
                        continue;
                    }
                }
                if (isset($trace[$i]['function']) && $trace[$i]['function'] == 'fb' &&
                    isset($trace[$i - 1]['file']) && substr($this->_standardizePath($trace[$i - 1]['file']), -1*$fbLength, $fbLength) == $parentFolder.'/fb.php') {
                    continue;
                }
                if (isset($trace[$i]['class']) && $trace[$i]['class'] == 'FB' &&
                    isset($trace[$i - 1]['file']) && substr($this->_standardizePath($trace[$i - 1]['file']), -1*$fbLength, $fbLength) == $parentFolder.'/fb.php') {
                    continue;
                }
                break;
            }
            // adjust trace offset
            $msg = $this->logToInsightConsole->option('encoder.trace.offsetAdjustment', $i);

            if ($object instanceof Exception) {
                $type = self::EXCEPTION;
            }
            if ($label && $type != self::TABLE && $type != self::GROUP_START) {
                $msg = $msg->label($label);
            }
            switch ($type) {
                case self::DUMP:
                case self::LOG:
                    return $msg->log($object);
                case self::INFO:
                    return $msg->info($object);
                case self::WARN:
                    return $msg->warn($object);
                case self::ERROR:
                    return $msg->error($object);
                case self::TRACE:
                    return $msg->trace($object);
                case self::EXCEPTION:
                    return $this->plugin('error')->handleException($object, $msg);
                case self::TABLE:
                    if (isset($object[0]) && !is_string($object[0]) && $label) {
                        $object = array($label, $object);
                    }
                    return $msg->table($object[0], array_slice($object[1], 1), $object[1][0]);
                case self::GROUP_START:
                    $insightGroupStack[] = $msg->group(md5($label))->open();
                    return $msg->log($label);
                case self::GROUP_END:
                    if (count($insightGroupStack) == 0) {
                        throw new ErrorException('Too many groupEnd() as opposed to group() calls!');
                    }
                    $group = array_pop($insightGroupStack);
                    return $group->close();
                default:
                    return $msg->log($object);
            }
        }

        if (!$this->detectClientExtension()) {
            return false;
        }

        $meta = array();
        $skipFinalObjectEncode = false;

        if ($object instanceof Exception) {

            $meta['file'] = $this->_escapeTraceFile($object->getFile());
            $meta['line'] = $object->getLine();

            $originalTrace = $object->getTrace();

            $trace = $this->filterDebugBacktrace($originalTrace);

            if (
                $object instanceof ErrorException &&
                isset($originalTrace[0]['function']) &&
                $trace[0]['function'] == 'errorHandler' &&
                isset($originalTrace[0]['class']) &&
                $originalTrace[0]['class'] == 'FirePHP'
            ) {

                $severity = false;
                switch ($object->getSeverity()) {
                    case E_WARNING:
                        $severity = 'E_WARNING';
                        break;

                    case E_NOTICE:
                        $severity = 'E_NOTICE';
                        break;

                    case E_USER_ERROR:
                        $severity = 'E_USER_ERROR';
                        break;

                    case E_USER_WARNING:
                        $severity = 'E_USER_WARNING';
                        break;

                    case E_USER_NOTICE:
                        $severity = 'E_USER_NOTICE';
                        break;

                    case E_STRICT:
                        $severity = 'E_STRICT';
                        break;

                    case E_RECOVERABLE_ERROR:
                        $severity = 'E_RECOVERABLE_ERROR';
                        break;

                    case E_DEPRECATED:
                        $severity = 'E_DEPRECATED';
                        break;

                    case E_USER_DEPRECATED:
                        $severity = 'E_USER_DEPRECATED';
                        break;
                }

                $object = array(
                    'Class' => get_class($object),
                    'Message' => $severity . ': ' . $object->getMessage(),
                    'File' => $this->_escapeTraceFile($object->getFile()),
                    'Line' => $object->getLine(),
                    'Type' => 'trigger',
                    'Trace' => $this->_escapeTrace($trace, array(
                        'maxDepth' => 2,
                        'includeMaxDepthProperties' => false,
                        'includeStaticProperties' => false,
                        'includePrivateProperties' => false,
                        'includeProtectedProperties' => false,
                        'includeUndeclaredProperties' => false
                    ))
                );
                $skipFinalObjectEncode = true;
            } else {
                $object = array(
                    'Class' => get_class($object),
                    'Message' => $object->getMessage(),
                    'File' => $this->_escapeTraceFile($object->getFile()),
                    'Line' => $object->getLine(),
                    'Type' => 'throw',
                    'Trace' => $this->_escapeTrace($trace, array(
                        'maxDepth' => 2,
                        'includeMaxDepthProperties' => false,
                        'includeStaticProperties' => false,
                        'includePrivateProperties' => false,
                        'includeProtectedProperties' => false,
                        'includeUndeclaredProperties' => false
                    ))
                );
                $skipFinalObjectEncode = true;
            }
            $type = self::EXCEPTION;

        } else if ($type == self::TRACE) {

            if (!isset($options['trace'])) {
                $options['trace'] = debug_backtrace();
            }

            $encounteredCount = 0;
            for ($i = 0; $i < sizeof($options['trace']); $i++) {
                if (
                    (
                        isset($options['lineNumberOffset']) &&
                        $encounteredCount < $options['lineNumberOffset']
                    ) ||
                    (
                        !isset($options['lineNumberOffset']) &&
                        $encounteredCount < $this->options['lineNumberOffset']
                    )
                ) {
                    $encounteredCount += 1;
                    continue;
                }
                $callingFrame = $options['trace'][$i];
                array_splice($options['trace'], 0, $i);
                break;
            }

//            $callingFrame = array_shift($options['trace']);

            $trace = $this->filterDebugBacktrace($options['trace']);

            $object = array(
                'Class' => isset($callingFrame['class']) ? $callingFrame['class'] : '',
                'Type' => isset($callingFrame['type']) ? $callingFrame['type'] : '',
                'Function' => isset($callingFrame['function']) ? $callingFrame['function'] : '',
                'Message' => $callingFrame['args'][0],
                'File' => isset($callingFrame['file']) ? $this->_escapeTraceFile($callingFrame['file']) : '',
                'Line' => isset($callingFrame['line']) ? $callingFrame['line'] : '',
                'Args' => isset($callingFrame['args']) ? $this->encodeObject($callingFrame['args'], 1, 1, 1, array(
                    'maxDepth' => 2,
                    'includeMaxDepthProperties' => false,
                    'includeStaticProperties' => false,
                    'includePrivateProperties' => false,
                    'includeProtectedProperties' => false,
                    'includeUndeclaredProperties' => false
                )) : '',
                'Trace' => $this->_escapeTrace($trace, array(
                    'maxDepth' => 2,
                    'includeMaxDepthProperties' => false,
                    'includeStaticProperties' => false,
                    'includePrivateProperties' => false,
                    'includeProtectedProperties' => false,
                    'includeUndeclaredProperties' => false
                ))
            );

            $skipFinalObjectEncode = true;
            $meta['file'] = isset($callingFrame['file']) ? $this->_escapeTraceFile($callingFrame['file']) : '';
            $meta['line'] = isset($callingFrame['line']) ? $callingFrame['line'] : '';

        } else
            if ($type == self::TABLE) {

                if (isset($object[0]) && is_string($object[0])) {
                    $object[1] = $this->encodeTable($object[1], array(
                        'maxDepth' => 2,
                        'includeMaxDepthProperties' => false,
                        'includeStaticProperties' => false,
                        'includePrivateProperties' => false,
                        'includeProtectedProperties' => false,
                        'includeUndeclaredProperties' => false
                    ));
                } else {
                    $object = $this->encodeTable($object, array(
                        'maxDepth' => 2,
                        'includeMaxDepthProperties' => false,
                        'includeStaticProperties' => false,
                        'includePrivateProperties' => false,
                        'includeProtectedProperties' => false,
                        'includeUndeclaredProperties' => false
                    ));
                }

                $skipFinalObjectEncode = true;

            } else if ($type == self::GROUP_START) {

                if (
                    is_null($label) ||
                    !isset($label)
                ) {
                    throw $this->newException('You must specify a label for the group!');
                }

            } else {
                if ($type === null) {
                    $type = self::LOG;
                }
            }

        if ($this->options['includeLineNumbers']) {
            if (!isset($meta['file']) || !isset($meta['line'])) {
                $trace = $this->filterDebugBacktrace(debug_backtrace(), -1);
                $encounteredCount = 0;
                for ($i = 0; $trace && $i < sizeof($trace); $i++) {
                    if (
                        (
                            isset($options['lineNumberOffset']) &&
                            $encounteredCount < $options['lineNumberOffset']
                        ) ||
                        (
                            !isset($options['lineNumberOffset']) &&
                            $encounteredCount < $this->options['lineNumberOffset']
                        )
                    ) {
                        $encounteredCount += 1;
                        continue;
                    }
                    $meta['file'] = isset($trace[$i]['file']) ? $this->_escapeTraceFile($trace[$i]['file']) : '';
                    $meta['line'] = isset($trace[$i]['line']) ? $trace[$i]['line'] : '';
                    break;
                }
            }
        } else {
            unset($meta['file']);
            unset($meta['line']);
        }

        $this->setHeader('X-Wf-Protocol-1', 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
        $this->setHeader('X-Wf-1-Plugin-1', 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/' . self::VERSION);

        $structureIndex = 1;
        if ($type == self::DUMP) {
            $structureIndex = 2;
            $this->setHeader('X-Wf-1-Structure-2', 'http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1');
        } else {
            $this->setHeader('X-Wf-1-Structure-1', 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
        }

        if ($type == self::DUMP) {
            $msg = '{"' . $label . '":' . $this->jsonEncode($object, $skipFinalObjectEncode) . '}';
        } else {
            $msgMeta = $options;
            foreach($this->options as $key => $val) {
                unset($msgMeta[$key]);
            }
            unset($msgMeta['trace']);
            $msgMeta['Type'] = $type;
            if ($label !== null) {
                $msgMeta['Label'] = $label;
            }
            if (isset($meta['file']) && !isset($msgMeta['File'])) {
                $msgMeta['File'] = $meta['file'];
            }
            if (isset($meta['line']) && !isset($msgMeta['Line'])) {
                $msgMeta['Line'] = $meta['line'];
            }
            $msg = '[' . $this->jsonEncode($msgMeta) . ',' . $this->jsonEncode($object, $skipFinalObjectEncode) . ']';
        }

        $parts = explode("\n", chunk_split($msg, 5000, "\n"));

        for ($i = 0; $i < count($parts); $i++) {

            $part = $parts[$i];
            if ($part) {

                if (count($parts) > 2) {
                    // Message needs to be split into multiple parts
                    $this->setHeader('X-Wf-1-' . $structureIndex . '-' . '1-' . $this->messageIndex,
                        (($i == 0) ? strlen($msg) : '')
                        . '|' . $part . '|'
                        . (($i < count($parts) - 2) ? '\\' : ''));
                } else {
                    $this->setHeader('X-Wf-1-' . $structureIndex . '-' . '1-' . $this->messageIndex,
                        strlen($part) . '|' . $part . '|');
                }

                $this->messageIndex++;

                if ($this->messageIndex > 99999) {
                    throw $this->newException('Maximum number (99,999) of messages reached!');
                }
            }
        }

        $this->setHeader('X-Wf-1-Index', $this->messageIndex - 1);

        return true;
    }

    protected function _standardizePath($path)
    {
        return preg_replace('/\\\\+/', '/', $path);
    }

    protected function _escapeTrace($trace, $options = array())
    {
        if (!$trace) return $trace;
        for ($i = 0; $i < sizeof($trace); $i++) {
            if (isset($trace[$i]['file'])) {
                $trace[$i]['file'] = $this->_escapeTraceFile($trace[$i]['file']);
            }
            if (isset($trace[$i]['args'])) {
                $trace[$i]['args'] = $this->encodeObject($trace[$i]['args'], 1, 1, 1, $options);
            }
        }
        return $trace;
    }

    protected function _escapeTraceFile($file)
    {
        /* Check if we have a windows filepath */
        if (strpos($file, '\\')) {
            /* First strip down to single \ */

            $file = preg_replace('/\\\\+/', '\\', $file);

            return $file;
        }
        return $file;
    }

    protected function headersSent(&$filename, &$linenum)
    {
        return headers_sent($filename, $linenum);
    }

    protected function setHeader($name, $value)
    {
        return header($name . ': ' . $value);
    }

    protected function getUserAgent()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public static function getAllRequestHeaders()
    {
        static $_cachedHeaders = false;
        if ($_cachedHeaders !== false) {
            return $_cachedHeaders;
        }
        $headers = array();
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                $headers[strtolower($name)] = $value;
            }
        } else {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[strtolower(str_replace(' ', '-', str_replace('_', ' ', substr($name, 5))))] = $value;
                }
            }
        }
        return $_cachedHeaders = $headers;
    }

    protected function getRequestHeader($name)
    {
        $headers = self::getAllRequestHeaders();
        if (isset($headers[strtolower($name)])) {
            return $headers[strtolower($name)];
        }
        return false;
    }

    protected function newException($message): \Exception
    {
        return new \Exception($message);
    }

    public function jsonEncode($object, bool $skipObjectEncode = false)
    {
        if (!$skipObjectEncode) {
            $object = $this->encodeObject($object);
        }

        if (function_exists('json_encode')
            && $this->options['useNativeJsonEncode'] != false) {

            return json_encode($object);
        } else {
            return $this->json_encode($object);
        }
    }

    protected function encodeTable($table, $options = array()): ?array
    {
        if (!$table)
            return $table;

        $newTable = array();
        foreach ($table as $row) {

            if (is_array($row)) {
                $newRow = array();

                foreach ($row as $item) {
                    $newRow[] = $this->encodeObject($item, 1, 1, 1, $options);
                }

                $newTable[] = $newRow;
            }
        }

        return $newTable;
    }

    protected function encodeObject($object, $objectDepth = 1, $arrayDepth = 1, $maxDepth = 1, $options = array())
    {
        if (
            $maxDepth > $this->options['maxDepth'] ||
            (
                isset($options['maxDepth']) &&
                $maxDepth > $options['maxDepth']
            )
        ) {
            return '** Max Depth (' . $maxDepth . ') **';
        }

        $return = array();

        //#2801 is_resource reports false for closed resources https://bugs.php.net/bug.php?id=28016
        if (is_resource($object) || gettype($object) === "unknown type") {

            return '** ' . (string) $object . ' **';

        } else if (is_object($object)) {

            if ($objectDepth > $this->options['maxObjectDepth']) {
                return '** Max Object Depth (' . $this->options['maxObjectDepth'] . ') **';
            }

            foreach ($this->objectStack as $refVal) {
                if ($refVal === $object) {
                    return '** Recursion (' . get_class($object) . ') **';
                }
            }
            array_push($this->objectStack, $object);

            $return['__className'] = $class = get_class($object);
            $classLower = strtolower($class);

            if (
                isset($this->objectFilters[$classLower]) &&
                is_bool($this->objectFilters[$classLower]) &&
                $this->objectFilters[$classLower] == true
            ) {
                return '** Excluded by Filter (' . $class . ') **';
            }

            $reflectionClass = new ReflectionClass($class);
            $properties = array();
            foreach ($reflectionClass->getProperties() as $property) {
                $properties[$property->getName()] = $property;
            }

            $members = (array)$object;

            foreach ($properties as $plainName => $property) {

                $name = $rawName = $plainName;
                if ($property->isStatic()) {
                    if (isset($options['includeStaticProperties']) && $options['includeStaticProperties'] === false) {
                        continue;
                    }
                    $name = 'static:' . $name;
                }
                if ($property->isPublic()) {
                    $name = 'public:' . $name;
                } else if ($property->isPrivate()) {
                    if (isset($options['includePrivateProperties']) && $options['includePrivateProperties'] === false) {
                        continue;
                    }
                    $name = 'private:' . $name;
                    $rawName = "\0" . $class . "\0" . $rawName;
                } else if ($property->isProtected()) {
                    if (isset($options['includeProtectedProperties']) && $options['includeProtectedProperties'] === false) {
                        continue;
                    }
                    $name = 'protected:' . $name;
                    $rawName = "\0" . '*' . "\0" . $rawName;
                }

                if (!$this->_isClassPropertyInObjectFilter($class, $plainName)) {
                    if (array_key_exists($rawName, $members) && !$property->isStatic()) {
                        $return[$name] = $this->encodeObject($members[$rawName], $objectDepth + 1, 1, $maxDepth + 1, $options);
                    } else {
                        if (method_exists($property, 'setAccessible')) {
                            $property->setAccessible(true);
                            $return[$name] = $this->encodeObject($property->getValue($object), $objectDepth + 1, 1, $maxDepth + 1, $options);
                        } else
                            if ($property->isPublic()) {
                                $return[$name] = $this->encodeObject($property->getValue($object), $objectDepth + 1, 1, $maxDepth + 1, $options);
                            } else {
                                $return[$name] = '** Need PHP 5.3 to get value **';
                            }
                    }
                } else {
                    $return[$name] = '** Excluded by Filter **';
                }

                if (
                    isset($options['includeMaxDepthProperties']) &&
                    $options['includeMaxDepthProperties'] === false &&
                    is_string($return[$name]) &&
                    substr($return[$name], 0, 14) === '** Max Depth ('
                ) {
                    unset($return[$name]);
                }
            }

            // Include all members that are not defined in the class
            // but exist in the object
            foreach ($members as $rawName => $value) {

                $name = $rawName;

                if ($name[0] == "\0") {
                    $parts = explode("\0", $name);
                    $name = $parts[2];
                }

                $plainName = $name;

                if (!isset($properties[$name])) {
                    if (isset($options['includeUndeclaredProperties']) && $options['includeUndeclaredProperties'] === false) {
                        continue;
                    }
                    $name = 'undeclared:' . $name;

                    if (!$this->_isClassPropertyInObjectFilter($class, $plainName)) {
                        $return[$name] = $this->encodeObject($value, $objectDepth + 1, 1, $maxDepth + 1, $options);
                    } else {
                        $return[$name] = '** Excluded by Filter **';
                    }

                    if (
                        isset($options['includeMaxDepthProperties']) &&
                        $options['includeMaxDepthProperties'] === false &&
                        is_string($return[$name]) &&
                        substr($return[$name], 0, 14) === '** Max Depth ('
                    ) {
                        unset($return[$name]);
                    }
                }
            }

            array_pop($this->objectStack);

        } else if (is_array($object)) {

            if ($arrayDepth > $this->options['maxArrayDepth']) {
                return '** Max Array Depth (' . $this->options['maxArrayDepth'] . ') **';
            }

            foreach ($object as $key => $val) {

                // Encoding the $GLOBALS PHP array causes an infinite loop
                // if the recursion is not reset here as it contains
                // a reference to itself. This is the only way I have come up
                // with to stop infinite recursion in this case.
                if ($key == 'GLOBALS'
                    && is_array($val)
                    && array_key_exists('GLOBALS', $val)) {
                    $val['GLOBALS'] = '** Recursion (GLOBALS) **';
                }

                if (!$this->is_utf8($key)) {
                    $key = utf8_encode($key);
                }

                $return[$key] = $this->encodeObject($val, 1, $arrayDepth + 1, $maxDepth + 1, $options);

                if (
                    isset($options['includeMaxDepthProperties']) &&
                    $options['includeMaxDepthProperties'] === false &&
                    is_string($return[$key]) &&
                    substr($return[$key], 0, 14) === '** Max Depth ('
                ) {
                    unset($return[$key]);
                }
            }

            if (
                isset($options['includeMaxDepthProperties']) &&
                $options['includeMaxDepthProperties'] === false &&
                count($object) > 0 &&
                count($return) == 0
            ) {
                return '** Max Depth (' . $maxDepth . ') **';
            }

        } else if ( is_bool($object) ) {
            return $object;
        } else if ( is_null($object) ) {
            return $object;
        } else if ( is_numeric($object) ) {
            return $object;
        } else {
            if ($this->is_utf8($object)) {
                return $object;
            } else {
                return utf8_encode($object);
            }
        }
        return $return;
    }

    protected function is_utf8($str)
    {
        if (function_exists('mb_detect_encoding')) {
            return (
                mb_detect_encoding($str, 'UTF-8', true) == 'UTF-8' &&
                ($str === null || $this->jsonEncode($str, true) !== 'null')
            );
        }
        $c = 0;
        $b = 0;
        $bits = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if ($c > 128) {
                if (($c >= 254)) return false;
                else if ($c >= 252) $bits = 6;
                else if ($c >= 248) $bits = 5;
                else if ($c >= 240) $bits = 4;
                else if ($c >= 224) $bits = 3;
                else if ($c >= 192) $bits = 2;
                else return false;
                if (($i + $bits) > $len) return false;
                while($bits > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || $b > 191) return false;
                    $bits--;
                }
            }
        }
        return ($str === null || $this->jsonEncode($str, true) !== 'null');
    }

    private $json_objectStack = array();

    private function json_utf82utf16($utf8)
    {
        // oh please oh please oh please oh please oh please
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }

        switch (strlen($utf8)) {
            case 1:
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return $utf8;

            case 2:
                // return a UTF-16 character from a 2-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x07 & (ord($utf8[0]) >> 2))
                    . chr((0xC0 & (ord($utf8[0]) << 6))
                        | (0x3F & ord($utf8[1])));

            case 3:
                // return a UTF-16 character from a 3-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr((0xF0 & (ord($utf8[0]) << 4))
                        | (0x0F & (ord($utf8[1]) >> 2)))
                    . chr((0xC0 & (ord($utf8[1]) << 6))
                        | (0x7F & ord($utf8[2])));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }

    private function json_encode($var)
    {
        if (is_object($var)) {
            if (in_array($var, $this->json_objectStack)) {
                return '"** Recursion **"';
            }
        }

        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';

            case 'NULL':
                return 'null';

            case 'integer':
                return (int) $var;

            case 'double':
            case 'float':
                return (float) $var;

            case 'string':
                // STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
                $ascii = '';
                $strlen_var = strlen($var);

                /*
                 * Iterate over every character in the string,
                 * escaping with a slash or encoding to UTF-8 where necessary
                 */
                for ($c = 0; $c < $strlen_var; ++$c) {

                    $ord_var_c = ord($var[$c]);

                    switch (true) {
                        case $ord_var_c == 0x08:
                            $ascii .= '\b';
                            break;
                        case $ord_var_c == 0x09:
                            $ascii .= '\t';
                            break;
                        case $ord_var_c == 0x0A:
                            $ascii .= '\n';
                            break;
                        case $ord_var_c == 0x0C:
                            $ascii .= '\f';
                            break;
                        case $ord_var_c == 0x0D:
                            $ascii .= '\r';
                            break;

                        case $ord_var_c == 0x22:
                        case $ord_var_c == 0x2F:
                        case $ord_var_c == 0x5C:
                            // double quote, slash, slosh
                            $ascii .= '\\' . $var[$c];
                            break;

                        case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                            // characters U-00000000 - U-0000007F (same as ASCII)
                            $ascii .= $var[$c];
                            break;

                        case (($ord_var_c & 0xE0) == 0xC0):
                            // characters U-00000080 - U-000007FF, mask 110XXXXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var[$c + 1]));
                            $c += 1;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF0) == 0xE0):
                            // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                ord($var[$c + 1]),
                                ord($var[$c + 2]));
                            $c += 2;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF8) == 0xF0):
                            // characters U-00010000 - U-001FFFFF, mask 11110XXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                ord($var[$c + 1]),
                                ord($var[$c + 2]),
                                ord($var[$c + 3]));
                            $c += 3;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFC) == 0xF8):
                            // characters U-00200000 - U-03FFFFFF, mask 111110XX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                ord($var[$c + 1]),
                                ord($var[$c + 2]),
                                ord($var[$c + 3]),
                                ord($var[$c + 4]));
                            $c += 4;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFE) == 0xFC):
                            // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                ord($var[$c + 1]),
                                ord($var[$c + 2]),
                                ord($var[$c + 3]),
                                ord($var[$c + 4]),
                                ord($var[$c + 5]));
                            $c += 5;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;
                    }
                }

                return '"' . $ascii . '"';

            case 'array':
                /*
                 * As per JSON spec if any array key is not an integer
                 * we must treat the the whole array as an object. We
                 * also try to catch a sparsely populated associative
                 * array with numeric keys here because some JS engines
                 * will create an array with empty indexes up to
                 * max_index which can cause memory issues and because
                 * the keys, which may be relevant, will be remapped
                 * otherwise.
                 *
                 * As per the ECMA and JSON specification an object may
                 * have any string as a property. Unfortunately due to
                 * a hole in the ECMA specification if the key is a
                 * ECMA reserved word or starts with a digit the
                 * parameter is only accessible using ECMAScript's
                 * bracket notation.
                 */

                // treat as a JSON object
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {

                    $this->json_objectStack[] = $var;

                    $properties = array_map(array($this, 'json_name_value'),
                        array_keys($var),
                        array_values($var));

                    array_pop($this->json_objectStack);

                    foreach ($properties as $property) {
                        if ($property instanceof Exception) {
                            return $property;
                        }
                    }

                    return '{' . join(',', $properties) . '}';
                }

                $this->json_objectStack[] = $var;

                // treat it like a regular array
                $elements = array_map(array($this, 'json_encode'), $var);

                array_pop($this->json_objectStack);

                foreach ($elements as $element) {
                    if ($element instanceof Exception) {
                        return $element;
                    }
                }

                return '[' . join(',', $elements) . ']';

            case 'object':
                $vars = self::encodeObject($var);

                $this->json_objectStack[] = $var;

                $properties = array_map(array($this, 'json_name_value'),
                    array_keys($vars),
                    array_values($vars));

                array_pop($this->json_objectStack);

                foreach ($properties as $property) {
                    if ($property instanceof Exception) {
                        return $property;
                    }
                }

                return '{' . join(',', $properties) . '}';

            default:
                return null;
        }
    }

    private function json_name_value($name, $value)
    {
        // Encoding the $GLOBALS PHP array causes an infinite loop
        // if the recursion is not reset here as it contains
        // a reference to itself. This is the only way I have come up
        // with to stop infinite recursion in this case.
        if ($name == 'GLOBALS'
            && is_array($value)
            && array_key_exists('GLOBALS', $value)) {
            $value['GLOBALS'] = '** Recursion **';
        }

        $encodedValue = $this->json_encode($value);

        if ($encodedValue instanceof Exception) {
            return $encodedValue;
        }

        return $this->json_encode(strval($name)) . ':' . $encodedValue;
    }

    public function setProcessorUrl($URL)
    {
        trigger_error('The FPHP::setProcessorUrl() method is no longer supported', E_USER_DEPRECATED);
    }

    public function setRendererUrl($URL)
    {
        trigger_error('The FPHP::setRendererUrl() method is no longer supported', E_USER_DEPRECATED);
    }
}