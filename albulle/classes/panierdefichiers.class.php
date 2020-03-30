<?php

///////////////////////////////
// LICENCE
///////////////////////////////
//
// © DUCARRE Cedric, Bubulles Creations, (09/05/2005) 
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
 * @since 20/08/2005
 * @version 0.6 (PHP4)
 * 
 */

// nom de la variable du panier dans la session
define( 'NOM_PANIER_SESSION', 'JB_PANIER_FICHIERS' );

// chemin d'accès à la librairie de compression
define( 'COMPRESS_LIB', 'pclzip.lib.php' );

class PanierDeFichiers {
	
	/**
	 * Nombre maximum de fichiers dans le panier.
	 *
	 * @var [INTEGER]
	 * @access [PRIVATE]
	 */
	var $_iNbFichiersMax = 0;
	
	/**
	 * Constructeur de la classe.
	 *
	 * Peut recevoir un nombre maximum de fichiers pour limiter le contenu du panier.
	 * Si aucun paramètre ou que le nombre passé vaut 0 ou est négatif le panier est illimité.
	 *
	 * @param [INTEGER]	$iNbFichiersMax	Nombre de fichiers que l'on peut mettre dans le panier.
	 * @return [VOID]
	 */	
	function PanierDeFichiers( $iNbFichiersMax = 0 )
	{
		
		// verification que le module de compression est actif sur le serveur
		if( !extension_loaded( 'zlib' ) )
			die( '<strong>[ Erreur fatale ]</strong> L\'extension \'zlib\' n\'est pas charg&eacute;e. Impossible d\'utiliser le panier sans elle.');
			
		// s'il n'y a pas de session démarrée, il faut la créer
		if( session_id() === '' )
			session_start();
			
		// creation du panier s'il n'existe pas déjà
		if( !isset( $_SESSION[NOM_PANIER_SESSION] ) )
			$_SESSION[NOM_PANIER_SESSION] = array();
			
		// initialisation du nombre max de fichiers
		$this->_iNbFichiersMax = ( $iNbFichiersMax < 0 ) ? 0 : $iNbFichiersMax;
		
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
		if( ( array_search( $sCheminFichier, $_SESSION[NOM_PANIER_SESSION] ) === false ) && !$this->PanierPlein() )
		{
			
			$_SESSION[NOM_PANIER_SESSION][] = $sCheminFichier;
			return true;
			
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
		if( ( $iPosition = array_search( $sCheminFichier, $_SESSION[NOM_PANIER_SESSION] ) ) !== false )
		{
			
			unset( $_SESSION[NOM_PANIER_SESSION][$iPosition] );
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
	function ViderPanier()
	{
		$_SESSION[NOM_PANIER_SESSION] = array();
	}
	
	/**
	 * Création de l'archive qui contient les fichiers du panier.
	 * Par défaut, l'archive est écrite sur le disque puis chargée en mémoire et enfin supprimée.
	 * L'archive chargée en mémoire est alors envoyée au navigateur client pour le télechargement.
	 *
	 * @param [STRING]	$sNomFichier	Nom à donner à l'archive.
	 * @param [BOOLEAN]	$bEcrireSeulement	Si TRUE, l'archive n'est pas effacée du disque et n'est pas envoyée au navigateur.
	 * @return [VOID]
	 */
	function CreerArchive( $sNomFichier, $bEcrireSeulement = false )
	{

		$sFichierZip = $sNomFichier.'.zip';
		
		// inclusion de la librairie de compression zip	
		require_once( COMPRESS_LIB );
		
		// creation de l'objet du fichier zip
		$oZip = new PclZip( $sFichierZip );
		
		// tri des index du panier qui peuvent n'être plus bon aps des suppressions
		sort( $_SESSION[NOM_PANIER_SESSION] );
		
		// ajout des fichiers au zip et ecriture sur le disque
		if( $oZip->create( $_SESSION[NOM_PANIER_SESSION] ) == 0 )
			exit( "# PANIER # <strong>[ Erreur ]</strong> => ".$oZip->errorInfo(true) );
		
		// destruction objet zip
		unset( $oZip );	
		
		if( $bEcrireSeulement === false )
		{
		
			// lecture binaire de l'archive
			$fpHandle = fopen( $sFichierZip, 'rb' );
			$iSize = filesize( $sFichierZip );
			$sArchive = fread( $fpHandle, $iSize );
			fclose( $fpHandle );
			
			// suppression de l'archive
			unlink( $sFichierZip );
			
			// chargement des entetes HTTP pour l'envoi de l'archive
			header( 'Content-type: application/zip');
			header( 'Content-length: '.$iSize );
			header( 'Content-disposition: attachment; filename="'.basename( $sNomFichier ).'.zip"');
			
			// envoi au navigateur
			echo $sArchive;
			
		}
		
	}
	
	/**
	 * Compte le nombre de fichiers dans le panier.
	 *
	 * @param [VOID]
	 * @return [INTEGER]	Retourne le nombre de fichiers dans le panier.
	 */
	function CompterFichiers()
	{
		return sizeof( $_SESSION[NOM_PANIER_SESSION] );
	}
	
	/**
	 * Vérifie l'existence d'un fichier dans le panier.
	 *
	 * @param [STRING]	$sCheminFichier	Chemin du fichier à vérifier.
	 * @return [BOOLEAN]				TRUE si le fichier est dans le panier, FALSE sinon.
	 */
	function EstDansLePanier( $sCheminFichier )
	{
		
		if( array_search( $sCheminFichier, $_SESSION[NOM_PANIER_SESSION] ) !== false )
			return true;
		else
			return false;
			
	}
	
	/**
	 * Permet de savoir si le panier est plein.
	 *
	 * @param [VOID]
	 * @return [BOOLEAN]	TRUE si le panier est plein, FALSE sinon.
	 */	
	function PanierPlein()
	{
		
		// si un nombre max de fichiers a été défini et que le panier est plein
		if( ($this->_iNbFichiersMax > 0 ) && ( $this->CompterFichiers() >= $this->_iNbFichiersMax ) )
			return true;
			
		return false;
			
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