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
// Ce fichier fait partie d'AlBulle, script de gestion d'albums photos.
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
 * @name download.php
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @since 10/11/2006
 * @version 1.0rc1
 */

define( '_JB_INCLUDE_AUTH', 1 );
define( 'JB_AL_ROOT',		dirname(__FILE__).'/' );

require_once( JB_AL_ROOT.'../config.php' );
require_once( JB_AL_ROOT.'includes/classes/panierdefichiers.class.php' );
require_once( JB_AL_ROOT.'includes/classes/util.class.php' );

$oPanier = new PanierDeFichiers( JB_AL_PANIER_CAPACITE_MAX );

if( $oPanier->CompterFichiers() !== 0 )
{
	// Définition nom archive
	$sNomArchive = ( JB_AL_MODE_CENTRE === true ) ? 'Fichiers' : JB_AL_PANIER_NOM_ARCHIVE;

	//
	// Nécessité de modifier les chemins des éléments du panier car ils sont relatifs au script qui exécute
	// Albulle, soit le index.php de l'installation par défaut. Il faut donc enlever le 1er dossier de
	// chaque chemin et redéfinir le panier avant de créer l'archive.
	//

	// Lecture nombre d'éléments dans le panier
	$iNbPanier = $oPanier->CompterFichiers();

	$aChangementChemins = $oPanier->obtenirPanier();
	$aNomPourArchive = array();

	$oOutils = new Util();

	for( $i = 0 ; $i < $iNbPanier ; $i++ ) {
		$aChangementChemins[$i] = '../'.$oOutils->sousChaineDroite($aChangementChemins[$i], '/', 1);
		$aNomPourArchive[] = $oOutils->sousChaineDroite($aChangementChemins[$i], '/', 2);
	}

	$oPanier->CreerArchive( $sNomArchive, $aChangementChemins, $aNomPourArchive );
}
else
{
	header( 'Content-type: text/html; charset=utf-8' );
	echo('# ALBULLE # <strong>[ Erreur ]</strong> => Le panier est vide, il n\'y a rien à télécharger !<br /><a href="javascript: history.go(-1)">Revenir</a>' );
}

exit();
?>
