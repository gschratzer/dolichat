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
require '../../main.inc.php';  
                                // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory

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

//$var=! $var;
//print '<tr '.$bc[$var].'><td>';
//print $langs->trans("DelShowInt");
//print '<td colspan="2">';
//print $html->selectyesno("dolichat_SHOW_INT",$conf->global->dolichat_SHOW_INT,1,$disable);
//if($disable)print '<input type="hidden" name="dolichat_SHOW_INT" value="'.$conf->global->dolichat_SHOW_INT.'">';
//print "</td></tr>\n";

//$var=! $var;
//print '<tr '.$bc[$var].'><td>';
//print $langs->trans("DelShowLinkAsItOwn");
//print '<td colspan="2">';
//print $html->selectyesno("dolichat_SHOW_LINK_AS_IT_OWN",$conf->global->dolichat_SHOW_LINK_AS_IT_OWN,1,$disable);
//if($disable)print '<input type="hidden" name="dolichat_SHOW_LINK_AS_IT_OWN" value="'.$conf->global->dolichat_SHOW_LINK_AS_IT_OWN.'">';
//print "</td></tr>\n";

//$var=! $var;
//print '<tr '.$bc[$var].'><td>';
//print $langs->trans("DelShowLink");
//print '<td colspan="2">';
//print $html->selectyesno("dolichat_SHOW_LINK",$conf->global->dolichat_SHOW_LINK,1,$disable);
//if($disable)print '<input type="hidden" name="dolichat_SHOW_LINK" value="'.$conf->global->dolichat_SHOW_LINK.'">';
//print "</td></tr>\n";

//$var=! $var;
//print '<tr '.$bc[$var].'><td>';
//print $langs->trans("DelShowLinkAgenda");
//print '<td colspan="2">';
//print $html->selectyesno("dolichat_SHOW_LINK_AGENDA",$conf->global->dolichat_SHOW_LINK_AGENDA,1,$disable);
//if($disable)print '<input type="hidden" name="dolichat_SHOW_LINK_AGENDA" value="'.$conf->global->dolichat_SHOW_LINK_AGENDA.'">';
//print "</td></tr>\n";

/*
$var=! $var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("DelUseInWebapp");
print '<td colspan="2">';
print $html->selectyesno("dolichat_USE_IN_WEBAPP",$conf->global->dolichat_USE_IN_WEBAPP,1,$disable);
if($disable)print '<input type="hidden" name="dolichat_USE_IN_WEBAPP" value="'.$conf->global->dolichat_USE_IN_WEBAPP.'">';
print "</td></tr>\n";
*/

//$var=! $var;
//print '<tr '.$bc[$var].'><td>';
//print $langs->trans("DelTimeUse");
//print '<td colspan="2">';
//print $html->selectyesno("dolichat_USE_DEL_TIME",$conf->global->dolichat_USE_DEL_TIME,1,$disable);
//if($disable)print '<input type="hidden" name="dolichat_USE_DEL_TIME" value="'.$conf->global->dolichat_USE_DEL_TIME.'">';
//print "</td></tr>\n";

$var=! $var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("DelTime").'</td>';
print '<td><input type="text" class="flat" name="dolichat_DEL_TIME" value="'. ($_POST["dolichat_DEL_TIME"]?$_POST["dolichat_DEL_TIME"]:$conf->global->dolichat_DEL_TIME) . '" size="8"> '.$langs->trans("Days").'</td>';
print '</tr>';

//$var=! $var;
//print '<tr '.$bc[$var].'><td>';
//print $langs->trans("DisplayInfo");
//print '<td colspan="2">';
//print $html->selectyesno("dolichat_WERBUNG",$conf->global->dolichat_WERBUNG,1,$disable);
//if($disable)print '<input type="hidden" name="dolichat_WERBUNG" value="'.$conf->global->dolichat_WERBUNG.'">';
//print "</td></tr>\n";

print '</table>';
print '<br>';

print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';

print "</form>\n";

