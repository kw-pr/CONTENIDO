<?php
/**
 * Project:
 * CONTENIDO Content Management System
 *
 * Description:
 * Generic database callback execution.
 *
 * Requirements:
 * @con_php_req 5.0
 *
 *
 * @package    CONTENIDO Backend Classes
 * @version    0.1
 * @author     Dominik Ziegler
 * @copyright  four for business AG <www.4fb.de>
 * @license    http://www.contenido.org/license/LIZENZ.txt
 * @link       http://www.4fb.de
 * @link       http://www.contenido.org
 * @since      file available since CONTENIDO release 4.9
 *
 * {@internal
 *   created  2012-07-07
 *   $Id$:
 * }}
 */

if (!defined('CON_FRAMEWORK')) {
    die('Illegal call');
}

/**
 * Class cGenericDb.
 * Handles the generic execution of callbacks.
 *
 * @copyright  four for business AG <www.4fb.de>
 */
class cGenericDb {
    /**
     * Callbacks are executed before a item is created.
     * Expected parameters for callback: none
     */
    const CREATE_BEFORE = 10;

    /**
     * Callbacks are executed if item could not be created.
     * Expected parameters for callback: none
     */
    const CREATE_FAILURE = 11;

    /**
     * Callbacks are executed if item could be created successfully.
     * Expected parameters for callback: ID of created item
     */
    const CREATE_SUCCESS = 12;


    /**
     * Callbacks are executed before store process is executed.
     * Expected parameters for callback: Item instance
     */
    const STORE_BEFORE = 20;

    /**
     * Callbacks are executed if store process failed.
     * This is also likely to happen if query would not change anything in database!
     * Expected parameters for callback: Item instance
     */
    const STORE_FAILURE = 21;

    /**
     * Callbacks are executed if store process saved the values in the database.
     * Expected parameters for callback: Item instance
     */
    const STORE_SUCCESS = 22;


    /**
     * Callbacks are executed before deleting an item.
     * Expected parameters for callback: ID of them item to delete
     */
    const DELETE_BEFORE = 30;

    /**
     * Callbacks are executed if deletion of an item fails.
     * Expected parameters for callback: ID of them item to delete
     */
    const DELETE_FAILURE = 31;

    /**
     * Callbacks are executed if item was deleted successfully.
     * Expected parameters for callback: ID of them item to delete
     */
    const DELETE_SUCCESS = 32;

    /**
     * Callback stack.
     * @var    array
     */
    private static $_callbacks = array();

    /**
     * Registers a new callback.
     *
     * Example:
     * cGenericDb::register(cGenericDb::CREATE_SUCCESS, 'itemCreateHandler', 'cApiArticle');
     * cGenericDb::register(cGenericDb::CREATE_SUCCESS, array('cCallbackHandler', 'executeCreateHandle'), 'cApiArticle');
     *
     * @param    string    $event        Callback event, must be a valid value of a cGenericDb event constant
     * @param    mixed    $callback    Callback to register
     * @param    mixed    $class        Class name for registering callback (can be string of array with names of the concrete Item classes)
     *
     * @return    void
     */
    public static function register($event, $callback, $class) {
        if (isset($event) === false) {
            throw new cItemException("No callback event for execution was given");
        }

        if (is_callable($callback) === false) {
            throw new cItemException("Given callback is not callable.");
        }

        if (isset($class) === false) {
            throw new cItemException("No class for registering callback was given.");
        }

        if (is_array($class)) {
            foreach ($class as $className) {
                self::$_callbacks[$className][$event][] = $callback;
            }
        } else {
            self::$_callbacks[$class][$event][] = $callback;
        }
    }

    /**
     * Unregisters all callbacks for a specific event in a class.
     *
     * Example:
     * cGenericDb::unregister(cGenericDb::CREATE_SUCCESS, 'cApiArticle');
     *
     * @param    string    $event    Callback event, must be a valid value of a cGenericDb event constant
     * @param    mixed    $class    Class name for unregistering callback (can be string of array with names of the concrete Item classes)
     *
     * @return    void
     */
    public static function unregister($event, $class) {
        if (isset($event) === false) {
            throw new cItemException("No callback event for execution was given");
        }

        if (isset($class) === false) {
            throw new cItemException("No class for unregistering callbacks was given.");
        }

        if (is_array($class)) {
            foreach ($class as $className) {
                unset(self::$_callbacks[$className][$event]);
            }
        } else {
            unset(self::$_callbacks[$class][$event]);
        }
    }

    /**
     * Executes all callbacks for a specific event in a class.
     *
     * @param    string    $event        Callback event, must be a valid value of a cGenericDb event constant
     * @param    string    $class        Class name for executing callback
     * @param     array    $arguments    Arguments to pass to the callback function
     *
     * @return    void
     */
    protected final function _executeCallbacks($event, $class, $arguments = array()) {
        if (isset($event) === false) {
            throw new cItemException("No callback event for execution was given");
        }

        if (isset($class) === false) {
            throw new cItemException("No class for executing callbacks was given.");
        }

        if (!isset(self::$_callbacks[$class])) {
            return;
        }

        if (!isset(self::$_callbacks[$class][$event])) {
            return;
        }

        foreach (self::$_callbacks[$class][$event] as $callback) {
            call_user_func_array($callback, $arguments);
        }
    }

}

?>