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
 * Objet de gestion de l'URL pour Albulle.
 *
 * @author		SamRay1024
 * @copyright	Bubulles Creation - http://jebulle.net
 * @since		03/10/2006
 * @version		1.0
 *
 */

class Url {
	
	/**
	 * Définit si l'on doit construire une Url en respectant $_SERVER['REQUEST_URI'] ou 
	 * en utilisant simplement $_SERVEUR['PHP_SELF'].
	 * 
	 * Si cet attribut vaut True, alors il faut respecter $_SERVER['REQUEST_URI'].
	 *
	 * @var		boolean
	 * @access	public
	 */
	var $bRequestUri = false;
	
	/**
	 * Chaîne qui stockera les Url générées.
	 *
	 * @var		string
	 * @access	public
	 */
	var $sUrl = '';
	
	/**
	 * Le fichier exécuté avec son chemin d'accès depuis la racine.
	 * (pour http://hostname.com/dir1/dir2/index.php, $sPath vaudra '/dir1/dir2/index.php')
	 *
	 * @var		string
	 * @access	public
	 */
	var $sPath = '';
	
	/**
	 * Le tableau associatif qui contient les paramètres et leur valeurs passés dans l'Url.
	 *
	 * @var		array
	 * @access	public
	 */
	var $aQuery = array();
	
	/**
	 * Tout ce qui se trouve après la hachure # (comme les ancres).
	 *
	 * @var		string
	 * @access	public
	 */
	var $sFragment = '';
	
	/**
	 * Constructeur.
	 *
	 * @param	boolean 	$bModeRequestUri	Indique le mode de construction de l'Url.
	 * @return	Url
	 */
	function Url( $bModeRequestUri )
	{
		if( !is_bool($bModeRequestUri) ) $bModeRequestUri = false;
		
		if( ($this->bRequestUri = $bModeRequestUri) === true )
		{
			$aUrl = parse_url( $_SERVER['REQUEST_URI'] );
			
			$this->sPath		= $aUrl['path'];
			$this->sFragment	= $aUrl['fragment'];
			$this->aQuery		= $this->explodeParams( $aUrl['query'] );
		}
	}
	
	/**
	 * Contruit une Url.
	 *
	 * @param	string	$sQuery		Paramètres à ajouter dans l'url.
	 * @param	string	$sFragment	Element à ajouter après la hachure (remplace l'élément courant dans le cas du respect de REQUEST_URI).
	 * @return	string				L'Url fraîchement créée. Elle se trouve aussi dans l'attribut $sUrl.
	 */
	function construireUrl( $sQuery, $sFragment = '' )
	{
		if( $sFragment !== '' )	$this->sFragment = $sFragment;
		
		if( $this->bRequestUri === false )
			
			$this->sUrl = $_SERVER['PHP_SELF']
							.( $sQuery !== '' ? '?'.$sQuery : '' )
							.( $sFragment !== '' ? '#'.$sFragment : '' );
		
		else {
			// Suppression des paramètres redéfinis dans la nouvelle chaîne du tableau des paramètres d'origine
			$this->verifierDoublons( $this->explodeParams($sQuery) );
			
			// Génération des paramètres originaux moins les nouveaux
			$sOriginalQuery = $this->implodeParams($this->aQuery);
			
			// Concaténation de l'url
			$this->sUrl = $_SERVER['PHP_SELF'];
			
			if( !empty($sOriginalQuery) || !empty($sQuery) )	$this->sUrl .= '?';
			if( !empty($sOriginalQuery) &&  empty($sQuery) )	$this->sUrl .= $sOriginalQuery;
			if(  empty($sOriginalQuery) && !empty($sQuery) )	$this->sUrl .= $sQuery;
			if( !empty($sOriginalQuery) && !empty($sQuery) )	$this->sUrl .= $sOriginalQuery.'&amp;'.$sQuery;
			if( !empty($this->sFragment) ) 						$this->sUrl .= '#'.$this->sFragment;
		}
				
		return $this->sUrl;
	}
	
	/**
	 * Transforme la chaine des paramètres d'une Url en un tableau associatif.
	 * 
	 * Le tableau est de la forme Tab['paramètre'] = valeur.
	 *
	 * @param	string	$sQuery		Chaîne de paramètres de la forme param1=valeur1&param2=valeur2&...
	 * @return	array				Le tableau associatif des paramètres.
	 */
	function explodeParams( $sQuery )
	{
		$aParams = array();
		
		$aTemp = explode( '&', $sQuery );
		
		foreach( $aTemp as $key => $value )
		{
			$aParam = explode('=', $value);
			$aParams[$aParam[0]] = (sizeof($aParam) > 0 ? $aParam[1] : '');
		}
		
		return $aParams;
	}
	
	/**
	 * Transforme un tableau associatif de paramètres d'Url en une chaîne formatée pour une Url.
	 * 
	 * Le tableau reçu doit être de la forme Tab['paramètre'] = valeur.
	 *
	 * @param	array	$aParams	Le tableau associatif des paramètres.
	 * @return	string				Chaîne de paramètres de la forme param1=valeur1&param2=valeur2&...
	 */
	function implodeParams( $aParams )
	{
		$aTemp = array();
		
		foreach($aParams as $key => $value)
			if( !empty($key) )
				$aTemp[] = $key.( !empty($value) ? '='.$value : '' );
			
		return (sizeof($aTemp) > 0 ? implode( '&amp;', $aTemp ) : '');
	}
	
	/**
	 * Supprime les paramètres originaux qui sont redéfinis dans les nouveaux.
	 *
	 * @param	array	$aParams	Tableau associatif des nouveaux paramètres.
	 */
	function verifierDoublons( $aParams )
	{
		// Récupérations paramètres originaux et nouveaux paramètres 
		$aParamsOriginaux	= array_keys( $this->aQuery );
		$aParamsNouveaux	= array_keys( $aParams );
		
		// Recherche paramètres originaux qui sont aussi dans les nouveaux paramètres
		$aParamsDoubles		= array_intersect( $aParamsOriginaux, $aParamsNouveaux );
		
		// Suppression des paramètres en double
		foreach( $aParamsDoubles as $key => $value )
			unset( $this->aQuery[$value] );
	}
	
	function pre($tab)
	{
		echo '<pre>';
		print_r($tab);
		echo '</pre>';
	}
}