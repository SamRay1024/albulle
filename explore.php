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
 * @since 15/06/2005
 * @version 0.5
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
$sVersion = '0.5';
$sPanierLienArchive = $sPanierLienVider = $sPanierLienToutAjouter = $sPanierLienToutSupprimer = '';
$sLiensDossiersPhotos = '';
$sNavigation = '';
set_magic_quotes_runtime(0);

// ====================
// LECTURE DES PARAMETRES PASSES DANS L'URL
//
( isset( $_GET['rep'] ) )	? $sRep		= stripslashes(rawurldecode( $_GET['rep'] ))	: $sRep		= '';
( isset( $_GET['page'] ) )	? $iPage	= $_GET['page']	: $iPage	= '';
( isset( $_GET['act'] ) )	? $sAct		= $_GET['act'] 	: $sAct		= '';
( isset( $_GET['img'] ) )	? $sImg		= $_GET['img']	: $sImg		= '';

// ====================
// GESTION DU PANIER
//
$oPanier = new PanierDeFichiers( $iFichiersMaxDansPanier );

// lancement des actions
switch ( $sAct )
{

	case 'ajouter' :	$oPanier->Ajouter( $sImg ); break;
	case 'supprimer' :	$oPanier->Supprimer( $sImg ); break;
	case 'vider':		$oPanier->ViderPanier(); break;
	case 'telecharger':	$oPanier->CreerArchive( $sDossierPhotos.'Photos' ); break;
	
}

// ====================
// GESTION DE LA LISTE DES DOSSIERS DE PHOTOS
//
$oOutils = new Util();

// Lecture des dossiers du répertoire des photos
$aListeRepPhotos = $oOutils->advScanDir( $sDossierPhotos, 'DOSSIERS_SEULEMENT' );

// concaténation des liens vers les dossiers
if ( sizeof( $aListeRepPhotos ) == 0 )
	$sLiensDossiersPhotos = '<li class="puceNoPhotos">Il n\'y a pas de photos actuellement</li>';
else 
	foreach ( $aListeRepPhotos as $key => $value )
		$sLiensDossiersPhotos .= '<li class="pucePhotos"><a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $value )).'">'.$value.'</a></li>'.chr(10).chr(13);
		
// ====================
// GESTION DE LA LISTE DES PHOTOS (si un répertoire est défini)
//
if ( !empty( $sRep ))
{

	// lecture des photos présentes dans le dossier
	$aListePhotos = $oOutils->advScanDir( $sDossierPhotos.$sRep, 'FICHIERS_SEULEMENT' );
	
	// calcul du nombre de pages
	$iNbPages = ceil( sizeof( $aListePhotos ) / $iImgParPage );
	
	// concaténation des numéros de page
	for( $i = 0 ; $i < $iNbPages ; $i++ )
	{

		$sIndex = $i + 1;
		$sNavigation .= ( $i != $iPage ) ? '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$i.'" class="page" >'.$sIndex.'</a>' : '<span class="pageActive">'.$sIndex.'</span>';
				
	}
	
	// creation du dossier des miniatures s'il n'existe pas
	$sDossierMiniatures = $sDossierPhotos.$sRep.'/miniatures';
	
	if ( !is_dir( $sDossierMiniatures ) )
	{

		if ( !mkdir( $sDossierMiniatures ) )
			die( '<strong>[ Erreur ]</strong> => Impossible de cr&eacute;er le dossier des miniatures. V&eacute;rifiez les droits d\'acc&egrave;s.' );
		
		chmod( $sDossierMiniatures, $iChmodDossierMiniatures );
		
	}
	
	// CREATION DES VIGNETTES
	$aMiniatures = array();
	$j = 0;		// compteur pour le tableau (il doit être indépendant du compteur de boucle)
	
	// vérification qu'on ne dépasse pas la taille du tableau
	$iImgAAfficher = ( ( $iPage * $iImgParPage ) > sizeof( $aListePhotos ) - $iImgParPage ) ? sizeof( $aListePhotos ) - ( $iPage * $iImgParPage ) : $iImgParPage;

	// pour chaque photo dans l'intervalle de la page
	for ( $i = $iPage * $iImgParPage ; $i < ( $iPage * $iImgParPage ) + $iImgAAfficher ; $i++ )
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
	
		// si la miniature n'existe pas => création
		if ( !file_exists( $sCheminMiniature ) )
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
				default: die( '<strong>[ Erreur ]</strong> => Type d\'image inconnu. Seuls les formats GIF, JPEG et PNG sont support&eacute;s.' );

			}

			$oOutils->processImgFile( $sTypeMime, $sCheminPhoto, $sCheminMiniature, $iLargeurMax, $iHauteurMax, '' );
			chmod( $sCheminMiniature, $iChmodFichiersMiniatures );

		}
				
		// lecture taille miniature
		$aImgInfos = getimagesize( $sCheminPhoto );
		
		// définition des chaines d'ajout et de retrait des images dans le panier
		$sAjout		= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=ajouter&amp;img='.$sCheminPhoto.'"><img src="./medias/images/puce_ajout.jpg" alt="+" /></a>';
		$sRetrait	= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=supprimer&amp;img='.$sCheminPhoto.'"><img src="./medias/images/puce_retrait.jpg" alt="-" /></a>';
		
		// création du lien de la miniature
		$aMiniatures[$j]['LIEN_PHOTO']		= '<a href="'.$sCheminPhoto.'"><img src="'.$sCheminMiniature.'" class="vignette" /></a>';
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

	$sPanierLienArchive	= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=telecharger"><img src="./medias/images/albulles_download.jpg" alt="T&eacute;l&eacute;charger" />T&eacute;l&eacute;charger les images</a><br />';
	$sPanierLienVider	= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=vider"><img src="./medias/images/albulles_poubelle.jpg" alt="Vider" />Vider le panier</a><br />';
	
}

// définition du nombre de fichiers dans le panier et des liens pour les ajouts / suppressions multiples
$sNbFichiersDansLePanier	= ( $oPanier->PanierPlein() ) ? '<span style="color: red;">'.$oPanier->CompterFichiers().'(Panier plein)</span>' : $oPanier->CompterFichiers();
$sPanierLienToutAjouter		= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=tout"><img src="./medias/images/puce_ajout.jpg" alt="+" title="Ajouter toutes les images de la page dans le panier" /></a>';
$sPanierLienToutSupprimer	= '<a href="'.$_SERVER['PHP_SELF'].'?rep='.rawurlencode(stripslashes( $sRep )).'&amp;page='.$iPage.'&amp;act=rien"><img src="./medias/images/puce_retrait.jpg" alt="-" title="Enlever toutes les images de la page du le panier" /></a>';

// ====================
// INITIALISATIONS DIVERSES POUR L'HTML
//
$sHeadTitre = ( empty( $sRep ) ) ? 'Accueil' : "Photos de $sRep";
$sBodyTitre = ( empty( $sRep ) ) ? 'Accueil' : $sRep;

// ====================
// Inclusion de la partie HTML pour l'affichage
//
require_once( 'medias/html.php' );

?>