<?php
/**
 * Project:
 * CONTENIDO Content Management System
 *
 * Description:
 * File manager
 *
 * Requirements:
 * @con_php_req 5.0
 *
 *
 * @package    CONTENIDO Backend Includes
 * @version    1.6.0
 * @author     Timo A. Hummel
 * @copyright  four for business AG <www.4fb.de>
 * @license    http://www.contenido.org/license/LIZENZ.txt
 * @link       http://www.4fb.de
 * @link       http://www.contenido.org
 * @since      file available since CONTENIDO release <= 4.6
 *
 * {@internal
 *   created 2003-12-29
 *   $Id$:
 * }}
 */

if (!defined('CON_FRAMEWORK')) {
    die('Illegal call');
}


cInclude('includes', 'api/functions.frontend.list.php');
cInclude('includes', 'functions.file.php');

if (!(int) $client > 0) {
    // if there is no client selected, display empty page
    $oPage = new cPage;
    $oPage->render();
    return;
}

$appendparameters = $_REQUEST["appendparameters"];
$file = $_REQUEST['file'];

if (!is_array($browserparameters) && ($appendparameters != "imagebrowser" || $appendparameters != "filebrowser")) {
    $browserparameters = array();
}

if (!$sess->is_registered("upl_last_path")) {
    $upl_last_path = $path;
    $sess->register("upl_last_path");
} elseif (!isset($path)) {
    $path = $upl_last_path;
}
$upl_last_path = $path;

$uploads = new cApiUploadCollection();

$dbfs = new cApiDbfsCollection();

if (cApiDbfs::isDbfs($path)) {
    $qpath = $path . "/";
} else {
    $qpath = $path;
}

if ($path && $action != '') {
    $sReloadScript = "<script type=\"text/javascript\">
                         var left_bottom = parent.parent.frames['left'].frames['left_bottom'];
                         if (left_bottom) {
                             var href = left_bottom.location.href;
                             href = href.replace(/&path.*/, '');
                             left_bottom.location.href = href+'&path='+'".$path."';
                             if (window.top.content.left.left_top.refresh()) {
                                 top.content.left.left_top.refresh();
                             }
                         }
                     </script>";
} else {
    $sReloadScript = "";
}

if ((is_writable($cfgClient[$client]["upl"]["path"].$path) || cApiDbfs::isDbfs($path)) && (int) $client > 0) {
    $bDirectoryIsWritable = true;
} else {
    $bDirectoryIsWritable = false;
}

if ($action == "upl_modify_file") {
    // Did the user upload a new file?
    if ($bDirectoryIsWritable == true && count($_FILES) == 1 && ($_FILES["file"]["size"] > 0) && ($_FILES["file"]["name"] != "")) {
        if ($_FILES['file']['tmp_name'] != "") {
            $tmp_name = $_FILES['file']['tmp_name'];
            $_cecIterator = $_cecRegistry->getIterator("Contenido.Upload.UploadPreprocess");

            if ($_cecIterator->count() > 0) {
                // Copy file to a temporary location
                move_uploaded_file($tmp_name, $cfg["path"]["contenido"] . $cfg["path"]["temp"].$file);
                $tmp_name = $cfg["path"]["contenido"] . $cfg["path"]["temp"].$file;

                while ($chainEntry = $_cecIterator->next()) {
                    if (cApiDbfs::isDbfs($path)) {
                        $sPathPrepend = '';
                        $sPathApppend = '/';
                    } else {
                        $sPathPrepend = $cfgClient[$client]['upl']['path'];
                        $sPathApppend = '';
                    }

                    $modified = $chainEntry->execute($tmp_name, $sPathPrepend.$path.$sPathApppend.uplCreateFriendlyName($_FILES['file']['name']));

                    if ($modified !== false) {
                        $tmp_name = $modified;
                    }
                }
            }

            if (cApiDbfs::isDbfs($path)) {
                $dbfs->writeFromFile($tmp_name, $qpath.$file);
                unlink($_FILES['file']['tmp_name']);
            } else {
                unlink($cfgClient[$client]['upl']['path'].$path.$file);

                if (is_uploaded_file($tmp_name)) {
                    move_uploaded_file($tmp_name, $cfgClient[$client]['upl']['path'].$path.$file);
                } else {
                    rename($tmp_name, $cfgClient[$client]['upl']['path'].$path.$file);
                }
            }
        }
    }

    $uploads->select("idclient = '$client' AND dirname = '$qpath' AND filename='$file'");
    $upload = $uploads->next();

    //$upload->set("description", stripslashes($description));
    $upload->store();

    $properties = new cApiPropertyCollection();
    $properties->setValue("upload", $qpath.$file, "file", "protected", stripslashes($protected));

    $bTimeMng = (isset($_REQUEST['timemgmt']) && strlen($_REQUEST['timemgmt']) > 1);
    $properties->setValue("upload", $qpath . $file, "file", "timemgmt", ($bTimeMng) ? 1 : 0);
    if ($bTimeMng) {
        $properties->setValue("upload", $qpath . $file, "file", "datestart", $_REQUEST['datestart']);
        $properties->setValue("upload", $qpath . $file, "file", "dateend", $_REQUEST['dateend']);
    }

    $iIdupl = $upload->get("idupl");
    if (!empty($iIdupl) && $iIdupl > 0) {
        // check for new entry:
        $sSql = "SELECT id_uplmeta FROM " . $cfg['tab']['upl_meta'] . " WHERE idupl = $iIdupl AND idlang = $lang " .
                "LIMIT 0, 1";
        $db->query($sSql);
        if ($db->num_rows() == 0) {    // new entry
            //$iNextId = $db->nextid($cfg['tab']['upl_meta']);
            $sSql = "INSERT INTO " . $cfg['tab']['upl_meta'] . " " .
                    "SET idupl = $iIdupl, idlang = $lang, " .
                    "medianame = '" . cSecurity::filter($medianame, $db) . "', " .
                    "description = '" . cSecurity::filter($description, $db) . "', " .
                    "keywords = '" . cSecurity::filter($keywords, $db) . "', " .
                    "internal_notice = '" . cSecurity::filter($medianotes, $db) . "', " .
                    "copyright = '" . cSecurity::filter($copyright, $db) . "', " .
                    "author = '" . $auth->auth['uid'] . "', " .
                    "created = NOW(), modified = NOW(), modifiedby = '" . $auth->auth['uid'] . "'";
        } else {    // update entry
            $db->next_record();
            $iIduplmeta = $db->f('id_uplmeta');
            $sSql = "UPDATE " . $cfg['tab']['upl_meta'] . " " .
                    "SET " .
                    "medianame = '" . cSecurity::filter($medianame, $db) . "', " .
                    "description = '" . cSecurity::filter($description, $db) . "', " .
                    "keywords = '" . cSecurity::filter($keywords, $db) . "', " .
                    "internal_notice = '" . cSecurity::filter($medianotes, $db) . "', " .
                    "copyright = '" . cSecurity::filter($copyright, $db) . "', " .
                    "modified = NOW(), modifiedby = '" . $auth->auth['uid'] . "' " .
                    "WHERE id_uplmeta = " . $iIduplmeta;
        }
        $db->query($sSql);
    }
}

