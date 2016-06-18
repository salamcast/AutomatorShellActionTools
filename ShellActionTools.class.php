<?php
/**
 *  main.command php Shell Action Tools
 *
 *  Created by Karl Holz on 2016-03-29.
 *  Copyright © 2016 Karl Holz. All rights reserved.
 *
 * @author Karl Holz
 *
 *  For more info on Automator Shell Script Actions
 *
 *  - https://macosxautomation.com/automator/shellacaction/index.html
 *  - https://macosxautomation.com/automator/xcodefix/index.html
 *  - https://developer.apple.com/library/mac/documentation/AppleApplications/Conceptual/AutomatorConcepts/Articles/ShellScriptActions.html
 *
 *  Automator.app Shell List hack for PHP, updates to the system may break this setup.
 *  - This is why I'm making this class, to cutdown the time in making an automator php action and avoid breakage of Automator.app hack ;)
 *
 *  http://www.patrickpatoray.com/index.php?Page=112
 *
 *  This guide can also be adapted for PHP installs for MacPorts or MAMP
 *  MAMP is an easy to use LAMP-like enviroment for Mac
 *  MacPorts is more ideal for older systems like Leopard (OS X 10.5) or Snow Leopard (OS X 10.6) for a more current version of php
 */

class ShellActionTools {
    /**
     * @var array $input holds each line of STDIN
     */
    public $input = array();
    /**
     * @var string $stdin STDIN document format
     */
    public $stdin = "";
    /**
     * @var array $config Configuration array from INI or JSON file
     */
    public $config = array();
    /**
     * @var array $keys The keys are the "Model Key Path" you binded in your Automator Action interface
     */
    public $keys = array();
    /**
     * @var array $env This will hold the values from your Automator action, the key is "Model Key Path" you set in the Xcode info
     */
    public $env = array();
    /**
     * @var bool $debug You will need to set this to TRUE if you want debug output for this class to the
     */
    public $debug = FALSE;

    function __construct() {
        stream_set_blocking(STDIN, 0);
        stream_set_timeout(STDIN, 1);
        $this->process_input();
        $this->process_env_keys();

        return TRUE;
    }

    /**
     *
     * These values will be bound with the Xcode interface elements you set, please review the link for more info
     *
     * https://macosxautomation.com/automator/shellacaction/index.html
     *
     * I have already set some filters for variables already configured on my _ENV used with normal /usr/bin/php
     * keep your "Model Key Path" values lower case and you'll be fine
     *
     * The way to debug in the shell, set before the command
     *
     * >$ arg="Argument" arg2="Arg num 2" php main.command
     *
     * @param $arg string key to search for in the _ENV
     * @return string returns the value or nothing
     */
    function __get($arg) {
        if (array_key_exists($arg, $_ENV)) {
            switch ($arg) {
                case 'LDFLAGS': case 'CPPFLAGS': case 'TMPDIR': case 'LANG': case 'PKG_CONFIG_PATH':
                case 'TERM_PROGRAM': case 'TERM': case 'SHELL': case 'TERM_PROGRAM_VERSION': case 'TERM_SESSION_ID':
                case 'USER': case 'SSH_AUTH_SOCK': case 'LOGNAME': case 'SECURITYSESSIONID':
                case 'DBUS_LAUNCHD_SESSION_BUS_SOCKET': case 'XPC_FLAGS': case 'XPC_SERVICE_NAME':
                case 'HOME': case 'PATH': case 'PWD': case 'OLDPWD': case 'PYTHONPATH':
                case 'SHLVL':
                // these are the _ENV keys that are not all CAPS
                case '_': case '__CF_USER_TEXT_ENCODING':
                case 'Apple_PubSub_Socket_Render':
                    return FALSE;
                    break;
                default:
                    return $_ENV[$arg];
            }
        } else { return FALSE; }
    }

    /**
     * get keys of the _ENV variable
     */
    function process_env_keys() {
        foreach (array_keys($_ENV) as $e) {
            if (is_string($this->$e)) {
                $this->keys[]=$e;
                $this->env[$e]=$this->$e;
            }
        }
        return TRUE;
    }

    /**
     * Process Input document or Piped in input
     * this is the value sent from a commant like:
     *
     * >$ ls -l | php main.command
     *
     * or a command like this:
     *
     * >$ php main.command <<+
     * [test]
     * one="Hello"
     * two="PHP"
     * three="Classes"
     * +
     * @return bool TRUE if input, FALSE if not input from prevous action
     */
    function process_input() {
        /**
         * Set both standard in document and array line per item
         */
        while (FALSE !== ($line = fgets(STDIN))){
            $this->input[]=trim($line);
            $this->stdin=$this->stdin . $line;
        }

        return (count($this->input) > 0) ? TRUE : FALSE;

    }

