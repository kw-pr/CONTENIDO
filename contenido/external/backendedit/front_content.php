<?php

/**
 * Frontend article view for CONTENIDO backend.
 *
 * @package Backend
 * @subpackage Article
 * @version SVN Revision $Rev:$
 * @version SVN Id $Id$
 *
 * @author unknown, Murat Purc <murat@purc.de>
 * @copyright four for business AG <www.4fb.de>
 * @license http://www.contenido.org/license/LIZENZ.txt
 * @link http://www.4fb.de
 * @link http://www.contenido.org
 */

if (!defined('CON_FRAMEWORK')) {
    define('CON_FRAMEWORK', true);
}

// CONTENIDO startup process
include_once('../../includes/startup.php');

$frontendPath = cRegistry::getFrontendPath();

// if directory does not exist, show error message
if (!is_dir($frontendPath)) {
    $notification = new cGuiNotification();
    $notification->displayMessageBox(cGuiNotification::LEVEL_ERROR, i18n('The given client\'s frontend directory (%s) is not a directory.', $frontendPath));
    exit;
}
chdir($frontendPath);

// Include the config file of the frontend to initialize client and language id
include_once($cfgClient[$client]['config']['path'] . 'config.php');

// Include article view handler
include(cRegistry::getBackendPath() . $cfg['path']['includes'] . '/frontend/include.front_content.php');

?>