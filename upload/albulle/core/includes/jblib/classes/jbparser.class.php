<?php

/**
 * Contient la définition de la classe JbParser.
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
 * Chargement configuration associée.
 */
require_once(JBL_ROOT .'conf/jbparser.conf.php');

/**
 * Classe JbParser
 *
 * Construit une page Html à partir d'une structure. Cette structure est composée
 * de patrons. Chaque patron représente un élément de base permettant la construction
 * finale de la page.
 *
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @since 16/02/2007
 * @version 31/03/2010
 * @package JbLib
 */
class JbParser {

	/**
	 * Le dossier de la structure Html.
	 *
	 * @var string
	 * @access private
	 */
	private $sRootDir = '';

	/**
	 * Le dossier du thème sélectionné.
	 *
	 * Les différents constituants de la structure Html peuvent être surchargés dans ce dossier.
	 *
	 * @var string
	 * @access private
	 */
	private $sSkinDir = '';

	/**
	 * Le fichier d'entrée du squelette Html.
	 *
	 * @var string
	 * @access private
	 */
	private $sMainFileName = '';

	/**
	 * Le contenu de la page générée.
	 *
	 * @var string
	 * @access private
	 */
	private $sPage = '';

	/**
	 * Tableau associatif qui contiendra les pseudos-variables à remplacer dans le patron html.
	 *
	 * Le contenu est de la forme suivante :
	 *
	 * 		$aVars['nom_pseudo_variable'] = 'Contenu à remplacer dans le patron'
	 *
	 * @var array
	 * @access private
	 */
	private $aVars = array();

	/**
	 * Tableau associatif des blocs conditionnels, qui seront à générer ou pas.
	 *
	 * Le contenu est de la forme suivante :
	 *
	 * 		$aBlocks['nom_du_bloc'] = true / false;
	 *
	 * 	- true : le bloc doit être généré
	 * 	- false : le bloc ne doit pas être généré
	 *
	 * @var array
	 * @access private
	 */
	private $aBlocks = array();

	/**
	 * Tableau indexé des boucles.
	 *
	 * Le contenu est de la forme :
	 *
	 * 		$aLoops['nom_de_la_boucle'] = array( array( 'nom_pseudo_variable' => 'contenu à remplacer', ...), ...)
	 *
	 * @var array
	 * @access private
	 */
	private $aLoops = array();

	/**
	 * Tableau des feuilles de styles à importer pour l'affichage de la page en cours.
	 *
	 * Pour utiliser cette fonctionnalité, il faut placer la pseudo-variable {$head_css} dans un
	 * gabarit. Elle sera automatiquement remplacée par le code XHTML qui permet l'import des feuilles de
	 * styles déclarées.
	 *
	 * @var array
	 * @access private
	 */
	private $aStylesLinks = array();

	/**
	 * Tableau des fichiers de code Javascript à importer dans la page en cours de construction.
	 *
	 * Pour utiliser cette fonctionnalité, il faut placer la pseudo-variable {$head_js} dans
	 * un gabarit. Elle sera automatiquement remplacée par le code XHTML qui permet l'import des
	 * codes Javascript déclarés.
	 *
	 * @var array
	 * @access private
	 */
	private $aJavascriptsLinks = array();

	/**
	 * Constructeur
	 *
	 * @param string	$sDefaultRootDir	Dossier par défaut où se trouve le squelette Html.
	 * @param string	$sMainFileName		Le fichier d'entrée du squelette sans l'extension .thm.php (ou une autre si vous l'avez changée)
	 * @param string	$sSkinRootDir		Définit un dossier d'alternative au squelette dans lequel peuvent se trouver des surcharges des fichiers Html.
	 */
	public function __construct( $sDefaultRootDir, $sMainFileName, $sSkinRootDir = '' ) {

		if(substr($sDefaultRootDir, -1) != '/')
			$sDefaultRootDir .= '/';
			
		if( !is_dir($sDefaultRootDir) )
			throw new JbError( 'parser.root_dir.not_found' );
	
		// Si alternative donnée
		if($sSkinRootDir != '') {

			if(substr($sSkinRootDir, -1) != '/')
				$sSkinRootDir .= '/';
			
			$sMainFileAddress 	= $sSkinRootDir . $sMainFileName . JBL_PARSER_FILE_EXTENSION;
			
			if( !file_exists($sMainFileAddress) )
				$sMainFileAddress = '';
		}
		
		// Si le gabarit d'entrée n'est toujours pas défini
		if( empty($sMainFileAddress) )
			$sMainFileAddress = $sDefaultRootDir . $sMainFileName . JBL_PARSER_FILE_EXTENSION;
	
		if( !file_exists($sMainFileAddress) )
			throw new JbError( 'parser.index_file.not_found', array($sMainFileAddress) );

		$this->sRootDir			= $sDefaultRootDir;
		$this->sSkinDir			= $sSkinRootDir;
		$this->sMainFileName	= $sMainFileName;
	}