/* WhatsApp Settings 
print_fiche_titre($langs->trans("WhatsAppSetup"),'','setup');

print $langs->trans("WhatsAppDesc")."<br>\n";
print "<br>\n";

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="setwa">';

	clearstatcache();
	$var=true;

	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre"><td>'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

	// Disable
	$var=!$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans("MAIN_DISABLE_WHATSAPP").'</td><td>';
	print $html->selectyesno('MAIN_DISABLE_WHATSAPP',$conf->global->MAIN_DISABLE_WHATSAPP,1);
	print '</td></tr>';
	
	// Separator
	$var=!$var;
	print '<tr '.$bc[$var].'><td colspan="2">&nbsp;</td></tr>';

  // WhatsApp Username
	$var=!$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans("MAIN_WHATSAPP_UN");
	$htmltooltip='<b>'.$langs->trans("WhatsAppUNDescTitle").'</b>: <br>';
	$htmltooltip.=$langs->trans("WhatsAppUNDescDesc").' <br>';
	print $html->textwithpicto('', $htmltooltip, 1, 'info').'</td>';
	print '<td><input class="flat" name="MAIN_WHATSAPP_UN" size="42" value="' . (! empty($conf->global->MAIN_WHATSAPP_UN)?$conf->global->MAIN_WHATSAPP_UN:'');
	print '"></td></tr>';

  
  // Nickname
	$var=!$var;
	print '<tr '.$bc[$var].'><td>'.$langs->trans("MAIN_WHATSAPP_NN");
	$htmltooltip='<b>'.$langs->trans("WhatsAppNNDescTitle").'</b>: <br>';
	$htmltooltip.=$langs->trans("WhatsAppNNDescDesc").' <br>';
	print $html->textwithpicto('', $htmltooltip, 1, 'info').'</td>';
	print '<td><input class="flat" name="MAIN_WHATSAPP_NN" size="42" value="' . (! empty($conf->global->MAIN_WHATSAPP_NN)?$conf->global->MAIN_WHATSAPP_NN:'');
	print '"></td></tr>';

  // PW
		$var=!$var;
		$mainwapw=(! empty(MAIN_WHATSAPP_PW)?$conf->global->MAIN_WHATSAPP_PW:'');
		print '<tr '.$bcdd[$var].'><td>'.$langs->trans("MAIN_WHATSAPP_PW");
		$htmltooltip='<b>'.$langs->trans("WhatsAppPWDescTitle").'</b>: <br>';
	  $htmltooltip.=$langs->trans("WhatsAppPWDescDesc").' <br>';
	  print $html->textwithpicto('', $htmltooltip, 1, 'info').'</td><td>';

		
		// SuperAdministrator access only
		if (empty($conf->multicompany->enabled) || ($user->admin && !$user->entity))
		{
			print '<input class="flat" type="password" name="MAIN_WHATSAPP_PW" size="32" value="' . $mainwapw . '">';
		}
		else
		{
			$htmltext = $langs->trans("ContactSuperAdminForChange");
			print $html->textwithpicto($conf->global->MAIN_WHATSAPP_PW,$htmltext,1,'superadmin');
			print '<input type="hidden" name="MAIN_WHATSAPP_PW" value="'.$mainwapw.'">';
		}
		print '</td></tr>';
print '</table>';
print '<br>';
print '<div class="tabsAction">';
print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=test&amp;mode=init">'.$langs->trans("DoTestSend").'</a>';
print '<center><input type="submit" class="button" value="'.$langs->trans("Save").'"></center>';
print '</div>';
print "</form>\n";	

// Show email send test form
	if ($action == 'test' || $action == 'testhtml')
	{
		print '<br>';
		print_titre($action == 'testhtml'?$langs->trans("DoTestSendHTML"):$langs->trans("DoTestSend"));

		// Cree l'objet formulaire mail
		
		include_once DOL_DOCUMENT_ROOT.'/dolichat/class/html.formwhatsapp.class.php';
		$formmail = new FormMail($db);
		$formmail->fromname = (isset($_POST['fromname'])?$_POST['fromname']:$conf->global->MAIN_MAIL_EMAIL_FROM);
		$formmail->frommail = (isset($_POST['frommail'])?$_POST['frommail']:$conf->global->MAIN_MAIL_EMAIL_FROM);
		$formmail->withfromreadonly=0;
		$formmail->withsubstit=0;
		$formmail->withfrom=1;
		$formmail->witherrorsto=1;
		$formmail->withto=(! empty($_POST['sendto'])?$_POST['sendto']:($user->email?$user->email:1));
		$formmail->withtocc=(! empty($_POST['sendtocc'])?$_POST['sendtocc']:1);       // ! empty to keep field if empty
		$formmail->withtoccc=(! empty($_POST['sendtoccc'])?$_POST['sendtoccc']:1);    // ! empty to keep field if empty
		$formmail->withtopic=(isset($_POST['subject'])?$_POST['subject']:$langs->trans("Test"));
		$formmail->withtopicreadonly=0;
		$formmail->withfile=2;
		$formmail->withbody=(isset($_POST['message'])?$_POST['message']:($action == 'testhtml'?$langs->transnoentities("PredefinedMailTestHtml"):$langs->transnoentities("PredefinedMailTest")));
		$formmail->withbodyreadonly=0;
		$formmail->withcancel=1;
		$formmail->withdeliveryreceipt=1;
		$formmail->withfckeditor=($action == 'testhtml'?1:0);
		$formmail->ckeditortoolbar='dolibarr_mailings';
		// Tableau des substitutions
		$formmail->substit=$substitutionarrayfortest;
		// Tableau des parametres complementaires du post
		$formmail->param["action"]=($action == 'testhtml'?"sendhtml":"send");
		$formmail->param["models"]="body";
		$formmail->param["mailid"]=0;
		$formmail->param["returnurl"]=$_SERVER["PHP_SELF"];

		// Init list of files
        if (GETPOST("mode")=='init')
		{
			$formmail->clear_attached_files();
		}

		print $formmail->get_form(($action == 'testhtml'?'addfilehtml':'addfile'),($action == 'testhtml'?'removefilehtml':'removefile'));

		print '<br>';
	}
/* WhatsApp Settings Ende */




dol_htmloutput_mesg($mesg);

$db->close();

llxFooter();
?>