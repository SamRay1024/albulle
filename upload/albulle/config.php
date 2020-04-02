<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

/**
 * Fichier de configuration d'Albulle à partir duquel vous pouvez paramétrer
 * vos galeries d'images.
 *
 * Les éléments de configuration sont classés par thème. N'hésitez pas à lire
 * les commentaires associés à chaque paramètre pour être sûr de ne pas fausser
 * la configuration.
 *
 * Tous les éléments (sauf quelques uns particuliers) sont surchargables. Il
 * est alors possible de définir des configurations relatives aux thèmes que vous possédez.
 * Pour ce faire, vous devez créer un fichier du nom de 'config_thm.php' à la racine
 * du thème pour lequel vous souhaitez avoir une configuration différente. Libre à
 * vous d'y placer les paramètres que vous souhaitez surcharger !
 */

if( !defined( '_JB_INCLUDE_AUTH' ) ) {

	header( 'Content-type: text/html; charset=utf-8' );
	exit( 'Vous n\'êtes pas autorisé à afficher cette page.' );
}

/**
 * Définir une contante.
 *
 * La constante est définie si aucune définition n'a été faite auparavant.
 *
 * @param string	$sConstante	Nom de la constante.
 * @param mixed		$mValeur	Valeur de la constante.
 */
function definir( $sConstante, $mValeur ) {

	defined( $sConstante ) or define( $sConstante, $mValeur );
}

// {{{ FERMETURE DES GALERIES

/**
 * Fermer Albulle.
 */
definir( 'JB_AL_FERMER', false );
/**
 * Message de fermeture.
 */
definir(
	'JB_AL_MSG_FERMETURE',	
	'Les galeries sont temporairement fermées. '.
	'Elle seront réouvertes dès que possible. <br /><br />'.
	'Merci de votre patience et de votre compréhension.'
);

// }}}
// {{{ DOSSIERS ET FICHIER

// /!\ -> Chaque paramètre étant un dossier doit comporter un '/' à la fin !

/**
 * URL des galeries.
 */
definir( 'JB_AL_BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/' );

/**
 * Dossier des thèmes.
 */
definir( 'JB_AL_DOSSIER_THEMES', 'themes/' );
/**
 * Dossier du thème à utiliser.
 */
definir( 'JB_AL_DOSSIER_THEME_ACTIF', 'albulle/' );

// Inclusion d'un éventuel fichier de configuration relatif au thème courant
if( file_exists(JB_AL_ROOT.JB_AL_DOSSIER_THEMES.JB_AL_DOSSIER_THEME_ACTIF.'config_thm.php') )
	require_once( JB_AL_DOSSIER_THEMES.JB_AL_DOSSIER_THEME_ACTIF.'config_thm.php' );

/**
 * Dossier des données.
 */
definir( 'JB_AL_DOSSIER_DATA', 'data/' );
/**
 * Dossier des photos.
 */
definir( 'JB_AL_DOSSIER_PHOTOS', 'photos/' );
/**
 * Dossier des miniatures.
 */
definir( 'JB_AL_DOSSIER_MINIATURES', 'miniatures/' );
/**
 * Dossier des photos en haute définition.
 */
definir( 'JB_AL_DOSSIER_ORIGINALES', 'originales/' );
/**
 * Fichier du texte de la page d'accueil.
 */
definir( 'JB_AL_FICHIER_ACCUEIL', 'texte_accueil.html' );
/**
 * Nom générique des fichiers optionnels des dossiers.
 */
definir( 'JB_AL_FICHIER_TEXTE_DOSSIER',	'texte.html');
/**
 * Drapeau à monter si le système de fichiers qui héberge Albulle est en UTF-8.
 */
definir( 'JB_AL_FICHIERS_UTF8', true );

// }}}
// {{{ TITRE ET SOUS TITRE

/**
 * Afficher l'entête de page.
 */
definir( 'JB_AL_AFFICHER_ENTETE', true );
/**
 * Titre des galeries.
 */
definir( 'JB_AL_TITRE_GALERIE', 'Ma galerie photos' );
/**
 * Sous-titre des galeries.
 */
definir( 'JB_AL_SOUS_TITRE_GALERIE', '...contient sûrement des images à découvrir !' );

// }}}
// {{{ DONNEES A AFFICHER

/**
 * Afficher le numéro de version.
 */
definir( 'JB_AL_AFFICHER_VERSION', true );
/**
 * Afficher le nombre de photos dans chaque dossier.
 */
definir( 'JB_AL_AFFICHER_NB_PHOTOS', true );
/**
 * Afficher le nombre de photos si le dossier est vide.
 */
definir( 'JB_AL_AFFICHER_NB_SI_VIDE', false );
/**
 * Dérouler tous les dossiers de l'arborescence plutôt que seulement le dossier courant.
 */
definir( 'JB_AL_DEROULER_TOUT', false );
/**
 * Afficher le nom de chaque photo dans les vignettes.
 */
