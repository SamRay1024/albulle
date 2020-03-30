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
 */

if( !defined( '_JB_INCLUDE_AUTH' ) ) {
	header( 'Content-type: text/html; charset=utf-8' );
	exit( 'Vous n\'êtes pas autorisé à afficher cette page.' );
}

// ================
// TITRE ET SOUS TITRE
//
if( !defined('JB_AL_AFFICHER_ENTETE') )
define( 'JB_AL_AFFICHER_ENTETE',		true );							// Affiche / cache l'entête de la page qui contient le titre et le sous-titre.
define( 'JB_AL_TITRE_GALERIE',			'Ma galerie photos' );
define( 'JB_AL_SOUS_TITRE_GALERIE',		'...contient sûrement des images à découvrir !' );


// ================
// DOSSIERS ET FICHIER
//
// /!\ -> Chaque paramètre étant un dossier doit comporter un '/' à la fin !
//
define( 'JB_AL_DOSSIER_PHOTOS',			'photos/' );					// Le nom du dossier qui contiendra vos albums photos.
define( 'JB_AL_DOSSIER_MINIATURES',		'miniatures/' );				// Le nom du dossier qui contiendra les miniatures générées.

define( 'JB_AL_DOSSIER_THEMES',			'themes/' );					// Dossier où se trouvent les thèmes.

if( !defined('JB_AL_DOSSIER_THEME_ACTIF') )
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
define( 'JB_AL_AFFICHER_NB_SI_VIDE',	false );						// Affiche le nombre de photos même quand il n'y en a pas.
define( 'JB_AL_DEROULER_TOUT',          false );						// Ne déroule que le dossier demandé. Mettez à 'true' pour que tous les dossiers soient déroulés.

// Vignettes
define( 'JB_AL_AFFICHER_NOMS',			true );							// Afficher le nom de chaque photo dans les vignettes.
define( 'JB_AL_REMPLACER_TIRETS_BAS',	true );							// Si true, les '_' présents dans les noms seront remplacés par des espaces.
define( 'JB_AL_AFFICHER_EXTENSION',		false );                        // Affiche ou non l'extension du fichier.

define( 'JB_AL_AFFICHER_POIDS',			true );							// Afficher la taille de chaque photo.
define( 'JB_AL_AFFICHER_DIMENSIONS',	true );							// Afficher les dimensions.

// Rappel des sous-dossiers
define( 'JB_AL_RAPPELER_SOUS_DOSSIERS',	true );							// Ceci rappellera les sous-dossiers du dossier courant après les vignettes.
define( 'JB_AL_RAPPELER_QUE_SI_VIDE',	false );						// Permet de n'afficher le rappel des sous-dossiers que si le dossier courant n'a pas de photos.

// Tri des dossiers et fichiers
define( 'JB_AL_FILTRE_PREFIXES_ACTIF',	true );	                       	// Si true, active le filtrage sur les préfixes de tous les noms (dossiers et fichiers).
define( 'JB_AL_PREFIXES_SEPARATEUR',	'_' );							// Séparateur à utiliser pour préfixer vos noms si l'option précédente est active.

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
// Lors de l'affichage des dossiers et des fichiers (si vous avez demandé l'affichage des noms des photos),
// tout ce qui se trouve devant le séparateur ('_' par défaut) ne sera pas affiché à l'écran (séparateur
// compris).
// /!\

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
// rendre Albulle inutilisable pour les personnes qui auraient interdit l'exécution de code Javascript.
define( 'JB_AL_OUVERTURE_JAVASCRIPT',	true );
define( 'JB_AL_OUVERTURE_LIGHTBOX',		true );		// Uniquement si l'ouverture javascript est activée : essayer-ce mode, c'est l'adopter !

// Vous pouvez redimensionner l'image qui s'affichera dans la popup
// Mettez les deux valeurs à 0 pour prendre la taille réelle.
define( 'JB_AL_POPUP_LARGEUR',			0 );							// Largeur max de l'image
define( 'JB_AL_POPUP_HAUTEUR',			0 );							// Hauteur max de l'image