	/**
	 * Créer une ou plusieurs pseudos-variables.
	 *
	 * A chaque pseudo-variable est automatiquement associé un block conditionnel, du même nom
	 * que la pseudo-variable. Ce bloc est vrai ou faux selon que la variable contient ou non
	 * une valeur.
	 *
	 * @access public
	 * @param string|array	$mVarName	Nom de la pseudo-variable à créer ou tableau associatif des pseudos-variables.
	 * @param mixed			$sValue		Valeur à associer à $mName si $mName n'est pas utilisé en tant que tableau.
	 */
	public function assignVar( $mVarName, $sValue = null ) {

		if( is_array($mVarName) ) {
		
			foreach( $mVarName as $key => $value ) {
			
				$this->aVars[$key] = $value;
				$this->assignBlock($key, !empty($value));
			}
		}
		else {
		
			$this->aVars[$mVarName] = $sValue;
			$this->assignBlock($mVarName, (!is_null($sValue) && !empty($sValue)));
		}
	}

	/**
	 * Créer un ou plusieurs blocs conditionnels.
	 *
	 * @access public
	 * @param string|array	$mBlockName	Le nom du bloc ou tableau associatif de blocs.
	 * @param boolean		$bValue		Vrai/faux selon que le bloc doit être affiché/caché uniquement si $mBlockName est une chaîne.
	 */
	public function assignBlock( $mBlockName, $bValue = true ) {

		if( is_array($mBlockName) ) {
		
			foreach( $mBlockName as $key => $value ) {
			
				$this->aBlocks[$key]		= (bool) $value;
				$this->aBlocks['!'. $key]	= !$this->aBlocks[$key];
			}
		}
		else {
			
			$this->aBlocks[$mBlockName]			= (bool) $bValue;
			$this->aBlocks['!'. $mBlockName]	= !$this->aBlocks[$mBlockName];
		}
	}

	/**
	 * Créer une boucle.
	 *
	 * @access public
	 * @param string	$sLoopName		Le nom de la boucle.
	 */
	public function assignLoop( $sLoopName ) {

		if( empty($sLoopName) )		throw new JbError( 'parser.loop_name.empty' );
		if( !$this->isLoopAssigned($sLoopName) )	$this->aLoops[$sLoopName] = '';
	}

	/**
	 * Ajoute une entrée de pseudos-variables à une boucle.
	 *
	 * @access public
	 * @param string	$sLoopName	Le nom de la boucle.
	 * @param array		$aVars		Le tableau associatifs des pseudos-variables à ajouter pour l'entrée ajoutée à la boucle.
	 */
	public function assignLoopVars( $sLoopName, $aVars ) {

		if( !$this->isLoopAssigned($sLoopName) )	$this->assignLoop($sLoopName);
		if( sizeof($aVars) == 0 )					throw new JbError( 'parser.array_vars.empty', array( $sLoopName ) );

		$this->aLoops[$sLoopName][] = $aVars;
	}

	/**
	 * Indique si une pseudo-variable existe déjà.
	 *
	 * @access public
	 * @param	string	$sVarName	Le nom de la pseudo-variable à vérifier.
	 * @return boolean				True / false selon que la pseudo-variable existe ou non.
	 */
	public function isVarAssigned( $sVarName ) {
	
		return isset($this->aVars[$sVarName]);
	}

