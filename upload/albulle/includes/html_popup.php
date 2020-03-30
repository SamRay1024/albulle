<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

/**
 * @name html_popup.php
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @since 11/06/2006
 * @version 0.1
 */

if( !defined( '_JB_INCLUDE_AUTH' ) ) { 
	header( 'Content-type: text/html; charset=utf-8' );
	exit( 'Vous n\'êtes pas autorisé à afficher cette page.' );
}

// Définition du tableau avec les pseudos-variables à remplacer
$aPseudosVariables = array(
	'`{POPUP_TITRE}`'	=> $sHeadTitre,
	'`{POPUP_SOURCE}`'	=> $sCheminImg
	);

// Remplacement dans le fichier theme des popup et renvoi au fichier appelant
require_once( JB_AL_ROOT.'includes/classes/util.class.php' );
$oOutils = new util();

return $oOutils->parser(JB_AL_ROOT.JB_AL_DOSSIER_THEMES.JB_AL_DOSSIER_THEME_ACTIF.'html/popup.thm.php', $aPseudosVariables);

?>
