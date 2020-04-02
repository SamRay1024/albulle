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
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @name Albulle
 * @since 09/05/2005
 * @version 30/09/2009
 */

/**
 * Fonction qui affiche les erreurs et quitte le programme.
 *
 * @param string	$sMessage	Message de l'erreur.
 */
function erreur( $sMessage ) {
	headers_sent() or header( 'Content-type: text/html; charset=utf-8' );
	exit( '
		<p style="background-color: papayawhip; border: 3px solid #c33; margin: 20px; padding: 20px;">
			<strong># ALBULLE #</strong> <strong style="color: #c33;">[ Erreur ]</strong> <br /><br />
			<strong>&raquo;</strong> '.$sMessage.
		'</p>
		<p style="background-color: azure; border: 3px solid steelblue; margin: 40px; padding: 10px; text-align: center;">
			N\'hésitez pas à vous rendre sur le forum d\'Albulle si vous n\'arrivez pas à trouver
			de solution à votre problème.<br />
			<strong>&raquo;</strong>
			<a href="http://forums.jebulle.net" style="text-decoration: none; color: steelblue;">http://forums.jebulle.net</a>
			<strong>&laquo;</strong>
		</p>'
	);
}

/**
 * Fonction qui permet d'inclure un fichier.
 *
 * @param string	$sFichier	Fichier à inclure.
 */
function inclure( $sFichier ) {
	if( !@include(JB_AL_ROOT.$sFichier) )
		erreur('Impossible de trouver le fichier <em>'.$sFichier.'</em>.
				Ce fichier est nécessaire pour le fonctionnement d\'Albulle.');
}

// Vérification que la racine est bien définie
if( !defined('JB_AL_ROOT') )
	erreur( 'La constante JB_AL_ROOT doit être définie et pointer sur une installation valide d\'Albulle.' );

define( '_JB_INCLUDE_AUTH', 1 );

// ====================
// INCLUSION DES FICHIERS NECESSAIRES
//
inclure( 'config.php' );
inclure( 'core/includes/fonctions.php' );
inclure( 'core/includes/classes/url.class.php' );
inclure( 'core/includes/classes/util.class.php' );
if(JB_AL_PANIER_ACTIF === true) inclure( 'core/includes/classes/panierdefichiers.class.php' );

// Vérifier si la galerie est ouverte
if( JB_AL_FERMER === true || file_exists(JB_AL_ROOT.'lock') )
	exit( '
		<p style="background-color: azure; border: 3px solid steelblue; margin: 40px; padding: 10px; text-align: center;">
			'.JB_AL_MSG_FERMETURE.'
		</p>'
	);


// ====================
// INITIALISATIONS
//
global $_JB_AL_VARS, $aActions, $oOutils, $oUrl;

$_JB_AL_VARS	= array();	// Tableau qui contiendra toutes les variables nécessaires et disponibles pour l'affichage
$_MINIATURES	= array();	// Tableau qui contiendra les miniatures
$_JB_AL_GET		= array();	// Tableau qui contiendra les paramètres reçus dans l'URL
$_JB_AL_POST	= array();	// Tableau qui contiendra les paramètres reçus par les formulaires

$_JB_AL_VARS['s_version']					= '1.1.1';

$_JB_AL_VARS['s_acces_theme']				= JB_AL_ROOT.JB_AL_DOSSIER_THEMES.JB_AL_DOSSIER_THEME_ACTIF;
$_JB_AL_VARS['s_arborescence']				= $_JB_AL_VARS['s_menu_panier'] = '';
$_JB_AL_VARS['s_lien_panier_tout_ajouter']	= $_JB_AL_VARS['s_lien_panier_tout_supprimer'] = '';
$_JB_AL_VARS['s_navigation']				= $_JB_AL_VARS['s_pagination'] = '';
$_JB_AL_VARS['s_classe_css_vignette']		= '';
$_JB_AL_VARS['s_texte_mode_affichage']		= $_JB_AL_VARS['s_lien_mode_affichage'] = '';
$_JB_AL_VARS['b_defilement_auto']			= false;

$aActions		= array( 'voir' => '' );	// Tableau des actions disponibles
$aDossiersUrl	= array();	// Tableau qui contiendra la liste des dossiers du répertoire courant passé par l'url
$aListePhotos	= array();	// Tableau qui contiendra la liste des photos pour la page courante

$iDiapo = $iImgAAfficher = 0;

$oOutils	= new Util();
$oUrl		= new Url(JB_AL_CONSERVER_URL_HOTE, array('rep', 'page', 'act', 'img', 'diapo', 'voir', 'diaporama', 'galerie'));

@set_magic_quotes_runtime(0);
@ini_set('session.use_trans_sid', '0');

// Envoi des entêtes HTTP
$sCharset = ( JB_AL_INTEGRATION_SITE === true && JB_AL_SORTIE_ISO === true ) ? 'iso-8859-1' : 'utf-8';
if(!headers_sent()) header( 'Content-type: text/html; charset='.$sCharset );		// Force l'encodage de sortie à l'UTF-8