definir( 'JB_AL_AFFICHER_NOMS', true );
/**
 * Remplacer les tirets bas par des espaces dans les noms des fichiers.
 */
definir( 'JB_AL_REMPLACER_TIRETS_BAS', true );
/**
 * Afficher l'extention des fichiers.
 */
definir( 'JB_AL_AFFICHER_EXTENSION', false );
/**
 * Afficher le poids des photos.
 */
definir( 'JB_AL_AFFICHER_POIDS', true );
/**
 * Afficher les dimensions des photos.
 */
definir( 'JB_AL_AFFICHER_DIMENSIONS', true );
/**
 * Afficher le rappel des sous-dossiers du dossier courant après les vignettes.
 */
definir( 'JB_AL_RAPPELER_SOUS_DOSSIERS', true );
/**
 * Afficher le rappel des sous-dossiers uniquement si le dossier courant est vide.
 */
definir( 'JB_AL_RAPPELER_QUE_SI_VIDE', false );
/**
 * Afficher le texte par défaut pour les dossiers vides.
 */
definir( 'JB_AL_AFFICHER_TXT_VIDE', false );
/**
 * Activer le filtrage des préfixes des noms des fichiers.
 */
definir( 'JB_AL_FILTRE_PREFIXES_ACTIF',	true );
/**
 * Délimiteur des préfixes.
 */
definir( 'JB_AL_PREFIXES_SEPARATEUR', '_' );
/**
 * Trier les photos dans l'ordre des dates de prises de vue.
 * @since 1.2
 */
definir( 'JB_AL_TRI_EXIF', true );
/**
 * Inverser le tri par date de prise de vue.
 * @since 1.2
 */
definir( 'JB_AL_TRI_EXIF_INV', false );
/**
 * Format de l'affichage des dates.
 * @since 1.2
 * @link http://fr.php.net/manual/fr/function.date.php
 */
definir( 'JB_AL_DATE_FORMAT', 'd/m/Y H\hi' );	

// /!\
// Mode d'emploi de l'utilisation des préfixes :
//
// Vous pouvez avoir besoin d'ordonner vos dossiers et photos dans un autre ordre que celui
// alphabétique. Si tel est le cas, activez le filtrage des préfixes pour pouvoir utiliser
// des préfixes sur vos noms. Ainsi vous pourrez redéfinir un classement qui vous est propre
// tout en gardant un affichage "propre".
//
// Pour utiliser votre classement vous devrez nommer vos dossiers et fichiers de la façon suivante :
//
//      01;;Mon_image.jpg
//      02;;Mon_autre_image.jpg
//      ...
//      (De la même façon pour des dossiers)
//
// De manière générale le nommage doit être de la forme :
//
//      [indice][séparateur][nom de l'image/nom du dossier].[extension si vous nommez un fichier]
//
// Lors de l'affichage des dossiers et des fichiers (si vous avez demandé l'affichage des noms des
// photos), tout ce qui se trouve devant le séparateur ('_' par défaut) ne sera pas affiché à
// l'écran (séparateur compris).
// /!\

// }}}
// {{{ PARAMETRES DU MODE GALERIE

/**
 * Ouvrir les images dans une nouvelle fenêtre.
 * /!\ : Cette option utilise l'attribut target="_blank" et invalide la compatibilité XHTML Strict.
 */
definir( 'JB_AL_OUVERTURE_BLK', false );
/**
 * Ouvrir des images dans une popup Javascript.
 * Prioritaire sur JB_AL_OUVERTURE_BLK.
 */
definir( 'JB_AL_OUVERTURE_JS', true );
/**
 * Ouvrir les images dans le cadre LightBox.
 * /!\ : JB_AL_OUVERTURE_JS doit valoir 'true'.
 */
definir( 'JB_AL_OUVERTURE_LBX', true );

// }}}
// {{{ PARAMETRES DU MODE DIAPORAMA

/**
 * @see JB_AL_OUVERTURE_BLK
 */
definir( 'JB_AL_OUVERTURE_BLK_DIAPO', false );
/**
 * @see JB_AL_OUVERTURE_JS
 */
definir( 'JB_AL_OUVERTURE_JS_DIAPO', false );
/**
 * @see JB_AL_OUVERTURE_BLK
 */
definir( 'JB_AL_OUVERTURE_LBX_DIAPO', false );

// }}}
// {{{ MODES D'AFFICHAGE DES IMAGES

/**
 * Utiliser le mode diaporama comme affichage par défaut.
 */
definir( 'JB_AL_MODE_DIAPO_DEFAUT', false );
/**
 * Largeur des popups Javascript.
 * /!\ : uniquement si JB_AL_OUVERTURE_JS ou JB_AL_OUVERTURE_JS_DIAPO valent 'true'.
 */
definir( 'JB_AL_POPUP_LARGEUR', 0 );
/**
 * Hauteur des popups Javascript.
 * /!\ : uniquement si JB_AL_OUVERTURE_JS ou JB_AL_OUVERTURE_JS_DIAPO valent 'true'.
 */