	/**
	 * Indique si un bloc existe déjà.
	 *
	 * @access public
	 * @param	string	$sBlockName	Le nom du bloc à vérifier.
	 * @return boolean				True/false selon que le bloc existe ou non.
	 */
	public function isBlockAssigned( $sBlockName ) {
	
		return isset($this->aBlocks[$sBlockName]);
	}

	/**
	 * Indique si une boucle existe déjà.
	 *
	 * @access public
	 * @param	string	$sLoopName	Le nom de la boucle à vérifier.
	 * @return boolean				True/false selon que la boucle existe ou non.
	 */
	public function isLoopAssigned( $sLoopName ) {
	
		return isset($this->aLoops[$sLoopName]);
	}
	
	/**
	 * Obtenir l'état d'un bloc.
	 *
	 * @param string	$sBlockName		Nom du bloc.
	 */
	public function getBlockStatus( $sBlockName ) {
	
		return ($this->isBlockAssigned($sBlockName) ? $this->aBlocks[$sBlockName] : false);
	}

	/**
	 * Création de la page complète.
	 *
	 * @access public
	 * @return string		La page entièrement construite.
	 */
	public function parse() {

		// Lancement de la construction de la page finale
		$this->sPage = $this->buildStructure( $this->sMainFileName );
		$this->parseLoops();
		$this->parseVars();

		return $this->sPage;
	}

	/**
	 * Construit la structure complète de la page Html en parcourant récursivement les appels de fichiers {>fichier}.
	 *
	 * @access private
	 * @param string	$sFile		Le nom sans extension du fichier à parcourir.
	 * @return	string				La chaîne de la page Html complète, prête à être parsée.
	 */
	private function buildStructure( $sFile ) {

		// Construction adresse fichier squelette
		$sFileName		= $sFile . JBL_PARSER_FILE_EXTENSION;
		$bSkinDirSet	= !empty($this->sSkinDir);
		$sFileAddress	= ($bSkinDirSet ? $this->sSkinDir : $this->sRootDir) . $sFileName;

		// Vérification que le fichier surchargé existe, sinon on se replace sur le fichier du squelette par défaut
		if( $bSkinDirSet && !file_exists($sFileAddress) ) {
		
			$sFileAddress = $this->sRootDir.$sFileName;
			$bSkinDirSet = false;
		}

		// Si fichier du squelette par défaut utilisé, on vérifie sa présence
		if( !$bSkinDirSet && !file_exists($sFileAddress) )
			throw new JbError( 'parser.include_file.not_found', array($sFileName) );

		// Lecture contenu du fichier
		$sFileContent = $this->parseBlocks(file_get_contents( $sFileAddress ));

		// Recherche des appels à d'autres fichiers (balise de la forme {>nom_fichier})
		$mRes = preg_match_all( "`{>([a-zA-Z0-9_!]+)}`iU", $sFileContent, $aResults );

		// Si appels trouvés
		if( $mRes !== false && $mRes > 0 ) {

			// Pour chaque inclusion
			foreach( $aResults[1] as $key => $sIncludeName ) {

				// Parcours du nouveau fichier
				$sNewContent = $this->buildStructure( $sIncludeName );

				// Remplacement de la balise par son contenu
				$sFileContent = str_replace( '{>'.$sIncludeName.'}', $sNewContent, $sFileContent );
			}
		}

		return $sFileContent;
	}

	/**
	 * Remplace les pseudos-variables par leur contenu.
	 *
	 * Par défaut, la méthode fonctionne avec les membres de l'objet. Si les paramètres d'entrée
	 * sont utilisés, la méthode fonctionnera uniquement avec eux.
	 *
	 * @access private
	 * @param string	$sText		Optionnel. Texte dans lequel remplacer les pseudos-variables données.
	 * @param array 	$aVars		Optionnel. Pseudos-variables à remplacer dans $sText.
	 * @return mixed				$sText parsé s'il est fourni. Aucun retour sinon.
	 */
	private function parseVars( $sText = null, $aVars = null ) {

		// Définition des données de travail, selon que les paramètres sont utilisés ou non
		$sWorkingText	= !is_null($sText) ? $sText : $this->sPage;
		$aWorkingVar	= !is_null($aVars) ? $aVars : $this->aVars;

		// Remplacement des pseudos-variables
		foreach( $aWorkingVar as $sPseudoVar => $sValue )
			$sWorkingText = str_replace( "{\$$sPseudoVar}", $sValue, $sWorkingText );

		// Réaffectations
		if( !is_null($sText) )
			return $sWorkingText;
		else
			$this->sPage = $sWorkingText;
	}

