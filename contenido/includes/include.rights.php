<?php

/**
 * Project: CONTENIDO Content Management System Description: CONTENIDO User
 * Rights
 *
 * @package CONTENIDO Backend Includes
 * @version 1.0.2
 * @author unknown
 * @copyright four for business AG <www.4fb.de>
 * @license http://www.contenido.org/license/LIZENZ.txt
 * @link http://www.4fb.de
 * @link http://www.contenido.org
 * @since file available since CONTENIDO release <= 4.6
 */

defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

include_once(cRegistry::getBackendPath() . 'includes/functions.rights.php');

if (!isset($actionarea)) {
    $actionarea = 'area';
}

if (!isset($rights_client)) {
    $rights_client = $client;
    $rights_lang = $lang;
}

if (!is_object($db2)) {
    $db2 = cRegistry::getDb();
}

if (!is_object($oTpl)) {
    $oTpl = new cTemplate();
}
$oTpl->reset();

// build list of rights for all relevant and online areas except "login" and their relevant actions
if (!is_array($right_list)) {
    $areaCollection   = new cApiAreaCollection();
    $navSubCollection = new cApiNavSubCollection();
    $actionCollection = new cApiActionCollection();
    try {
        $areaCollection->select('relevant = 1 AND online = 1 AND name != "login"');
        while ($area = $areaCollection->next()) {
            $right = [
                'perm'     => $area->get('name'),
                'location' => '',
            ];
            // get location
            $navSubCollection->select('idarea = ' . (int)$area->get('idarea'));
            if ($navSub = $navSubCollection->next()) {
                $right['location'] = $navSub->get('location');
            }
            // get relevant actions
            $actions = $actionCollection->select('relevant = 1 AND idarea = ' . (int)$area->get('idarea'));
            while ($action = $actionCollection->next()) {
                $right['action'][] = $action->get('name');
            }
            // insert into list
            if ($area->get('parent_id') == '0') {
                $key = $area->get('name');
            } else {
                $key = $area->get('parent_id');
            }
            $right_list[$key][$area->get('name')] = $right;
        }
    } catch (cDbException $e) {
        $right_list = [];
    } catch (cException $e) {
        $right_list = [];
    }
}

$oTpl->set('s', 'SESS_ID', $sess->id);
$oTpl->set('s', 'ACTION_URL', $sess->url('main.php'));
$oTpl->set('s', 'TYPE_ID', 'userid');
$oTpl->set('s', 'USER_ID', $userid);
$oTpl->set('s', 'AREA', $area);

$oUser = new cApiUser($userid);
$userPerms = $oUser->getField('perms');

ob_start();

$oTpl->set('s', 'RIGHTS_PERMS', $rights_perms);

// Selectbox for clients
$oHtmlSelect = new cHTMLSelectElement('rights_clientslang', '', 'rights_clientslang', false, NULL, "", "vAlignMiddle");

$oClientColl = new cApiClientCollection();
$clientList = $oClientColl->getAccessibleClients();
$firstSel = false;
$firstClientsLang = 0;

$availableClients = array();

foreach ($clientList as $key => $value) {
    $sql = "SELECT * FROM " . $cfg["tab"]["lang"] . " AS A, " . $cfg["tab"]["clients_lang"] . " AS B WHERE B.idclient=" . (int) $key . " AND A.idlang=B.idlang";
    $db->query($sql);

    while ($db->nextRecord()) {

        $idClientsLang = $db->f('idclientslang');

        if ((cString::findFirstPos($userPerms, "client[$key]") !== false) && (cString::findFirstPos($userPerms, "lang[" . $db->f("idlang") . "]") !== false) && ($perm->have_perm("lang[" . $db->f("idlang") . "]"))) {
            if ($firstSel == false) {
                $firstSel = true;
                $firstClientsLang = $idClientsLang;
            }

            if ($rights_clientslang == $idClientsLang) {

                $availableClients[] = array(
                    'idClientsLang' => $idClientsLang,
                    'value_name' => $value['name'],
                    'lang_name' => $db->f('name'),
                    'selected' => 1
                );

                if (!isset($rights_client)) {
                    $firstClientsLang = $idClientsLang;
                }
            } else {
                $availableClients[] = array(
                    'idClientsLang' => $idClientsLang,
                    'value_name' => $value['name'],
                    'lang_name' => $db->f('name'),
                    'selected' => 0
                );
            }
        }
    }
}

// Generate Select Box or simple the value as text
if (count($availableClients) > 1) {

    foreach ($availableClients as $key => $value) {
        $oHtmlSelectOption = new cHTMLOptionElement($availableClients[$key]['value_name'] . ' -> ' . $availableClients[$key]['lang_name'], $availableClients[$key]['idClientsLang'], $availableClients[$key]['selected']);
        $oHtmlSelect->appendOptionElement($oHtmlSelectOption);
    }

    $oTpl->set('s', 'INPUT_SELECT_CLIENT', $oHtmlSelect->render());
} else {
    $string = "<span class='vAlignMiddle'>" . $availableClients[0]['value_name'] . " -> " . $availableClients[0]['lang_name'] . "</span>&nbsp;";
    $oTpl->set('s', 'INPUT_SELECT_CLIENT', $string);
}