if ($action == "upl_multidelete" && $perm->have_perm_area_action($area, $action) && $bDirectoryIsWritable == true) {
    if (is_array($fdelete)) {
        // Check if it is in the upload table
        foreach ($fdelete as $file) {
            $uploads->select("idclient = '$client' AND dirname='$qpath' AND filename='$file'");
            if ($item = $uploads->next()) {
                if (cApiDbfs::isDbfs($qpath)) {
                    $dbfs->remove($qpath.$file);
                } else {
                    unlink($cfgClient[$client]['upl']['path'].$qpath.$file);
                }

                // Call chain
                $_cecIterator = $_cecRegistry->getIterator("Contenido.Upl_edit.Delete");
                if ($_cecIterator->count() > 0) {
                    while ($chainEntry = $_cecIterator->next()) {
                        $chainEntry->execute($item->get('idupl'), $qpath, $file);
                    }

                }
            }
        }
    }
}

if ($action == "upl_delete" && $perm->have_perm_area_action($area, $action) && $bDirectoryIsWritable == true) {
    $uploads->select("idclient = '$client' AND dirname='$qpath' AND filename='$file'");
     // FIXME  Code is similar/redundant to cApiUploadCollection->delete(), in previous version from UploadCollection->delete() too
    if ($uploads->next()) {
        if (cApiDbfs::isDbfs($qpath)) {
            $dbfs->remove($qpath.$file);
        } else {
            unlink($cfgClient[$client]['upl']['path'].$qpath.$file);
        }

        // Call chain
        $_cecIterator = $_cecRegistry->getIterator("Contenido.Upl_edit.Delete");
        if ($_cecIterator->count() > 0) {
            while ($chainEntry = $_cecIterator->next()) {
                $chainEntry->execute($uploads->f('idupl'), $qpath, $file);
            }

        }
    }
}

if ($action == "upl_upload" && $bDirectoryIsWritable == true) {
    if (count($_FILES) == 1) {
        foreach ($_FILES['file']['name'] as $key => $value) {
            if(is_utf8($_FILES['file']['name'][$key])) {
                $_FILES['file']['name'][$key] = utf8_decode($_FILES['file']['name'][$key]);
            }
            if ($_FILES['file']['tmp_name'][$key] != "") {
                $tmp_name = $_FILES['file']['tmp_name'][$key];
                $_cecIterator = $_cecRegistry->getIterator("Contenido.Upload.UploadPreprocess");

                if ($_cecIterator->count() > 0) {
                    // Copy file to a temporary location
                    move_uploaded_file($tmp_name, $cfg["path"]["contenido"] . $cfg["path"]["temp"].$_FILES['file']['name'][$key]);
                    $tmp_name = $cfg["path"]["contenido"] . $cfg["path"]["temp"].$_FILES['file']['name'][$key];

                    while ($chainEntry = $_cecIterator->next()) {
                        if (cApiDbfs::isDbfs($path)) {
                            $sPathPrepend = '';
                            $sPathApppend = '/';
                        } else {
                            $sPathPrepend = $cfgClient[$client]['upl']['path'];
                            $sPathApppend = '';
                        }

                        $modified = $chainEntry->execute($tmp_name, $sPathPrepend.$path.$sPathApppend.uplCreateFriendlyName($_FILES['file']['name'][$key]));
                        if ($modified !== false) {
                            $tmp_name = $modified;
                        }
                    }
                }

                if (cApiDbfs::isDbfs($qpath)) {
                    $dbfs->writeFromFile($tmp_name, $qpath.uplCreateFriendlyName($_FILES['file']['name'][$key]));
                    unlink($tmp_name);
                } else {
                    if (is_uploaded_file($tmp_name)) {
                        $final_filename = $cfgClient[$client]['upl']['path'].$path.uplCreateFriendlyName($_FILES['file']['name'][$key]);

                        move_uploaded_file($tmp_name, $final_filename);

                        $iterator = $_cecRegistry->getIterator("Contenido.Upload.UploadPostprocess");
                        while ($chainEntry = $iterator->next()) {
                            $chainEntry->execute($final_filename);
                        }
                    } else {
                        rename($tmp_name, $cfgClient[$client]['upl']['path'].$path.uplCreateFriendlyName($_FILES['file']['name'][$key]));
                    }
                }
            }
        }
    }
}

