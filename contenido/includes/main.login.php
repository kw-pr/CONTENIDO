<?php
/**
 * Project:
 * CONTENIDO Content Management System
 *
 * Description:
 * CONTENIDO Start Screen
 *
 * Requirements:
 * @con_php_req 5.0
 *
 *
 * @package    CONTENIDO Backend Includes
 * @version    1.0.8
 * @author     Jan Lengowski
 * @copyright  four for business AG <www.4fb.de>
 * @license    http://www.contenido.org/license/LIZENZ.txt
 * @link       http://www.4fb.de
 * @link       http://www.contenido.org
 * @since      file available since CONTENIDO release <= 4.6
 *
 * {@internal
 *   created 2003-01-21
 *   $Id$:
 * }}
 */

if (!defined('CON_FRAMEWORK')) {
    die('Illegal call');
}

cInclude('pear', 'XML/Parser.php');
cInclude('pear', 'XML/RSS.php');

$tpl->reset();

$vuser = new cApiUser($auth->auth['uid']);

if ($saveLoginTime == true) {
    $sess->register('saveLoginTime');
    $saveLoginTime = 0;

    $lastTime = $vuser->getUserProperty('system', 'currentlogintime');
    $timestamp = date('Y-m-d H:i:s');
    $vuser->setUserProperty('system', 'currentlogintime', $timestamp);
    $vuser->setUserProperty('system', 'lastlogintime', $lastTime);
}

$lastlogin = displayDatetime($vuser->getUserProperty('system', 'lastlogintime'));
if ($lastlogin == '') {
    $lastlogin = i18n('No Login Information available.');
}

// notification for requested password
$aNotificationText = array();
if ($vuser->getField('using_pw_request') == 1) {
    $aNotificationText[] = $notification->returnNotification('warning', i18n("You're logged in with a temporary password. Please change your password."));
}

// check for active maintenance mode
if (getSystemProperty('maintenance', 'mode') == 'enabled') {
    $aNotificationText[] = $notification->returnNotification('warning', i18n('CONTENIDO is in maintenance mode. Only sysadmins are allowed to login.'));
}

// check for size of log directory
$max_log_size = getSystemProperty('backend', 'max_log_size');
if ($max_log_size === false) {
    $max_log_size = 10;
}
if (in_array('sysadmin', explode(',', $vuser->getEffectiveUserPerms())) && $max_log_size > 0) {
    $log_size = getDirectorySize($cfg['path']['contenido_logs']);
    if ($log_size > $max_log_size * 1024 * 1024) {
        $aNotificationText[] = $notification->returnNotification('warning', i18n('The log directory is bigger than') . ' ' . human_readable_size($max_log_size * 1024 * 1024) . '.' . i18n('Current size') . ': ' . human_readable_size($log_size));
    }
}

$tpl->set('s', 'NOTIFICATION', implode('<br>', $aNotificationText));

$userid = $auth->auth['uid'];

$tpl->set('s', 'WELCOME', '<b>' . i18n('Welcome') . ' </b>' . $vuser->getRealname($userid, true) . '.');
$tpl->set('s', 'LASTLOGIN', i18n('Last login') . ': ' . $lastlogin);

$clients= $classclient->getAccessibleClients();

$cApiClient= new cApiClient();

if (count($clients) > 1) {
    $clientform = '<form style="margin: 0px" name="clientselect" method="post" target="_top" action="' . $sess->url('index.php') . '">';
    $select = new cHTMLSelectElement('changeclient');
    $choices = array();
    $warnings = array();

    foreach ($clients as $key => $v_client) {
        if ($perm->hasClientPermission($key)) {
            $cApiClient->loadByPrimaryKey($key);
            if ($cApiClient->hasLanguages()) {
                $choices[$key] = $v_client['name'] . ' (' . $key . ')';
            } else {
                $warnings[] = sprintf(i18n('Client %s (%s) has no languages'), $v_client['name'], $key);
            }
        }
    }

    $select->autoFill($choices);
    $select->setDefault($client);

    $clientselect = $select->render();

    $tpl->set('s', 'CLIENTFORM', $clientform);
    $tpl->set('s', 'CLIENTFORMCLOSE', '</form>');
    $tpl->set('s', 'CLIENTSDROPDOWN', $clientselect);

    if ($perm->have_perm() && count($warnings) > 0) {
        $tpl->set('s', 'WARNINGS', '<br>' . $notification->messageBox('warning', implode('<br>', $warnings), 0));
    } else {
        $tpl->set('s', 'WARNINGS', '');
    }
    $tpl->set('s', 'OKBUTTON', '<input type="image" src="images/but_ok.gif" alt="' . i18n('Change client') . '" title="' . i18n('Change client') . '" border="0">');
} else {
    $tpl->set('s', 'OKBUTTON', '');
    $sClientForm = '';
    if (count($clients) == 0) {
        $sClientForm = i18n('No clients available!');
    }
    $tpl->set('s', 'CLIENTFORM', $sClientForm);
    $tpl->set('s', 'CLIENTFORMCLOSE', '');

    foreach ($clients as $key => $v_client) {
        if ($perm->hasClientPermission($key)) {
            $cApiClient->loadByPrimaryKey($key);
            if ($cApiClient->hasLanguages()) {
                $name = $v_client['name'] . ' (' . $key . ')';
            } else {
                $warnings[] = sprintf(i18n('Client %s (%s) has no languages'), $v_client['name'], $key);
            }
        }
    }

    if ($perm->have_perm() && count($warnings) > 0) {
        $tpl->set('s', 'WARNINGS', '<br>' . $notification->messageBox('warning', implode('<br>', $warnings), 0));
    } else {
        $tpl->set('s', 'WARNINGS', '');
    }

    $tpl->set('s', 'CLIENTSDROPDOWN', $name);
}

