<?php
/* 
 * Copyright (C) 2016 Guido Schratzer <guido.schratzer@backbone.co.at>
 * Copyright (C) 2016 Niklas Spanring <n.spanring@backbone.co.at>
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *       \file       htdocs/dolichat/core/modules/modDolichat.class.php
 *       \ingroup    commercial
 *       \brief      Dolichat
 *       \version    $Id: modDolichat.class.php,v 3.0 2016/04/01 10:17:05 bbgs Exp $
 *       \author     Niklas Spanring / Guido Schratzer
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modMyModule
 *      \brief      Description and activation class for module MyModule
 */
class modDolichat extends DolibarrModules
{
	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	function modDolichat($DB)
	{
        global $langs,$conf;
		
        $this->db = $DB;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 124010;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'dolichat';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";

		$this->module_position = 001;
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Chat System module";
		$this->descriptionlong = "Dolichat is a live communications system, links the Dolibarr users to each other and allow a simple communication.";
		$this->editor_name = 'backbone internet service';
		$this->editor_url = 'https://backbone.co.at';
		$this->editor_web = 'https://backbone.co.at';
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '4.0.1';
		// Key used in " . MAIN_DB_PREFIX . "const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 1;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='dolichat_b@dolichat';

		$this->module_parts = array();
        

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		$this->triggers = 0;

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array("/dolichat/temp");
		$r=0;

		// Relative path to module style sheet if exists. Example: '/mymodule/css/mycss.css'.
		//$this->style_sheet = '/mymodule/mymodule.css.php';
		$this->module_parts = array('css' => array('/dolichat/css/dolichat.css.php'), 'hooks' => array('toprightmenu' , 'printTopRightMenu'));
		
		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("dolichat.php@dolichat");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,2);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("dolichat@dolichat");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0) );
		//                             2=>array('MAIN_MODULE_MYMODULE_NEEDSMARTY','chaine',1,'Constant to say module need smarty',1)
		$this->const = array();
		
		$r++;
		$this->const[$r][0] = "CHAT_ADDOON";
		$this->const[$r][1] = "chaine";
		$this->const[$r][2] = "mod_dolichat";
		$this->const[$r][3] = 'Chat System';
		$this->const[$r][4] = 0;

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',  // To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',  // To add another new tab identified by code tabname2
        //                              'objecttype:-tabname');                                                     // To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'thirdparty'       to add a tab in third party view
		// 'intervention'     to add a tab in intervention view
		// 'order_supplier'   to add a tab in supplier order view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'invoice'          to add a tab in customer invoice view
		// 'order'            to add a tab in customer order view
		// 'product'          to add a tab in product view
		// 'stock'            to add a tab in stock view
		// 'propal'           to add a tab in propal view
		// 'member'           to add a tab in fundation member view
		// 'contract'         to add a tab in contract view
		// 'user'             to add a tab in user view
		// 'group'            to add a tab in group view
		// 'contact'          to add a tab in contact view
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
        $this->tabs = array();

        // Dictionnaries
        $this->dictionnaries=array();
        /*
        $this->dictionnaries=array(
            'langs'=>'cabinetmed@cabinetmed',
            'tabname'=>array(MAIN_DB_PREFIX."cabinetmed_diaglec",MAIN_DB_PREFIX."cabinetmed_examenprescrit",MAIN_DB_PREFIX."cabinetmed_motifcons"),
            'tablib'=>array("DiagnostiqueLesionnel","ExamenPrescrit","MotifConsultation"),
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_diaglec as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_examenprescrit as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'cabinetmed_motifcons as f'),
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),
            'tabfield'=>array("code,label","code,label","code,label"),
            'tabfieldvalue'=>array("code,label","code,label","code,label"),
            'tabfieldinsert'=>array("code,label","code,label","code,label"),
            'tabrowid'=>array("rowid","rowid","rowid"),
            'tabcond'=>array($conf->cabinetmed->enabled,$conf->cabinetmed->enabled,$conf->cabinetmed->enabled)
        );
        */

        // Boxes
		// Add here list of php file(s) stored in includes/boxes that contains class to show a box.
    $this->boxes = array();			// List of boxes
		$r=0;
		
    $r++;
        
		// Permissions
		$this->rights = array();		// Permission array used by this module
		$this->rights_class = 'dolichat';

		$r=0;
		$n=$this->numero+1;
	
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
		$r++; $n++;	
		
		// Main menu entries


		// Add here entries to declare new menus
		// Example to declare the Top Menu entry:
	
		
		// Example to declare a Left Menu entry:

		
		// ##########################################################################################################################################################################################################
		
		// Example to declare another Left Menu entry:
		//$this->menu[$r] = array('fk_menu' => 0, // Put 0 if this is a top menu
        //                			'type' => 'top', // This is a Top menu entry
        //                			'titre' => 'Chat',
        //                			'mainmenu' => 'chat',
        //                			'leftmenu' => '0', // Use 1 if you also want to add left menu entries using this descriptor.
        //                			'url' => '/dolichat/index.php',
        //                			'langs' => 'dolichat@dolichat', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
        //                			'position' => 100,
        //                			'enabled' => '$conf->global->dolichat_SHOW_LINK_AS_IT_OWN', // Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible //if module is enabled.
        //                			'perms' => '$user->rights->dolichat->UseChat', // Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
        //                			'target' => '_blank',
        //                			'user'=>0);    // 0=Menu for internal users, 1=external users, 2=both
        //$r++;

		//$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=companies',	// Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'Chat',
		//							'mainmenu'=>'companies',
		//							'leftmenu'=>'chat',
		//							'url'=>'/dolichat/index.php?leftmenu=chat',
		//							'langs'=>'dolichat@dolichat',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>101,
		//							'enabled'=>'$conf->global->dolichat_SHOW_LINK',  // Define condition to show or hide menu entry. Use '$conf->moaauth->enabled' if entry must be visible if module is //enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
		//							'perms'=>'$user->rights->dolichat->UseChat',			                // Use 'perms'=>'$user->rights->moaauth->level1->level2' if you want your menu with a //permission rules
		//							'target'=>'_blank',
		//							'user'=>0);		
		//$r++;
		//$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=agenda',	// Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'Chat',
		//							'mainmenu'=>'agenda',
		//							'leftmenu'=>'chat',
		//							'url'=>'/dolichat/index.php',
		//							'langs'=>'dolichat@dolichat',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>102,
		//							'enabled'=>'$conf->global->dolichat_SHOW_LINK_AGENDA',  // Define condition to show or hide menu entry. Use '$conf->moaauth->enabled' if entry must be visible if //module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
		//							'perms'=>'$user->rights->dolichat->UseChat',			                // Use 'perms'=>'$user->rights->moaauth->level1->level2' if you want your menu with a //permission rules
		//							'target'=>'_blank',
		//							'user'=>0);		
		//$r++;
	}

