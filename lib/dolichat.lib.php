<?php

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to shoc
 */
function dolichat_prepare_head($object)
{
	global $langs, $conf, $user;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/dolichat/group/userconf.php';
	$head[$h][1] = $langs->trans("userconf");
	$head[$h][2] = 'userconf';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/dolichat/group/confkontakt.php';
	$head[$h][1] = $langs->trans("confkontakt");
	$head[$h][2] = 'confkontakt';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/dolichat/group/makegroup.php';
	$head[$h][1] = $langs->trans("makegroup");
	$head[$h][2] = 'makegroup';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/dolichat/group/ordgroup.php';
	$head[$h][1] = $langs->trans('ordgroup');
	$head[$h][2] = 'ordgroup';
	$h++;

	
	return $head;
}
?>