	/**
	 * Parcours des blocs conditionnels.
	 *
	 * @access public
	 * @param string	$sContent	Texte dans lequel faire la vérification des blocs conditionnels.
	 * @return string				Le texte nettoyé des blocs qui ne doivent pas être affichés.
	 */
	private function parseBlocks( $sContent ) {

		// Lecture des balises ouvrantes & fermantes de blocs conditionnels
		preg_match_all( "`\{\?([a-zA-Z0-9_!]+)\}`iU", $sContent, $aOpenedTags );
		preg_match_all( "`\{([a-zA-Z0-9_!]+)\?\}`iU", $sContent, $aClosedTags );
		
		// Vérification que chaque ouverture possède bien sa fermeture & réciproquement
		$aMissingClosedTag = array_diff($aOpenedTags[1], $aClosedTags[1]);
		$aMissingOpenedTag = array_diff($aClosedTags[1], $aOpenedTags[1]);

		// Lancement erreurs
		if( sizeof($aMissingClosedTag) !== 0 )
			throw new JbError(
				'parser.block.closedtag_missing',
				array(implode(', ', $aMissingClosedTag))
			);
			
		if( sizeof($aMissingOpenedTag) !== 0 )
			throw new JbError(
				'parser.block.openedtag_missing',
				array(implode(', ', $aMissingOpenedTag))
			);

		// Parcours de chaque bloc pour l'afficher ou le cacher
		$bOppositeState	= false;

		foreach( $aOpenedTags[1] as $sBlockName ) {

			$sBlockContent	= '';
			$sBlockAltern	= '';
			
			// Analyse du 1er caractère du bloc pour connaître la condition (true / not true)
			$bOppositeState = ($sBlockName[0] === '!');
				
			// Lecture contenu du bloc
			preg_match( 
				'`\{\?'. $sBlockName .'\}(.*)\{'. $sBlockName .'\?\}`isU',
				$sContent,
				$aMatches
			);
			
			if( !isset($aMatches[1]) )
				continue;
				
			// Si le bloc est inconnu, on génère une erreur
			if( !$this->isBlockAssigned($sBlockName) )
				throw new JbError( 'parser.block.undefined', array($sBlockName) );
				
			$sBlockContent = $aMatches[1];

			// Recherche alternative
			if( stripos($sBlockContent, '{or '. $sBlockName .'}') !== false ) {

				preg_match('`(.*)\{or '. $sBlockName .'\}(.*)`is', $sBlockContent, $aMatches);
				
				$sBlockContent = $aMatches[1];
				$sBlockAltern = $aMatches[2];
			}

			// Analyse du contenu final selon l'état du bloc et s'il doit être inversé
			$sReplacement = '';

			if( $this->aBlocks[$sBlockName] === true )
				$sReplacement = $sBlockContent;
				
			elseif( $sBlockAltern !== '' )
				$sReplacement = $sBlockAltern;

			// Remplacement
			$sContent = preg_replace(
				'`\{\?'. $sBlockName .'\}.*\{'. $sBlockName .'\?\}`isU',
				$sReplacement,
				$sContent
			);
		}

		return $sContent;
	}