    /**
     *
     * Predefined error messages, you should beable to add your own when you extend this class
     *
     * @var array $error
     */

    public $error = array (
        //INI
        'no_ini_func'   => "Your PHP configuration won't support ini configuration/parsing",
        'no_stdin_ini'  => "No input on stdin, please provie an INI config",
        'failed_ini'    => "Ini failed to parse, check your configuration:",
        //JSON
        'no_json_func'  => "Your PHP configuration won't support JSON configuration/decoding",
        'no_stdin_json' => "No input recived on stdin, please provide a JSON config file",
        'failed_json'   => "JSON failed to parse, check your configuration"
    );

    /**
     *
     * The error printing function to the STDERR
     *
     * @param $e string the key to query for in the $error array
     */
    function error($e) {
        if ($this->debug) $this->debug();
        fwrite(STDERR, "");
        array_key_exists($e, $this->error) ? fwrite(STDERR, 'ERROR: ' . $this->error[$e]) : fwrite(STDERR, 'Unknown ERROR: ' . $e);
        fwrite(STDERR, "");
        exit(1);

    }

    /**
     * Debug function, dumps out input and keys to standard error
     * This should be view able in the automator error output
     */

    function debug() {
        fwrite(STDERR, "");
        if (count($this->input) > 0) {
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "| Piped STDIN in Input:                    |\n");
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "");
            fwrite(STDERR, $this->stdin . "\n");
            fwrite(STDERR, "");
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "| Input STDIN as an ARRAY():               |\n");
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "");
            foreach ($this->input as $k => $v) fwrite(STDERR, "$k => $v \n");
        } else {
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "| No STDIN:                                |\n");
            fwrite(STDERR, "+------------------------------------------+\n");
        }

        if (count($this->keys) > 0) {
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "| Keys set by Automator 'Model Key' binds: |\n");
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "");
            foreach ($this->keys as $k => $v) fwrite(STDERR, "$k => $v \t". $this->env[$v] . "\n");
        } else {
            fwrite(STDERR, "+------------------------------------------+\n");
            fwrite(STDERR, "| No Enviroment Variables from Action:     |\n");
            fwrite(STDERR, "+------------------------------------------+\n");
        }
        fwrite(STDERR, "\n");
    }

    /**
     *
     * open_ini(), the input is either a file reference or a string
     *
     * it is passed from the previous action in the automator, it is assumed that it is either a string or
     * the first (and only) argument if passed as a file reference.  This will process sections of your ini configuration
     *
     * The way you would debug it in the shell
     *
     * >$ arg="test" php main.command < file
     *
     * @var $conf string configuration file
     * @return array returns the ini configuration as array
     */

    function open_ini($conf='') {
        $ini=FALSE;
        if (function_exists("parse_ini_file") && function_exists("parse_ini_string")) {
            if (isset($conf)) {
                $ini=(is_file($conf)) ? parse_ini_file($conf, TRUE) : parse_ini_string($conf, TRUE);
            } else {
                if (count($this->input) > 0) $this->error('no_stdin_ini');
                $ini=(is_file($this->input[0])) ? parse_ini_file($this->input[0], TRUE) : parse_ini_string($this->stdin, TRUE);
            }
        } else {
            $this->error('no_ini_func');
        }
        if (! is_array($ini)) $this->error('failed_ini');
        $this->config=$ini;
        return $ini;
    }

    /**
     * open_json(), the input is either a JSON file or string
     *
     * it is passed from the previous action in the automator, it is assumed that it is either a string or
     * the first (and only) argument if passed as a file reference.  This will process sections of your ini configuration
     *
     * The way you would debug it in the shell
     *
     * >$ arg="test" php main.command < file
     *
     * @var $conf string configuration file
     * @return array returns the JSON configuration as array
     */
    function open_json($conf='') {
        $c=FALSE;
        if (function_exists("json_decode")) {
            if (isset($conf)) {
                if(is_file($conf)) {
                    $j=file($conf);
                    $c=json_decode($j, TRUE);
                } else {
                    $c=json_decode($conf, TRUE);
                }
            } else {
                if (count($this->input) > 0) $this->error('no_stdin_json');
                if(is_file($this->input[0])) {
                    $j=file($this->input[0]);
                    $c=json_decode($j, TRUE);
                } else {
                    $c=json_decode($this->stdin, TRUE);
                }
            }
        } else {
            $this->error('no_json_func');
        }
        if (! is_array($c)) $this->error('failed_json');
        $this->config=$c;
        return $c;
    }

}