// ====================
// VERIFICATIONS
//
verifications();


// ====================
// LECTURE DES PARAMETRES PASSES DANS L'URL
//
$_JB_AL_GET['s_rep_courant']	= isset( $_GET['rep']		)	? stripslashes(rawurldecode( $_GET['rep'] ))	: '';
$_JB_AL_GET['i_page_courante']	= isset( $_GET['page']		)	? (int) $_GET['page']							: 1;
$_JB_AL_GET['s_action']			= isset( $_GET['act']		)	? $_GET['act']									: '';
$_JB_AL_GET['s_image']			= isset( $_GET['img']		)	? stripslashes(rawurldecode( $_GET['img'] ))	: '';
$_JB_AL_GET['s_diapo_courante']	= isset( $_GET['diapo'] 	)	? stripslashes(rawurldecode( $_GET['diapo'] ))	: '';
$_JB_AL_GET['b_voir_panier']	= isset( $_GET['voir']		);

// ====================
// Gestion du mode diaporama
//
// Initialisation
if( session_id() === '' )	session_start();
if( !isset($_SESSION['JB_AL_MODE_DIAPORAMA']) ) $_SESSION['JB_AL_MODE_DIAPORAMA'] = JB_AL_MODE_DIAPO_DEFAUT;

// Si mode diaporama demandé
if( isset($_GET['diaporama']) )		$_SESSION['JB_AL_MODE_DIAPORAMA'] = true;
if( isset($_GET['galerie']) )		$_SESSION['JB_AL_MODE_DIAPORAMA'] = false;

// Sauvegarde du mode d'affichage dans le tableau des variables
$_JB_AL_VARS['b_mode_diaporama'] = $_SESSION['JB_AL_MODE_DIAPORAMA'];


// ====================
// GESTION DU PANIER
//
if(JB_AL_PANIER_ACTIF === true)
{
	$oPanier = new PanierDeFichiers( JB_AL_ROOT.JB_AL_DOSSIER_DATA, JB_AL_PANIER_CAPACITE_MAX, JB_AL_PANIER_POIDS_MAX );
	
	$sCheminDansPanier = cheminDansPanier($_JB_AL_GET['s_image']);
	
	// Lancement des actions.
	// L'action de télechargement est désormais située dans le fichier download.php (ou le nom que nous lui avez donné).
	switch ( $_JB_AL_GET['s_action'] )
	{
		case 'ajouter' :	$oPanier->Ajouter( $sCheminDansPanier ); break;
		case 'supprimer' :
				$oPanier->Supprimer( $sCheminDansPanier );
	
				if( $_JB_AL_GET['b_voir_panier'] &&										// Si on visionne le panier ...
					$_JB_AL_VARS['b_mode_diaporama'] && 								// ... en mode diaporama ...
					basename($_JB_AL_GET['s_image']) === $_JB_AL_GET['s_diapo_courante']		// ... et que l'image supprimée du panier est celle visionnée
				)
					// alors on efface la diapo courante ... pour ne pas réafficher l'image supprimée ;-)
					$_JB_AL_GET['s_diapo_courante'] = '';
	
			break;
		case 'vider':		$oPanier->ViderPanier(); break;
	}
	
	// Gestion de l'exploration du panier :
	// si exploration du panier demandée et qu'il y a des fichiers dans le panier, on définit l'action pour l'url ; sinon
	// on désactive l'exporation du panier
	($_JB_AL_GET['b_voir_panier'] && $oPanier->CompterFichiers() > 0) ? $aActions['voir'] = '&amp;voir' : $_JB_AL_GET['b_voir_panier'] = false;
}


// ====================
// GESTION DU CHEMIN PASSE EN PARAMETRE
//

// Nettoyage du chemin (pour éviter les failles d'accès)
// On récupère la liste des dossiers de ce chemin pour pouvoir connaitre le niveau dans lequel on se trouve
if( !empty($_JB_AL_GET['s_rep_courant']) )
{
	$aDossiersUrl = $oOutils->nettoyerCheminURL( $_JB_AL_GET['s_rep_courant'] );
	$_JB_AL_VARS['s_rep_courant_url'] = $oOutils->preparerUrl($_JB_AL_GET['s_rep_courant']);

	// Vérification que le dossier passé dans l'url existe sinon on l'efface
	// ce qui a pour effet de revenir à la page d'accueil.
	if( !file_exists(JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_PHOTOS.$_JB_AL_GET['s_rep_courant']) )
	{
		$_JB_AL_GET['s_rep_courant'] = '';
		$aDossiersUrl = array();
	}
}
else $_JB_AL_VARS['s_rep_courant_url'] = '';


// ====================
// CALCUL NIVEAU DANS L'ARBORESCENCE
//
$iNiveau = sizeof($aDossiersUrl);
if( $iNiveau === 0 ) $iNiveau = 1;


