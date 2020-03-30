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
// © DUCARRE Cedric, Bubulles Creations, (09/05/2005) 
// 
// webmaster@jebulle.net
// http://jebulle.net
// 
// Ce logiciel est un programme de galerie photos pour site internet.
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
 * AlBulles - Gallerie photos
 *
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @name AlBulles
 * @since 31/08/2005
 * @version 0.6
 */


// ====================
// INCLUSION DES FICHIERS NECESSAIRES
//
require_once( 'config.php' );
require_once( './classes/panierdefichiers.class.php' );
require_once( './classes/util.class.php' );


// ====================
// INITIALISATIONS
//
$sVersion = '0.6';
$sAccesTheme = $sDossierThemes.$sDossierThemeUtilise;
$sMenuPanier = $sPanierLienToutAjouter = $sPanierLienToutSupprimer = $sLiensDossiersPhotos = $sNavigation = '';
$sNomDossierMiniatures = '_miniatures';
$aListePhotos = $aMiniatures = array();
set_magic_quotes_runtime(0);
header( 'Content-type: text/html; charset=utf-8' );


// ====================
// VERIFICATIONS
//
if( !is_dir( $sDossierPhotos ) )	// existence dossier des photos
	exit( '# ALBULLES # <strong>[ Erreur ]</strong> =>
			Le dossier <em>'.$sDossierPhotos.'</em> est introuvable.
			Vérifiez la configuration dans le fichier <strong>config.php</strong>.' );
	
if( !file_exists( $sAccesTheme.$sFichierHtml ) )	// existence thème spécifié
	exit( '# ALBULLES # <strong>[ Erreur ]</strong> =>
			Le fichier du thème <em>'.$sAccesTheme.$sFichierHtml.'</em> est introuvable.
			Vérifiez la configuration dans le fichier <strong>config.php</strong>.' );

	
// ====================
// LECTURE DES PARAMETRES PASSES DANS L'URL
//
( isset( $_GET['rep'] ) )	? $sRep		= stripslashes(rawurldecode( $_GET['rep'] ))	: $sRep		= '';
( isset( $_GET['page'] ) )	? $iPage	= $_GET['page']	: $iPage	= 1;
( isset( $_GET['act'] ) )	? $sAct		= $_GET['act'] 	: $sAct		= '';
( isset( $_GET['img'] ) )	? $sImg		= stripslashes(rawurldecode( $_GET['img'] ))	: $sImg		= '';


// ====================
// GESTION DU PANIER
//
$oPanier = new PanierDeFichiers( $iFichiersMaxDansPanier );

// Lancement des actions.
// L'action de télechargement est désormais située dans le fichier download.php (ou le nom que nous lui avez donné).
switch ( $sAct )
{
	case 'ajouter' :	$oPanier->Ajouter( $sImg ); break;
	case 'supprimer' :	$oPanier->Supprimer( $sImg ); break;
	case 'vider':		$oPanier->ViderPanier(); break;	
}


// ====================
// GESTION DU CHEMIN PASSE EN PARAMETRE
//
$iNiveau = 0;

// Protection de l'accès aux répertoires
$aDossiers = explode( '/', $sRep );

// on calcul le nombres de dossiers passés dans l'url (qui sera le niveau dans l'arborescence)
$iNiveau = sizeof($aDossiers); 

// nettoyage des dossiers, pour enlever les chaines vides et les accès du type ./ et ../
// L'accès aux dossiers miniatures est aussi interdit.
for( $i = 0 ; $i < $iNiveau ; $i++ )
	if( $aDossiers[$i] === '' || preg_match('/^[.]+$|'.$sNomDossierMiniatures.'/', $aDossiers[$i]) !== 0 )
		unset( $aDossiers[$i] );

// reconstruction des index du tableau
$aDossiers = array_merge($aDossiers);

// Reconstruction de la chaine d'accès nettoyée
$sRep = implode( '/', $aDossiers );

// Vérification que le dossier passé dans l'url existe sinon on l'efface
// ce qui a pour effet de revenir à la page d'accueil.
if( !file_exists($sDossierPhotos.$sRep) )
{
	$sRep = '';
	$aDossiers = array();
}

// on recalcule le niveau aps nettoyage
$iNiveau = sizeof($aDossiers);


// ====================
// GESTION DE LA LISTE DES DOSSIERS DE PHOTOS
//
// AlBulles permet désormais de gérer une arborescence de dossiers pour pouvoir classer plus finement les photos.
// Cette arborescence est à 2 niveaux seulement.
$oOutils = new Util();
$sRepCourant = $sRep;	// pour la construction de l'arborescence on prend une autre variable car elle va être modifiée.
$sResultat = $oOutils->SousChaineGauche( $sRepCourant, '/', 1 );
$sRepParent = ( $sResultat === $sRepCourant ) ? '' : $sResultat;

// Détermination du premier niveau à lire selon ce qui a été demandé et où l'utilisateur se trouve.
if( $iNiveau > 1 )
	$sDossierLecture = $sDossierPhotos.$sRepParent;
else $sDossierLecture = $sDossierPhotos;

// lecture répertoires
$mResultat = $oOutils->advScanDir( $sDossierLecture, 'TOUT', array( $sNomDossierMiniatures ) );
$aListeRepPhotos = ( $mResultat === false ) ? array() : $mResultat;

// lecture nombre dossiers lus
$iNbDossiers = sizeof( $aListeRepPhotos['dir'] );

// S'il n'y a pas de dossiers.
if ( $iNbDossiers === 0 )
	$sLiensDossiersPhotos = '<li class="puceNoDossier">Il n\'y a pas de photos actuellement</li>';

if( $iNiveau > 1 )
	$sLiensDossiersPhotos = '<li class="puceRemonter">
								<a href="'.$_SERVER['PHP_SELF'].'?rep='
								.rawurlencode(stripslashes( $sRepParent ))
								."\">Remonter</a></li>\n";

// Création liste dossiers
for( $i = 0 ; $i < $iNbDossiers ; $i++ )
{	
	$sLienNiveau1 = ( $sRepParent === '' ) ? $aListeRepPhotos['dir'][$i] : $sRepParent.'/'.$aListeRepPhotos['dir'][$i];
	$sGrasDebut = '';
	$sGrasFin = '';
	
	// lecture sous dossiers du dossier courant
	$mResultat = $oOutils->advScanDir( $sDossierLecture.'/'.$aListeRepPhotos['dir'][$i], 'TOUT', array( $sNomDossierMiniatures ) );
	$aListeSousRepPhotos = ( $mResultat === false ) ? array() : $mResultat;
	
	// lecture nombre sous-dossiers lus
	$iNbSousDossiers = sizeof( $aListeSousRepPhotos['dir'] );
	
	// test si on se trouve sur le dossier courant pour le mettre en gras
	if( $sLienNiveau1 === $sRepCourant )
	{
		$sGrasDebut = '<strong>';
		$sGrasFin = '</strong> ';
		$aListePhotos = $aListeSousRepPhotos['file'];		
	}

	// lien dossier parent
	$sNbPhoto = (  $bAfficherNbPhotos === true ) ? '<em>('.sizeof( $aListeSousRepPhotos['file'] ).')</em>' : ''; 
	
	$sLiensDossiersPhotos .= '<li class="pucePhotos">
								<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sLienNiveau1 )).'">'
								.$sGrasDebut.str_replace( '_', ' ', $aListeRepPhotos['dir'][$i] )
								."$sGrasFin</a> $sNbPhoto</li>\n";
	
	// Concaténation sous-liste	
	for( $j = 0 ; $j < $iNbSousDossiers ; $j++ )
	{
		// on ne calcule le nombre de photo d'un dossier que si autorisé dans la config
		if(  $bAfficherNbPhotos === true )
		{
			$mResultat = $oOutils->advScanDir( $sDossierLecture.'/'.$aListeRepPhotos['dir'][$i].'/'.$aListeSousRepPhotos['dir'][$j], 'FICHIERS_SEULEMENT' );
			$aListeSousSousRep = ( $mResultat === false ) ? array() : $mResultat;
			$sNbPhoto = '<em>('.sizeof( $aListeSousSousRep ).')</em>';
		}
		else $sNbPhoto = '';
		
		$sLiensDossiersPhotos .= '<li class="puceSousDossier">
									<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sLienNiveau1.'/'.$aListeSousRepPhotos['dir'][$j] )).'">'
									.str_replace( '_', ' ', $aListeSousRepPhotos['dir'][$j] )
									."</a> $sNbPhoto</li>\n";
	}
}

		
// ====================
// GESTION DE LA LISTE DES PHOTOS (si un répertoire est défini)
//
$iNbPhotos = sizeof( $aListePhotos );

