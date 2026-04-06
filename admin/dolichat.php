<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2011 	   Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2012 	   Ferran Marcet        <fmarcet@2byte.es>
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
 *	\file       htdocs/mobiles/admin/mobiles.php
 *	\ingroup    mobiles
 *	\brief      Setup page for mobiles module
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

require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");

// Security check
if (!$user->admin)
accessforbidden();

$langs->load("admin");
$langs->load("dolichat@dolichat");


/*
 * Actions
 */

if (GETPOST('action','string') == 'updateMask')
{
    $maskconstticket=GETPOST('maskconstticket');
    $maskconstticketcredit=GETPOST('maskconstticketcredit');
    $maskticket=GETPOST('maskticket');
    $maskcredit=GETPOST('maskcredit');
    $maskconstfacsim=GETPOST('maskconstfacsim');
    $maskconstfacsimcredit=GETPOST('maskconstfacsimcredit');
    $maskfacsim=GETPOST('maskfacsim');
    $maskfacsimcredit=GETPOST('maskfacsimcredit');
    if ($maskconstticket) dolibarr_set_const($db,$maskconstticket,$maskticket,'chaine',0,'',$conf->entity);
    if ($maskconstticketcredit) dolibarr_set_const($db,$maskconstticketcredit,$maskcredit,'chaine',0,'',$conf->entity);
    if ($maskconstfacsim) dolibarr_set_const($db,$maskconstfacsim,$maskfacsim,'chaine',0,'',$conf->entity);
    if ($maskconstfacsimcredit) dolibarr_set_const($db,$maskconstfacsimcredit,$maskfacsimcredit,'chaine',0,'',$conf->entity);
 
}

if (GETPOST("action") == 'set')
{
	$db->begin();
	
	$res = dolibarr_set_const($db,"dolichat_USE_DEL_TIME", GETPOST("dolichat_USE_DEL_TIME"),'chaine',0,'',$conf->entity);

	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"dolichat_DEL_TIME", GETPOST("dolichat_DEL_TIME"),'chaine',0,'',$conf->entity);
	
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"dolichat_SHOW_INT", GETPOST("dolichat_SHOW_INT"),'chaine',0,'',$conf->entity);
	
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"dolichat_SHOW_LINK", GETPOST("dolichat_SHOW_LINK"),'chaine',0,'',$conf->entity);

	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"dolichat_SHOW_LINK_AGENDA", GETPOST("dolichat_SHOW_LINK_AGENDA"),'chaine',0,'',$conf->entity);
	
	//if (! $res > 0) $error++;
	//$res = dolibarr_set_const($db,"dolichat_USE_IN_WEBAPP", GETPOST("dolichat_USE_IN_WEBAPP"),'chaine',0,'',$conf->entity);
	
	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"dolichat_SHOW_LINK_AS_IT_OWN", GETPOST("dolichat_SHOW_LINK_AS_IT_OWN"),'chaine',0,'',$conf->entity);

	if (! $res > 0) $error++;
	$res = dolibarr_set_const($db,"dolichat_WERBUNG", GETPOST("dolichat_WERBUNG"),'chaine',0,'',$conf->entity);
			

	if (! $res > 0) $error++;
 	if (! $error)
    {
        $db->commit();
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $db->rollback();
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}
elseif (GETPOST("action") == 'setwa')
{
  $db->begin();
  // Konstanten
  
  if (isset($_POST["MAIN_DISABLE_WHATSAPP"]))    $res = dolibarr_set_const($db, "MAIN_DISABLE_WHATSAPP",    GETPOST("MAIN_DISABLE_WHATSAPP"), 'chaine',0,'',0);
  if (isset($_POST["MAIN_WHATSAPP_PW"]))    $res = dolibarr_set_const($db, "MAIN_WHATSAPP_PW",    GETPOST("MAIN_WHATSAPP_PW"), 'chaine',0,'',0);
  if (isset($_POST["MAIN_WHATSAPP_UN"]))    $res = dolibarr_set_const($db, "MAIN_WHATSAPP_UN",    GETPOST("MAIN_WHATSAPP_UN"), 'chaine',0,'',0);
  if (isset($_POST["MAIN_WHATSAPP_NN"]))    $res = dolibarr_set_const($db, "MAIN_WHATSAPP_NN",    GETPOST("MAIN_WHATSAPP_NN"), 'chaine',0,'',0); 
   
   
  if (! $res > 0) $error++;
 	if (! $error)
  {
      $db->commit();
      $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
  }
  else
  {
      $db->rollback();
      $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
  }
}
/*
 * View
 */