// ====================
// GESTION DE LA LISTE DES DOSSIERS DE PHOTOS
//
// Albulle permet de gérer une arborescence multi-niveaux de dossiers pour pouvoir classer plus finement les photos.
$aResultats = genererArborescence(
					JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_PHOTOS,	// Répertoire racine
					$_JB_AL_GET['s_rep_courant'], 						// Répertoire demandé
					$iNiveau, 											// Niveau de profondeur du répertoire demandé
					array(),						 					// Dossiers à ne pas afficher
					array( 'gif', 'jpe', 'jpeg', 'jpg', 'png' ),		// Fichier autorisés
					array( 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/x-png', 'image/png'), 	// Types MIME autorisés
					JB_AL_AFFICHER_NB_PHOTOS, 							// Afficher le nombre de fichiers par dossier
					JB_AL_AFFICHER_NB_SI_VIDE,							// Afficher le nombre de fichiers même si dossier vide
					JB_AL_DEROULER_TOUT, 								// Dérouler tous les dossiers ou seulement celui demandé
					JB_AL_FILTRE_PREFIXES_ACTIF,						// Filtrage activé ou non (cf. explications dans includes/config.php)
					JB_AL_PREFIXES_SEPARATEUR							// Séparateur des filtres
				);

$_JB_AL_VARS['s_arborescence']			= $aResultats['arborescence_html'];
$_JB_AL_VARS['s_rappel_sous_dossiers']	= $aResultats['dossiers_rep_courant'];
$aListePhotos							= !$_JB_AL_GET['b_voir_panier'] ? $aResultats['fichiers_dossier_courant'] : $oPanier->obtenirPanier();


// ====================
// GESTION DE LA LISTE DES PHOTOS (si un répertoire est défini)
//
$iNbPhotos = sizeof( $aListePhotos );

if ( ((!empty( $_JB_AL_GET['s_rep_courant'] ) && is_dir( JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_PHOTOS.$_JB_AL_GET['s_rep_courant'] )) || $_JB_AL_GET['b_voir_panier']) &&  $iNbPhotos > 0 )
{
	// Choix des dimensions des miniatures
	$iMinLargeur = $_JB_AL_VARS['b_mode_diaporama'] ? JB_AL_VIGNETTES_DP_LARGEUR : JB_AL_VIGNETTES_LARGEUR;
	$iMinHauteur = $_JB_AL_VARS['b_mode_diaporama'] ? JB_AL_VIGNETTES_DP_HAUTEUR : JB_AL_VIGNETTES_HAUTEUR;

	// calcul du nombre de pages
	$iNbPages = ceil( sizeof( $aListePhotos ) / abs(JB_AL_VIGNETTES_PAR_PAGE) );

	// génération de la pagination
	$_JB_AL_VARS['s_pagination'] = $oOutils->paginer( $iNbPages, $_JB_AL_GET['i_page_courante'], $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].$aActions['voir'] ) );

	// CREATION DES VIGNETTES
	$_MINIATURES = array();
	$iDiapoCourante = $j = 0;		// compteur pour le tableau (il doit être indépendant du compteur de boucle)
	$_JB_AL_VARS['i_diapo_courante'] = 0;

	// vérification qu'on ne dépasse pas la taille du tableau
	$iImgAAfficher = ( ( ($_JB_AL_GET['i_page_courante']-1) * JB_AL_VIGNETTES_PAR_PAGE ) >$iNbPhotos - JB_AL_VIGNETTES_PAR_PAGE ) ? $iNbPhotos - ( ($_JB_AL_GET['i_page_courante']-1) * JB_AL_VIGNETTES_PAR_PAGE ) : JB_AL_VIGNETTES_PAR_PAGE;

	//
	// Pour chaque image dans l'intervalle de la page
	//
	for ( $i = ($_JB_AL_GET['i_page_courante'] - 1) * JB_AL_VIGNETTES_PAR_PAGE ; $i < ( ($_JB_AL_GET['i_page_courante'] - 1) * JB_AL_VIGNETTES_PAR_PAGE ) + $iImgAAfficher ; $i++ )
	{
		$sRepCourant = $_JB_AL_GET['s_rep_courant'];	// On utilise une variable car elle doit être modifiée en mode diaporama

		// Si on se trouve en mode exploration du panier, il faut définir $sRepCourant à chaque fois,
		// et écraser le chemin de l'image pour qu'il n'y ai que le nom de l'image.
		if($_JB_AL_GET['b_voir_panier'])
		{
			$sRepCourant		= $oOutils->SousChaineGauche($aListePhotos[$i], '/', 1);
			
			// Troncature du dossier préfixe (photos ou originales ou centre)
			if( strpos($sRepCourant, JB_AL_DOSSIER_CENTRE) !== false ) 		{ $sRepCourant = $oOutils->sousChaineDroite($sRepCourant, '/', substr_count(JB_AL_DOSSIER_CENTRE, '/')); }
			if( strpos($sRepCourant, JB_AL_DOSSIER_PHOTOS) !== false ) 		{ $sRepCourant = $oOutils->sousChaineDroite($sRepCourant, '/', substr_count(JB_AL_DOSSIER_PHOTOS, '/')); }
			if( strpos($sRepCourant, JB_AL_DOSSIER_ORIGINALES) !== false )	{ $sRepCourant = $oOutils->sousChaineDroite($sRepCourant, '/', substr_count(JB_AL_DOSSIER_ORIGINALES, '/')); }
			
			$aListePhotos[$i]	= $oOutils->sousChaineDroite($aListePhotos[$i], '/', substr_count($aListePhotos[$i], '/'));
		}

		//
		// Definition chemin de la photo courante et de sa miniature
		//
		$aFichier 		= explode( '.', $aListePhotos[$i] );
		$sNomFichier 	= $aFichier[0];
		$sExtension		= $aFichier[1];

		$sCheminPhoto		= JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_PHOTOS.$sRepCourant.'/'.$aListePhotos[$i];
		$sCheminPhotoHQ		= JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_ORIGINALES.$sRepCourant.'/'.$aListePhotos[$i];
		$sCheminPhotoMd5	= md5($sCheminPhoto);
		$sCheminMiniature	= JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_MINIATURES.$sNomFichier.'_'.$iMinLargeur.'x'.$iMinHauteur.'_'.$sCheminPhotoMd5.'.'.$sExtension;

		//
		// Lectures infos image courante
		//
		$bHauteQualite = file_exists($sCheminPhotoHQ);
		$sCheminLectureInfos = ($bHauteQualite ? $sCheminPhotoHQ : $sCheminPhoto);
		$sTypeMime	= $oOutils->imageTypeMime($sCheminPhoto);
		$aImgInfos	= getimagesize( $sCheminLectureInfos );
		$iPoids		= filesize( $sCheminLectureInfos );
		$sPoids		= intval( $iPoids / 1024 ) < 1 ? $iPoids.' Octets' : intval( $iPoids / 1024 ).' Ko';
		if( $bHauteQualite ) $sPoids .= ' (*)';

		//
		// Création miniature
		//

		// Si l'image ne dépasse pas la limite de 5,3 millions de pixels, on peut lancer le traitement
		$aImgPublieeInfos = getimagesize($sCheminPhoto);
		if( ($aImgPublieeInfos[0] * $aImgPublieeInfos[1]) <= 5300000 ) {
			$sCssClasseVignette = 'miniature';

			// si la miniature n'existe pas ou que la photo est plus récente que la miniature => création ou remplacement
			if ( !file_exists($sCheminMiniature) || ( filemtime($sCheminMiniature) < filemtime($sCheminPhoto) ) )
			{
				$oOutils->processImgFile( $sTypeMime, $sCheminPhoto, $sCheminMiniature, $iMinLargeur, $iMinHauteur, '', JB_AL_VIGNETTES_QUALITE );
				@chmod( $sCheminMiniature, JB_AL_CHMOD_FICHIERS );
			}
		}
		// sinon, on écrase le chemin de la miniature pour afficher celle par défaut
		else {
			$sCssClasseVignette = 'miniature_defaut';
			$sCheminMiniature = $_JB_AL_VARS['s_acces_theme'].(isIE() ? 'images/ie/miniature_defaut.gif' : 'images/miniature_defaut.png');
		}

		//
		// Gestion du panier : si on demande la sélection de toutes les images de la page
		// avant d'afficher chaque vignette, on l'ajoute au panier. Idem si on demande le
		// retrait.
		//
		if( JB_AL_PANIER_ACTIF === true ) {
			$sChemin = substr($sCheminPhoto, strpos($sCheminPhoto, JB_AL_DOSSIER_PHOTOS) + strlen(JB_AL_DOSSIER_PHOTOS) );
			$sCheminDansPanier = cheminDansPanier($sChemin);			
			
			switch ( $_JB_AL_GET['s_action'] )
			{
				case 'tout':	$oPanier->Ajouter( $sCheminDansPanier ); break;
				case 'rien':	$oPanier->Supprimer( $sCheminDansPanier ); if($_JB_AL_GET['b_voir_panier']) continue; break;
			}
			
			// Définition des chaines d'ajout et de retrait de l'image dans le panier
			$sParamDiapo	= ($_JB_AL_GET['s_diapo_courante'] !== '') ? '&amp;diapo='.basename($_JB_AL_GET['s_diapo_courante']) : '';
			
			$sUrlAjout		= $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].'&amp;page='.$_JB_AL_GET['i_page_courante'].$sParamDiapo.'&amp;act=ajouter&amp;img='.$oOutils->preparerUrl($sChemin).$aActions['voir'] );
			$sUrlRetrait	= $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].'&amp;page='.$_JB_AL_GET['i_page_courante'].$sParamDiapo.'&amp;act=supprimer&amp;img='.$oOutils->preparerUrl($sChemin).$aActions['voir'] );
		}

		//
		// Sauvegarde l'indice de la photo visionnée si on est en mode diaporama
		// OU placement sur la première diapo s'il n'y en a pas de définie, uniquement quand on se trouve sur la 1ère itération de la boucle.
		//
		if( $_JB_AL_VARS['b_mode_diaporama'] &&
			($aListePhotos[$i] === $_JB_AL_GET['s_diapo_courante'] || empty($_JB_AL_GET['s_diapo_courante'])) ) {

			$iDiapo								= $i;
			$_JB_AL_VARS['i_diapo_courante']	= $j;
			$iDiapoCourante						= $j;
			if( empty($_JB_AL_GET['s_diapo_courante']) )	$_JB_AL_GET['s_diapo_courante'] = $sCheminPhoto;
		}
		else $iDiapoCourante = -1;

		//
		// Construction du lien de la vignette de la photo
		//

		$sLienHref = $oOutils->preparerUrl($sCheminPhoto, true);		// lien par défaut

		$sLienHrefJS = $sBaliseLightBox = '';

		// Si ouverture des photos demandé avec target="_blank"
		$sTargetBlank = ( (JB_AL_OUVERTURE_BLK === true) && (JB_AL_OUVERTURE_JS === false) ) ? ' target="_blank"' : '';

		// Ouverture avec Javascript sans Lightbox
		if( ($_JB_AL_VARS['b_mode_diaporama'] === false && JB_AL_OUVERTURE_JS === true && JB_AL_OUVERTURE_LBX === false) ||
			($_JB_AL_VARS['b_mode_diaporama'] === true && JB_AL_OUVERTURE_JS_DIAPO === true && JB_AL_OUVERTURE_LBX_DIAPO === false &&
			 $iDiapoCourante === $j) )
		{
			// Détermination de la largeur et de la hauteur de la popup si demandé dans la config
			$iLargeurMax = ( (JB_AL_POPUP_LARGEUR !== 0) && ($aImgInfos[0] > JB_AL_POPUP_LARGEUR) ) ? JB_AL_POPUP_LARGEUR : $aImgInfos[0];
			$iHauteurMax = ( (JB_AL_POPUP_HAUTEUR !== 0) && ($aImgInfos[1] > JB_AL_POPUP_HAUTEUR) ) ? JB_AL_POPUP_HAUTEUR : $aImgInfos[1];

			// Réajustement des dimensions
			$fRatioImage = $aImgInfos[0] / $aImgInfos[1];
			if( (JB_AL_POPUP_HAUTEUR === 0 || JB_AL_POPUP_HAUTEUR !== 0) && JB_AL_POPUP_LARGEUR !== 0 )
				$iHauteurMax = JB_AL_POPUP_LARGEUR * (1/$fRatioImage);

			if( JB_AL_POPUP_HAUTEUR !== 0 && JB_AL_POPUP_LARGEUR === 0 )
				$iLargeurMax = JB_AL_POPUP_HAUTEUR * $fRatioImage;

			$sLienHrefJS = ' onclick="javascript:popup( encodeURI(\''.$oOutils->preparerUrl($sCheminPhoto, true).'\'), '.$iLargeurMax.', '.$iHauteurMax.' ); return false;"';
		}

		// Ouverture avec Javascript et Lightbox (pas besoin d'écraser le lien mais il faut construire la balise rel)
		if( (JB_AL_OUVERTURE_JS === true && JB_AL_OUVERTURE_LBX === true) && (
			($_JB_AL_VARS['b_mode_diaporama'] === false) ||
			($_JB_AL_VARS['b_mode_diaporama'] === true && $iDiapoCourante === $j) ) )
		{
			$sDescTitle = $aListePhotos[$i];

			if( JB_AL_FILTRE_PREFIXES_ACTIF )	$sDescTitle = $oOutils->enleverPrefixe( $sDescTitle, JB_AL_PREFIXES_SEPARATEUR );
			if( JB_AL_REMPLACER_TIRETS_BAS )	$sDescTitle = str_replace( '_', ' ', $sDescTitle );
			if( !JB_AL_AFFICHER_EXTENSION )	    $sDescTitle = $oOutils->SousChaineGauche( $sDescTitle, '.', 1 );

			$sBaliseLightBox = ' rel="lightbox'.($_JB_AL_VARS['b_mode_diaporama'] === true ? '' : '[albulle]').'" title="['.$aImgInfos[0].' x '.$aImgInfos[1].' | '.$sPoids.'] » '.utf8($sDescTitle).'"';
		}

		// En mode diaporama, il faut écraser le lien pour afficher les images
		if( $_JB_AL_VARS['b_mode_diaporama'] ) {
			$sLienHref = $oUrl->construireUrl(
									'rep='.$_JB_AL_VARS['s_rep_courant_url'].
									'&amp;page='.$_JB_AL_GET['i_page_courante']
									.$aActions['voir'].
									'&amp;diapo='.$oOutils->preparerUrl($aListePhotos[$i]),
									'marqueur' );
		}


		//
		// Ajout de la vignette dans le tableau global
		//

		$_MINIATURES[$j]['CHEMIN_PHOTO']		= $sCheminPhoto;
		$_MINIATURES[$j]['CHEMIN_PHOTO_URL']	= $oOutils->preparerUrl($sCheminPhoto, true);
		$_MINIATURES[$j]['LIEN_PHOTO']			= array(
													'HREF'			=> $sLienHref,
													'TARGET'		=> $sTargetBlank,
													'JAVASCRIPT'	=> ($_JB_AL_VARS['b_mode_diaporama'] === false) ? $sLienHrefJS : '',
													'LIGHTBOX'		=> ($_JB_AL_VARS['b_mode_diaporama'] === false) ? $sBaliseLightBox : '',
													'CHEMIN_MIN'	=> $oOutils->preparerUrl($sCheminMiniature, true),
													'CLASSE_CSS'	=> $sCssClasseVignette,
													'ALT'			=> $sCheminPhoto
												);
		$_MINIATURES[$j]['NOM_PHOTO']			= ( JB_AL_AFFICHER_NOMS === true || $_JB_AL_VARS['b_mode_diaporama'] )	? utf8($aListePhotos[$i]) : '';
		$_MINIATURES[$j]['DIM_PHOTO']			= ( JB_AL_AFFICHER_DIMENSIONS === true )			? $aImgInfos[0].' x '.$aImgInfos[1] : '';
		$_MINIATURES[$j]['SIZE_PHOTO']			= ( JB_AL_AFFICHER_POIDS === true )					? $sPoids : '' ;
		$_MINIATURES[$j]['TYPE_MIME']			= $sTypeMime;
		$_MINIATURES[$j]['EXIF']				= $oOutils->afficherExif($sCheminPhoto);

		// Lien pour l'ajout/retrait du panier
		if(JB_AL_PANIER_ACTIF === true)
		{
			if( $oPanier->EstDansLePanier( $sCheminDansPanier ) !== false ) {
				$_MINIATURES[$j]['PANIER']['MODE']	= 'retrait';
				$_MINIATURES[$j]['PANIER']['URL']	=  $sUrlRetrait;
			}
			else {
				$_MINIATURES[$j]['PANIER']['MODE']	= 'ajout';
				$_MINIATURES[$j]['PANIER']['URL']	=  $sUrlAjout;
			}
		}

		// Diapositive courante si mode diaporama actif
		if( $_JB_AL_VARS['b_mode_diaporama'] && $iDiapoCourante === $j ) {
			$_MINIATURES[$j]['LIEN_DIAPO'] = array(
												'HREF'			=> $_MINIATURES[$j]['CHEMIN_PHOTO_URL'],
												'TARGET'		=> $sTargetBlank,
												'JAVASCRIPT'	=> $sLienHrefJS,
												'LIGHTBOX'		=> (JB_AL_OUVERTURE_JS_DIAPO === true && JB_AL_OUVERTURE_LBX_DIAPO === true) ? $sBaliseLightBox : '',
												'ALT'			=> $sCheminPhoto
											);
		}


		//
		// Application filtres sur le nom de la photo
		//
		if( JB_AL_FILTRE_PREFIXES_ACTIF )	$_MINIATURES[$j]['NOM_PHOTO'] = $oOutils->enleverPrefixe( $_MINIATURES[$j]['NOM_PHOTO'], JB_AL_PREFIXES_SEPARATEUR );
		if( JB_AL_REMPLACER_TIRETS_BAS )	$_MINIATURES[$j]['NOM_PHOTO'] = str_replace( '_', ' ', $_MINIATURES[$j]['NOM_PHOTO'] );
		if( !JB_AL_AFFICHER_EXTENSION )	    $_MINIATURES[$j]['NOM_PHOTO'] = $oOutils->SousChaineGauche( $_MINIATURES[$j]['NOM_PHOTO'], '.', 1 );

		$j++;
		
		@set_time_limit(0);
	}
}

