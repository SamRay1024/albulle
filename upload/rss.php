<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

///////////////////////////////
// LICENCE
///////////////////////////////
//
// © DUCARRE Cédric (SamRay1024), Bubulles Créations, (09/05/2005)
//
// webmaster@jebulle.net
// http://jebulle.net
//
// Albulle est un programme de galerie photos pour site internet.
//
// Ce logiciel est régi par la licence CeCILL soumise au droit français et
// respectant les principes de diffusion des logiciels libres. Vous pouvez
// utiliser, modifier et/ou redistribuer ce programme sous les conditions
// de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
// sur le site "http://www.cecill.info".
//
// En contrepartie de l'accessibilité au code source et des droits de copie,
// de modification et de redistribution accordés par cette licence, il n'est
// offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
// seule une responsabilité restreinte pèse sur l'auteur du programme,  le
// titulaire des droits patrimoniaux et les concédants successifs.
//
// A cet égard  l'attention de l'utilisateur est attirée sur les risques
// associés au chargement,  à l'utilisation,  à la modification et/ou au
// développement et à la reproduction du logiciel par l'utilisateur étant
// donné sa spécificité de logiciel libre, qui peut le rendre complexe à
// manipuler et qui le réserve donc à des développeurs et des professionnels
// avertis possédant  des  connaissances  informatiques approfondies.  Les
// utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
// logiciel à leurs besoins dans des conditions permettant d'assurer la
// sécurité de leurs systèmes et ou de leurs données et, plus généralement,
// à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.
//
// Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
// pris connaissance de la licence CeCILL, et que vous en avez accepté les
// termes.
//
///////////////////////////////

/**
 * Albulle - Galerie photos
 *
 * Génère un fichier RSS du dossier donné pour CoolIris.
 * (http://cooliris.com)
 *
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @name Albulle
 * @since 23/10/2008
 * @version 12/04/2010
 */
 
define('JB_AL_ROOT',		'albulle/');

define( '_JB_INCLUDE_AUTH', 1 );

require_once( JB_AL_ROOT.'config.php');
require_once( JB_AL_ROOT.'core/includes/classes/util.class.php' );
 
$_JB_AL_GET['s_rep_courant']	= isset( $_GET['rep']		)	? stripslashes(rawurldecode( (string) $_GET['rep'] ))	: '';

header( 'Content-type: text/html; charset=utf-8' );

echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>';

// Si dossier donné
if( !empty($_JB_AL_GET['s_rep_courant']) ) {
	
	// Lecture des images présentes dans le dossier
	$aImages = Util::advScanDir(
		JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_PHOTOS.$_JB_AL_GET['s_rep_courant'],
		'FICHIERS_SEULEMENT',
		array(),
		array( 'gif', 'jpe', 'jpeg', 'jpg', 'png' ),
		array( 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/x-png', 'image/png')
	);
						
	$sItems = '';
	foreach( $aImages as $sImage ) {
		
		$aImage 		= explode( '.', $sImage );

		$sCheminPhoto	= JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_PHOTOS.$_JB_AL_GET['s_rep_courant'].'/'.$sImage;
		$sUrlPhoto		= JB_AL_BASE_URL.$sCheminPhoto;
		$sUrlMiniature	= JB_AL_BASE_URL.JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_MINIATURES.$aImage[0].'_'.JB_AL_VIGNETTES_LARGEUR.'x'.JB_AL_VIGNETTES_HAUTEUR.'_'.md5($sCheminPhoto).'.'.$aImage[1];
		
		$sTitre			= basename($sImage);
		if( JB_AL_FILTRE_PREFIXES_ACTIF )	$sTitre = Util::enleverPrefixe( $sTitre, JB_AL_PREFIXES_SEPARATEUR );
		if( JB_AL_REMPLACER_TIRETS_BAS )	$sTitre = str_replace( '_', ' ', $sTitre );
		if( !JB_AL_AFFICHER_EXTENSION )	    $sTitre = Util::sousChaineGauche( $sTitre, '.', 1 );
		
		echo "\n\t\t<item>\n\t\t\t";
		echo '<title>'.basename($sImage)."</title>\n\t\t\t";
		echo '<link>'.$sUrlPhoto."</link>\n\t\t\t";
		echo '<media:thumbnail url="'.$sUrlMiniature.'"/>'."\n\t\t\t";
		echo '<media:content url="'.$sUrlPhoto."\"/>\n\t\t";
		echo "</item>";
	}
}

echo '
	</channel>
</rss>';