if ($action == "upl_renamefile" && $bDirectoryIsWritable == true) {
    $newname = str_replace("/", "", $newname);
    rename($cfgClient[$client]['upl']['path'].$path.$oldname, $cfgClient[$client]['upl']['path'].$path.$newname);
}

class UploadList extends FrontendList
{
    var $dark;
    var $size;

    function convert($field, $data)
    {
        global $cfg, $path, $sess, $cfgClient, $client, $appendparameters;

        if ($field == 4) {
            return human_readable_size($data);
        }

        if ($field == 3) {
            if ($appendparameters == "imagebrowser" || $appendparameters == "filebrowser") {
                if (cApiDbfs::isDbfs($path.'/'.$data)) {
                    $mstr = '<a href="javascript://" onclick="javascript:parent.parent.frames[\'left\'].frames[\'left_top\'].document.getElementById(\'selectedfile\').value= \''.$cfgClient[$client]['htmlpath']['frontend'].'dbfs.php?file='.$path.'/'.$data.'\'; window.returnValue=\''.$cfgClient[$client]['htmlpath']['frontend'].'dbfs.php?file='.$path.'/'.$data.'\'; window.close();"><img src="'.$cfg["path"]["contenido_fullhtml"].$cfg["path"]["images"].'but_ok.gif" title="'.i18n("Use file").'">&nbsp;'.$data.'</a>';
                } else {
                    $mstr = '<a href="javascript://" onclick="javascript:parent.parent.frames[\'left\'].frames[\'left_top\'].document.getElementById(\'selectedfile\').value= \''.$cfgClient[$client]['htmlpath']['frontend'].$cfgClient[$client]["upl"]["frontendpath"].$path.$data.'\'; window.returnValue=\''.$cfgClient[$client]['htmlpath']['frontend'].$cfgClient[$client]["upl"]["frontendpath"].$path.$data.'\'; window.close();"><img src="'.$cfg["path"]["contenido_fullhtml"].$cfg["path"]["images"].'but_ok.gif" title="'.i18n("Use file").'">&nbsp;'.$data.'</a>';
                }
            } else {
                $tmp_mstr = '<a onmouseover="this.style.cursor=\'pointer\'" href="javascript:conMultiLink(\'%s\', \'%s\', \'%s\', \'%s\')">%s</a>';
                $mstr = sprintf($tmp_mstr, 'right_bottom',
                                $sess->url("main.php?area=upl_edit&frame=4&path=$path&file=$data&appendparameters=$appendparameters&startpage=".$_REQUEST['startpage']."&sortby=".$_REQUEST['sortby']."&sortmode=".$_REQUEST['sortmode']."&thumbnailmode=".$_REQUEST['thumbnailmode']),
                                'right_top',
                                $sess->url("main.php?area=upl&frame=3&path=$path&file=$data"),
                                $data);
            }
            return $mstr;
        }

        if ($field == 5) {
            return uplGetFileTypeDescription($data);
        }

        if ($field == 2) {
            // If this file is an image, try to open
            $fileType = strtolower(getFileType($data));
            switch ($fileType) {
                case "png":
                case "gif":
                case "tiff":
                case "bmp":
                case "jpeg":
                case "jpg":
                case "bmp":
                case "iff":
                case "xbm":
                case "wbmp":
                    $sCacheThumbnail = uplGetThumbnail($data, 150);
                    $sCacheName = substr($sCacheThumbnail, strrpos($sCacheThumbnail, "/")+1, strlen($sCacheThumbnail)-(strrchr($sCacheThumbnail, '/')+1));
                    $sFullPath = $cfgClient[$client]['cache_path'].$sCacheName;
                    if (cFileHandler::exists($sFullPath)) {
                        $aDimensions = getimagesize($sFullPath);
                        $iWidth = $aDimensions[0];
                        $iHeight = $aDimensions[1];
                    } else {
                        $iWidth = 0;
                        $iHeight = 0;
                    }

                    if (cApiDbfs::isDbfs($data)) {
                        $retValue =
                            '<a href="javascript:iZoom(\''.$sess->url($cfgClient[$client]["path"]["htmlpath"]."dbfs.php?file=".$data).'\');">
                                <img class="hover" name="smallImage" onmouseover="correctPosition(this, '.$iWidth.', '.$iHeight.');" onmouseout="if (typeof(previewHideIe6) == \'function\') {previewHideIe6(this)}" src="'.$sCacheThumbnail.'">
                                <img class="preview" name="prevImage" src="'.$sCacheThumbnail.'">
                            </a>';
                        return $retValue;
                    } else {
                        $retValue =
                            '<a href="javascript:iZoom(\''.$cfgClient[$client]["path"]["htmlpath"].$cfgClient[$client]["upload"].$data.'\');">
                                <img class="hover" name="smallImage" onmouseover="correctPosition(this, '.$iWidth.', '.$iHeight.');" onmouseout="if (typeof(previewHideIe6) == \'function\') {previewHideIe6(this)}" src="'.$sCacheThumbnail.'">
                                <img class="preview" name="prevImage" src="'.$sCacheThumbnail.'">
                            </a>';
                        $retValue .= '<a href="javascript:iZoom(\''.$cfgClient[$client]["path"]["htmlpath"].$cfgClient[$client]["upload"].$data.'\');"><img class="preview" name="prevImage" src="'.$sCacheThumbnail.'"></a>';
                        return $retValue;
                    }
                    break;
                default:
                    $sCacheThumbnail = uplGetThumbnail($data, 150);
                    return '<img class="hover_none" name="smallImage" src="'.$sCacheThumbnail.'">';
            }
        }

        return $data;
    }
}

