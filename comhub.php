<?php
/* Copyright (C) 2013 Guido Schratzer <guido.schratzer@backbone.co.at>
 * Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
 *	\file       htdocs/comm/index.php
 *	\ingroup    commercial
 *	\brief      Home page of commercial area
 */

$res = 0;
if (!$res && file_exists(__DIR__ . '/../main.inc.php')) {
    $res = @include __DIR__ . '/../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../main.inc.php')) {
    $res = @include __DIR__ . '/../../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../../main.inc.php')) {
    $res = @include __DIR__ . '/../../../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../../../main.inc.php')) {
    $res = @include __DIR__ . '/../../../../main.inc.php';
}
if (!$res) {
    die('Include of main fails');
}
require_once(DOL_DOCUMENT_ROOT . "/core/class/html.formother.class.php");
//require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
//require_once DOL_DOCUMENT_ROOT.'/societe/class/client.class.php';
//require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
//require_once DOL_DOCUMENT_ROOT.'/core/lib/agenda.lib.php';
//if (! empty($conf->contrat->enabled)) require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
//if (! empty($conf->propal->enabled))  require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
//if (! empty($conf->commande->enabled))  require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';

//if (! $user->rights->societe->lire) accessforbidden();

if ( empty($conf->dolimail->enabled) &&  empty($conf->ccd->enabled)){
	header("Location: index.php");
	exit;
}

/*
 * Actions
 */

$basis_module = 'dolichat'; // TODO Read wich Module of Communication is aktive and set this 

$langs->load($basis_module.'@'.$basis_module);

/*
 * View
 */
$form = new Form($db);

llxHeader();
print_fiche_titre($langs->trans("ComHub"),'','img/object_communications.png', 1);

print '<div class="fichecenter"><div class="fichethirdleft connectedSortable ui-sortable">';
$max = 5;
/*
 * Search invoices
 */

if (! empty($conf->dolimail->enabled)) //  && $user->rights->facture->lire
{
	print '<form method="post" action="'.DOL_URL_ROOT.'/dolimail/liste.php">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="list">';
	print '<input type="hidden" name="button_search" value="Suchen">';
	print '<table class="noborder nohover" width="100%">';
	print "<tr class=\"liste_titre\">";
	print '<td colspan="3">'.$langs->trans("Mail").'</td></tr>';
	print "<tr ".$bc[0]."><td><label for=\"smailfrom\">".$langs->trans("DolimaillFrom").'</label>:</td><td><input type="text" name="smailfrom" id="smailfrom" class="flat" size="18"></td>';
	print '<td rowspan="2"><input type="submit" value="'.$langs->trans("Search").'" class="button"></td></tr>';
	print "<tr ".$bc[0]."><td><label for=\"sall\">".$langs->trans("Other").'</label>:</td><td><input type="text" name="sall" id="sall" class="flat" size="18"></td>';
	print '</tr>';
	print "</table></form><br>";
}

if (! empty($conf->ccd->enabled)) //  && $user->rights->facture->lire
{
	//print '<form method="post" action="'.DOL_URL_ROOT.'/ccd/asterisk/societecall.php">';
	//print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	//print '<table class="noborder nohover" width="100%">';
	//print "<tr class=\"liste_titre\">";
	//print '<td colspan="3">'.$langs->trans("ccd").'</td></tr>';
	//print "<tr ".$bc[0]."><td><label for=\"sf_ref\">".$langs->trans("Ref").'</label>:</td><td><input type="text" name="sf_ref" id="sf_ref" class="flat" size="18"></td>';
	//print '<td rowspan="2"><input type="submit" value="'.$langs->trans("Search").'" class="button"></td></tr>';
	//print "<tr ".$bc[0]."><td><label for=\"sall\">".$langs->trans("Other").'</label>:</td><td><input type="text" name="sall" id="sall" class="flat" size="18"></td>';
	//print '</tr>';
	//print "</table></form><br>";

	require_once(DOL_DOCUMENT_ROOT.'/ccd/core/boxes/box_ccd.php');
	$box_ccd = new box_ccd();
	$box_ccd->loadBox();
	print $box_ccd->showBox();

	print '<br>';
}

if (! empty($conf->externalrss->enabled)) //  && $user->rights->facture->lire
{
	require_once(DOL_DOCUMENT_ROOT.'/core/boxes/box_external_rss.php');
	$i = 1;
	while (!empty(constant("EXTERNAL_RSS_URLRSS_".$i))) {
		$rssBox = new box_external_rss($db, $i." ");
		$rssBox->loadBox(5);
		print $rssBox->showBox();
		$i++;
	}
}

