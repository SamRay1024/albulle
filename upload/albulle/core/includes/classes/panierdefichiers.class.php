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
 * @since 16/05/2005
 * @version 03/05/2010
 */

// nom de la variable du panier dans la session
define( 'NOM_PANIER_SESSION', 'JB_PANIER_FICHIERS' );

// chemin d'accès à la librairie de compression
define( 'COMPRESS_LIB', 'OMzip.php' );

class PanierDeFichiers {

	/**
	 * Dossier racine où sont stockés les fichiers ajoutés au panier.
	 *
	 * @var	string
	 * @access private
	 */
	private $_sRoot = '';
	
	/**
	 * Nombre maximum de fichiers dans le panier.
	 *
	 * @var integer
	 * @access private
	 */
	private $_iNbFichiersMax = 0;

	/**
	 * Poids maximum de l'archive en Mo.
	 *
	 * @var float
	 * @access private
	 */
	private $_fPoidsMax = 0;

	/**
	 * Constructeur de la classe.
	 *
	 * Peut recevoir un nombre maximum de fichiers pour limiter le contenu du panier.
	 * Si aucun paramètre ou que le nombre passé vaut 0 ou est négatif le panier est illimité.
	 *
	 * @param string	$sRoot			Dossier racine où se situent les fichiers du panier.
	 * @param integer	$iNbFichiersMax	Nombre de fichiers que l'on peut mettre dans le panier.
	 * @param float		$fPoidsMax		Poids maximum en Mo que peut prendre l'archive du panier.
	 */
	public function __construct( $sRoot, $iNbFichiersMax = 0, $fPoidsMax = 0 ) {
	
		// verification que le module de compression est actif sur le serveur
		if( !extension_loaded( 'zlib' ) )
			exit('# PANIER # <strong>[ Erreur fatale ]</strong> L\'extension \'zlib\' n\'est pas charg&eacute;e. Impossible d\'utiliser le panier sans elle.');

		// s'il n'y a pas de session démarrée, il faut la créer
		if( session_id() === '' )
			session_start();
		
		$this->_sRoot = realpath($sRoot) . DIRECTORY_SEPARATOR;

		// creation du panier s'il n'existe pas déjà
		if( !isset( $_SESSION[NOM_PANIER_SESSION] ) )
			$_SESSION[NOM_PANIER_SESSION] = array();
		else
			$this->verifierPanier();

		// initialisation du nombre max de fichiers
		$this->_iNbFichiersMax	= ( $iNbFichiersMax < 0 ? 0 : $iNbFichiersMax );
		$this->_fPoidsMax		= ( $fPoidsMax < 0 ? 0 : $fPoidsMax );
	}

	/**
	 * Ajouter un fichier au panier.
	 *
	 * Ajoute un fichier au panier que s'il n'y est pas déjà et si le panier n'est pas plein.
	 * La recherche si le fichier se trouve déjà dans le panier s'effectue
	 * avec le chemin complet du fichier (autorise alors deux noms de fichiers
	 * identiques mais dans des dossiers différents).
	 *
	 * @param string	$sCheminFichier	Chemin du fichier.
	 * @return boolean					TRUE si le fichier a été ajouté, FALSE sinon.
	 */
	public function ajouter( $sCheminFichier ) {
	
		// ajout du fichier s'il n'y est pas déjà et si le panier n'est pas plein
		if( ($this->estDansLePanier($sCheminFichier) === false) && !$this->estPanierPlein() ) {
		
			// Vérification chemin
			if( $this->verifierChemin($sCheminFichier) ) {
			
				$_SESSION[NOM_PANIER_SESSION][] = $sCheminFichier;
				return true;
			}
		}

		return false;
	}

	/**
	 * Supprimer le fichier spécifié du panier.
	 *
	 * @param string	$sCheminFichier	Chemin du fichier à supprimer. (Idem méthode d'ajout)
	 * @return boolean					TRUE si le fichier a été supprimé, FALSE sinon.
	 */
	public function supprimer( $sCheminFichier ) {
	
		// si l'image se trouve bien dans le panier on la supprime
		if( ($iPosition = $this->estDansLePanier($sCheminFichier)) !== false ) {
		
			unset( $_SESSION[NOM_PANIER_SESSION][$iPosition] );

			// Mise-à-jour des index des éléments pour éviter les trous dans l'indexation
			sort( $_SESSION[NOM_PANIER_SESSION] );

			return true;
		}

		return false;
	}