// /!\
// L'ouverture des images par popup Javascript est prioritaire sur l'ouverture sans Javascript.
// C'est-à-dire que si vous mettez JB_AL_OUVERTURE_JAVASCRIPT à 'true', quelque soit la valeur de ce
// que vous metterez pour JB_AL_OUVERTURE_BLANK, elle sera ignorée.
// /!\


// ================
// PARAMETRAGE DES VIGNETTES
//
define( 'JB_AL_VIGNETTES_PAR_PAGE',		20 );							// Nombre de vignettes à afficher par page.

// Vignettes du mode gallerie
define( 'JB_AL_VIGNETTES_LARGEUR',		150 );							// Largeur maximum des miniatures, en pixel (ne doit pas valoir 0).
define( 'JB_AL_VIGNETTES_HAUTEUR',		113 );							// Hauteur maximum des miniatures, en pixel (idem).

// Vignettes du mode diaporama
define( 'JB_AL_VIGNETTES_DP_LARGEUR', 	78 );							// Idem paramètres précédents
define( 'JB_AL_VIGNETTES_DP_HAUTEUR',	59 );							// Idem


// /!\
// Si vous changez la largeur ou la hauteur, il faut supprimer toutes les miniatures existantes pour les regénérer
// avec les nouvelles dimensions.
// /!>

// ================
// PARAMETRAGE DU PANIER
//
define( 'JB_AL_PANIER_CAPACITE_MAX',	0 );							// Nombre maximum de fichiers que peut contenir le panier (0 = désactiver la limitation).
define( 'JB_AL_PANIER_POIDS_MAX',		20 );							// Poids maximum que peut faire un panier en Mo. 0 = poids infini.
define( 'JB_AL_PANIER_NOM_ARCHIVE',		'Photos' );						// Le nom que prendra les archives téléchargées.
define( 'JB_AL_PANIER_NO_READFILE', 	false);         				// Si les zip ( >= 10MB) sont corrompus, passez cette valeur à true, pour éviter d'utiliser "readfile".
																		// Attention, en mettant true, le script consommera plus de temps d'exécution (souvent limité à 30sec).


// ================
// SPECIAL
//

// Si vous souhaitez intégrer Albulle dans votre site Internet, mettez le paramètre suivant
// à 'true'. Cela aura pour effet d'enlever les entêtes HTML lors de la génération des pages
// d'Albulle pour qu'il n'y ait pas de redondance avec votre propre site.
if( !defined('JB_AL_INTEGRATION_SITE') )
define( 'JB_AL_INTEGRATION_SITE',		false );

// Utiliser Albulle comme centre de téléchargement.
define( 'JB_AL_MODE_CENTRE',			false );						// Mettez ce paramètre à 'true' pour basculer de mode.
define( 'JB_AL_DOSSIER_CENTRE',			'centre/' );					// Dossier dans lequel se trouvent les fichiers disponibles au téléchargement.
define( 'JB_AL_EXTENSION_FICHIERS',		'.zip' );						// Extension des fichiers à télécharger.

// Vous pouvez définir une url vers votre site principal pour le cas où Albulle
// ne le serait pas. Le lien s'affichera à gauche de celui qui donne sur la page d'accueil d'Albulle.
define( 'JB_AL_HOME_HREF',				'' );							// La page de votre site (Laissez vide pour ne pas utiliser cette fonctionnalité).
define( 'JB_AL_HOME_TEXTE',				'' );							// Le texte du lien (Non affiché si paramètre précédent vide).

// ================
// PARAMETRAGE DES CREATIONS DE FICHIERS (pour les miniatures)
//
define( 'JB_AL_CHMOD_FICHIERS',			0644 );

/* EOC (End Of Configuration) ;-) */
?>
