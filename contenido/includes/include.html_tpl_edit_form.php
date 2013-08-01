<?php
/**
 * This file contains the backend page for editing html template files.
 * @fixme: Rework logic for creation of cApiFileInformation entries
 * It may happpen, that we have already a file but not a entry or vice versa!
 *
 * @package          Core
 * @subpackage       Backend
 * @version          SVN Revision $Rev:$
 *
 * @author           Willi Man
 * @copyright        four for business AG <www.4fb.de>
 * @license          http://www.contenido.org/license/LIZENZ.txt
 * @link             http://www.4fb.de
 * @link             http://www.contenido.org
 */

defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

cInclude('external', 'codemirror/class.codemirror.php');
cInclude('includes', 'functions.file.php');

$sActionCreate = 'htmltpl_create';
$sActionEdit = 'htmltpl_edit';
$sActionDelete = 'htmltpl_delete';
$sFilename = '';

$page = new cGuiPage('html_tpl_edit_form');

$tpl->reset();

if (!$perm->have_perm_area_action($area, $action)) {
    $notification->displayNotification('error', i18n('Permission denied'));
    return;
}

if (!(int) $client > 0) {
    // If there is no client selected, display empty page
    $page->render();
    return;
}

if ($action == $sActionDelete) {

    $path = $cfgClient[$client]['tpl']['path'];
    // delete file
    // TODO also delete the versioning files
    if (!strrchr($_REQUEST['delfile'], '/')) {
        if (cFileHandler::exists($path . $_REQUEST['delfile'])) {
            $fileInfoCollection = new cApiFileInformationCollection();

            $fileIds = $fileInfoCollection->getIdsByWhereClause("`filename`='" . cSecurity::toString($_REQUEST["delfile"]) . "'");

            if (cSecurity::isInteger($fileIds[0]) && is_dir($cfgClient[$client]['version']['path'] . "templates/" . $fileIds[0])) {
                cFileHandler::recursiveRmdir($cfgClient[$client]['version']['path'] . "templates/" . $fileIds[0]);

                $fileInfoCollection->removeFileInformation(array(
                    'idclient' => cSecurity::toInteger($client),
                    'filename' => cSecurity::toString($_REQUEST['delfile']),
                    'type' => 'templates'
                ));
            }

            unlink($path . cSecurity::toString($_REQUEST['delfile']));

            $page->displayInfo(i18n('Deleted template file successfully!'));
        }
    }
    $sReloadScript = "<script type=\"text/javascript\">
        var left_bottom = parent.parent.frames['left'].frames['left_bottom'];
        if (left_bottom) {
            var href = left_bottom.location.href;
            href = href.replace(/&file[^&]*/, '');
            left_bottom.location.href = href+'&file='+'" . $sFilename . "';
        }
    </script>";

    $page->addScript($sReloadScript);
    $page->render();

} else {

    $path = $cfgClient[$client]['tpl']['path'];

    $sTempFilename = stripslashes($_REQUEST['tmp_file']);
    $sOrigFileName = $sTempFilename;

    // determine allowed extensions for template files in client template folder
    $allowedExtensions = cRegistry::getConfigValue('client_template', 'allowed_extensions', 'html');
    $allowedExtensions = explode(',', $allowedExtensions);
    $allowedExtensions = array_map('trim', $allowedExtensions);

    $sFilename = $_REQUEST['file'];
    if (!in_array(cFileHandler::getExtension($sFilename), $allowedExtensions) && strlen(stripslashes(trim($sFilename))) > 0) {
        // determine default extension for new template files
        $defaultExtension = cRegistry::getConfigValue('client_template', 'default_extension', 'html');
        $sFilename = stripslashes($sFilename) . '.' . $defaultExtension;
    } else {
        $sFilename = stripslashes($sFilename);
    }

    if (stripslashes($_REQUEST['file'])) {
        $sReloadScript = "<script type=\"text/javascript\">
                             var left_bottom = parent.parent.frames['left'].frames['left_bottom'];
                             if (left_bottom) {
                                 var href = left_bottom.location.href;
                                 href = href.replace(/&file[^&]*/, '');
                                 left_bottom.location.href = href+'&file='+'" . $sFilename . "';
                             }
                         </script>";
    } else {
        $sReloadScript = '';
    }

    // Content Type is template
    $sTypeContent = 'templates';

    // Create new file
    if ($_REQUEST['action'] == $sActionCreate && $_REQUEST['status'] == 'send') {
        $sTempFilename = $sFilename;
        // check filename and create new file
        cFileHandler::validateFilename($sFilename);
        cFileHandler::create($path . $sFilename, $_REQUEST['code']);
        $bEdit = cFileHandler::read($path . $sFilename);
        $sReloadScript .= "<script type=\"text/javascript\">
                 var right_top = top.content.right.right_top;
                 if (right_top) {
                     var href = '" . $sess->url("main.php?area=$area&frame=3&file=$sTempFilename") . "';
                     right_top.location.href = href;
                 }
                 </script>";
        $fileInfoCollection = new cApiFileInformationCollection();
        $fileInfoCollection->create('templates', $sFilename, $_REQUEST['description']);

        $page->displayInfo(i18n('Created new template file successfully!'));
    }

    // Edit selected file
    if ($_REQUEST['action'] == $sActionEdit && $_REQUEST['status'] == 'send') {
        $sTempTempFilename = $sTempFilename;
        if ($sFilename != $sTempFilename) {
            cFileHandler::validateFilename($sFilename);
            if (cFileHandler::rename($path . $sTempFilename, $sFilename)) {
                $sTempFilename = $sFilename;
            } else {
                $notification->displayNotification('error', sprintf(i18n('Can not rename file %s'), $path . $sTempFilename));
                exit();
            }
            $sReloadScript .= "<script type=\"text/javascript\">
                 var right_top = top.content.right.right_top;
                 if (right_top) {
                     var href = '" . $sess->url("main.php?area=$area&frame=3&file=$sTempFilename") . "';
                     right_top.location.href = href;
                 }
                 </script>";
        } else {
            $sTempFilename = $sFilename;
        }

        if ($sFilename != $sTempTempFilename) {
            $page->displayInfo(i18n('Renamed template file successfully!'));
        } else {
            $page->displayInfo(i18n('Saved changes successfully!'));
        }

        $fileInfoCollection = new cApiFileInformationCollection();
        $aFileInfo = $fileInfoCollection->getFileInformation($sOrigFileName, $sTypeContent);

        // @fixme: Rework logic. Even if we have already a file, there may be no
        // db entry available!
        if (0 == count($aFileInfo)) {
            // No entry, create it
            $fileInfoCollection->create('templates', $sFilename, $_REQUEST['description']);
        }

        // @fixme: Check condition below, how is it possible to have an db entry
        // with primary key?
        if ((count($aFileInfo) > 0) && ($aFileInfo['idsfi'] != '')) {
            $oVersion = new cVersionFile($aFileInfo['idsfi'], $aFileInfo, $sFilename, $sTypeContent, $cfg, $cfgClient, $db, $client, $area, $frame, $sOrigFileName);
            // Create new Layout Version in cms/version/css/ folder
            $oVersion->createNewVersion();
        }

        // @fixme: no need to update if it was created before (see code above)
        $fileInfoCollection = new cApiFileInformationCollection();
        $fileInfoCollection->updateFile($sOrigFileName, 'templates', $_REQUEST['description'], $sFilename);

        // Track version
        $sTypeContent = 'templates';

        cFileHandler::validateFilename($sFilename);
        cFileHandler::write($path . $sFilename, $_REQUEST['code']);
        $bEdit = cFileHandler::read($path . $sFilename);
    }

    // Generate edit form
    if (isset($_REQUEST['action'])) {
        $sAction = ($bEdit)? $sActionEdit : $_REQUEST['action'];

        if ($_REQUEST['action'] == $sActionEdit) {
            $sCode = cFileHandler::read($path . $sFilename);
            if ($sCode === false) {
                exit();
            }
        } else {
            // stripslashes is required here in case of creating a new file
            $sCode = stripslashes($_REQUEST['code']);
        }

        // Try to validate html
        if (getEffectiveSetting('layout', 'htmlvalidator', 'true') == 'true' && $sCode !== '') {
            $v = new cHTMLValidator();
            $v->validate($sCode);
            $msg = '';

            foreach ($v->missingNodes as $value) {
                $idqualifier = '';

                $attr = array();

                if ($value['name'] != '') {
                    $attr['name'] = "name '" . $value['name'] . "'";
                }

                if ($value['id'] != '') {
                    $attr['id'] = "id '" . $value['id'] . "'";
                }

                $idqualifier = implode(', ', $attr);

                if ($idqualifier != '') {
                    $idqualifier = "($idqualifier)";
                }
                $msg .= sprintf(i18n("Tag '%s' %s has no end tag (start tag is on line %s char %s)"), $value['tag'], $idqualifier, $value['line'], $value['char']) . '<br>';
            }

            if ($msg != '') {
                $page->displayWarning($msg);
            }
        }

        $fileInfoCollection = new cApiFileInformationCollection();
        $aFileInfo = $fileInfoCollection->getFileInformation($sTempFilename, $sTypeContent);

        $form = new cGuiTableForm('file_editor');
        $form->addHeader(i18n('Edit file'));
        $form->setVar('area', $area);
        $form->setVar('action', $sAction);
        $form->setVar('frame', $frame);
        $form->setVar('status', 'send');
        $form->setVar('tmp_file', $sTempFilename);

        $tb_name = new cHTMLTextbox('file', $sFilename, 60);
        $ta_code = new cHTMLTextarea('code', conHtmlSpecialChars($sCode), 100, 35, 'code');
        $descr = new cHTMLTextarea('description', conHtmlSpecialChars($aFileInfo['description']), 100, 5);

        $ta_code->setStyle('font-family: monospace;width: 100%;');
        $descr->setStyle('font-family: monospace;width: 100%;');
        $ta_code->updateAttributes(array(
            'wrap' => getEffectiveSetting('html_editor', 'wrap', 'off')
        ));

        $form->add(i18n('Name'), $tb_name);
        $form->add(i18n('Description'), $descr->render());
        $form->add(i18n('Code'), $ta_code);

        $page->setContent($form);

        $oCodeMirror = new CodeMirror('code', 'html', substr(strtolower($belang), 0, 2), true, $cfg);
        $page->addScript($oCodeMirror->renderScript());

        if (!empty($sReloadScript)) {
            $page->addScript($sReloadScript);
        }
        $page->render();

    } else {

        $page = new cGuiPage('generic_page');
        $page->setContent('');
        $page->render();

    }

}