	/**
	 * Vider le panier.
	 */
	public function viderPanier() {
	
		$_SESSION[NOM_PANIER_SESSION] = array();
	}

	/**
	 * Créer l'archive qui contient les fichiers du panier.
	 *
	 * L'archive est générée à la volée pour être directement envoyée à la sortie standard, soit
	 * le navigateur du client qui demande le téléchargement de son panier.
	 *
	 * @param string	$sNomArchive		Nom à donner à l'archive sans extension. Valeur par défaut : 'Panier'.
	 * @param array		$aNomsInternes		Utilisez ce tableau si la structure interne de l'archive doit être différente de la structure
	 * 										des fichiers d'origine. Pour fonctionner, ce tableau doit contenir autant d'éléments que
	 * 										le panier. Chacun d'eux correspond au nouveau chemin + nom de l'élément dans l'archive.
	 */
	public function creerArchive( $sNomArchive = 'Panier', $aNomsInternes = array() ) {
	
		// inclusion de la librairie de compression zip
		require_once( COMPRESS_LIB );

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
	 * Compter le nombre de fichiers dans le panier.
	 *
	 * @return integer
	 */
	public function compterFichiers() {
		
		return sizeof( $_SESSION[NOM_PANIER_SESSION] );
	}

	/**
	 * Calculer le poids estimé que fera l'archive.
	 *
	 * @return float	Poids en octets.
	 */
	 public function calculerPoids() {
	 
	 	$fPoids = 0;
		
	 	foreach( $_SESSION[NOM_PANIER_SESSION] as $key => $value )
			$fPoids += filesize($this->_sRoot.$value);
			
		// On ramène le poids de l'archive à 97% de la taille totale
		// (ratio généralement constaté pour zip & tar)
	 	return $fPoids * (97/100);
	 }

	/**
	 * Vérifier l'existence d'un fichier dans le panier.
	 *
	 * @param string	$sCheminFichier	Chemin du fichier à vérifier.
	 * @return mixed					La position de l'élément dans le panier si existant, FALSE sinon.
	 */
	public function estDansLePanier( $sCheminFichier ) {
		
		return array_search( $sCheminFichier, $_SESSION[NOM_PANIER_SESSION] );
	}

	/**
	 * Savoir si le panier est plein.
	 *
	 * @return boolean		TRUE si le panier est plein, FALSE sinon.
	 */
	public function estPanierPlein() {
	
		// si un nombre max de fichiers a été défini et que le panier est plein
		if( (($this->_iNbFichiersMax > 0 ) && ( $this->compterFichiers() >= $this->_iNbFichiersMax )) ||
			(($this->_fPoidsMax > 0 ) && ( $this->calculerPoids() >= $this->_fPoidsMax * 1024 * 1024 )) )
			return true;

		return false;
	}

	/**
	 * Retourner le contenu du panier.
	 *
	 * @return	array		Le tableau qui représente le contenu du panier.
	 */
	public function obtenirPanier() {
	
		return $_SESSION[NOM_PANIER_SESSION];
	}

	/**
	 * Vérifier que l'adresse d'un fichier donné se trouve bien dans le dossier racine.
	 * 
	 * Cette vérification est nécessaire pour prévénir l'utilisation des '../' dans l'adresse
	 * d'un fichier pour tenter de remonter à des fichiers sensibles.
	 *
	 * @param	string	$sChemin	Adresse à vérifier.
	 * @return	boolean				True si le chemin est correct et que le fichier existe, false dans le cas contraire.
	 */
	private function verifierChemin( $sChemin ) {
	
		$sCheminReel = realpath($this->_sRoot . $sChemin);	
		return ( is_string($sCheminReel) && strpos($sChemin, '../') === false );
	}
	
	/**
	 * Vérifier que les fichiers présents dans le panier existent.
	 */
	private function verifierPanier() {
	
		foreach($_SESSION[NOM_PANIER_SESSION] as $key => $value) {
		
			if( !$this->verifierChemin($value) )
				unset( $_SESSION[NOM_PANIER_SESSION][$key] );
		}

		sort( $_SESSION[NOM_PANIER_SESSION] );
	}
}