function uplRender($path, $sortby, $sortmode, $startpage = 1,$thumbnailmode)
{
    global $cfg, $client, $cfgClient, $area, $frame, $sess, $browserparameters, $appendparameters, $perm, $auth, $sReloadScript, $notification, $bDirectoryIsWritable;

    if ($sortby == "") {
        $sortby = 3;
        $sortmode = "ASC";
    }

    if ($startpage == "") {
        $startpage = 1;
    }

    $thisfile = $sess->url("main.php?idarea=$area&frame=$frame&path=$path&thumbnailmode=$thumbnailmode&appendparameters=$appendparameters");
    $scrollthisfile = $thisfile."&sortmode=$sortmode&sortby=$sortby&appendparameters=$appendparameters";

    if ($sortby == 3 && $sortmode == "DESC") {
        $fnsort = '<a class="gray" href="'.$thisfile. '&sortby=3&sortmode=ASC&startpage='.$startpage.'">'.i18n("Filename / Description").'<img src="images/sort_down.gif" border="0"></a>';
    } else {
        if ($sortby == 3) {
            $fnsort = '<a class="gray" href="'.$thisfile. '&sortby=3&sortmode=DESC&startpage='.$startpage.'">'.i18n("Filename / Description").'<img src="images/sort_up.gif" border="0"></a>';
        } else {
            $fnsort = '<a class="gray" href="'.$thisfile. '&sortby=3&sortmode=ASC&startpage='.$startpage.'">'.i18n("Filename / Description").'</a>';
        }
    }

    if ($sortby == 5 && $sortmode == "DESC") {
        $sizesort = '<a class="gray" href="'.$thisfile. '&sortby=5&sortmode=ASC&startpage='.$startpage.'">'.i18n("Size").'<img src="images/sort_down.gif" border="0"></a>';
    } else {
        if ($sortby == 5) {
            $sizesort = '<a class="gray" href="'.$thisfile. '&sortby=5&sortmode=DESC&startpage='.$startpage.'">'.i18n("Size").'<img src="images/sort_up.gif" border="0"></a>';
        } else {
            $sizesort = '<a class="gray" href="'.$thisfile. '&sortby=5&sortmode=ASC&startpage='.$startpage.'">'.i18n("Size")."</a>";
        }
    }

    if ($sortby == 6 && $sortmode == "DESC") {
        $typesort = '<a class="gray" href="'.$thisfile. '&sortby=6&sortmode=ASC&startpage='.$startpage.'">'.i18n("Type").'<img src="images/sort_down.gif" border="0"></a>';
    } else {
        if ($sortby == 6) {
            $typesort = '<a class="gray" class="gray" href="'.$thisfile. '&sortby=6&sortmode=DESC&startpage='.$startpage.'">'.i18n("Type").'<img src="images/sort_up.gif" border="0"></a>';
        } else {
            $typesort = '<a class="gray" href="'.$thisfile. '&sortby=6&sortmode=ASC&startpage='.$startpage.'">'.i18n("Type")."</a>";
        }
    }

    // Multiple deletes at top of table
    if ($perm->have_perm_area_action("upl", "upl_multidelete") && $bDirectoryIsWritable == true) {
        $sConfirmation = "box.confirm('".i18n('Delete Files')."', '".i18n('Are you sure you want to delete the selected files?')."', 'document.del.action.value = \\\\'upl_multidelete\\\\'; document.del.submit()');";
        $sDelete = '<a href="javascript:'.$sConfirmation.'"><img src="images/delete.gif" style="vertical-align:middle; margin-right:10px;" title="'.i18n("Delete selected files").'" alt="'.i18n("Delete selected files").'" onmouseover="this.style.cursor=\'pointer\'">'.i18n("Delete selected files").'</a>';
    } else {
        $sDelete = '';
    }

    if (cApiDbfs::isDbfs($path)) {
        $mpath = $path."/";
    } else {
        $mpath = "upload/".$path;
    }

    $sDisplayPath = generateDisplayFilePath($mpath, 85);

    $sToolsRow = '<tr>
                    <th colspan="6" style="border-bottom: 1px solid #b3b3b3; height:20px; line-height:20px; vertical-align:middle; text-align:right; adding-left:5px;" id="cat_navbar">
                        <div style="float:left; heigth:20px; line-height:20px; vertical-align:middle; width:400px; padding:0px 5px; text-align:left;">
                            <a href="javascript:invertSelection();"><img style="margin-right:10px; vertical-align:middle;" src="images/but_invert_selection.gif" title="'.i18n("Flip Selection").'" alt="'.i18n("Flip Selection").'" onmouseover="this.style.cursor=\'pointer\'"> '.i18n("Flip Selection").'</a>
                            <span style="padding-left:15px;">&nbsp;</span>
                            '.$sDelete.'
                        </div>

                        '.i18n("Path:")." ". $sDisplayPath.'

                        <div style="clear:both;"></div>
                    </th>
                </tr>';
    $sSpacedRow = '<tr height="10">
                        <td colspan="6" style="border-bottom-width: 0px;"></td>
                   </tr>';


    // List wraps

    $pagerwrap = '<tr>
                    <th colspan="6" style="border-top-width: 1px; border-bottom: 1px solid #b3b3b3; padding-left:5px;" id="cat_navbar">
                        <div style="float:right; heigth:20px; line-height:20px; vertical-align:middle; width:100px; padding:0px 5px; text-align:right;">-C-SCROLLRIGHT-</div>
                        <div style="float:right; heigth:20px; line-height:20px; vertical-align:middle; width:100px; padding:0px 5px; text-align:right;">-C-PAGE-</div>
                        <div style="float:right; heigth:20px; line-height:20px; vertical-align:middle; width:100px; padding:0px 5px; text-align:right;">-C-SCROLLLEFT-</div>
                        <span style="margin-right:10px; line-height:20px; vertical-align:middle;">'.i18n("Files per Page").'</span> -C-FILESPERPAGE-
                        <div style="clear:both;"></div>
                    </th>
                </tr>';

    $startwrap = '<table class="hoverbox generic" cellspacing="0" cellpadding="2" border="0">
                    '.$pagerwrap.$sSpacedRow.$sToolsRow.$sSpacedRow.'
                   <tr>
                        <th align="left" valign="top" style="white-space:nowrap;" nowrap="nowrap">'.i18n("Mark").'</th>
                        <th align="left" valign="top" style="white-space:nowrap;" nowrap="nowrap">'.i18n("Preview").'</th>
                        <th width="100%" align="left" valign="top" style="white-space:nowrap;" nowrap="nowrap">'.$fnsort.'</th>
                        <th align="left" valign="top" style="white-space:nowrap;" nowrap="nowrap">'.$sizesort.'</th>
                        <th align="left" valign="top" style="white-space:nowrap;" nowrap="nowrap">'.$typesort.'</th>
                        <th align="left" valign="top" style="white-space:nowrap;" nowrap="nowrap">'.i18n("Actions").'</th>
                    </tr>';
    $itemwrap = '<tr>
                        <td align="center" valign="top" class="text_medium" style="white-space:nowrap;" nowrap="nowrap">%s</td>
                        <td align="left" valign="top" class="text_medium" style="white-space:nowrap;" nowrap="nowrap">%s</td>
                        <td align="left" valign="top" class="text_medium" style="white-space:nowrap;" width="200" nowrap="nowrap">%s</td>
                        <td align="left" valign="top" class="text_medium" style="white-space:nowrap;" width="60" nowrap="nowrap">%s</td>
                        <td align="left" valign="top" class="text_medium" style="white-space:nowrap;" width="60" nowrap="nowrap">%s</td>
                        <td align="left" valign="top" class="text_medium" style="white-space:nowrap;" width="75" nowrap="nowrap">%s</td>
                    </tr>';
    $endwrap = $sSpacedRow.$sToolsRow.$sSpacedRow.$pagerwrap.'</table>';

    // Object initializing
    $page = new UI_Page();
    $page->addScript('reloadscript', $sReloadScript);
    $list2 = new UploadList($startwrap, $endwrap, $itemwrap);

    $uploads = new cApiUploadCollection();

    // Fetch data
    if (substr($path,strlen($path)-1,1) != "/") {
        if ($path != "") {
            $qpath = $path . "/";
        }
    } else {
        $qpath = $path;
    }

    $uploads->select("idclient = '$client' AND dirname = '$qpath'");

    $user = new cApiUser($auth->auth["uid"]);

    if ($thumbnailmode == '') {
        $current_mode = $user->getUserProperty('upload_folder_thumbnailmode', md5($path));
        if ($current_mode != '') {
            $thumbnailmode = $current_mode;
        } else {
            $thumbnailmode = getEffectiveSetting('backend','thumbnailmode',100);
        }
    }

    switch ($thumbnailmode) {
        case 25: $numpics = 25; break;
        case 50: $numpics = 50; break;
        case 100:$numpics = 100; break;
        case 200:$numpics = 200; break;
        default: $thumbnailmode = 100;
                 $numpics = 15;
                 break;
    }

    $user->setUserProperty('upload_folder_thumbnailmode', md5($path), $thumbnailmode);

    $list2->setResultsPerPage($numpics);

    $list2->size = $thumbnailmode;

    $rownum = 0;

    $properties = new cApiPropertyCollection();

    while ($item = $uploads->next()) {
        $filename = $item->get("filename");

        $bAddFile = true;

        if ($appendparameters == "imagebrowser") {
            $restrictvar = "restrict_".$appendparameters;
            if (array_key_exists($restrictvar, $browserparameters)) {
                $fileType = strtolower(getFileType($filename));
                if (count($browserparameters[$restrictvar]) > 0) {
                    $bAddFile = false;
                    if (in_array($fileType, $browserparameters[$restrictvar])) {
                        $bAddFile = true;
                    }
                }
            }
        }

        $dirname = $item->get("dirname");
        $filesize = $item->get("size");

        if ($filesize == 0) {
            if (cFileHandler::exists($cfgClient[$client]["upl"]["path"].$dirname . $filename)) {
                $filesize = filesize($cfgClient[$client]["upl"]["path"].$dirname . $filename);
            }
        }

        $actions = "";

        $medianame = $properties->getValue ("upload", $path.$filename, "file", "medianame");
        $medianotes = $properties->getValue ("upload", $path.$filename, "file", "medianotes");

        $todo = new TODOLink("upload",$path.$filename, "File $path$filename","");

        $proptitle = i18n("Display properties");

        if ($appendparameters == "imagebrowser" || $appendparameters == "filebrowser") {
            $mstr = "";
        } else {
            $tmp_mstr = '<a href="javascript:conMultiLink(\'%s\', \'%s\', \'%s\', \'%s\')">%s</a>';
            $mstr = sprintf($tmp_mstr, 'right_bottom',
                    $sess->url("main.php?area=upl_edit&frame=4&path=$path&file=$filename&startpage=$startpage&sortby=$sortby&sortmode=$sortmode&thumbnailmode=$thumbnailmode"),
                    'right_top',
                    $sess->url("main.php?area=upl&frame=3&path=$path&file=$filename"),
                    '<img style="margin-left: 2px; margin-right: 2px;" alt="'.$proptitle.'" title="'.$proptitle.'" src="images/but_art_conf2.gif" onmouseover="this.style.cursor=\'pointer\'">');
        }

        $actions = $mstr . $actions;

        $showfilename = $filename;

        $check = new cHTMLCheckbox("fdelete[]", $filename);

        $mark = $check->toHTML(false);

        if ($bAddFile == true) {
            // 'bgcolor' is just a placeholder...
            $list2->setData($rownum, $mark, $dirname.$filename,
                             $showfilename,
                             $filesize,
                             strtolower(getFileType($filename)),
                             $todo->render().$actions);
            $rownum++;
        }
    }

    if ($rownum == 0) {
        $page->setContent(i18n("No files found"));
        $page->render();
        return;
    }

    if ($sortmode == "ASC") {
        $list2->sort($sortby, SORT_ASC);
    } else {
        $list2->sort($sortby, SORT_DESC);
    }

    if ($startpage < 1) {
        $startpage = 1;
    }

    if ($startpage > $list2->getNumPages()) {
        $startpage = $list2->getNumPages();
    }

    $list2->setListStart($startpage);

    // Create scroller
    if ($list2->getCurrentPage() > 1) {
        $prevpage = '<a href="'.$scrollthisfile.'&startpage='.($list2->getCurrentPage()-1).'" class="invert_hover">'.i18n("Previous Page").'</a>';
    } else {
        $prevpage = '&nbsp;';
    }

    if ($list2->getCurrentPage() < $list2->getNumPages()) {
        $nextpage = '<a href="'.$scrollthisfile.'&startpage='.($list2->getCurrentPage()+1).'" class="invert_hover">'.i18n("Next Page").'</a>';
    } else {
        $nextpage = '&nbsp;';
    }

    #$curpage = $list2->getCurrentPage() . " / ". $list2->getNumPages();

    if ($list2->getNumPages() > 1) {
        $num_pages = $list2->getNumPages();

        $paging_form .= "<script type=\"text/javascript\">
            function jumpToPage(select) {
                var pagenumber = select.selectedIndex + 1;
                url = '".$sess->url('main.php')."';
                document.location.href = url + '&area=upl&frame=4&appendparameters=$appendparameters&path=$path&sortmode=$sortmode&sortby=$sortby&thumbnailmode=$thumbnailmode&startpage=' + pagenumber;
            }
        </script>";
        $paging_form .= "<select name=\"start_page\" class=\"text_medium\" onChange=\"jumpToPage(this);\">";
        for ($i=1; $i<=$num_pages; $i++) {
            if ($i==$startpage) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            $paging_form .= "<option value=\"$i\"$selected>$i</option>";
        }

        $paging_form .= "</select>";
    } else {
        $paging_form = "1";
    }
    $curpage = $paging_form . " / ". $list2->getNumPages();

    $scroller = $prevpage . $nextpage;
    $output = $list2->output(true);
    $output = str_replace("-C-SCROLLLEFT-", $prevpage, $output);
    $output = str_replace("-C-SCROLLRIGHT-", $nextpage, $output);
    $output = str_replace("-C-PAGE-", i18n("Page")." ".$curpage, $output);

    $select = new cHTMLSelectElement("thumbnailmode_input");

    $values = array(25 => "25", 50 => "50", 100 => "100", 200 => "200");

    foreach ($values as $key => $value) {
        $option = new cHTMLOptionElement($value, $key);
        $select->addOptionElement($key, $option);
    }

    $select->setDefault($thumbnailmode);
    $select->setEvent('change', "document.del.thumbnailmode.value = this.value;");

    $topbar = $select->render().'<input type="image" onmouseover="this.style.cursor=\'pointer\'" src="images/submit.gif" style="vertical-align:middle; margin-left:5px;">';

    $output = str_replace("-C-FILESPERPAGE-", $topbar, $output);

    $page->addScript('messagebox', '<script type="text/javascript" src="scripts/messageBox.js.php?contenido='.$sess->id.'"></script>');

    $sDelTitle = i18n("Delete file");
    $sDelDescr = i18n("Do you really want to delete the following file:<br>");

    $script = '<script type="text/javascript">
        // Session-ID
        var sid = "{SID}";

        // Create messageBox instance
        box = new messageBox("", "", "", 0, 0);

        function showDelMsg(strElement, path, file, page) {
            box.confirm("'.$sDelTitle.'", "'.$sDelDescr.'<b>" + strElement + "</b>", "deleteFile(\'" + path + "\', \'" + file + "\', " + page + ")");
        }

        // Function for deleting items
        function deleteFile(path, file, page) {
            url  = \'main.php?area=upl\';
            url += \'&action=upl_delete\';
            url += \'&frame=4\';
            url += \'&path=\' + path;
            url += \'&file=\' + file;
            url += \'&startpage=\' + page;
            url += \'&contenido=\' + sid;
            url += \'&appendparameters='.$appendparameters.'\';

            window.location.href = url;
        }

        function renameFile (oldname, path, page) {
            var newname = prompt("{RENAME}", oldname),
                url;

            if (newname) {
                url  = \'main.php?area=upl\';
                url += \'&action=upl_renamefile\';
                url += \'&frame=4\';
                url += \'&newname=\' + newname;
                url += \'&oldname=\' + oldname;
                url += \'&startpage=\' + page;
                url += \'&path=\' + path;
                url += \'&contenido=\' + sid;

                window.location.href = url;
            }
        }

        function getY(e) {
            var y = 0;
            while (e) {
                y += e.offsetTop;
                e = e.offsetParent;
            }
            return y;
        }

        function getX(e) {
            var x = 0;
            while (e) {
                x += e.offsetLeft;
                e = e.offsetParent;
            }
            return x;
        }

        function findPreviewImage(smallImg) {
            var prevImages = document.getElementsByName("prevImage"),

                i;
            for (i=0; i<prevImages.length; i++) {
                if (prevImages[i].src == smallImg.src) {
                    return prevImages[i];
                }
            }
        }

        // Hoverbox
        function correctPosition(theImage, iWidth, iHeight) {
            var previewImage = findPreviewImage(theImage);

            if (typeof(previewShowIe6) == "function") {
                previewShowIe6(previewImage);
            }
            previewImage.style.width = iWidth;
            previewImage.style.height = iHeight;
            previewImage.style.marginTop = getY(theImage);
            previewImage.style.marginLeft = getX(theImage) + 100;
        }

        // Invert selection of checkboxes
        function invertSelection() {
            var delcheckboxes = document.getElementsByName("fdelete[]"),

                i;
            for (i=0; i<delcheckboxes.length; i++) {
                delcheckboxes[i].checked = !(delcheckboxes[i].checked);
            }
        }

        if (parent.parent.frames["right"].frames["right_top"].document.getElementById(\'c_0\')) {
            menuItem = parent.parent.frames["right"].frames["right_top"].document.getElementById(\'c_0\');
            parent.parent.frames["right"].frames["right_top"].sub.clicked(menuItem.firstChild);
        }
    </script>
    <!--[if IE 6]>
        <script type="text/javascript">
            function previewShowIe6(previewImage) {
                previewImage.style.display = "block"
                previewImage.style.position = "absolute"
                previewImage.style.top = "-33px"
                previewImage.style.left = "-45px"
                previewImage.style.zIndex = "1"
            }

            function previewHideIe6(theImage) {
                var previewImage = findPreviewImage(theImage);
                previewImage.style.display = "none";
            }
        </script>
    <![endif]-->';

    $script = str_replace('{SID}', $sess->id, $script);
    $script = str_replace('{RENAME}', i18n("Enter new filename"), $script);

    $page->addScript("script", $script);
    $markSubItem = markSubMenuItem(0, true);

    $delform = new UI_Form("del");
    $delform->setVar("area", $area);
    $delform->setVar("action", "");
    $delform->setVar("startpage", $startpage);
    $delform->setVar("thumbnailmode", $thumbnailmode);
    $delform->setVar("sortmode" , $sortmode);
    $delform->setVar("sortby", $sortby);
    $delform->setVar("appendparameters", $appendparameters);
    $delform->setVar("path", $path);
    $delform->setVar("frame", 4);

    // Table with (preview) images
    $delform->add("list", $output);

    $page->addScript('iZoom', '<script type="text/javascript" src="'.$sess->url("scripts/iZoom.js.php").'"></script>');
    $page->addScript('style', '<style type="text/css">
                               select {
                                vertical-align:middle;
                               }
                               a.invert_hover:active, a.invert_hover:link, a.invert_hover:visited {
                                   cursor: pointer;
                                   color: #0060B1;
                               }
                               a.invert_hover:hover {
                                  color: #000000;
                               }
                               </style>');

    if ($bDirectoryIsWritable == false) {
        $sErrorMessage = $notification->returnNotification("error", i18n("Directory not writable")  . ' (' . $cfgClient[$client]["upl"]["path"].$path . ')');
        $sErrorMessage .= '<br />';
    } else {
        $sErrorMessage = '';
    }

    $page->setContent($sScriptinBody . $sErrorMessage . $delform->render());

    $page->render();
}

//update description from con_upl to con_upl_meta
function updateUpl2Meta() {
    global $cfg, $client, $cfgClient, $db;
    //get
    $aUpl = array();
    $sSql = "SELECT * FROM " . $cfg['tab']['upl'] . " WHERE idclient = $client AND `description` != '' ORDER BY idupl ASC";
    $db->query($sSql);
    while ($db->next_record()) {
        $aUpl[$db->f('idupl')]['description'] = $db->f('description');
        $aUpl[$db->f('idupl')]['author'] = $db->f('author');
        $aUpl[$db->f('idupl')]['created'] = $db->f('created');
        $aUpl[$db->f('idupl')]['lastmodified'] = $db->f('lastmodified');
        $aUpl[$db->f('idupl')]['modifiedby'] = $db->f('modifiedby');
    }
    $aLang = array();
    $sSql = "SELECT idlang FROM " . $cfg['tab']['clients_lang'] . " WHERE idclient = $client ORDER BY idlang ASC";
    $db->query($sSql);
    while ($db->next_record()) {
        $aLang[] = $db->f('idlang');
    }
    $bError = true;
    $j = 0;
    foreach ($aUpl as $idupl => $elem) {
        if ($elem['description'] != '') {
            foreach ($aLang as $idlang) {
                $aUplMeta = array();
                $sSql = "SELECT * FROM " . $cfg['tab']['upl_meta'] . " WHERE idlang = $idlang  AND idupl = $idupl ORDER BY idupl ASC";
                $db->query($sSql);
                $i = 0;
                while ($db->next_record()) {
                    $aUplMeta[$i]['description'] = $db->f('description');
                    $aUplMeta[$i]['id_uplmeta'] = $db->f('id_uplmeta');
                    $i++;
                }
                if (count($aUplMeta) < 1) {
                    //there is no entry in con_upl_meta for this upload
                    $sSql = "INSERT INTO " . $cfg['tab']['upl_meta'] . " SET
                        idupl = $idupl,
                        idlang = $idlang,
                        medianame = '',
                        description = '" . $elem['description'] ."',
                        keywords = '',
                        internal_notice = '',
                        author = '" . $elem['author'] ."',
                        created = '" . $elem['created'] ."',
                        modified = '" . $elem['lastmodified'] ."',
                        modifiedby = '" . $elem['modifiedby'] ."',
                        copyright = ''";
                } elseif (count($aUplMeta) == 1 && $aUplMeta[0]['description'] == '') {
                    //there is already an entry and the field "description" is empty
                    $sSql = "UPDATE " . $cfg['tab']['upl_meta'] . " SET
                        description = '" . $elem['description'] ."'
                        WHERE id_uplmeta = " . $aUplMeta[0]['id_uplmeta'];
                } else {
                    //there is already an entry with an exising content in "description"
                    //do nothing;
                }
                $db->query($sSql);
                if ($db->Error !=0) {
                    $bError = false;
                    echo "<pre>" . $sql . "\nMysql Error:" . $db->Error . "(" . $db->Errno . ")</pre>";
                }
            }
        }
        $j++;
    }
    //At the end remove all values of con_upl.description and drop the field from table
    if ($bError && $j == count($aUpl)) {
        $sSql = "ALTER TABLE `".$cfg['tab']['upl']."` DROP `description`";
        $db->query($sSql);
        if ($db->Error !=0) {
            echo "<pre>" . $sql . "\nMysql Error:" . $db->Error . "(" . $db->Errno . ")</pre>";
        }
    } else {
        echo "error on updateUpl2Meta();".$j.'=='.count($aUpl);
    }
}
$do = false;
$sSql = "SHOW FIELDS FROM ".$cfg['tab']['upl'];
$db->query($sSql);
while ($db->next_record()) {
    if ($db->f("Field") == 'description') {
        $do = true;
    }
}
if ($done) {
    updateUpl2Meta();
}
uplSyncDirectory($path);
uplRender($path, $sortby, $sortmode, $startpage, $thumbnailmode);

?>