$props = new cApiPropertyCollection();
$props->select("itemtype = 'idcommunication' AND idclient = " . (int) $client . " AND type = 'todo' AND name = 'status' AND value != 'done'");

$todoitems = array();

while ($prop = $props->next()) {
    $todoitems[] = $prop->get('itemid');
}

if (count($todoitems) > 0) {
    $in = 'idcommunication IN (' . implode(',', $todoitems) . ')';
} else {
    $in = 1;
}
$todoitems = new TODOCollection();
$recipient = $auth->auth['uid'];
$todoitems->select("recipient = '$recipient' AND idclient = " . (int) $client . " AND $in");

while ($todo = $todoitems->next()) {
    if ($todo->getProperty('todo', 'status') != 'done') {
        $todoitems++;
    }
}

$sTaskTranslation = '';
if ($todoitems->count() == 1) {
    $sTaskTranslation = i18n('Reminder list: %d Task open');
} else {
    $sTaskTranslation = i18n('Reminder list: %d Tasks open');
}

$mycontenido_overview = '<a class="blue" href="' . $sess->url("main.php?area=mycontenido&frame=4") . '">' . i18n('Overview') . '</a>';
$mycontenido_lastarticles = '<a class="blue" href="' . $sess->url("main.php?area=mycontenido_recent&frame=4") . '">' . i18n('Recently edited articles') . '</a>';
$mycontenido_tasks = '<a class="blue" onclick="sub.highlightById(\'c_1\', top.content.right_top)" href="' . $sess->url("main.php?area=mycontenido_tasks&frame=4") . '">' . sprintf($sTaskTranslation, $todoitems->count()) . '</a>';
$mycontenido_settings = '<a class="blue" onclick="sub.highlightById(\'c_2\', top.content.right_top)" href="' . $sess->url("main.php?area=mycontenido_settings&frame=4") . '">' . i18n('Settings') . '</a>';

$tpl->set('s', 'MYCONTENIDO_OVERVIEW', $mycontenido_overview);
$tpl->set('s', 'MYCONTENIDO_LASTARTICLES', $mycontenido_lastarticles);
$tpl->set('s', 'MYCONTENIDO_TASKS', $mycontenido_tasks);
$tpl->set('s', 'MYCONTENIDO_SETTINGS', $mycontenido_settings);

// Systemadmins list
$sAdminTemplate = '<li class="welcome">%s, %s</li>';
$sOutputAdmin = '';
$userColl = new cApiUserCollection();
$admins = $userColl->fetchSystemAdmins(true);
foreach ($admins as $pos => $item) {
    if ($item->get('email') != '') {
        $sAdminEmail = '<a class="blue" href="mailto:' . $item->get('email') . '">' . $item->get('email') . '</a>';
        $sAdminName = $item->get('realname');
        $sOutputAdmin .= sprintf($sAdminTemplate, $sAdminName, $sAdminEmail);
    }
}

$tpl->set('s', 'ADMIN_EMAIL', $sOutputAdmin);

$tpl->set('s', 'SYMBOLHELP', '<a href="' . $sess->url("frameset.php?area=symbolhelp&frame=4") . '">' . i18n('Symbol help') . '</a>');

if (file_exists($cfg['contenido']['handbook_path'])) {
    $tpl->set('s', 'CONTENIDOMANUAL', '<a href="' . $cfg['contenido']['handbook_url'] . '" target="_blank">' . i18n('CONTENIDO Manual') . '</a>');
} else {
    $tpl->set('s', 'CONTENIDOMANUAL', '');
}

// For display current online user in CONTENIDO-Backend
$aMemberList = array();
$oActiveUsers = new cApiOnlineUserCollection();
$iNumberOfUsers = 0;

// Start()
$oActiveUsers->startUsersTracking();

// Currently User Online
$iNumberOfUsers = $oActiveUsers->getNumberOfUsers();

// Find all User who is online
$aMemberList = $oActiveUsers->findAllUser();

// Template for display current user
$sOutput = '';
$sTemplate = '<li class="welcome">%s, %s</li>';

foreach ($aMemberList as $key) {
    $sRealName = $key['realname'];
    $aPerms['0'] = $key['perms'];
    $sOutput .= sprintf($sTemplate,  $sRealName, $aPerms['0']);
}

// set template welcome
$tpl->set('s', 'USER_ONLINE', $sOutput);
$tpl->set('s', 'NUMBER', $iNumberOfUsers);

// check for new updates
$oUpdateNotifier = new Contenido_UpdateNotifier($cfg, $vuser, $perm, $sess, $belang);
$sUpdateNotifierOutput = $oUpdateNotifier->displayOutput();
$tpl->set('s', 'UPDATENOTIFICATION', $sUpdateNotifierOutput);

$tpl->generate($cfg['path']['templates'] . $cfg['templates']['welcome']);

?>