/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		global $db, $conf;
		require_once(DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php');
		$dirodt=DOL_DATA_ROOT.'/dolichat';
		dol_mkdir($dirodt);
		dol_copy(dol_buildpath('/dolichat/img/noimage.jpg',0),$dirodt.'/noimage.jpg',0,0);
		
		if(empty($conf->global->dolichat_USE_DEL_TIME))
		{
			
			dolibarr_set_const($db,"dolichat_USE_DEL_TIME", '1','chaine',0,'',$conf->entity);
			dolibarr_set_const($db,"dolichat_DEL_TIME", '30','chaine',0,'',$conf->entity);
			dolibarr_set_const($db,"dolichat_SHOW_INT", '1','chaine',0,'',$conf->entity);
		}
		if(empty($conf->global->dolichat_SHOW_INT)){

			dolibarr_set_const($db,"dolichat_SHOW_INT", '0','chaine',0,'',$conf->entity);
		}
		if(empty($conf->global->dolichat_SHOW_LINK)){
			
			dolibarr_set_const($db,"dolichat_SHOW_LINK", '0','chaine',0,'',$conf->entity);
		}
		if(empty($conf->global->dolichat_SHOW_LINK_AGENDA)){
			
			dolibarr_set_const($db,"dolichat_SHOW_LINK_AGENDA", '0','chaine',0,'',$conf->entity);
		}
		/*
		if(empty($conf->global->dolichat_USE_IN_WEBAPP)){
			
			dolibarr_set_const($db,"dolichat_USE_IN_WEBAPP", '0','chaine',0,'',$conf->entity);
		}
		*/
		if(empty($conf->global->dolichat_SHOW_LINK_AS_IT_OWN)){
			
			dolibarr_set_const($db,"dolichat_SHOW_LINK_AS_IT_OWN", '1','chaine',0,'',$conf->entity);
		}
		if(empty($conf->global->dolichat_WERBUNG)){
			
			dolibarr_set_const($db,"dolichat_WERBUNG", '1','chaine',0,'',$conf->entity);
		}

		try 
		{
			// Test if the columm chattextblob and the table chattext exist [new colum chattextblob !!!]
	      	$sql = "SELECT chattextblob FROM " . MAIN_DB_PREFIX . "chattext where rowid = 1"; 
	      	$ergebnis = $db->query($sql);
	      	if($ergebnis)
	      	{
		      	$row = $db->fetch_object($ergebnis);
		      	if($row)
		      	{
		      		$chattextblob_exist = true;
		      	}
		      	else
		      	{
		      		// for compatibility with older dolichat system! [new colum chattextblob !!!]
		      		$sql = "ALTER TABLE `" . MAIN_DB_PREFIX . "chattext` ADD `chattextblob` blob NOT NULL;"; 
			      	$ergebnis = $db->query($sql);
		      	}
			}
			else
			{
				// for compatibility with older dolichat system! [new colum chattextblob !!!]
		    	$sql = "ALTER TABLE `" . MAIN_DB_PREFIX . "chattext` ADD `chattextblob` blob NOT NULL;"; 
				$ergebnis = $db->query($sql);
			}
	    } 
	    catch (Exception $e) 
	    {
	    	try 
	    	{
	    		// for compatibility with older dolichat system! [new colum chattextblob !!!]
		    	$sql = "ALTER TABLE `" . MAIN_DB_PREFIX . "chattext` ADD `chattextblob` blob NOT NULL;"; 
		      	$ergebnis = $db->query($sql);
	      	} 
	      	catch (Exception $e) 
	      	{
	      		// no need to output this error (only throws when chattextblob already exist)
	      	}
	    }

		$sql = array();
		$result=$this->load_tables();

					// chatstat
		$sql = array("CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatstat` (
					  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `user_id` int(11) NOT NULL,
					  `checks` int(11) NOT NULL,
					  `online` int(11) NOT NULL,
					  `last_stat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

					// chattext
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
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

					// chatpic
					"CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatpic` (
					  `rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `PicName` varchar(60) NOT NULL,
					  `MsgID` int(11) NOT NULL,
					  `UserID` int(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
				);
				  
		return $this->_init($sql);
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted.
	 *      @return     int             1 if OK, 0 if KO
	 */
	function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql);
	}


	/**
	 *		\brief		Create tables, keys and data required by module
	 * 					Files " . MAIN_DB_PREFIX . "table1.sql, " . MAIN_DB_PREFIX . "table1.key.sql " . MAIN_DB_PREFIX . "data.sql with create table, create keys
	 * 					and create data commands must be stored in directory /mymodule/sql/
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		// chatstat
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
				  ADD KEY `rowid` (`rowid`);
				";
		// chattext
		$sql.= "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chattext` (
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
		// chatpic
		$sql.= "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatpic` (
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
		//return $this->_load_tables('/dolichat/sql/');
	}
}

?>