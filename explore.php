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
 * @since 11/06/2005
 * @version 0.3
 */

/************************************* DEBUT PARAMETRES EDITABLES *************************************/

// ====================
// INITIALISATIONS
//

$sDossierPhotos				= 'photos/';		// !! n'oubliez pas le '/' à la fin
$iFichiersMaxDansPanier		= 15;				// mettre à 0 pour désactiver la limitation du panier
$iLargeurMax				= 150;				// valeur en pixels
$iHauteurMax				= 113;				// idem
$iImgParPage				= 15;
$iChmodDossierMiniatures	= 0755;
$iChmodFichiersMiniatures	= 0644;

/************************************** FIN PARAMETRES EDITABLES **************************************/

set_magic_quotes_runtime(0);

// ====================
// INCLUSION DES FICHIERS NECESSAIRES
//
require_once( './classes/panierdefichiers.class.php' );
require_once( './classes/util.class.php' );

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
$sPanierLienArchive = $sPanierLienVider = $sPanierLienToutAjouter = $sPanierLienToutSupprimer = '';

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
	
			$oOutils->processImgFile( mime_content_type( $sCheminPhoto ), $sCheminPhoto, $sCheminMiniature, $iLargeurMax, $iHauteurMax, '' );
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

?>

<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'.chr(10).chr(13); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://wwww.w3.org/1999/xhtml">
	<head>
		
		<link rel="stylesheet" href="./medias/style.css" type="text/css" />
		
		<title>.: AlBulles :. .: <?php echo ( empty( $sRep ) ) ? 'Accueil' : "Photos de $sRep"; ?> :.</title>
 	
	</head>
	
	<body>
	
		<!-- DEBUT droite -->
		<div class="droite">
			
			<h1><?php echo ( empty( $sRep ) ) ? 'Accueil' : $sRep; ?></h1>
			
			<?php
			if ( empty( $sRep ) )
			{
			?>
			
			<div class="accueil">
			
				<strong>Bienvenue dans AlBulles !</strong>
				
				<br /><br />
				
				AlBulles est un programme de galerie photos.<br /><br />
				
				Ce texte est un exemple. Vous pouvez le remplacer par ce que vous souhaitez. Pour consulter les photos
				disponibles, utilisez la liste des dossiers.<br /><br />
				
				Un panier virtuel est &agrave; votre disposition. Il vous suffit d'ajouter les images &agrave; votre panier pour
				ensuite les t&eacute;l&eacute;charger sous forme d'archive zip.
				
			</div>
			
			<?php
			}
			else {
			?>
			
			<!-- DEBUT barre de navigation -->
			<div class="navigation">
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="page">Accueil</a>
				Pages : <?php echo $sNavigation; ?>
			</div>
			<!-- FIN barre de navigation -->
			
			<?php
			for ( $i = 0 ; $i < sizeof( $aMiniatures ) ; $i++ ) { ?>
			<!-- DEBUT une miniature -->
			<div class="miniature">
			
				<?php echo $aMiniatures[$i]['LIEN_PHOTO']; ?>
				
				<div class="infosImg">
					<?php
					echo $aMiniatures[$i]['DIM_PHOTO'].'<br />';
					echo $aMiniatures[$i]['SIZE_PHOTO'];
					?>
				</div>
				
				<div class="puce"><?php echo $aMiniatures[$i]['AJOUT_PANIER']; ?></div>
				
			</div>
			<!-- FIN une miniature -->
			
			<?php
			} // for
			} // if
			?>
			
			<div class="spacer"></div>
		
		</div>
		<!-- FIN droite -->
	
		<!-- DEBUT gauche -->
		<div class="gauche">
		
			<!-- DEBUT liste dossiers photos -->
			<div class="dossiers">
			
				<img src="./medias/images/albulles_dossiers_dispos.jpg" alt="Dossiers des photos" />
				
				<ul class="liens">
					<?php echo $sLiensDossiersPhotos; ?>
				</ul>
				
				<div class="spacer"></div>
			
			</div>
			<!-- FIN liste dossiers photos -->
			
			<!-- DEBUT barre de gestion du panier -->
			<div class="panier">
			
				<img src="./medias/images/albulles_panier.jpg" alt="Panier" /><br /><br />
				
				Fichiers dans le panier : <br /><strong><?php echo $sNbFichiersDansLePanier; ?></strong>
				
				<br /><br />
				
				<div class="actions">
					<?php echo $sPanierLienArchive,chr(10),chr(13),$sPanierLienVider,chr(10),chr(13); ?>
				</div>
				
				<br />
				
				<?php echo $sPanierLienToutAjouter,chr(10),chr(13),$sPanierLienToutSupprimer,chr(10),chr(13); ?>
			
			</div>
			<!-- FIN barre de gestion du panier -->
			
			<!-- DEBUT Copyright -->
			<div class="copyright">
			
				<a href="http://www.mozilla.eu.org/fr/products/firefox/" title="Ce site s'affiche mieux avec un navigateur respectant les normes">
					<img src="./medias/images/firefox_80x15.png" width="80" height="15" title="Ce site s'affiche mieux avec un navigateur respectant les normes" alt="T&eacute;l&eacute;chargez FireFox" />
				</a>
				
				<br />
				
				<a href="http://jebulle.net/index.php?rubrique=albulles" title="T&eacute;l&eacute;chargez AlBulles">
					<img src="./medias/images/AlBulles_80x15.png" width="80" height="15" title="T&eacute;l&eacute;chargez AlBulles" alt="T&eacute;l&eacute;chargez AlBulles" />
				</a>
				
				<br />
				
				AlBulles 0.3 &copy; <a href="http://jebulle.net">Bubulles Creations</a> - 2005
			
			</div>
			<!-- FIN Copyright -->
		
		</div>
		<!-- FIN gauche -->
	
	</body>
	
</html>