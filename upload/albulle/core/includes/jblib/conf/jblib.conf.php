<?php

/**
 * Contient la configuration de JbLib.
 *
 * @package JbLib
 */
 
/**
 * Adresse des fichiers de langues.
 */
defined('JBL_ROOT_LANG')	or define('JBL_ROOT_LANG', JBL_ROOT .'locales'. DIR_SEP);
/**
 * Langue à utiliser.
 */
defined('JBL_LOCALE')		or define('JBL_LOCALE', 'fr_FR');
/**
 * Fuseau horaire.
 */
defined('JBL_TIMEZONE')		or define('JBL_TIMEZONE', 'Europe/Paris');
/**
 * UTF-8 actif.
 */
defined('JBL_UTF8')			or define('JBL_UTF8', true);
/**
 * Activer le mode production.
 */
define('JBL_PRODUCTION', false);