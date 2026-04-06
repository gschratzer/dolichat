<?php
/*
 * Copyright (C) 2016 Guido Schratzer <guido.schratzer@backbone.co.at>
 * Copyright (C) 2016 Niklas Spanring <n.spanring@backbone.co.at>
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *       ile       htdocs/dolichat/core/modules/modDolichat.class.php
 *       \ingroup    communication
 *       rief      Description and activation file for module Dolichat
 */

require_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

class modDolichat extends DolibarrModules
{
    /**
     * Constructor. Define names, constants, directories, boxes, permissions.
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        global $langs;

        $this->db = $db;
        $this->numero = 124010;
        $this->rights_class = 'dolichat';

        $this->family = 'communication';
        $this->module_position = 2;
        $this->familyinfo = array('communication' => array('position' => '010', 'label' => $langs->trans('Communication')));

        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = 'Chat System module';
        $this->descriptionlong = 'Dolichat is a live communications system, links the Dolibarr users to each other and allow a simple communication.';
        $this->editor_name = 'backbone internet service';
        $this->editor_url = 'https://backbone.co.at';
        $this->editor_web = 'https://backbone.co.at';
        $this->version = '5.0.2';
        $this->compatible = array('21.0');
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        $this->special = 0;
        $this->picto = 'dolichat_b@dolichat';

        $this->module_parts = array(
            'css' => array('/dolichat/css/dolichat.css.php', '/dolichat/css/communication.css'),
            'hooks' => array('toprightmenu', 'printTopRightMenu'),
        );

        $this->triggers = 0;
        $this->dirs = array('/dolichat/temp');
        $this->config_page_url = array('dolichat.php@dolichat');

        $this->depends = array();
        $this->requiredby = array();
        $this->phpmin = array(7, 4);
        $this->need_dolibarr_version = array(21, 0);
        $this->langfiles = array('dolichat@dolichat');

        $this->const = array(
            1 => array('COMMUNICATION_AKTIVE_MENU_ID', 'chaine', $this->numero, 'Aktive Module to show the Menu Tab', 0, 'current', 0),
        );

        $r = 1;
        $this->const[$r][0] = 'CHAT_ADDOON';
        $this->const[$r][1] = 'chaine';
        $this->const[$r][2] = 'mod_dolichat';
        $this->const[$r][3] = 'Chat System';
        $this->const[$r][4] = 0;

        $this->tabs = array();
        $this->dictionnaries = array();
        $this->boxes = array();

        $this->rights = array();
        $r = 0;
        $n = $this->numero + 1;

        $this->rights[$r][0] = $n;
        $this->rights[$r][1] = 'Use dolichat';
        $this->rights[$r][2] = 'a';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'UseChat';
        $r++; $n++;

        $this->rights[$r][0] = $n;
        $this->rights[$r][1] = 'Chat Admin';
        $this->rights[$r][2] = 'w';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'Admin';
        $r++; $n++;

        $this->rights[$r][0] = $n;
        $this->rights[$r][1] = 'Chat Pic Upload';
        $this->rights[$r][2] = 'a';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'UseUpload';
        $r++; $n++;

        $this->rights[$r][0] = $n;
        $this->rights[$r][1] = 'Read Pic';
        $this->rights[$r][2] = 'r';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'read';

        $this->menus = array();
        $r = 0;

        $this->menu[$r] = array(
            'fk_menu' => 0,
            'type' => 'top',
            'titre' => 'CommunicationsMenuTitle',
            'mainmenu' => 'communications',
            'leftmenu' => 'communications',
            'url' => '/dolichat/comhub.php',
            'langs' => 'dolichat@dolichat',
            'position' => 100,
            'enabled' => '$conf->dolichat->enabled',
            'perms' => '$conf->global->COMMUNICATION_AKTIVE_MENU_ID=='.$this->numero,
            'target' => '',
            'user' => 0,
        );
        $r++;

        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=communications',
            'type' => 'left',
            'titre' => 'DolichatMenuTitle',
            'mainmenu' => 'communications',
            'leftmenu' => 'dolichat',
            'url' => '/dolichat/index.php',
            'langs' => 'dolichat@dolichat',
            'position' => 100,
            'enabled' => '$conf->dolichat->enabled',
            'perms' => '$user->rights->dolichat->UseChat',
            'target' => '',
            'user' => 0,
        );
    }

    /**
     * Backward-compatible constructor wrapper.
     *
     * @param DoliDB $db Database handler
     * @return void
     */
    public function modDolichat($db)
    {
        self::__construct($db);
    }