$helpurl='EN:Module_Chat|FR:Module_Chat_FR|ES:M&oacute;dulo_Chat';
llxHeader('',$langs->trans("Chat Setup"),$helpurl);

$html=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("Chat Setup"),$linkback,'setup');
print '<br>';

if($conf->global->MOBILE_FACTURE == 1){
print_titre($langs->trans("FacsimNumberingModule"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td nowrap>'.$langs->trans("Example").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="16">'.$langs->trans("Infos").'</td>';
print '</tr>'."\n";

clearstatcache();

$var=true;
foreach ($conf->file->dol_document_root as $dirroot)
{
	$dir = $dirroot . "/mobiles/backend/numerotation/numerotation_facsim/";

	if (is_dir($dir))
	{
		$handle = opendir($dir);
		if (is_resource($handle))
		{
			while (($file = readdir($handle))!=FALSE)
			{
				if (! is_dir($dir.$file) || (substr($file, 0, 1) <> '.' && substr($file, 0, 3) <> 'CVS'))
				{
					$filebis = $file;
					$classname = preg_replace('/\.php$/','',$file);
					// For compatibility
					if (! is_file($dir.$filebis))
					{
						$filebis = $file."/".$file.".modules.php";
						$classname = "mod_facsim_".$file;
					}
					//print "x".$dir."-".$filebis."-".$classname;
					if (! class_exists($classname) && is_readable($dir.$filebis) && (preg_match('/mod_/',$filebis) || preg_match('/mod_/',$classname)) && substr($filebis, dol_strlen($filebis)-3, 3) == 'php')
					{
						// Chargement de la classe de numerotation
						require_once($dir.$filebis);

						$module = new $classname($db);

						// Show modules according to features level
						if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) continue;
						if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) continue;

						if ($module->isEnabled())
						{
							$var = !$var;
							print '<tr '.$bc[$var].'><td width="100">';
							echo preg_replace('/mod_facsim_/','',preg_replace('/\.php$/','',$file));
							print "</td><td>\n";

							print $module->info();

							print '</td>';

							// Show example of numbering module
							print '<td nowrap="nowrap">';
							$tmp=$module->getExample();
							if (preg_match('/^Error/',$tmp)) print $langs->trans($tmp);
							else print $tmp;
							print '</td>'."\n";

							print '<td align="center">';
							//print "> ".$conf->global->FACTURE_ADDON." - ".$file;
							if ($conf->global->FACSIM_ADDON == $file || $conf->global->FACSIM_ADDON.'.php' == $file)
							{
								print img_picto($langs->trans("Activated"),'on');
							}
							else
							{
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=setmodfacsim&amp;value='.preg_replace('/\.php$/','',$file).'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
							}
							print '</td>';

							//$facture=new Ticket($db);
							//$facture->initAsSpecimen();

							// Example for standard invoice
							$htmltooltip='';
							$htmltooltip.=''.$langs->trans("Version").': <b>'.$module->getVersion().'</b><br>';
							$facture->type=0;
							$nextval=$module->getNextValue($mysoc,$facture);
							if ("$nextval" != $langs->trans("NotAvailable"))	// Keep " on nextval
							{
								$htmltooltip.=$langs->trans("NextValueForFacsims").': ';
								if ($nextval)
								{
									$htmltooltip.=$nextval.'<br>';
								}
								else
								{
									$htmltooltip.=$langs->trans($module->error).'<br>';
								}
							}


							print '<td align="center">';
							print $html->textwithpicto('',$htmltooltip,1,0);

							if ($conf->global->FACSIM_ADDON.'.php' == $file)  // If module is the one used, we show existing errors
							{
								if (! empty($module->error)) dol_htmloutput_mesg($module->error,'','error',1);
							}

							print '</td>';

							print "</tr>\n";

						}
					}
				}
			}
			closedir($handle);
		}
	}
}

print '</table>';

print "<br>";
}

print $langs->trans("ChatAdminFunction").': <p>';
print $langs->trans("DelAllMessages").': <b>/clear</b>';

print_titre($langs->trans("OtherOptions"));

// Mode
$var=true;
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td><td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

$var=! $var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("DelTime").'</td>';
print '<td><input type="text" class="flat" name="dolichat_DEL_TIME" value="'. ($_POST["dolichat_DEL_TIME"]?$_POST["dolichat_DEL_TIME"]:$conf->global->dolichat_DEL_TIME) . '" size="8"> '.$langs->trans("Days").'</td>';
print '</tr>';

print '</table>';
print '<br>';

print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';

print "</form>\n";

dol_htmloutput_mesg($mesg);

$db->close();

llxFooter();
?>