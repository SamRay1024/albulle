<?php

/**
 * Contient la définition de la classe JbError.
 */

///////////////////////////////
// LICENCE
///////////////////////////////
//
// © Cédric Ducarre (SamRay1024), (01/11/2007)
//
// webmaster@jebulle.net
//
// Ce fichier fait partie de la librairie JbLib.
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
 * Exception par défaut de JbLib.
 *
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @since 03/12/2006
 * @version 26/01/2010
 * @package JbLib
 */
class JbError extends Exception {

	/**
	 * Contenu HTML de l'erreur.
	 *
	 * @var string
	 * @access private
	 */
	private $_sHtml = '';

	/**
	 * Constructeur.
	 *
	 * @param	string	$sErrorCode		Le code de l'erreur à afficher.
	 * @param	array	$aArgs			Tableau optionnel avec du texte à remplacer dans les messages d'erreurs.
	 * @param	string	$sLocale		L'identifiant de la langue dans laquelle afficher l'erreur.
	 * @return 	void
	 */
	public function __construct( $sErrorCode, array $aArgs = array(), $sLocale = '' ) {

		$sLocale = JBL_ROOT_LANG . (empty($sLocale) ? JBL_LOCALE : $sLocale);

		$sFileLangErrors = $sLocale . DIRECTORY_SEPARATOR .'errors.xml';

		if( is_file($sFileLangErrors) && is_readable($sFileLangErrors)) {

			// Chargement fichier xml des messages d'erreurs
			$oErrorsLocale = simplexml_load_file( $sFileLangErrors );

			// Récupération code Html de l'erreur
			$this->_sHtml = $oErrorsLocale->html;

			// Récupération message
			$sError = $oErrorsLocale->$sErrorCode;

			if(empty($sError))
				$sError = 'Code de l\'erreur inconnu.';
			
			// Remplacement des variables s'il y en a
			elseif( sizeof($aArgs) > 0 ) {

				// Créé un tableau de masques de la forme %n.
				// Autant de masques de remplacements qu'il y a de variables
				// sont créés
				$aPatterns = array();
				$iNbVars = sizeof($aArgs);

				for( $i = 0 ; $i < $iNbVars ; $i++ )
					$aPatterns[] = '%'.($i+1);

				// Remplacement des masques par leur valeur dans le texte de l'erreur
				$sError = str_replace( $aPatterns, $aArgs, $sError );
			}
		}
		
		if(empty($sError))
			$sError = 'Erreur inconnue, impossible de charger le fichier d\'erreurs : ' .
						$sFileLangErrors;

		// Appel du constructeur de la classe Exception
		parent::__construct( $sError, 0 );
	}

	/**
	 * Surcharge de la fonction __toString de la classe mère.
	 *
	 * @param	void
	 * @return	string		Le code Html de l'erreur.
	 */
	public function __toString() {
		
		$aReplace = array(
			'%type'		=> __CLASS__,
			'%code'		=> $this->getCode(),
			'%file'		=> $this->getFile(),
			'%line'		=> $this->getLine(),
			'%message'	=> $this->getMessage(),
			'%trace'	=> $this->getBackTrace()
		);
		
		$sError = str_replace(array_keys($aReplace), array_values($aReplace), $this->_sHtml);
		
		if( !JBL_UTF8 )
			$sError = utf8_decode($sError);
			
		return $sError;
	}

	/**
	 * Obtenir la pile d'exécution.
	 *
	 * @return string
	 */
	private function getBackTrace() {

		$aTrace = array_reverse($this->getTrace());

		$sBackTrace = '<p class="trace">#0 » <strong>{ main }</strong></p>';

		foreach( $aTrace as $iLevel => $aElt ) {

			$this->convertArgs($aElt['args']);

			$sBackTrace .=
				'<p class="trace">'.
				'<span class="right">'.	$aElt['file'] .
				' ('. $aElt['line'] .')</span>#'.
				($iLevel + 1) .' » <strong>{ '.
				( isset($aElt['class']) ? $aElt['class'] : '' ) .
				( isset($aElt['type']) ? $aElt['type'] : '' ) . $aElt['function'] .
				'('. implode(', ', $aElt['args']) .')'.
				' }</strong></p>';
		}

		return $sBackTrace;
	}

	/**
	 * Convertir les arguments d'une méthode en texte lisible.
	 *
	 * @param array $aArgs	Tableau d'arguments.
	 */
	private function convertArgs( &$aArgs ) {

		foreach($aArgs as $i => $arg) {

			// Cas des booléens
			if(is_bool($arg))
				$aArgs[$i] = ($arg ? 'true': 'false');

			// Cas de la valeur nulle
			if(is_null($arg))
				$aArgs[$i] = 'NULL';

			// Chaînes trop longues
			if( strlen($arg) > 20 )
				$aArgs[$i] = substr($arg, 0, 20). '[...]';
		}
	}
}