<?php

///////////////////////////////
// LICENCE
///////////////////////////////
//
// © DUCARRE Cédric (SamRay1024), Bubulles Créations, (09/05/2005)
//
// webmaster@jebulle.net
// http://jebulle.net
//
// Ce logiciel est un programme servant à gérer un panier de fichiers pour
// sites internet.
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
 * Classe de gestion de panier de fichiers.
 *
 * Permet d'ajouter des fichiers à un panier virtuel et de créer une archive
 * télechargeable de ces fichiers.
 *
 * @author SamRay1024
 * @copyright Bubulles Creation - http://jebulle.net
 * @since 22/05/2007
 * @version 1.0rc3
 *
 */

// nom de la variable du panier dans la session
define( 'NOM_PANIER_SESSION', 'JB_PANIER_FICHIERS' );

// chemin d'accès à la librairie de compression
define( 'COMPRESS_LIB', 'OMzip.php' );

class PanierDeFichiers {

	/**
	 * Dossier racine où sont stockés les fichiers ajoutés au panier.
	 *
	 * @var	[STRING]
	 * @access [PRIVATE]
	 */
	var $_sRoot = '';
	
	/**
	 * Nombre maximum de fichiers dans le panier.
	 *
	 * @var [INTEGER]
	 * @access [PRIVATE]
	 */
	var $_iNbFichiersMax = 0;

	/**
	 * Poids maximum de l'archive en Mo.
	 *
	 * @var [FLOAT]
	 * @access [PRIVATE]
	 */
	var $_fPoidsMax = 0;

	/**
	 * Constructeur de la classe.
	 *
	 * Peut recevoir un nombre maximum de fichiers pour limiter le contenu du panier.
	 * Si aucun paramètre ou que le nombre passé vaut 0 ou est négatif le panier est illimité.
	 *
	 * @param [STRING]	$sRoot			Dossier racine où se situent les fichiers du panier.
	 * @param [INTEGER]	$iNbFichiersMax	Nombre de fichiers que l'on peut mettre dans le panier.
	 * @param [FLOAT]	$fPoidsMax		Poids maximum en Mo que peut prendre l'archive du panier.
	 * @return [VOID]
	 */
	function PanierDeFichiers( $sRoot, $iNbFichiersMax = 0, $fPoidsMax = 0 )
	{
		// verification que le module de compression est actif sur le serveur
		if( !extension_loaded( 'zlib' ) )
			exit('# PANIER # <strong>[ Erreur fatale ]</strong> L\'extension \'zlib\' n\'est pas charg&eacute;e. Impossible d\'utiliser le panier sans elle.');

		// s'il n'y a pas de session démarrée, il faut la créer
		if( session_id() === '' )	session_start();
		
		$this->_sRoot = realpath($sRoot).'/';

		// creation du panier s'il n'existe pas déjà
		if( !isset( $_SESSION[NOM_PANIER_SESSION] ) )	$_SESSION[NOM_PANIER_SESSION] = array();
		else $this->verifierPanier();

		// initialisation du nombre max de fichiers
		$this->_iNbFichiersMax	= ( $iNbFichiersMax < 0 ) ? 0 : $iNbFichiersMax;
		$this->_fPoidsMax		= ( $fPoidsMax < 0 ) ? 0 : $fPoidsMax;
	}

	/**
	 * Ajoute un fichier au panier que s'il n'y est pas déjà et si le panier n'est pas plein.
	 * La recherche si le fichier se trouve déjà dans le panier s'effectue
	 * avec le chemin complet du fichier (autorise alors deux noms de fichiers
	 * identiques mais dans des dossiers différents).
	 *
	 * @param [STRING]	$sCheminFichier	Chemin du fichier.
	 * @return [BOOLEAN]				TRUE si le fichier a été ajouté, FALSE sinon.
	 */
	function Ajouter( $sCheminFichier )
	{
		// ajout du fichier s'il n'y est pas déjà et si le panier n'est pas plein
		if( ($this->EstDansLePanier($sCheminFichier) === false) && !$this->PanierPlein() )
		{
			// Vérification chemin
			if( $this->verifierChemin($sCheminFichier) ) {
				$_SESSION[NOM_PANIER_SESSION][] = $sCheminFichier;
				return true;
			}
		}

		return false;
	}

	/**
	 * Supprime le fichier spécifié du panier.
	 *
	 * @param [STRING]	$sCheminFichier	Chemin du fichier à supprimer. (Idem méthode d'ajout)
	 * @return [BOOLEAN]				TRUE si le fichier a été supprimé, FALSE sinon.
	 */
	function Supprimer( $sCheminFichier )
	{
		// si l'image se trouve bien dans le panier on la supprime
		if( ( $iPosition = $this->EstDansLePanier($sCheminFichier) ) !== false )
		{
			unset( $_SESSION[NOM_PANIER_SESSION][$iPosition] );

			// Mise-à-jour des index des éléments pour éviter les trous dans l'indexation
			sort( $_SESSION[NOM_PANIER_SESSION] );

			return true;
		}

		return false;
	}

	/**
	 * Vide le panier.
	 *
	 * @param [VOID]
	 * @return [VOID]
	 */
	function ViderPanier() { $_SESSION[NOM_PANIER_SESSION] = array(); }