if (!isset($rights_clientslang)) {
    $rights_clientslang = $firstClientsLang;
}

if ($area != 'user_content') {
    $oTpl->set('s', 'INPUT_SELECT_RIGHTS', '');
    $oTpl->set('s', 'DISPLAY_RIGHTS', 'none');
} else {
    // Filter for displaying rights
    $oHtmlSelect = new cHTMLSelectElement('filter_rights', '', 'filter_rights');
    $oHtmlSelectOption = new cHTMLOptionElement('--- ' . i18n('All') . ' ---', '', false);
    $oHtmlSelect->addOptionElement(0, $oHtmlSelectOption);
    $oHtmlSelectOption = new cHTMLOptionElement(i18n('Article rights'), 'article', false);
    $oHtmlSelect->addOptionElement(1, $oHtmlSelectOption);
    $oHtmlSelectOption = new cHTMLOptionElement(i18n('Category rights'), 'category', false);
    $oHtmlSelect->addOptionElement(2, $oHtmlSelectOption);
    $oHtmlSelectOption = new cHTMLOptionElement(i18n('Template rights'), 'template', false);
    $oHtmlSelect->addOptionElement(3, $oHtmlSelectOption);
    $oHtmlSelectOption = new cHTMLOptionElement(i18n('Plugin/Other rights'), 'other', false);
    $oHtmlSelect->addOptionElement(4, $oHtmlSelectOption);
    $oHtmlSelect->setEvent('change', 'document.rightsform.submit();');
    $oHtmlSelect->setDefault($_POST['filter_rights']);

    // Set global array which defines rights to display
    $aArticleRights = array(
        'con_syncarticle',
        'con_lock',
        'con_deleteart',
        'con_makeonline',
        'con_makestart',
        'con_duplicate',
        'con_editart',
        'con_newart',
        'con_edit',
        'con_meta_edit',
        'con_meta_deletetype'
    );
    $aCategoryRights = array(
        'con_synccat',
        'con_makecatonline',
        'con_makepublic'
    );
    $aTemplateRights = array(
        'con_changetemplate',
        'con_tplcfg_edit'
    );

    $aViewRights = array();
    $bExclusive = false;
    if (isset($_POST['filter_rights'])) {
        switch ($_POST['filter_rights']) {
            case 'article':
                $aViewRights = $aArticleRights;
                break;
            case 'category':
                $aViewRights = $aCategoryRights;
                break;
            case 'template':
                $aViewRights = $aTemplateRights;
                break;
            case 'other':
                $aViewRights = array_merge($aArticleRights, $aCategoryRights, $aTemplateRights);
                $bExclusive = true;
                break;
            default:
                break;
        }
    }
    $oTpl->set('s', 'INPUT_SELECT_RIGHTS', $oHtmlSelect->render());
    $oTpl->set('s', 'DISPLAY_RIGHTS', 'block');
}

$bEndScript = false;

$oClientLang = new cApiClientLanguage((int) $rights_clientslang);
if ($oClientLang->isLoaded()) {
    $rights_client = $oClientLang->get('idclient');
    $rights_lang = $oClientLang->get('idlang');
    $oTpl->set('s', 'NOTIFICATION', '');
    $oTpl->set('s', 'DISPLAY_FILTER', 'block');
} else {
    $bEndScript = true;
    ob_end_clean();

    // Account is sysadmin
    if (cString::findFirstPos($userPerms, 'sysadmin') !== false) {
        $oTpl->set('s', 'NOTIFICATION', $notification->returnMessageBox('warning', i18n("The selected user is a system administrator. A system administrator has all rights for all clients for all languages and therefore rights can't be specified in more detail."), 0));
    } else if (cString::findFirstPos($userPerms, 'admin[') !== false) {
        // Account is only assigned to clients with admin rights
        $oTpl->set('s', 'NOTIFICATION', $notification->returnMessageBox('warning', i18n("The selected user is assigned to clients as admin, only. An admin has all rights for a client and therefore rights can't be specified in more detail."), 0));
    } else {
        $oTpl->set('s', 'NOTIFICATION', $notification->returnMessageBox('error', i18n("Current user doesn't have any rights to any client/language."), 0));
    }
    $oTpl->set('s', 'DISPLAY_FILTER', 'none');
}

if ($bEndScript != true) {
    $tmp = ob_get_contents();
    ob_end_clean();
    $oTpl->set('s', 'OB_CONTENT', $tmp);
} else {
    $oTpl->set('s', 'OB_CONTENT', '');
}

if ($bEndScript == true) {
    $oTpl->set('s', 'NOTIFICATION_SAVE_RIGHTS', '');
    $oTpl->set('s', 'RIGHTS_CONTENT', '');
    $oTpl->set('s', 'JS_SCRIPT_BEFORE', '');
    $oTpl->set('s', 'JS_SCRIPT_AFTER', '');
    $oTpl->set('s', 'RIGHTS_CONTENT', '');
    $oTpl->set('s', 'EXTERNAL_SCRIPTS', '');
    $oTpl->generate('templates/standard/' . $cfg['templates']['rights']);
    die();
}