    /**
     * Function called when module is enabled.
     *
     * @param string $options Options when enabling module
     * @return int 1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        global $db, $conf;

        dolibarr_set_const($db, 'COMMUNICATION_AKTIVE_MENU_ID', $this->numero);

        $newModuleIdArr = '';
        foreach (explode(',', (string) $conf->global->COMMUNICATION_MODULE_ID) as $id) {
            if ($id > 0 && is_numeric($id) && (int) $id !== (int) $this->numero) {
                $newModuleIdArr .= ',' . $id;
            }
        }
        $newModuleIdArr = ltrim($newModuleIdArr . ',' . $this->numero, ',');
        dolibarr_set_const($db, 'COMMUNICATION_MODULE_ID', $newModuleIdArr);

        require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
        $dirodt = DOL_DATA_ROOT . '/dolichat';
        dol_mkdir($dirodt);
        dol_copy(dol_buildpath('/dolichat/img/noimage.jpg', 0), $dirodt . '/noimage.jpg', 0, 0);

        if (empty($conf->global->dolichat_USE_DEL_TIME)) {
            dolibarr_set_const($db, 'dolichat_USE_DEL_TIME', '1', 'chaine', 0, '', $conf->entity);
            dolibarr_set_const($db, 'dolichat_DEL_TIME', '30', 'chaine', 0, '', $conf->entity);
            dolibarr_set_const($db, 'dolichat_SHOW_INT', '1', 'chaine', 0, '', $conf->entity);
        }
        if (empty($conf->global->dolichat_SHOW_INT)) {
            dolibarr_set_const($db, 'dolichat_SHOW_INT', '0', 'chaine', 0, '', $conf->entity);
        }
        if (empty($conf->global->dolichat_SHOW_LINK)) {
            dolibarr_set_const($db, 'dolichat_SHOW_LINK', '0', 'chaine', 0, '', $conf->entity);
        }
        if (empty($conf->global->dolichat_SHOW_LINK_AGENDA)) {
            dolibarr_set_const($db, 'dolichat_SHOW_LINK_AGENDA', '0', 'chaine', 0, '', $conf->entity);
        }
        if (empty($conf->global->dolichat_SHOW_LINK_AS_IT_OWN)) {
            dolibarr_set_const($db, 'dolichat_SHOW_LINK_AS_IT_OWN', '1', 'chaine', 0, '', $conf->entity);
        }
        if (empty($conf->global->dolichat_WERBUNG)) {
            dolibarr_set_const($db, 'dolichat_WERBUNG', '1', 'chaine', 0, '', $conf->entity);
        }

        try {
            $sql = 'SELECT chattextblob FROM ' . MAIN_DB_PREFIX . 'chattext WHERE rowid = 1';
            $resql = $db->query($sql);
            if (!$resql) {
                $db->query('ALTER TABLE `' . MAIN_DB_PREFIX . 'chattext` ADD `chattextblob` blob NOT NULL');
            }
        } catch (Exception $e) {
            try {
                $db->query('ALTER TABLE `' . MAIN_DB_PREFIX . 'chattext` ADD `chattextblob` blob NOT NULL');
            } catch (Exception $e) {
                // Keep silent for backward compatibility if column already exists.
            }
        }

        $this->load_tables();

        $sql = array(
            "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatstat` (
                `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `user_id` int(11) NOT NULL,
                `checks` int(11) NOT NULL,
                `online` int(11) NOT NULL,
                `last_stat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chattext` (
                `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `chattext` text NOT NULL,
                `chattextblob` blob NOT NULL,
                `user` varchar(60) NOT NULL,
                `user_id` int(11) NOT NULL,
                `privat` int(11) NOT NULL,
                `privat_name` varchar(60) NOT NULL,
                `gesehen` int(11) NOT NULL DEFAULT '0',
                `gesehen_Broadcast` varchar(120) NOT NULL,
                `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatpic` (
                `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `PicName` varchar(60) NOT NULL,
                `MsgID` int(11) NOT NULL,
                `UserID` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );

        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     *
     * @param string $options Options when disabling module
     * @return int 1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        global $db, $conf;

        $newModuleIdArr = '';
        foreach (explode(',', (string) $conf->global->COMMUNICATION_MODULE_ID) as $id) {
            if ((string) $id !== (string) $this->numero && $id !== '') {
                $newModuleIdArr .= ',' . $id;
            }
        }
        $newModuleIdArr = ltrim($newModuleIdArr, ',');
        dolibarr_set_const($db, 'COMMUNICATION_MODULE_ID', $newModuleIdArr);

        if ((string) $conf->global->COMMUNICATION_AKTIVE_MENU_ID === (string) $this->numero) {
            $posNewModule = explode(',', (string) $conf->global->COMMUNICATION_MODULE_ID);
            dolibarr_set_const($db, 'COMMUNICATION_AKTIVE_MENU_ID', !empty($posNewModule[0]) ? $posNewModule[0] : '');
        }

        $sql = array();
        return $this->_remove($sql, $options);
    }

    /**
     * Create tables, keys and data required by module.
     *
     * @return int <=0 if KO, >0 if OK
     */
    public function load_tables()
    {
        $sql = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
                SET time_zone = '+00:00';

                CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatstat` (
                  `rowid` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `checks` int(11) NOT NULL,
                  `online` int(11) NOT NULL,
                  `last_stat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

                ALTER TABLE `" . MAIN_DB_PREFIX . "chatstat`
                  ADD PRIMARY KEY (`rowid`),
                  ADD KEY `rowid` (`rowid`);";

        $sql .= "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chattext` (
                  `rowid` int(11) NOT NULL,
                  `chattext` text NOT NULL,
                  `chattextblob` blob NOT NULL,
                  `user` varchar(60) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `privat` int(11) NOT NULL,
                  `privat_name` varchar(60) NOT NULL,
                  `gesehen` int(11) NOT NULL DEFAULT '0',
                  `gesehen_Broadcast` varchar(120) NOT NULL,
                  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                ALTER TABLE `" . MAIN_DB_PREFIX . "chattext`
                  ADD PRIMARY KEY (`rowid`),
                  ADD KEY `UserID` (`user_id`,`privat`),
                  ADD KEY `GesID` (`user_id`,`privat`,`gesehen`),
                  ADD KEY `RowidID` (`rowid`) USING BTREE,
                  ADD KEY `UserIN` (`user_id`),
                  ADD KEY `PrivatIN` (`privat`);

                ALTER TABLE `" . MAIN_DB_PREFIX . "chattext`
                  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `" . MAIN_DB_PREFIX . "chattext` ADD `chattextblob` blob NOT NULL;";

        $sql .= "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatpic` (
                  `rowid` int(11) NOT NULL,
                  `PicName` varchar(60) NOT NULL,
                  `MsgID` int(11) NOT NULL,
                  `UserID` int(11) NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

                ALTER TABLE `" . MAIN_DB_PREFIX . "chatpic`
                  ADD PRIMARY KEY (`rowid`);

                ALTER TABLE `" . MAIN_DB_PREFIX . "chatpic`
                  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;";

        return $this->_load_tables($sql);
    }
}