if ( !empty( $sRep ) && is_dir( $sDossierPhotos.$sRep ) &&  $iNbPhotos > 0 )
{
	
	// calcul du nombre de pages
	$iNbPages = ceil( sizeof( $aListePhotos ) / $iImgParPage );
	
	// génération de la pagination
	$sNavigation = $oOutils->paginer( $iNbPages, $iPage, $_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )) );
	
	// creation du dossier des miniatures s'il n'existe pas
	$sDossierMiniatures = $sDossierPhotos.$sRep.'/'.$sNomDossierMiniatures;
	
	if ( $iNbPhotos !== 0 && !is_dir( $sDossierMiniatures ) )
	{

		if ( !@mkdir( $sDossierMiniatures ) )
			exit( '# ALBULLES # <strong>[ Erreur ]</strong> => Impossible de cr&eacute;er le dossier des miniatures. V&eacute;rifiez les droits d\'acc&egrave;s.' );
		
		@chmod( $sDossierMiniatures, $iChmodDossierMiniatures );
		
	}
	
	// CREATION DES VIGNETTES
	$aMiniatures = array();
	$j = 0;		// compteur pour le tableau (il doit être indépendant du compteur de boucle)
	
	// vérification qu'on ne dépasse pas la taille du tableau
	$iImgAAfficher = ( ( ($iPage-1) * $iImgParPage ) >$iNbPhotos - $iImgParPage ) ? $iNbPhotos - ( ($iPage-1) * $iImgParPage ) : $iImgParPage;

	// pour chaque photo dans l'intervalle de la page
	for ( $i = ($iPage - 1) * $iImgParPage ; $i < ( ($iPage - 1) * $iImgParPage ) + $iImgAAfficher ; $i++ )
	{

		$sCheminPhoto		= $sDossierPhotos.$sRep.'/'.$aListePhotos[$i];
		$sCheminMiniature	= $sDossierMiniatures.'/min_'.$aListePhotos[$i];
		
		//
		// Gestion du panier : si on demande la sélection de toutes les images de la page
		// avant d'afficher chaque vignette, on l'ajoute au panier. Idem si on demande le
		// retrait.
		//
		switch ( $sAct )
		{
			case 'tout':	$oPanier->Ajouter( $sCheminPhoto ); break;
			case 'rien':	$oPanier->Supprimer( $sCheminPhoto ); break;
		}
	
		// si la miniature n'existe pas ou que la photo est plus récente que la miniature => création ou remplacement
		if ( !file_exists($sCheminMiniature) || ( filemtime($sCheminMiniature) < filemtime($sCheminPhoto) ) )
		{

			$aExplode = explode( '.', $sCheminPhoto );
			$sExt = strtolower( $aExplode[sizeof( $aExplode ) - 1] );

			switch ( $sExt )
			{

				case 'jpg':
				case 'jpeg':
				case 'jpe': $sTypeMime = 'image/jpeg'; break;
				case 'gif': $sTypeMime = 'image/gif'; break;
				case 'png': $sTypeMime = 'image/png'; break;
				default: exit( '# ALBULLES # <strong>[ Erreur ]</strong> => Un fichier non supporté (autre que GIF, JPEG ou PNG) se trouve dans ce répertoire. Veuillez le supprimer.' );

			}

			$oOutils->processImgFile( $sTypeMime, $sCheminPhoto, $sCheminMiniature, $iLargeurMax, $iHauteurMax, '' );
			@chmod( $sCheminMiniature, $iChmodFichiersMiniatures );

		}
				
		// lecture taille photo
		$aImgInfos = getimagesize( $sCheminPhoto );
		
		// définition des chaines d'ajout et de retrait des images dans le panier
		$sAjout		= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=ajouter&amp;img='.rawurlencode(stripslashes( $sCheminPhoto )).'" class="puceAjout" title="Ajouter l\'image">+</a>';
		$sRetrait	= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=supprimer&amp;img='.rawurlencode(stripslashes( $sCheminPhoto )).'" class="puceRetrait" title="Retirer l\'image">-</a>';
		
		// création du lien de la miniature
		$sTargetBlank = ( $bPhotoDansNouvellePage === true ) ? 'target="_blank"' : '';
		
		$aMiniatures[$j]['LIEN_PHOTO']		= "<a href=\"$sCheminPhoto\" $sTargetBlank>".'<img src="'.$sCheminMiniature.'" class="miniature" alt="Photo '.basename($sCheminPhoto).'" /></a>';
		$aMiniatures[$j]['DIM_PHOTO']		= $aImgInfos[0].' x '.$aImgInfos[1];
		$aMiniatures[$j]['SIZE_PHOTO']		= ( intval( filesize( $sCheminPhoto ) / 1024 ) < 1 ) ? filesize( $sCheminPhoto ).' Octets' : intval( filesize( $sCheminPhoto ) / 1024 ).' Ko' ;
		$aMiniatures[$j]['AJOUT_PANIER']	= ( $oPanier->EstDansLePanier( $sCheminPhoto ) ) ? $sRetrait : $sAjout;
		
		$j++;
		
	}
	
}