// Affichage de l'accueil
elseif( empty($_JB_AL_GET['s_rep_courant']) ) {

	// Si pas de texte alternatif, on charge le document par défaut
	if( !defined('JB_AL_ACCUEIL_ALT') ) {
		
		ob_start();
		eval('require_once(JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_FICHIER_ACCUEIL);');
		$_JB_AL_VARS['accueil'] = ob_get_contents();
		ob_end_clean();
	}
	// Sinon, on affiche le texte alternatif
	else $_JB_AL_VARS['accueil'] = JB_AL_ACCUEIL_ALT;
}

// ====================
// GESTION DES LIENS DU PANIER
//

// état du panier : plein ou pas, nombre de fichiers dans le panier, poids estimé de l'archive
if(JB_AL_PANIER_ACTIF === true)
{
	$_JB_AL_VARS['a_panier']['b_plein']			= $oPanier->PanierPlein();
	$_JB_AL_VARS['a_panier']['i_nb_fichiers']	= $oPanier->CompterFichiers();
	
	$iPoidsEstime = $oPanier->CalculerPoids();
	$_JB_AL_VARS['a_panier']['s_poids_estime']	= (intval( $iPoidsEstime / 1024 ) < 1) ? $iPoidsEstime.' Octets' : intval( $iPoidsEstime / 1024 ).' Ko';
	
	// création des liens si des fichiers se trouvent dans le panier
	if ( $_JB_AL_VARS['a_panier']['i_nb_fichiers'] > 0 )
	{
		$_JB_AL_VARS['a_menu_panier']['s_url_download']	= JB_AL_ROOT.'core/download.php';
		$_JB_AL_VARS['a_menu_panier']['s_url_voir']		= $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].'&amp;voir' );
		$_JB_AL_VARS['a_menu_panier']['s_url_vider']	= $oUrl->construireUrl(
																'rep='.$_JB_AL_VARS['s_rep_courant_url']
																.($_JB_AL_GET['b_voir_panier'] ? '' : '&amp;page='.$_JB_AL_GET['i_page_courante'])
																.'&amp;act=vider'
															);
	}
	
	// Liens pour les ajouts / retraits globaux
	$_JB_AL_VARS['s_lien_panier_tout_ajouter']		= ( $iNbPhotos > 0 && !$_JB_AL_GET['b_voir_panier'] ) ? $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].'&amp;page='.$_JB_AL_GET['i_page_courante'].'&amp;act=tout'.$aActions['voir'] ) : '';
	$_JB_AL_VARS['s_lien_panier_tout_supprimer']	= ( $iNbPhotos > 0 && !$_JB_AL_GET['b_voir_panier'] ) ? $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].'&amp;page='.$_JB_AL_GET['i_page_courante'].'&amp;act=rien'.$aActions['voir'] ) : '';
}
else {
	$_JB_AL_VARS['a_panier']['b_plein']			= 0;
	$_JB_AL_VARS['a_panier']['i_nb_fichiers']	= 0;
	$_JB_AL_VARS['a_panier']['s_poids_estime']	= 0;
}

