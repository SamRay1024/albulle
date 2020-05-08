<?php

/**
 * Contient le nécessaire au chargement de JbLib.
 *
 * @author SamRay1024
 * @copyright Bubulles Créations
 * @link http://jebulle.net
 * @since 25/01/2010
 * @version 03/05/2020
 * @package JbLib
 */
 
/**
 * Alias de DIRECTORY_SEPARATOR.
 */
define('DIR_SEP', DIRECTORY_SEPARATOR);
 
/**
 * Adresse de la racine de JbLib.
 */
defined('JBL_ROOT') or define('JBL_ROOT', dirname(__FILE__) . DIR_SEP);

/**
 * Chargement du fichier de configuration principal.
 */
require_once(JBL_ROOT .'conf/jblib.conf.php');

/**
 * Initialisations.
 */
 
// Désactivation de la fonction de magic_quotes
if (PHP_VERSION < 7)
	set_magic_quotes_runtime(0);

// Désactivation transfert de l'id de session par les URL
ini_set('session.use_trans_sid', '0');

// Définition du fuseau horaire
date_default_timezone_set(JBL_TIMEZONE);

// Si pas en production
if( !JBL_PRODUCTION ) {

	// Rapport des erreurs
	error_reporting( E_ALL | E_STRICT | E_NOTICE );
}

/**
 * Chargement des modules installés.
 */
$_JBL_LOADED_EXTENSIONS = glob(JBL_ROOT .'classes/*.class.php');

is_array($_JBL_LOADED_EXTENSIONS) or $_JBL_LOADED_EXTENSIONS = array();

foreach( $_JBL_LOADED_EXTENSIONS as $sExtension )
	require_once($sExtension);