// ====================
// GESTION DES LIENS DU PANIER
//

// création des liens si des fichiers se trouvent dans le panier
if ( $oPanier->CompterFichiers() > 0 )
{

	$sMenuPanier .= '<ul class="menu">'."\n";
	$sMenuPanier .= '<li class="puceDownload"><a href="'.$sFichierDownload.'">T&eacute;l&eacute;charger les images</a></li>'."\n";
	$sMenuPanier .= '<li class="puceVider"><a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=vider">Vider le panier</a></li>'."\n";
	$sMenuPanier .= "</ul>\n";
	
}

// définition du nombre de fichiers dans le panier et des liens pour les ajouts / suppressions multiples
$sNbFichiersDansLePanier	= ( $oPanier->PanierPlein() ) ? '<span style="color: red;">'.$oPanier->CompterFichiers().'(Panier plein)</span>' : $oPanier->CompterFichiers();
$sPanierLienToutAjouter		= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=tout" class="puceAjoutPage" title="Ajouter toutes les images de la page">+</a>';
$sPanierLienToutSupprimer	= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=rien" class="puceRetraitPage" title="Retirer toutes les images de la page">-</a>';


// ====================
// INITIALISATIONS DIVERSES POUR L'HTML
//
$sHeadTitre = ( empty( $sRep ) ) ? 'Accueil' : "Photos de {$aDossiers[$iNiveau-1]}";

// liens sur le titre qui contient le chemin où l'utilisateur se trouve
if( empty( $sRep ) )	$sBodyTitre = 'Accueil';
else
{
	$sBodyTitre = $sLien = '';
	
	for( $i = 0 ; $i < $iNiveau - 1 ; $i++ )
	{
		$sLien .= ( $i !== 0 ) ? '/'.$aDossiers[$i] : $aDossiers[$i];
		$sBodyTitre .= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sLien )).'">'.str_replace( '_', ' ', $aDossiers[$i] ).'</a> > ';
	}
	
	$sBodyTitre .= str_replace( '_', ' ', $aDossiers[$iNiveau-1] );
}

// pour la version
$sVersion = ( $bAfficherVersion === true ) ? ' v'.$sVersion : '';

// ====================
// Inclusion de la partie HTML pour l'affichage
//
require_once( $sAccesTheme.$sFichierHtml );

?>