// ====================
// INITIALISATIONS DIVERSES POUR L'HTML
//

$_JB_AL_VARS['s_chemin_rep']		= JB_AL_ROOT.JB_AL_DOSSIER_DATA.JB_AL_DOSSIER_PHOTOS.$_JB_AL_GET['s_rep_courant'].'/';
$_JB_AL_VARS['s_rep_courant']		= $oOutils->preparerUrl($_JB_AL_GET['s_rep_courant']);
$_JB_AL_VARS['b_voir_panier']		= $_JB_AL_GET['b_voir_panier'];

// Nettoyage préfixe et tirets bas
if( !empty($_JB_AL_GET['s_rep_courant']) )
{
	if(!$_JB_AL_GET['b_voir_panier'])
	{
		$sTitreFiltre = JB_AL_FILTRE_PREFIXES_ACTIF ? $oOutils->enleverPrefixe( $aDossiersUrl[$iNiveau-1], JB_AL_PREFIXES_SEPARATEUR ) : $aDossiersUrl[$iNiveau-1];
		$sTitreFiltre = utf8(str_replace( '_', ' ', $sTitreFiltre ));
	}
	else $sTitreFiltre = 'panier';
}

$_JB_AL_VARS['s_titre_meta'] = ( empty( $_JB_AL_GET['s_rep_courant'] ) ) ? 'Accueil' : "Photos de $sTitreFiltre";
if($_JB_AL_GET['b_voir_panier'])	$_JB_AL_VARS['s_titre_meta'] = str_replace('de', 'dans le', $_JB_AL_VARS['s_titre_meta']);	// Pour remplacer le 'de' de la ligne précédente quand on visionne le contenu du panier.

