<?php

/**
 * This file contains the nav sub collection and item class.
 *
 * @package          Core
 * @subpackage       GenericDB_Model
 * @author           Frederic Schneider
 * @copyright        four for business AG <www.4fb.de>
 * @license          http://www.contenido.org/license/LIZENZ.txt
 * @link             http://www.4fb.de
 * @link             http://www.contenido.org
 */

defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

/**
 * File collection
 *
 * @package Core
 * @subpackage GenericDB_Model
 */
class cApiNavSubCollection extends ItemCollection {
    /**
     * Constructor to create an instance of this class.
     *
     * @throws cInvalidArgumentException
     */
    public function __construct() {
        global $cfg;
        parent::__construct($cfg['tab']['nav_sub'], 'idnavs');
        $this->_setItemClass('cApiNavSub');

        // set the join partners so that joins can be used via link() method
        $this->_setJoinPartner('cApiNavMainCollection');
        $this->_setJoinPartner('cApiAreaCollection');
    }

    /**
     * Create new item with given values.
     *
     * @param int        $navm
     * @param int|string $idarea as ID or name
     * @param int        $level
     * @param string     $location
     * @param int        $online [optional]
     *
     * @return cApiNavSub
     * @throws cDbException
     * @throws cException
     * @throws cInvalidArgumentException
     */
    public function create($navm, $idarea, $level, $location, $online = 1)
    {
        if (is_string($idarea)) {
            $areaName = $idarea;
            $area     = new cApiArea();
            $area->loadBy('name', $areaName);
            $idarea = $area->isLoaded() ? $area->get('idarea') : 0;
            if (0 === $idarea) {
                cWarning(__FILE__, __LINE__, "Could not resolve area [$areaName] passed to method [create], assuming 0");
            }
        }

        /** @var cApiNavSub $item */
        $item = $this->createNewItem();
        $item->set('idnavm', $navm);
        $item->set('idarea', $idarea);
        $item->set('level', $level);
        $item->set('location', $location);
        $item->set('online', $online);
        $item->store();

        return $item;
    }

    /**
     * Returns sub navigation by area name
     * @param string $area
     * @param int    $level  [optional]
     * @param int    $online [optional]
     * @return array
     *                       List of assiziative arrays like
     *                       <pre>
     *                       $arr[] = array(
     *                       'location' => location xml path
     *                       'caption' => The tanslation of location from XML file
     *                       'name' => area name for sub navigation item
     *                       'menulesss' => Menuless state
     *                       );
     *                       </pre>
     * @throws cDbException
     * @throws cException
     */
    public function getSubnavigationsByAreaName($area, $level = 1, $online = 1) {
        global $cfg;

        $level = (int) $level;
        $online = (1 == $online) ? 1 : 0;

        $areasNsRs = array();

        $nav = new cGuiNavigation();

        $sql = "SELECT
                    ns.location AS location,
                    a.name AS name,
                    a.menuless AS menuless
                FROM
                    " . $cfg['tab']['area'] . " AS a,
                    " . $this->table . " AS ns
                WHERE
                    a.idarea = ns.idarea
                AND
                    ns.level = " . $level . "
                AND
                    ns.online = " . $online . "
                AND (
                    a.parent_id = '" . $this->db->escape($area) . "'
                    OR
                    a.name = '" . $this->db->escape($area) . "'
                )
                ORDER BY
                    a.parent_id ASC,
                    ns.idnavs ASC";

        $this->db->query($sql);

        while ($this->db->nextRecord()) {
            $rs = $this->db->toArray();
            $rs['caption'] = $nav->getName($rs['location']);
            $areasNsRs[] = $rs;
        }

        return $areasNsRs;
    }

}

/**
 * NavMain item
 *
 * @package Core
 * @subpackage GenericDB_Model
 */
class cApiNavSub extends Item {
    /**
     * Constructor to create an instance of this class.
     *
     * @param mixed $mId [optional]
     *                   Specifies the ID of item to load
     *
     * @throws cDbException
     * @throws cException
     */
    public function __construct($mId = false) {
        global $cfg;
        parent::__construct($cfg['tab']['nav_sub'], 'idnavs');
        $this->setFilters(array(
            'addslashes'
        ), array(
            'stripslashes'
        ));
        if ($mId !== false) {
            $this->loadByPrimaryKey($mId);
        }
    }

    /**
     * Userdefined setter for navsub fields.
     *
     * @param string $name
     * @param mixed $value
     * @param bool $bSafe [optional]
     *         Flag to run defined inFilter on passed value
     * @return bool
     */
    public function setField($name, $value, $bSafe = true) {
        switch ($name) {
            case 'idarea':
            case 'idnavm':
            case 'level':
                $value = (int) $value;
                break;
            case 'online':
                $value = (1 == $value) ? 1 : 0;
                break;
        }

        return parent::setField($name, $value, $bSafe);
    }

}