	/**
	 * Parcours des boucles.
	 *
	 * @access private
	 */
	private function parseLoops() {

		// Lecture des balises ouvrantes & fermantes de blocs conditionnels
		preg_match_all( "`\{%([a-zA-Z0-9_]+)\}`iU", $this->sPage, $aOpenedTags );
		preg_match_all( "`\{([a-zA-Z0-9_]+)%\}`iU", $this->sPage, $aClosedTags );

		// Vérification que chaque ouverture possède bien sa fermeture & réciproquement
		$aMissingClosedTag = array_diff($aOpenedTags[1], $aClosedTags[1]);
		$aMissingOpenedTag = array_diff($aClosedTags[1], $aOpenedTags[1]);

		// Lancement erreurs
		if( sizeof($aMissingClosedTag) !== 0 )
			throw new JbError(
				'parser.loop.closedtag_missing',
				array(implode(', ', $aMissingClosedTag))
			);
			
		if( sizeof($aMissingOpenedTag) !== 0 )
			throw new JbError(
				'parser.loop.openedtag_missing',
				array(implode(', ', $aMissingOpenedTag))
			);

		// Parcours de chaque boucle
		foreach( $aOpenedTags[1] as $sLoopName ) {

			$sLoop = $sLoopIteration = '';

			// Si la boucle est inconnue, on génère une erreur
			if( !$this->isLoopAssigned($sLoopName) )
				throw new JbError( 'parser.loop.undefined', array($sLoopName) );

			preg_match( "`\{!".$sLoopName."%\}`isU", $this->sPage, $aMatches );
			$sAltern = isset($aMatches[0]) ? '!' : '';

			// Si il y a des données pour boucler...
			if (!empty($this->aLoops[$sLoopName])) {

				// Lecture contenu de la boucle
				preg_match( "`\{%".$sLoopName."\}(.*)\{".$sLoopName."%\}`isU", $this->sPage, $aMatches );
				$sLoopIteration = $aMatches[1];

				// Construction de l'ensemble des itérations de la boucle
				foreach( $this->aLoops[$sLoopName] as $aData ) {
				
					$sTemp = $this->parseVars($sLoopIteration, $aData)."\n";
					$sLoop.= $this->parseCondition($sTemp, $aData);
				}
			}
			else {
			
				preg_match( "`\{".$sLoopName."%\}(.*)\{!".$sLoopName."%\}`isU", $this->sPage, $aMatches );
				$sLoop = isset($aMatches[1]) ? $aMatches[1] : '';
			}

			// Remplacement de la boucle parsée dans la page
			$this->sPage = preg_replace( "`\{%".$sLoopName."\}.*\{".$sAltern.$sLoopName."%\}`isU", $sLoop, $this->sPage );
		}
	}

	/**
	 * Parcours des affichages conditionnels
	 *
	 * @access private
	 */
	private function parseCondition($sContent = '', $aData = array()) {

		if (empty($sContent)) $sContent &= $this->sPage;

		// Lecture des balises ouvrantes & fermantes de blocs conditionnels
		preg_match_all( "`\{#([a-zA-Z0-9_]+)\}`iU", $sContent, $aOpenedTags );
		preg_match_all( "`\{([a-zA-Z0-9_]+)#\}`iU", $sContent, $aClosedTags );

		// Vérification que chaque ouverture possède bien sa fermeture & réciproquement
		$aMissingClosedTag = array_diff($aOpenedTags[1], $aClosedTags[1]);
		$aMissingOpenedTag = array_diff($aClosedTags[1], $aOpenedTags[1]);

		// Lancement erreurs
		if( sizeof($aMissingClosedTag) !== 0 )	throw new JbError( 'parser.loop.closedtag_missing', array(implode(', ', $aMissingClosedTag)) );
		if( sizeof($aMissingOpenedTag) !== 0 )	throw new JbError( 'parser.loop.openedtag_missing', array(implode(', ', $aMissingOpenedTag)) );

		foreach( $aOpenedTags[1] as $sCondName ) {

			$sCondContent = '';

			preg_match( "`\{!".$sCondName."#\}`isU", $sContent, $aMatches );
			$sAltern = isset($aMatches[0]) ? '!' : '';

			// Lecture contenu de la boucle
			preg_match( "`\{#".$sCondName."\}(.*)\{".$sCondName."#\}`isU", $sContent, $aMatches );
			$sLoopIteration = $aMatches[1];

			if ($aData[$sCondName] === true)
				preg_match( "`\{#".$sCondName."\}(.*)\{".$sCondName."#\}`isU", $sContent, $aMatches );
			else
				preg_match( "`\{".$sCondName."#\}(.*)\{!".$sCondName."#\}`isU", $sContent, $aMatches );

			$sCond = isset($aMatches[1]) ? $aMatches[1] : '';

			// Remplacement de la boucle parsée dans la page
			$sContent = preg_replace( "`\{#".$sCondName."\}.*\{".$sAltern.$sCondName."#\}`isU", $sCond, $sContent );
		}

		return $sContent;
	}
}