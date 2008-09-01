DELETE FROM !PREFIX!_actions WHERE idaction < 10000;
INSERT INTO !PREFIX!_actions VALUES('63', '1', '10', 'con_makestart', 'conMakeStart ($idcatart, $is_start);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('2', '1', '33', 'con_makeonline', 'conMakeOnline ($idart, $lang);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('3', '1', '41', 'con_deleteart', 'conDeleteArt ($idart);\r\n$tmp_notification = $notification->returnNotification("info", i18n("Article deleted"));', '', '1');
INSERT INTO !PREFIX!_actions VALUES('4', '1', '50', 'con_expand', 'conExpand ($idcat, $lang, $expanded);', '', '0');
INSERT INTO !PREFIX!_actions VALUES('5', '3', '30', 'con_edit', '// Nothing', '', '1');
INSERT INTO !PREFIX!_actions VALUES('9', '6', '11', 'str_newtree', '$tmp_newid = strNewTree($categoryname, $categoryalias, $visible, $public, $idtplcfg);\r\nstrRemakeTreeTable();\r\n$cecHookRes = CEC_Hook::execute("Contenido.Action.str_newtree.AfterCall", array(\r\n    ''newcategoryid'' => $tmp_newid,\r\n    ''categoryname''  => $categoryname, \r\n    ''categoryalias'' => $categoryalias, \r\n    ''visible''       => $visible, \r\n    ''public''        => $public, \r\n    ''idtplcfg''      => $idtplcfg,\r\n));', '', 1);
INSERT INTO !PREFIX!_actions VALUES('10', '6', '21', 'str_newcat', '$tmp_newid  = strNewCategory($idcat, $categoryname, true, $categoryalias, $visible, $public, $idtplcfg);\r\n$cecHookRes = CEC_Hook::execute("Contenido.Action.str_newcat.AfterCall", array(\r\n    ''newcategoryid'' => $tmp_newid,\r\n    ''idcat''         => $idcat,\r\n    ''categoryname''  => $categoryname, \r\n    ''categoryalias'' => $categoryalias, \r\n    ''visible''       => $visible, \r\n    ''public''        => $public, \r\n    ''idtplcfg''      => $idtplcfg,\r\n));', '', 1);
INSERT INTO !PREFIX!_actions VALUES('11', '6', '31', 'str_renamecat', 'strRenameCategory ($idcat, $lang, $newcategoryname, $newcategoryalias);\r\n$cecHookRes = CEC_Hook::execute("Contenido.Action.str_renamecat.AfterCall", array(\r\n    ''newcategoryid''    => $tmp_newid,\r\n    ''idcat''            => $idcat,\r\n    ''lang''             => $lang,\r\n    ''newcategoryname''  => $newcategoryname, \r\n    ''newcategoryalias'' => $newcategoryalias\r\n));\r\n', '', 1);
INSERT INTO !PREFIX!_actions VALUES('12', '6', '40', 'str_makevisible', 'strMakeVisible($idcat, $lang, !$visible);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('13', '6', '50', 'str_makepublic', 'strMakePublic($idcat, $lang, !$public);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('14', '6', '61', 'str_deletecat', '$errno = strDeleteCategory($idcat);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('15', '6', '70', 'str_moveupcat', 'strMoveUpCategory($idcat);\r\nstrRemakeTreeTable();\r\n$cecHookRes = CEC_Hook::execute("Contenido.Action.str_moveupcat.AfterCall", $idcat);', '', 1);
INSERT INTO !PREFIX!_actions VALUES('16', '6', '81', 'str_movesubtree', 'strMoveSubtree($idcat, $parentid_new);\r\nstrRemakeTreeTable();\r\n$cecHookRes = CEC_Hook::execute("Contenido.Action.str_movesubtree.AfterCall", array(\r\n    ''idcat''        => $idcat,\r\n    ''parentid_new'' => $parentid_new\r\n));', '', 1);
INSERT INTO !PREFIX!_actions VALUES('17', '7', '31', 'upl_mkdir', '$errno = uplmkdir($path,$foldername);  \r\n', '', '1');
INSERT INTO !PREFIX!_actions VALUES('61', '7', '31', 'upl_upload', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('62', '7', '31', 'upl_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('18', '9', '20', 'lay_edit', '$idlay = layEditLayout($idlay, $layname, $description, $code);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('19', '8', '31', 'lay_delete', '$errno = layDeleteLayout($idlay);\r\n', '', '1');
INSERT INTO !PREFIX!_actions VALUES('20', '11', '20', 'mod_edit', 'if (empty($type)) {\r\n$type = $customtype;\r\n}\r\n\r\n$idmod = modEditModule($idmod, $name, $descr, $input, $output, $template, $type);        ', '', '1');
INSERT INTO !PREFIX!_actions VALUES('21', '10', '31', 'mod_delete', 'modDeleteModule($idmod);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('22', '12', '31', 'tpl_delete', '$tmp_notification =  tplDeleteTemplate($idtpl);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('23', '13', '20', 'tpl_edit', '$idtpl = tplEditTemplate($changelayout, $idtpl, $tplname, $description, $idlay, $c, $vdefault);        ', '', '1');
INSERT INTO !PREFIX!_actions VALUES('30', '6', '', 'str_movedowncat', 'strMoveDownCategory($idcat);\r\nstrRemakeTreeTable();\r\n$cecHookRes = CEC_Hook::execute("Contenido.Action.str_movedowncat.AfterCall", $idcat);', '', 1);
INSERT INTO !PREFIX!_actions VALUES('347', '31', '', 'style_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('348', '31', '', 'style_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('349', '32', '', 'js_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('337', '16', '', 'news_save', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('338', '16', '', 'news_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('339', '16', '', 'news_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('341', '50', '', 'recipients_save', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('342', '50', '', 'recipients_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('343', '50', '', 'recipients_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('345', '32', '', 'js_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('346', '11', '', 'mod_new', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('351', '20', '', 'stat_show', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('350', '49', '', 'log_show', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('64', '24', '', 'request_pw', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('35', '47', '10', 'lang_newlanguage', 'if (!is_numeric($targetclient)) { $targetclient = $client; } $errno = langNewLanguage("-- ".i18n("New language")." --",$targetclient);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('36', '22', '21', 'lang_renamelanguage', '$errno = langRenameLanguage($idlang, $name);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('37', '47', '31', 'lang_deletelanguage', 'if (!is_numeric($targetclient)) { $targetclient = $client; } $errno = langDeleteLanguage($idlang,$targetclient);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('38', '22', '40', 'lang_activatelanguage', 'langActivateDeactivateLanguage($idlang, 1);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('39', '22', '41', 'lang_deactivatelanguage', 'langActivateDeactivateLanguage($idlang, 0);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('328', '13', '', 'tpl_new', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('48', '26', '20', '20', 'include($cfgPathInc."con_edittpl.inc.php");', '', '0');
INSERT INTO !PREFIX!_actions VALUES('44', '25', '12', 'user_saverightsarea', 'saverightsarea();  ', '', '0');
INSERT INTO !PREFIX!_actions VALUES('327', '9', '', 'lay_new', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('47', '40', '10', 'user_edit', '//fake action => edit frontenduser', '', '1');
INSERT INTO !PREFIX!_actions VALUES('352', '13', '', 'tpl_duplicate', 'tplDuplicateTemplate($idtpl);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('53', '28', '10', '10', 'if ( $installed == 0 ) {\r\n   installplugin();\r\n} else {\r\n   deinstallplugin();\r\n}', '', '1');
INSERT INTO !PREFIX!_actions VALUES('320', '7', '51', '51', 'uplrename($path,$edit,$newfile); ', '', '0');
INSERT INTO !PREFIX!_actions VALUES('317', '29', '10', '10', '//fake action for editing whole langfile', '', '1');
INSERT INTO !PREFIX!_actions VALUES('316', '29', '21', '21', 'langnew ($idbereich);', '', '0');
INSERT INTO !PREFIX!_actions VALUES('314', '29', '50', '50', 'langfileExpand ($expbereich,$expanded);', '', '0');
INSERT INTO !PREFIX!_actions VALUES('315', '29', '20', '20', 'bereichnew ();', '', '0');
INSERT INTO !PREFIX!_actions VALUES('313', '29', '44', '44', 'langDelete ($idkey);', '', '0');
INSERT INTO !PREFIX!_actions VALUES('312', '29', '41', '41', 'bereichDelete ($idbereich);', '', '0');
INSERT INTO !PREFIX!_actions VALUES('7', '2', '41', '41', 'bereichDelete ($idbereich);', '', '0');
INSERT INTO !PREFIX!_actions VALUES('58', '1', '', 'con_makepublic', 'conMakePublic($idcat, $lang, $public);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('321', '30', '', 'tplcfg_edit', '// include ($cfg["path"]["includes"] . "include.tplcfg_edit_form.php");', '', '0');
INSERT INTO !PREFIX!_actions VALUES('57', '1', '', 'con_tplcfg_edit', 'include ($cfg["path"]["includes"] . "include.tplcfg_edit_form.php");', '', '1');
INSERT INTO !PREFIX!_actions VALUES('322', '31', '', 'style_edit', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('323', '32', '', 'js_edit', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('59', '1', '', 'con_makecatonline', 'conMakeCatOnline($idcat, $lang, $online);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('60', '1', '', 'con_changetemplate', 'if ($perm->have_perm_area_action("con","con_changetemplate") ||\r\n  $perm->have_perm_area_action_item("con","con_changetemplate",$idcat))\r\n{\r\nconChangeTemplateForCat($idcat, $idtpl);\r\n} else {\r\n$notification->displayNotification("error", i18n("Permission denied"));\r\n}', '', '1');
INSERT INTO !PREFIX!_actions VALUES('325', '39', '', 'user_createuser', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('326', '21', '', 'user_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('0', '0', '', 'fake_permission_action', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('329', '45', '', 'mycontenido_editself', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('330', '24', '', 'login', '//fake login action', '', '1');
INSERT INTO !PREFIX!_actions VALUES('353', '30', '', 'str_tplcfg', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('334', '48', '', 'client_new', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('335', '48', '', 'client_edit', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('336', '46', '', 'client_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('354', '54', '', 'group_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('355', '60', '', 'group_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('356', '61', '', 'group_edit', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('357', '63', '', 'group_deletemember', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('358', '63', '', 'group_addmember', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('359', '6', '', 'front_allow', '// fake action', '', '1');
INSERT INTO !PREFIX!_actions VALUES('56', '2', '', 'con_editart', '/* Action fuer ''con_editart'' */\r\n$path = $cfg["path"]["contenido_fullhtml"] . "external/backendedit/";\r\n\r\nif ($tmpchangelang != $lang)\r\n{\r\n    $url = $sess->url("front_content.php?changeview=$changeview&client=$client&lang=$lang&action=$action&idartlang=$idartlang&idart=$idart&idcat=$idcat&tmpchangelang=$tmpchangelang");\r\n} else {\r\n    $url = $sess->url("front_content.php?changeview=$changeview&client=$client&lang=$lang&action=$action&idartlang=$idartlang&idart=$idart&idcat=$idcat&lang=$lang");\r\n}\r\n\r\nheader("location: $path$url");\r\n\r\n', 'rights/content/article/edit', '1');
INSERT INTO !PREFIX!_actions VALUES('55', '3', '', 'con_saveart', 'if (!isset($idtpl))\r\n{\r\n  $idtpl = false;\r\n}\r\n\r\nif (!isset($artspec))\r\n{\r\n  $artspec = "";\r\n}\r\n\r\nif (!isset($online))\r\n{\r\n  $online = false;\r\n}\r\n\r\nif (isset($title)) \r\n{\r\n	\r\n	if (1 == $tmp_firstedit) \r\n	{\r\n	\r\n		$idart = conEditFirstTime($idcat, $idcatnew, $idart, $is_start, $idtpl, $idartlang, $lang, $title, $summary, $artspec, $created, $lastmodified, $author, $online, $datestart, $dateend, $artsort);\r\n		$tmp_notification = $notification->returnNotification("info", i18n("Changes saved"));\r\n		\r\n		if ( !isset($idartlang) ) \r\n		{\r\n			$sql = "SELECT idartlang FROM ".$cfg["tab"]["art_lang"]." WHERE idart = $idart AND idlang = $lang";\r\n			$db->query($sql);\r\n			$db->next_record();\r\n			$idartlang = $db->f("idartlang");\r\n		}\r\n		\r\n		if ( in_array($idcat, $idcatnew) ) \r\n		{\r\n		\r\n			$sql = "SELECT idcatart FROM ".$cfg["tab"]["cat_art"]." WHERE idcat = ''".$idcat."'' AND idart = ''".$idart."''";\r\n			\r\n			$db->query($sql);\r\n			$db->next_record();\r\n			\r\n			$tmp_idcatart = $db->f("idcatart");\r\n			\r\n			if ( $is_start == 1 ) \r\n			{\r\n				conMakeStart($tmp_idcatart, $is_start);\r\n			}\r\n			\r\n			if (!isset($is_start))\r\n			{\r\n				if ($cfg["is_start_compatible"] == true)\r\n				{\r\n				\r\n					$sql = "SELECT * FROM ".$cfg["tab"]["cat_art"]." WHERE idcat = ''$idcat'' AND is_start = ''1'' ";\r\n					$db->query($sql);\r\n					if ( $db->next_record() ) \r\n					{\r\n						; \r\n					}else\r\n					{\r\n						conMakeStart($tmp_idcatart, 0);\r\n					}\r\n					\r\n				}else\r\n				{\r\n				\r\n					$sql = "SELECT * FROM ".$cfg["tab"]["cat_lang"]." WHERE idcat = ''$idcat'' AND idlang = ''$lang'' AND startidartlang != ''0'' ";\r\n					$db->query($sql);\r\n					if ( $db->next_record() ) \r\n					{\r\n						$tmp_startidartlang = $db->f(''startidartlang'');\r\n						if ($idartlang == $tmp_startidartlang)\r\n						{\r\n							conMakeStart($tmp_idcatart, 0);\r\n						}else\r\n						{\r\n							; # do nothing\r\n						}\r\n						\r\n					}else\r\n					{\r\n						conMakeStart($tmp_idcatart, 0);\r\n					}\r\n					\r\n				}\r\n			}\r\n			\r\n		}\r\n	\r\n		if ( is_array($idcatnew) ) \r\n		{\r\n		\r\n			foreach ( $idcatnew as $idcat ) \r\n			{\r\n			\r\n				$sql = "SELECT idcatart FROM ".$cfg["tab"]["cat_art"]." WHERE idcat = $idcat AND idart = $idart";\r\n				\r\n				$db->query($sql);\r\n				$db->next_record();\r\n				\r\n				conSetCodeFlag( $db->f("idcatart") );\r\n			\r\n			}\r\n		}\r\n	\r\n	}	\r\n	else \r\n	{\r\n	\r\n		conEditArt($idcat, $idcatnew, $idart, $is_start, $idtpl, $idartlang, $lang, $title, $summary, $artspec, $created, $lastmodified, $author, $online, $datestart, $dateend, $artsort);\r\n		\r\n		$tmp_notification = $notification->returnNotification("info", i18n("Changes saved"));\r\n		\r\n		if ( !isset($idartlang) ) \r\n		{\r\n			$sql = "SELECT idartlang FROM ".$cfg["tab"]["art_lang"]." WHERE idart = $idart AND idlang = $lang";\r\n			$db->query($sql);\r\n			$db->next_record();\r\n			$idartlang = $db->f("idartlang");\r\n		}\r\n		\r\n		if ( is_array($idcatnew) ) \r\n		{\r\n			if ( in_array($idcat, $idcatnew) ) \r\n			{\r\n			\r\n				$sql = "SELECT idcatart FROM ".$cfg["tab"]["cat_art"]." WHERE idcat = ''".$idcat."'' AND idart = ''".$idart."''";\r\n				\r\n				$db->query($sql);\r\n				$db->next_record();\r\n				\r\n				$tmp_idcatart = $db->f("idcatart");\r\n				\r\n				if ( $is_start == 1 ) \r\n				{\r\n					conMakeStart($tmp_idcatart, $is_start);\r\n				}\r\n				\r\n				if (!isset($is_start))\r\n				{\r\n					if ($cfg["is_start_compatible"] == true)\r\n					{\r\n					\r\n						$sql = "SELECT * FROM ".$cfg["tab"]["cat_art"]." WHERE idcat = ''$idcat'' AND is_start = ''1'' ";\r\n						$db->query($sql);\r\n						if ( $db->next_record() ) \r\n						{\r\n							; \r\n						}else\r\n						{\r\n							conMakeStart($tmp_idcatart, 0);\r\n						}\r\n						\r\n					}else\r\n					{\r\n					\r\n						$sql = "SELECT * FROM ".$cfg["tab"]["cat_lang"]." WHERE idcat = ''$idcat'' AND idlang = ''$lang'' AND startidartlang != ''0'' ";\r\n						$db->query($sql);\r\n						if ( $db->next_record() ) \r\n						{\r\n							$tmp_startidartlang = $db->f(''startidartlang'');\r\n							if ($idartlang == $tmp_startidartlang)\r\n							{\r\n								conMakeStart($tmp_idcatart, 0);\r\n							}else\r\n							{\r\n								; # do nothing\r\n							}\r\n							\r\n						}else\r\n						{\r\n							conMakeStart($tmp_idcatart, 0);\r\n						}\r\n						\r\n					}\r\n					\r\n				}\r\n			}\r\n		}\r\n		\r\n		if ( is_array($idcatnew) ) \r\n		{\r\n		\r\n			foreach ( $idcatnew as $idcat ) \r\n			{\r\n			\r\n				$sql = "SELECT idcatart FROM ".$cfg["tab"]["cat_art"]." WHERE idcat = $idcat AND idart = $idart";\r\n				\r\n				$db->query($sql);\r\n				$db->next_record();\r\n				\r\n				conSetCodeFlag( $db->f("idcatart") );\r\n				\r\n			}\r\n		}\r\n	}\r\n}\r\n\r\n\r\n$cecHookRes = CEC_Hook::execute("Contenido.Action.con_saveart.AfterCall", array(\r\n    ''idcat''        => $idcat, \r\n    ''idcatnew''     => $idcatnew, \r\n    ''idart''        => $idart, \r\n    ''is_start''     => $is_start, \r\n    ''idtpl''        => $idtpl, \r\n    ''idartlang''    => $idartlang, \r\n    ''lang''         => $lang, \r\n    ''title''        => $title, \r\n    ''urlname''      => $urlname,\r\n    ''summary''      => $summary, \r\n    ''artspec''      => $artspec, \r\n    ''created''      => $created, \r\n    ''lastmodified'' => $lastmodified, \r\n    ''author''       => $author, \r\n    ''online''       => $online, \r\n    ''datestart''    => $datestart, \r\n    ''dateend''      => $dateend, \r\n    ''artsort''      => $artsort\r\n));', '', 0);
INSERT INTO !PREFIX!_actions VALUES('54', '3', '', 'con_newart', '/* Code for action\r\n   ''con_newart'' */\r\n$sql = "SELECT\r\n            a.idtplcfg,\r\n            a.name\r\n        FROM\r\n            ".$cfg["tab"]["cat_lang"]." AS a,\r\n            ".$cfg["tab"]["cat"]." AS b\r\n        WHERE\r\n            a.idlang    = ''".$lang."'' AND\r\n            b.idclient  = ''".$client."'' AND\r\n            a.idcat     = ''".$idcat."'' AND\r\n            b.idcat     = a.idcat";\r\n\r\n$db->query($sql);\r\n$db->next_record();\r\n\r\nif ( $db->f("idtplcfg") != 0 ) {\r\n$newart = true;\r\n \r\n\r\n} else {\r\n\r\n    $noti_html = ''<table cellspacing="0" cellpadding="2" border="0">\r\n\r\n                    <tr class="text_medium">\r\n                        <td colspan="2">\r\n                            <b>Fehler bei der Erstellung des Artikels</b><br><br>\r\n                            Der Kategorie ist kein Template zugewiesen.\r\n                        </td>\r\n                    </tr>\r\n\r\n                    <tr>\r\n                        <td colspan="2">&nbsp;</td>\r\n                    </tr>\r\n\r\n                  </table>'';\r\n\r\n    $code = ''\r\n            <html>\r\n                <head>\r\n                    <title>Error</title>\r\n                    <link rel="stylesheet" type="text/css" href="''.$cfg["path"]["contenido_fullhtml"].$cfg["path"]["styles"].''contenido.css"></link>\r\n                </head>\r\n                <body style="margin: 10px">''.$notification->returnNotification("error", $noti_html).''</body>\r\n            </html>'';\r\n\r\n    echo $code;\r\n\r\n}', '', '1');
INSERT INTO !PREFIX!_actions VALUES('378', '1', '', 'con_lock', 'conLock ($idart, $lang);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('379', '65', '', 'emptyLog', '$tmp_notification = emptyLogFile();', '', '0');
INSERT INTO !PREFIX!_actions VALUES('380', '66', '', 'sendMail', '$tmpReturnVar = sendBugReport();', '', '0');
INSERT INTO !PREFIX!_actions VALUES('381', '7', '', 'upl_rmdir', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('387', '1', '', 'con_syncarticle', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('386', '1', '', 'con_synccat', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('385', '67', '', 'systemsettings_delete_item', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('384', '67', '', 'systemsettings_edit_item', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('383', '67', '', 'systemsettings_save_item', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('388', '68', '', 'client_artspec_save', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('389', '68', '', 'client_artspec_edit', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('390', '68', '', 'client_artspec_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('391', '68', '', 'client_artspec_online', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('392', '71', '', 'htmltpl_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('393', '71', '', 'htmltpl_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('394', '71', '', 'htmltpl_edit', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('395', '72', '', 'tpl_visedit', '$idtpl = tplEditTemplate($changelayout, $idtpl, $tplname, $description, $idlay, $c, $tplisdefault);', '', '1');
INSERT INTO !PREFIX!_actions VALUES('396', '68', '', 'client_artspec_default', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('397', '7', '', 'upl_renamedir', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('398', '7', '', 'upl_modify_file', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('400', '7', '', 'upl_renamefile', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('401', '76', '', 'frontend_save_user', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('402', '76', '', 'frontend_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('403', '76', '', 'frontend_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('404', '1', '', 'con_duplicate', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('405', '77', '', 'frontendgroup_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('406', '77', '', 'frontendgroup_save_group', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('407', '77', '', 'frontendgroup_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('408', '77', '', 'frontendgroups_user_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('409', '44', '', 'todo_save_item', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('410', '44', '', 'mycontenido_tasks_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('412', '81', '', 'mod_translation_save', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('413', '81', '', 'mod_importexport_translation', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('414', '7', '', 'upl_multidelete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('415', '47', '', 'lang_edit', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('416', '6', '', 'str_duplicate', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('417', '83', '', 'clientsettings_save_item', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('418', '83', '', 'clientsettings_delete_item', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('419', '83', '', 'clientsettings_edit_item', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('420', '11', '', 'mod_importexport_module', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('421', '3', '', 'remove_assignments', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('422', '86', '', 'recipientgroup_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('423', '86', '', 'recipientgroup_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('424', '86', '', 'recipientgroup_recipient_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('425', '86', '', 'recipientgroup_save_group', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('427', '16', '', 'news_duplicate', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('428', '50', '', 'recipients_purge', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('429', '82', '', 'fegroups_save_perm', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('430', '85', '', 'note_save_item', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('431', '85', '', 'note_delete', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('434', '16', '', 'news_add_job', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('435', '16', '', 'news_send_test', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('436', '90', '', 'news_job_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('437', '90', '', 'news_job_details', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('438', '90', '', 'news_job_detail_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('439', '16', '', 'news_html_settings', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('440', '91', '', 'recipients_import', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('441', '90', '', 'news_job_run', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES('442', '91', '', 'recipients_import_exec', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES('443', '92', '', 'mod_importexport_package', '', '', '0');
INSERT INTO !PREFIX!_actions VALUES ('500', '500', '', 'linkchecker', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('501', '500', '', 'whitelist_view', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('600', '601', '', 'workflow_show', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('601', '601', '', 'workflow_create', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('602', '601', '', 'workflow_save', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('603 ', '600', '', 'workflow_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('604', '602', '', 'workflow_step_edit', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('605', '602', '', 'workflow_step_up', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('606', '602', '', 'workflow_step_down', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('607', '602', '', 'workflow_save_step', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('608', '602', '', 'workflow_create_step', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('609', '602', '', 'workflow_step_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('610', '602', '', 'workflow_user_up', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('611', '602', '', 'workflow_user_down', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('612', '602', '', 'workflow_create_user', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('613', '602', '', 'workflow_user_delete', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('614', '6', '', 'workflow_cat_assign', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('615', '1', '', 'workflow_do_action', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('616', '6', '', 'workflow_inherit_down', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('617', '604', '', 'workflow_task_user_select', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('618', '604', '', 'workflow_do_action', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('800', '802', '', 'storeallocation', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('444', '77', '', 'frontendgroup_user_add', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('200', '400', 'lay_history_manage', 'lay_history_manage', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('201', '401', 'style_history_manage', 'style_history_manage', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('202', '402', 'js_history_manage', 'js_history_manage', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('203', '403', 'htmltpl_history_manage', 'htmltpl_history_manage', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('204', '70', 'mod_history_manage', 'mod_history_manage', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('801', '400', 'history_truncate', 'history_truncate', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('802', '401', 'history_truncate', 'history_truncate', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('803', '402', 'history_truncate', 'history_truncate', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('804', '403', 'history_truncate', 'history_truncate', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('805', '70', 'history_truncate', 'history_truncate', '', '', '1');
INSERT INTO !PREFIX!_actions VALUES ('806', '415', 'edit_sysconf', 'edit_sysconf', '', '', '1');