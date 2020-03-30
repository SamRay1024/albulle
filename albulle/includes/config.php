<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

/**
 * Fichier de configuration d'AlBulle à partir duquel vous pouvez paramétrer
 * vos galeries d'images.
 * 
 * Les éléments de configuration sont classés par thème. N'hésitez pas à lire
 * les commentaires associés à chaque paramètre pour être sûr de ne pas fausser
 * la configuration.
 */


// ================
// DOSSIERS ET FICHIER
//
// /!\ -> Chaque paramètre étant un dossier doit comporter un '/' à la fin ! 
//
define( 'JB_AL_DOSSIER_PHOTOS',			'photos/' );					// Le nom du dossier qui contiendra vos albums photos.

define( 'JB_AL_DOSSIER_THEMES',			'themes/' );					// Dossier où se trouvent les thèmes.
define( 'JB_AL_DOSSIER_THEME_ACTIF',	'albulle/' );					// Dans le dossier des thèmes, nom du dossier du thème à utiliser.
define(	'JB_AL_FICHIER_THEME',			'html.php' );					// Nom du fichier template, dans le dossier du thème choisi.

define( 'JB_AL_FICHIER_ACCUEIL',		'texte_accueil.html' );			// Chemin d'accès au fichier qui contiendra le texte d'accueil.


// ================
// DONNEES A AFFICHER
//

// Copyright
define( 'JB_AL_AFFICHER_VERSION',		true );							// Afficher le numéro de version dans le copyright.

// Arborescence
define( 'JB_AL_AFFICHER_NB_PHOTOS',		true );							// Afficher dans l'arborescence des albums le nombre de photos présentes dans chaque dossier.

// Vignettes
define( 'JB_AL_AFFICHER_NOMS',			false );						// Afficher le nom de chaque photo dans les vignettes.
define( 'JB_AL_AFFICHIER_POIDS',		true );							// Afficher la taille de chaque photo.
define( 'JB_AL_AFFICHER_DIMENSIONS',	true );							// Afficher les dimensions.


// ================
// MODES D'AFFICHAGE DES IMAGES
//

// Ouvrir les images dans une nouvelle fenêtre sans Javascript. La nouvelle fenêtre ne sera
// pas aux dimensions de l'images et les barres d'outils seront visibles. L'utilisation de cette
// option invalide la compatibilité XHTML Strict à cause du target="_blank".
define( 'JB_AL_OUVERTURE_BLANK',		false );

// Ouvrir les images dans une nouvelle fenêtre par Javascript.
// Cela permet à l'inverse du paramètre précédent de disposer d'une fenêtre sans barres d'outils
// et aux dimensions de l'images. Vous conservez la validité XHTML, mais vous prenez le risque de
// rendre AlBulle inutilisable pour les personnes qui auraient interdit l'exécution de code Javascript.
define( 'JB_AL_OUVERTURE_JAVASCRIPT',	false );

// /!\
// L'ouverture des images par popup Javascript est prioritaire sur l'ouverture sans Javascript.
// C'est-à-dire que si vous mettez JB_AL_OUVERTURE_JAVASCRIPT à 'true', quelque soit la valeur de ce
// que vous metterez pour JB_AL_OUVERTURE_BLANK, elle sera ignorée.
// /!\


// ================
// PARAMETRAGE DES VIGNETTES
//
define( 'JB_AL_VIGNETTES_LARGEUR',		150 );							// Largeur maximum des miniatures, en pixel (ne doit pas valoir 0).
define( 'JB_AL_VIGNETTES_HAUTEUR',		113 );							// Hauteur maximum des miniatures, en pixel (idem).
define( 'JB_AL_VIGNETTES_PAR_PAGE',		10 );							// Nombre de vignettes à afficher par page.

// /!\
// Si vous changez la largeur ou la hauteur, il faut supprimer toutes les miniatures existantes pour les regénérer
// avec les nouvelles dimensions.
// /!>

// ================
// PARAMETRAGE DU PANIER
//
define( 'JB_AL_PANIER_CAPACITE_MAX',	10 );							// Nombre maximum de fichiers que peut contenir le panier (0 = désactiver la limitation).


// ================
// SPECIAL
//

// Si vous souhaitez intégrer AlBulle dans votre site Internet, mettez le paramètre suivant
// à 'true'. Cela aura pour effet d'enlever les entêtes HTML lors de la génération des pages
// d'AlBulle pour qu'il n'y ait pas de redondance avec votre propre site.
define( 'JB_AL_INTEGRATION_SITE',		false );

// Utiliser AlBulle comme centre de téléchargement.
define( 'JB_AL_MODE_CENTRE',			false );						// Mettez ce paramètre à 'true' pour basculer de mode.
define( 'JB_AL_DOSSIER_CENTRE',			'centre/' );					// Dossier dans lequel se trouvent les fichiers disponibles au téléchargement.
define( 'JB_AL_EXTENSION_FICHIERS',		'.zip' );						// Extension des fichiers à télécharger.

// Vous pouvez définir une url vers votre site principal pour le cas où AlBulle
// ne le serait pas. Le lien s'affichera à gauche de celui qui donne sur la page d'accueil d'AlBulle.
define( 'JB_AL_HOME_HREF',				'' );							// La page de votre site (Laissez vide pour ne pas utiliser cette fonctionnalité).
define( 'JB_AL_HOME_TEXTE',				'' );							// Le texte du lien (Non affiché si paramètre précédent vide).

// ================
// PARAMETRAGE DES CREATIONS DE DOSSIERS ET FICHIERS (pour les dossiers de miniatures)
//
define( 'JB_AL_CHMOD_DOSSIERS',			0755 );
define( 'JB_AL_CHMOD_FICHIERS',			0644 );

/* EOC (End Of Configuration) ;-) */
?>