// Construction de la chaine de navigation dans les dossiers
if( empty($_JB_AL_GET['s_rep_courant']) && !$_JB_AL_GET['b_voir_panier'] )	$_JB_AL_VARS['s_navigation'] = 'Accueil';
else
{
	$sLien = '';
	$_JB_AL_VARS['s_navigation'] = '<a href="'.$oUrl->construireUrl('').'">Accueil</a> » ';

	for( $i = 0 ; $i < $iNiveau - 1 && !$_JB_AL_GET['b_voir_panier'] ; $i++ )
	{
	    // Nettoyage préfixe
		$sDossierFiltre = JB_AL_FILTRE_PREFIXES_ACTIF ? $oOutils->enleverPrefixe( $aDossiersUrl[$i], JB_AL_PREFIXES_SEPARATEUR ) : $aDossiersUrl[$i];

		$sLien .= ( $i !== 0 ) ? '/'.$aDossiersUrl[$i] : $aDossiersUrl[$i];
		$_JB_AL_VARS['s_navigation'] .= '<a href="'.$oUrl->construireUrl( 'rep='.$oOutils->preparerUrl($sLien).$aActions['voir'] ).'">'.str_replace( '_', ' ', utf8($sDossierFiltre) ).'</a> » ';
	}

	// Nettoyage préfixe
	if(!$_JB_AL_GET['b_voir_panier'])
		$sDossierFiltre = JB_AL_FILTRE_PREFIXES_ACTIF ? $oOutils->enleverPrefixe( utf8($aDossiersUrl[$iNiveau-1]), JB_AL_PREFIXES_SEPARATEUR ) : $aDossiersUrl[$iNiveau-1];
	else $sDossierFiltre = 'Photos dans le panier';

	$_JB_AL_VARS['s_navigation'] .= str_replace( '_', ' ', $sDossierFiltre );

	// Lien modes galerie / diaporama
	if( $_JB_AL_VARS['b_mode_diaporama'] )
	{
		// Pour le défilement automatique
		if( !isset($_SESSION['DIAPORAMA_INTERVALLE']) )	$_SESSION['DIAPORAMA_INTERVALLE'] = 0;

		// Si lancement défilement demandé
		if( isset($_POST['diaporama_intervalle']) )	$_SESSION['DIAPORAMA_INTERVALLE'] =  $_POST['diaporama_intervalle'];
		$_SESSION['DIAPORAMA_INTERVALLE'] = is_int((int) $_SESSION['DIAPORAMA_INTERVALLE']) ? (int) $_SESSION['DIAPORAMA_INTERVALLE'] : 0;

		// Si arrêt du défilement demandé
		if( isset($_POST['arreter']) )	$_SESSION['DIAPORAMA_INTERVALLE'] = 0;

		// Sauvegarde du temps du défilement et définition du drapeau qui nous servira pour les futurs tests
		$_JB_AL_VARS['i_intervalle_tps'] = $_SESSION['DIAPORAMA_INTERVALLE'];
		$_JB_AL_VARS['b_defilement_auto'] = ($_SESSION['DIAPORAMA_INTERVALLE'] > 0);

		// Définition des attributs du bouton de défilement
		if( !$_JB_AL_VARS['b_defilement_auto'] ) {
			$_JB_AL_VARS['s_defilement_submit_name']	= 'lancer';
			$_JB_AL_VARS['s_defilement_submit_value']	= 'Lancer !';
		}
		else {
			$_JB_AL_VARS['s_defilement_submit_name']	= 'arreter';
			$_JB_AL_VARS['s_defilement_submit_value']	= 'Arrêter';
		}

		// Construction liens précédente / suivante
		$_JB_AL_VARS['s_url_img_precedente'] = $_JB_AL_VARS['s_url_img_suivante'] = '';
		$sPagePrecedente = $sPageSuivante = '&amp;page=';

		// S'il y a des images précédentes
		if( $iDiapo > 0 || $_JB_AL_GET['i_page_courante'] > 1 )
		{
			$sPagePrecedente .= ( $iDiapo === ($_JB_AL_GET['i_page_courante'] - 1) * JB_AL_VIGNETTES_PAR_PAGE ) ? $_JB_AL_GET['i_page_courante'] - 1 : $_JB_AL_GET['i_page_courante'];

			$_JB_AL_VARS['s_url_img_precedente'] = $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].$sPagePrecedente.$aActions['voir'].'&amp;diapo='.$oOutils->preparerUrl($aListePhotos[$iDiapo - 1]), 'marqueur');
		}

		// S'il y a des images qui suivent
		if( $iDiapo < ($iNbPhotos - 1) )
		{
			$sPageSuivante .= ( $iDiapo === ((($_JB_AL_GET['i_page_courante'] - 1) * JB_AL_VIGNETTES_PAR_PAGE) + $iImgAAfficher) - 1 ) ? $_JB_AL_GET['i_page_courante'] + 1 : $_JB_AL_GET['i_page_courante'];

			$_JB_AL_VARS['s_url_img_suivante']		= $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].$sPageSuivante.$aActions['voir'].'&amp;diapo='.$oOutils->preparerUrl($aListePhotos[$iDiapo + 1]), 'marqueur');
		}

		if( $iDiapo === ($iNbPhotos - 2) )	$_SESSION['DIAPORAMA_INTERVALLE'] = 0;

	 	$_JB_AL_VARS['s_lien_mode_affichage']	= $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].'&amp;page='.$_JB_AL_GET['i_page_courante'].'&amp;galerie'.$aActions['voir'] );
		$_JB_AL_VARS['s_texte_mode_affichage']	= 'Mode galerie';
		$_JB_AL_VARS['s_classe_css_vignette']	= 'vignetteDiapo';
	}
	else
	{
		$_JB_AL_VARS['s_lien_mode_affichage']	= $oUrl->construireUrl( 'rep='.$_JB_AL_VARS['s_rep_courant_url'].'&amp;page='.$_JB_AL_GET['i_page_courante'].'&amp;diaporama'.$aActions['voir'], 'marqueur' );
		$_JB_AL_VARS['s_texte_mode_affichage']	= 'Mode diaporama';
		$_JB_AL_VARS['s_classe_css_vignette']	= 'vignette';
	}

}


// ====================
// Affichage
//
$sPageFinale = file_exists($_JB_AL_VARS['s_acces_theme'].'html.php') ? require_once( $_JB_AL_VARS['s_acces_theme'].'html.php' ) : require_once( 'includes/html.php' );
return (JB_AL_INTEGRATION_SITE === true && JB_AL_SORTIE_ISO === true) ? utf8_decode($sPageFinale) : $sPageFinale;

?>