definir( 'JB_AL_POPUP_HAUTEUR', 0 );

// }}}
// {{{ PARAMETES DES VIGNETTES

/**
 * Nombre de vignettes par page.
 */
definir( 'JB_AL_VIGNETTES_PAR_PAGE', 20 );
/**
 * Largeur maximum des miniatures du mode galerie, en pixels.
 * Ne peut valoir 0.
 */
definir( 'JB_AL_VIGNETTES_LARGEUR', 150 );
/**
 * Hauteur maximum des miniatures du mode galerie, en pixels.
 * Ne peut valoir 0.
 */
definir( 'JB_AL_VIGNETTES_HAUTEUR', 113 );
/**
 * Largeur maximum des miniatures du mode diaporama, en pixels.
 * Ne peut valoir 0.
 */
definir( 'JB_AL_VIGNETTES_DP_LARGEUR', 78 );
/**
 * Hauteur maximum des miniatures du mode diaporama, en pixels.
 * Ne peut valoir 0.
 */
definir( 'JB_AL_VIGNETTES_DP_HAUTEUR', 59 );

// /!\
// N.b. : si vous changez les dimensions des vignettes, vous devrez très certainement 
// 1. faire des adaptations dans les CSS (fichier structure.css),
// 2. supprimer les miniatures existantes afin de les regénérer avec les nouvelles dimensions.
// /!\

/**
 * Qualité des vignettes.
 *
 * S'applique uniquement aux images JPEG sur une échelle de 0 à 100.
 * 0 : mauvaise qualité = petit fichier - 100 : meilleure qualité = gros fichier
 */
definir( 'JB_AL_VIGNETTES_QUALITE', 80 );

// }}}
// {{{ PARAMETRES DU PANIER

/**
 * Activer le panier.
 */
definir( 'JB_AL_PANIER_ACTIF', true );
/**
 * Nombre maximum de photos que peut contenir le panier.
 * (0 = désactiver la limitation)
 */
definir( 'JB_AL_PANIER_CAPACITE_MAX', 0 );
/**
 * Poids maximum que peut faire un panier en Mo.
 * (0 = poids infini)
 */
definir( 'JB_AL_PANIER_POIDS_MAX', 20 );
/**
 * Nom à donner aux archives générées pour les téléchargements.
 */
definir( 'JB_AL_PANIER_NOM_ARCHIVE', 'Photos' );
/**
 * L'archive générée reprend la structure des dossiers des photos sélectionnées.
 * @since 1.2
 */
definir( 'JB_AL_PANIER_ARCHIVE_STRUCTUREE', true );
/**
 * Eviter l'utilisation de la fonction "readfile".
 *
 * Si les zip ( >= 10MB) sont corrompus, passez cette valeur à true.
 * /!\ Le script consommera plus de temps d'exécution (souvent limité à 30sec).
 */
definir( 'JB_AL_PANIER_NO_READFILE', false );

// }}}
// {{{ SPECIAL

/**
 * Albulle est intégré dans un site Internet.
 */
definir( 'JB_AL_INTEGRATION_SITE', false );
/**
 * Activer l'encodage en ISO-8859-1 plutôt qu'UTF-8.
 * Consomme un peu plus de temps en raison de l'exécution de la convertion.
 */
definir( 'JB_AL_SORTIE_ISO', false );
/**
 * Conserver les paramètres des URL hôtes.
 * Valable uniquement en mode intégration.
 */
definir( 'JB_AL_CONSERVER_URL_HOTE', false );
/**
 * Utiliser Albulle comme centre de téléchargement.
 *
 * Dans ce mode, les miniatures renvoient vers des fichiers plutôt que vers des images.
 */
definir( 'JB_AL_MODE_CENTRE', false );
/**
 * Dossier des fichiers proposés au téléchargement (relatif à JB_AL_DOSSIER_DATA).
 */
definir( 'JB_AL_DOSSIER_CENTRE', 'centre/' );
/**
 * Extention des fichiers à télécharger.
 */
definir( 'JB_AL_EXTENSION_FICHIERS', '.zip' );
/**
 * URL du lien de retour vers votre site principal.
 *
 * Permet d'afficher un bouton de retour vers votre site depuis la barre de navigation.
 * Laissez vide pour ne pas utiliser cette fonctionnalité.
 */
definir( 'JB_AL_HOME_HREF', '' );
/**
 * Texte du lien de retour vers votre site.
 *
 * Laisser vide pour ne pas utiliser cette fonctionnalité.
 */
definir( 'JB_AL_HOME_TEXTE', '' );

// }}}
// {{{ PARAMETRES DES CREATIONS DE FICHIERS (pour les miniatures)

/**
 * Chmod par défaut.
 */
definir( 'JB_AL_CHMOD_FICHIERS', 0644 );

// }}}
// EOC (End Of Configuration)