//if (! empty($conf->agenda->enabled)) //  && $user->rights->facture->lire
//{
//	require_once(DOL_DOCUMENT_ROOT.'/core/boxes/box_actions.php');
//	$box_actions = new box_actions();
//	$box_actions->loadBox();
//	print $box_actions->showBox();
//}

print '</div><div class="fichetwothirdright"><div class="ficheaddleft connectedSortable ui-sortable">';
	
if (! empty($conf->dolimail->enabled)) //  && $user->rights->facture->lire
{
	require_once(DOL_DOCUMENT_ROOT.'/dolimail/core/boxes/box_graf_priority.php');
	$box_graf_priority = new box_graf_priority();
	$box_graf_priority->loadBox();
	print $box_graf_priority->showBox();

	print '<br>';

	require_once(DOL_DOCUMENT_ROOT.'/dolimail/core/boxes/box_dolimail.php');
	$box_dolimail = new box_dolimail();
	$box_dolimail->loadBox();
	print $box_dolimail->showBox();

	print '<br>';

	// User Mail Boxen suchen
	$sql = "SELECT *";
	$sql.= " FROM ".MAIN_DB_PREFIX."usermailboxconfig ";
	$sql.= " WHERE fk_user = ".$user->id;

	$result = $db->query($sql);
	if ($result)
	{
		while ($objp = $db->fetch_object($result))
		{
			$_SESSION['ImapBoxMailBox']['rowid'][] = $objp->rowid;
			$_SESSION['ImapBoxMailBox']['mailbox_imap_label'][] = $objp->mailbox_imap_label;
		}
	}

	if(is_array($_SESSION['ImapBoxMailBox']['rowid'])){
		$_SESSION['ImapBoxMailBox']['counter'] = count($_SESSION['ImapBoxMailBox']['rowid']);
		$_SESSION['ImapBoxMailBox']['num'] = 0;
	}
	
	require_once(DOL_DOCUMENT_ROOT.'/dolimail/core/boxes/box_imap_dolimail.php');
	$box_imap_dolimail = new box_imap_dolimail();
	$box_imap_dolimail->loadBox();
	print $box_imap_dolimail->showBox();
}



print '</div></div></div>';

llxFooter();

$db->close();

/*
if (! empty($conf->salesassistent->enabled)) //  && $user->rights->facture->lire
{
	print '<form method="post" action="'.DOL_URL_ROOT.'/compta/facture/list.php">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<table class="noborder nohover" width="100%">';
	print "<tr class=\"liste_titre\">";
	print '<td colspan="3">'.$langs->trans("salesassistent").'</td></tr>';
	print "<tr ".$bc[0]."><td><label for=\"sf_ref\">".$langs->trans("Ref").'</label>:</td><td><input type="text" name="sf_ref" id="sf_ref" class="flat" size="18"></td>';
	print '<td rowspan="2"><input type="submit" value="'.$langs->trans("Search").'" class="button"></td></tr>';
	print "<tr ".$bc[0]."><td><label for=\"sall\">".$langs->trans("Other").'</label>:</td><td><input type="text" name="sall" id="sall" class="flat" size="18"></td>';
	print '</tr>';
	print "</table></form><br>";
}

if (! empty($conf->dolichat->enabled)) //  && $user->rights->facture->lire
{
	print '<form method="post" action="'.DOL_URL_ROOT.'/compta/facture/list.php">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<table class="noborder nohover" width="100%">';
	print "<tr class=\"liste_titre\">";
	print '<td colspan="3">'.$langs->trans("dolichat").'</td></tr>';
	print "<tr ".$bc[0]."><td><label for=\"sf_ref\">".$langs->trans("Ref").'</label>:</td><td><input type="text" name="sf_ref" id="sf_ref" class="flat" size="18"></td>';
	print '<td rowspan="2"><input type="submit" value="'.$langs->trans("Search").'" class="button"></td></tr>';
	print "<tr ".$bc[0]."><td><label for=\"sall\">".$langs->trans("Other").'</label>:</td><td><input type="text" name="sall" id="sall" class="flat" size="18"></td>';
	print '</tr>';
	print "</table></form><br>";
}
*/
?>
