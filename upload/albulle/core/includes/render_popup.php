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
 * @version 06/05/2010
 */

if( !defined( '_JB_INCLUDE_AUTH' ) ) { 
	header( 'Content-type: text/html; charset=utf-8' );
	exit( 'Vous n\'êtes pas autorisé à afficher cette page.' );
}

$oRenduPage = new JbParser( $_JB_AL_VARS['s_acces_theme'] .'html/', 'popup' );

$oRenduPage->assignVar(array(
	'charset'		=> (JB_AL_SORTIE_ISO ? 'ISO-8859-1' : 'UTF-8'),
	'popup_titre'	=> $sHeadTitre,
	'popup_source'	=> $sCheminImg
	);

return $oRenduPage->parse();