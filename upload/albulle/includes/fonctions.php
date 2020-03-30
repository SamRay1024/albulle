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
 * AlBulle - Galerie photos
 *
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @name AlBulle
 * @since 26/09/2006
 * @version 0.2
 */

if( !defined( '_JB_INCLUDE_AUTH' ) ) {
	header( 'Content-type: text/html; charset=utf-8' );
	exit( 'Vous n\'êtes pas autorisé à afficher cette page.' );
}

/**
 * Vérifications des éléments nécessaires au fonctionnement d'AlBulle.
 *
 * @param	[Void]
 * @return	[Void]
 */
function verifications()
{
	global $_JB_AL_VARS;

	if( !extension_loaded( 'gd' ) )
		erreur( 'La librairie GD n\'est pas chargée sur votre serveur PHP. Elle est obligatoire pour
				assurer le fonctionnement d\'AlBulle. Activez-la dans le fichier <em>php.ini</em> ou
				contactez votre administrateur si vous n\'êtes pas propriétaire du serveur. <br /><br />
				Rendez-vous sur les forums de JeBulle.Net pour visualiser <a href="http://forums.jebulle.net/viewtopic.php?id=417">
				ce message</a> (Lisez l\'erreur 1) qui concerne cette erreur.' );

	if( !is_dir(JB_AL_ROOT.JB_AL_DOSSIER_PHOTOS) )	// existence dossier des photos
		erreur( 'Le dossier <em>'.JB_AL_ROOT.JB_AL_DOSSIER_PHOTOS.'</em> est introuvable.
				Vérifiez la configuration dans le fichier <strong>config.php</strong>. Il s\'agit
				du répertoire qui doit contenir vos albums photos !' );

	if( !is_dir(JB_AL_ROOT.JB_AL_DOSSIER_MINIATURES) )	// existence dossier des miniatures
		erreur( 'Le dossier <em>'.JB_AL_ROOT.JB_AL_DOSSIER_MINIATURES.'</em> est introuvable.
				Vérifiez la configuration dans le fichier <strong>config.php</strong>. Il s\'agit
				du répertoire qui va contenir les miniatures de vos images !' );

	if( !is_writeable(JB_AL_ROOT.JB_AL_DOSSIER_MINIATURES) )    // est-ce que le dossier des miniatures est autorisé en écriture
		erreur( 'Le dossier <em>'.JB_AL_ROOT.JB_AL_DOSSIER_MINIATURES.'</em> n\'est pas autorisé en écriture. Vous devez
				rendre ce dossier accessible en écriture pour que les miniatures de vos photos puissent être générées.' );

	if( !file_exists(JB_AL_ROOT.JB_AL_FICHIER_ACCUEIL) )	// existence fichier accueil
		erreur( 'Le fichier <em>'.JB_AL_ROOT.JB_AL_FICHIER_ACCUEIL.'</em> est introuvable.
				Vérifiez la configuration dans le fichier <strong>config.php</strong>. Si ce fichier
				n\'existe pas, créez-le et complétez-le pour bénéficier d\'un texte d\'accueil.' );

	if( (JB_AL_VIGNETTES_HAUTEUR === 0) || (JB_AL_VIGNETTES_LARGEUR === 0) )	// Vérification dimensions de redimensionnement
		erreur( 'Les valeurs de hauteur et largeur pour le redimensionnement des photos pour la génération
				des miniatures ne peuvent être nulles. Veuillez modifier ces valeurs dans la configuration.' );

	if( JB_AL_VIGNETTES_PAR_PAGE === 0 )	// nombre d'images par page
		erreur( 'Le nombre d\'images par page ne peut pas être nul. Veuillez corriger sa valeur dans la configuration.' );

	if( JB_AL_MODE_CENTRE === true )	// Vérification dossier centre de téléchargement
	{
		if( !is_dir(JB_AL_ROOT.JB_AL_DOSSIER_CENTRE) )
			erreur( 'Le dossier <em>'.JB_AL_ROOT.JB_AL_DOSSIER_CENTRE.'</em> est introuvable.
					Vérifiez la configuration dans le fichier <strong>config.php</strong>.' );
	}
}

/**
 * Générer une arborescence depuis un dossier de base.
 *
 * Cette fonction permet de contruire une liste HTML <ul>...</ul> dont les éléments <li>...</li>
 * représentent les sous-dossiers présents dans celui de base.
 *
 * @param [STRING]		$sBaseRep						Répertoire de base
 * @param [STRING]		$sRepCourant					Répertoire sélectionné
 * @param [INTEGER]		$iNiveau						Profonfeur du répertoire demandé
 * @param [ARRAY]		$aDossiersInterdits				Tableau des dossiers à ne pas afficher
 * @param [ARRAY]		$aExtensionsFichiersAutorises	Extension des fichiers autorisés
 * @param [BOOLEAN]		$bAfficherNbFichiers			Afficher le nombre de fichiers dans un dossier
 * @param [BOOLEAN]		$bAfficherNbSiVide				Afficher ce nombre même si le dossier est vide
 * @param [BOOLEAN] 	$bDeroulerTout					Dérouler tous les noeuds ou seulement celui sélectionné
 * @param [BOOLEAN] 	$bFiltrerPrefixes				Filtrer les noms des dossiers
 * @param [STRING]		$sSeparateurFiltres				Si le paramètre précédent est vrai, le séparateur qui sépare préfixe et nom du dossier.
 * @return [STRING]										La chaîne HTML de l'arborescence complète.
 */
function genererArborescence( $sBaseRep, $sRepCourant, $iNiveau, $aDossiersInterdits, $aExtensionsFichiersAutorises,
								$bAfficherNbFichiers, $bAfficherNbSiVide, $bDeroulerTout, $bFiltrerPrefixes, $sSeparateurFiltres = '' )
{
	global $_JB_AL_VARS, $aActions, $oOutils, $oUrl;

	$sArborescenceHTML = '';			// La chaîne qui contiendra la liste <ul>
	$sDossiersRepCourant = '';			// La chaîne qui contiendra la liste <ul> des sous-dossiers du dossier courant
	$aFichiersRepCourant = array();		// Le tableau qui contiendra les fichiers du répertoire sélectionné

	// Lecture du fichier du thème qui contient les définitions des li
	$aLignes = file( $_JB_AL_VARS['s_acces_theme'].'html/arborescence.thm.php' );

	$sLiVide		= rtrim($aLignes[0]);
	$sLiRemonter	= rtrim($aLignes[1]);
	$sLiCourant		= rtrim($aLignes[2]);

	// Lecture du répertoire parent à celui de base reçu
	$sResultat = $oOutils->SousChaineGauche( $sRepCourant, '/', 1 );
	$sRepParent = ( $sResultat === $sRepCourant ) ? '' : $sResultat;

	// Détermination du premier niveau à lire selon ce qui a été demandé et où l'utilisateur se trouve.
	if( $iNiveau > 1 )
		$sDossierLecture = $sBaseRep.$sRepParent;
	else $sDossierLecture = $sBaseRep;

	// lecture répertoires
	$mResultat = $oOutils->advScanDir( $sDossierLecture, 'TOUT', $aDossiersInterdits );
	$aListeTotale = ( $mResultat === false ) ? array() : $mResultat;

	// lecture nombre dossiers lus
	$iNbDossiers = sizeof( $aListeTotale['dir'] );

	// S'il n'y a pas de dossiers.
	if ( $iNbDossiers === 0 )
		$sArborescenceHTML = $sLiVide;

	// Si on n'est pas à la racine, il faut ajouter le lien pour remonter
	if( $iNiveau > 1 )
		$sArborescenceHTML = str_replace(
								'{HREF_REMONTER}',
								$oUrl->construireUrl( 'rep='.rawurlencode(stripslashes( $sRepParent )).$aActions['diaporama'] ),
								$sLiRemonter
							)."\n";

	// Création liste dossiers
	for( $i = 0 ; $i < $iNbDossiers ; $i++ )
	{
		$sLienNiveau1 = ( $sRepParent === '' ) ? $aListeTotale['dir'][$i] : $sRepParent.'/'.$aListeTotale['dir'][$i];
		$sGrasDebut = '';
		$sGrasFin = '';

		// lecture sous dossiers du dossier courant
		$mResultat = $oOutils->advScanDir(	$sDossierLecture.'/'.$aListeTotale['dir'][$i],	// Le dossier à lire
											'TOUT',												// Ce qu'il faut lire
											$aDossiersInterdits,					// Les dossiers à exclure
											$aExtensionsFichiersAutorises			// Les extensions des fichiers à lister
										);
		$aListeSousRepPhotos = ( $mResultat === false ) ? array() : $mResultat;

		// lecture nombre sous-dossiers lus
		$iNbSousDossiers = sizeof( $aListeSousRepPhotos['dir'] );

		// test si on se trouve sur le dossier courant pour le mettre en gras
		if( $sLienNiveau1 === $sRepCourant )
		{
			$sCssIdCourant = ' id="courant"';
			$aFichiersRepCourant = $aListeSousRepPhotos['file'];
		}
		else $sCssIdCourant = '';

		// lien dossier parent
		$iNbPhotos = sizeof( $aListeSousRepPhotos['file'] );
		$sNbPhotos = ( $bAfficherNbFichiers && ( $iNbPhotos > 0 || ($iNbPhotos === 0 && $bAfficherNbSiVide) )) ? '<em>('.$iNbPhotos.')</em>' : '';

		// Application filtres sur le nom du dossier si actif
		$sNomRep = $bFiltrerPrefixes ? $oOutils->enleverPrefixe( $aListeTotale['dir'][$i], $sSeparateurFiltres ) : $aListeTotale['dir'][$i];

		// Création li
		$sArborescenceHTML .= preg_replace(
								array( '`{ID_COURANT}`', '`{HREF_DOSSIER}`', '`{NOM_DOSSIER}`', '`{NB_IMAGES}`' ),
								array(
									$sCssIdCourant,
									$oUrl->construireUrl( 'rep='.rawurlencode(stripslashes( $sLienNiveau1 )).$aActions['diaporama'] ),
									str_replace( '_', ' ', $sNomRep ),
									$sNbPhotos
								),
								$sLiCourant
							);

		// Concaténation sous-liste	(uniquement pour le dossier courant si la config est définie comme telle)
		if( $bDeroulerTout || ($sLienNiveau1 === $sRepCourant) )
		{
			$sSousArborescenceHTML = '';

			if( $iNbSousDossiers > 0 )
				$sSousArborescenceHTML = "\n<ul>\n";

			for( $j = 0 ; $j < $iNbSousDossiers ; $j++ )
			{
				// on ne calcule le nombre de photo d'un dossier que si autorisé dans la config
				if(  $bAfficherNbFichiers === true )
				{
					$mResultat = $oOutils->advScanDir( $sDossierLecture.'/'.$aListeTotale['dir'][$i].'/'.$aListeSousRepPhotos['dir'][$j], 'FICHIERS_SEULEMENT', $aDossiersInterdits, $aExtensionsFichiersAutorises );
					$aListeSousSousRep = ( !$mResultat ) ? array() : $mResultat;

					$iNbPhotos = sizeof( $aListeSousSousRep );
					$sNbPhotos = ($iNbPhotos > 0 || ($iNbPhotos === 0 && $bAfficherNbSiVide)) ? '<em>('.$iNbPhotos.')</em>' : '';
				}
				else $sNbPhotos = '';

	            // Application filtres sur le nom du dossier si actif
				$sNomSousRep = $bFiltrerPrefixes ? $oOutils->enleverPrefixe( $aListeSousRepPhotos['dir'][$j], $sSeparateurFiltres ) : $aListeSousRepPhotos['dir'][$j];

				// Création li
				$sSousArborescenceHTML .= preg_replace(
										array( '`{ID_COURANT}`', '`{HREF_DOSSIER}`', '`{NOM_DOSSIER}`', '`{NB_IMAGES}`' ),
										array(
											'',
											$oUrl->construireUrl( 'rep='.rawurlencode(stripslashes( $sLienNiveau1.'/'.$aListeSousRepPhotos['dir'][$j] )).$aActions['diaporama'] ),
											str_replace( '_', ' ', $sNomSousRep ),
											$sNbPhotos
										),
										$sLiCourant
									)."</li>\n";
			}


			if( $iNbSousDossiers > 0 ) {
				$sSousArborescenceHTML .= "\n</ul>\n";

				// Si on est sur le dossier courant, on stocke la liste des sous-dossiers
				if( $sLienNiveau1 === $sRepCourant )
					$sDossiersRepCourant = $sSousArborescenceHTML;
			}

			$sArborescenceHTML .= $sSousArborescenceHTML;
	    }

	    $sArborescenceHTML .= "</li>\n";
	}

	return array( 'arborescence_html' => $sArborescenceHTML, 'fichiers_dossier_courant' => $aFichiersRepCourant, 'dossiers_rep_courant' => $sDossiersRepCourant );
}

/**
 * Permet de savoir si le navigateur du client est Internet Explorer.
 *
 * @return [BOOLEAN]			True : le navigateur est IE. False : autre navigateur.
 */
function isIE()
{
	return !(strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) === false);
}

?>
