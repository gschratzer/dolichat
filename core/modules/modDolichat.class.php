<?php
require_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

class modDolichat extends DolibarrModules
{
    public function __construct($db)
    {
        $this->db = $db;
        $this->numero = 124600;
        $this->rights_class = 'dolichat';
        $this->family = 'crm';
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = 'Internal chat for users';
        $this->version = 'dolibarr';
        $this->const_name = 'MAIN_MODULE_DOLICHAT';
        $this->picto = 'chat';

        $this->module_parts = array(
            'hooks' => array('main'),
        );

        $this->dirs = array('/dolichat/temp');
        $this->config_page_url = array('dolichat.php@dolichat');

        $this->rights = array();
        $r = 0;
        $this->rights[$r][0] = 104601;
        $this->rights[$r][1] = 'Read chat';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'read';
        $r++;
        $this->rights[$r][0] = 104602;
        $this->rights[$r][1] = 'Use chat';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'UseChat';
        $r++;
        $this->rights[$r][0] = 104603;
        $this->rights[$r][1] = 'Admin chat';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'admin';

        $ownerCondition = '!empty($conf->dolichat->enabled) && !empty($user->rights->dolichat->read)'
            . ' && empty(!empty($conf->dolimail->enabled) && ($user->rights->dolimail->read || $user->rights->dolimail->modify || $user->rights->dolimail->connect || $user->rights->dolimail->config))'
            . ' && empty(!empty($conf->onlineoffice->enabled) && !empty($user->rights->onlineoffice->read))';

        $this->menu = array();
        $r = 0;
        $this->menu[$r++] = array(
            'fk_menu' => '',
            'type' => 'top',
            'titre' => 'Communication',
            'mainmenu' => 'communication',
            'leftmenu' => '',
            'url' => '/dolimail/communication.php?mainmenu=communication',
            'langs' => 'dolichat@dolichat',
            'position' => 100,
            'enabled' => $ownerCondition,
            'perms' => '$user->rights->dolichat->read',
            'target' => '',
            'user' => 2,
        );

        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=communication',
            'type' => 'left',
            'titre' => 'Chat',
            'mainmenu' => 'communication',
            'leftmenu' => 'dolichat',
            'url' => '/dolichat/index.php?mainmenu=communication&leftmenu=dolichat',
            'langs' => 'dolichat@dolichat',
            'position' => 400,
            'enabled' => '$conf->dolichat->enabled',
            'perms' => '$user->rights->dolichat->read',
            'target' => '',
            'user' => 2,
        );

        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=communication',
            'type' => 'left',
            'titre' => 'DolichatSetup',
            'mainmenu' => 'communication',
            'leftmenu' => 'dolichat_setup',
            'url' => '/dolichat/admin/dolichat.php?mainmenu=communication&leftmenu=dolichat_setup',
            'langs' => 'dolichat@dolichat',
            'position' => 401,
            'enabled' => '$conf->dolichat->enabled',
            'perms' => '$user->rights->dolichat->admin',
            'target' => '',
            'user' => 2,
        );
    }

    public function init($options = '')
    {
        $this->_load_tables('/dolichat/sql/');

        return $this->_init(array(), $options);
    }

    public function remove($options = '')
    {
        return $this->_remove(array(), $options);
    }
}