	/**
	 * Création de l'archive qui contient les fichiers du panier.
	 *
	 * L'archive est générée à la volée pour être directement envoyée à la sortie standard, soit
	 * le navigateur du client qui demande le téléchargement de son panier.
	 *
	 * @param [STRING]	$sNomArchive		Nom à donner à l'archive sans extension. Valeur par défaut : 'Panier'.
	 * @param [ARRAY]	$aNomsInternes		Utilisez ce tableau si la structure interne de l'archive doit être différente de la structure
	 * 											des fichiers d'origine. Pour fonctionner, ce tableau doit contenir autant d'éléments que
	 * 											le panier. Chacun d'eux correspond au nouveau chemin + nom de l'élément dans l'archive.
	 * @return [VOID]
	 */
	function CreerArchive( $sNomArchive = 'Panier', $aNomsInternes = array() )
	{
		// inclusion de la librairie de compression zip
		require_once ( COMPRESS_LIB );

		// Création du tableau avec les chemins réels des fichiers présents dans le panier
		$aElementsPourArchive = $_SESSION[NOM_PANIER_SESSION];
		foreach( $aElementsPourArchive as $key => $value )
			$aElementsPourArchive[$key] = $this->_sRoot.$value;
			
		// On place tous les éléments du panier dans un dossier racine du même nom que l'archive si aucune surcharge des chemins du panier n'est demandée
		if( sizeof($aNomsInternes) == 0 ) {
			foreach( $_SESSION[NOM_PANIER_SESSION] as $key => $value )
				$aNomsInternes[] = $sNomArchive.'/'.$_SESSION[NOM_PANIER_SESSION][$key];
		}

		// tri des index du panier qui peuvent n'être plus bon aps des suppressions
		sort($aElementsPourArchive);

		// Envoi de l'archive du panier
		OnTheFlyZIP( $sNomArchive.'.zip', $aElementsPourArchive, $aNomsInternes );
	}

	/**
	 * Compte le nombre de fichiers dans le panier.
	 *
	 * @param [VOID]
	 * @return [INTEGER]	Retourne le nombre de fichiers dans le panier.
	 */
	function CompterFichiers() { return sizeof( $_SESSION[NOM_PANIER_SESSION] ); }

	/**
	 * Calcule le poids que fera l'archive qui contiendra les éléments du panier.
	 *
	 * @param [VOID]
	 * @return [FLOAT]		Retourne le poids du panier.
	 */
	 function CalculerPoids()
	 {
	 	$fPoids = 0;
	 	foreach( $_SESSION[NOM_PANIER_SESSION] as $key => $value )	$fPoids += filesize($this->_sRoot.$value);
	 	return $fPoids * (97/100);	// On ramène le poids de l'archive à 97% de la taille totale (ratio généralement constaté pour zip & tar)
	 }

	/**
	 * Vérifie l'existence d'un fichier dans le panier.
	 *
	 * @param [STRING]	$sCheminFichier	Chemin du fichier à vérifier.
	 * @return [MIXED]					La position de l'élément dans le panier si existant, FALSE sinon.
	 */
	function EstDansLePanier( $sCheminFichier ) { return array_search( $sCheminFichier, $_SESSION[NOM_PANIER_SESSION] ); }

	/**
	 * Permet de savoir si le panier est plein.
	 *
	 * @param [VOID]
	 * @return [BOOLEAN]	TRUE si le panier est plein, FALSE sinon.
	 */
	function PanierPlein()
	{
		// si un nombre max de fichiers a été défini et que le panier est plein
		if( (($this->_iNbFichiersMax > 0 ) && ( $this->CompterFichiers() >= $this->_iNbFichiersMax )) ||
			(($this->_fPoidsMax > 0 ) && ( $this->CalculerPoids() >= $this->_fPoidsMax * 1024 * 1024 )) )
			return true;

		return false;
	}

	/**
	 * Retourne le contenu du panier.
	 *
	 * @param	[VOID]
	 * @return	[ARRAY]		Le tableau qui représente le contenu du panier.
	 */
	function obtenirPanier()
	{	return $_SESSION[NOM_PANIER_SESSION]; }

	/**
	 * Vérifie que l'adresse d'un fichier donné se trouve bien dans le dossier racine.
	 * 
	 * Cette vérification est nécessaire pour prévénir l'utilisation des './' ou '../' dans l'adresse
	 * d'un fichier pour tenter de remonter à des fichiers sensibles.
	 *
	 * @param	[STRING]	$sChemin	Adresse à vérifier.
	 * @return	[BOOLEAN]				True si le chemin est correct et que le fichier existe, false dans le cas contraire.
	 */
	function verifierChemin( $sChemin )
	{
		$sCheminReel = realpath($this->_sRoot.$sChemin);
		return ( is_string($sCheminReel) && strpos($sCheminReel, $this->_sRoot) !== false );
	}
	
	/**
	 * Vérifie que les fichiers présents dans le panier existent.
	 *
	 * @param	[VOID]
	 * @return	[VOID]
	 */
	function verifierPanier()
	{
		foreach($_SESSION[NOM_PANIER_SESSION] as $key => $value) {
			if( !$this->verifierChemin($value) )
				unset( $_SESSION[NOM_PANIER_SESSION][$key] );
		}

		sort( $_SESSION[NOM_PANIER_SESSION] );
	}

	/**
	 * Affiche le contenu du panier.
	 * Fonction utile uniquement pour du déboguage.
	 *
	 * @param [VOID]
	 * @return [VOID]
	 */
	function EtatPanier()
	{
		echo '<pre>';
		print_r( $_SESSION[NOM_PANIER_SESSION] );
		echo '</pre>';
	}

}

?>
