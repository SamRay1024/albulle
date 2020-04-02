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

// Fermer vos galeries
if( !defined('JB_AL_FERMER') )					define( 'JB_AL_FERMER',					false );
if( !defined('JB_AL_MSG_FERMETURE') )			define( 'JB_AL_MSG_FERMETURE',			'Les galeries sont temporairement fermées. Elle seront réouvertes dès que possible. <br /><br />Merci de votre patience et de votre compréhension.' );


// ================
// DOSSIERS ET FICHIER
//
// /!\ -> Chaque paramètre étant un dossier doit comporter un '/' à la fin !
//

if( !defined('JB_AL_BASE_URL') )				define( 'JB_AL_BASE_URL', 				'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/' );

// On commence par définir le thème pour pouvoir aller chercher le fichier de configuration du thème, s'il existe,
// ce qui permet la surcharge de la configuration par défaut
define( 'JB_AL_DOSSIER_THEMES',															'themes/' );			// Dossier où se trouvent les thèmes.
if( !defined('JB_AL_DOSSIER_THEME_ACTIF') )		define( 'JB_AL_DOSSIER_THEME_ACTIF',	'albulle/' );			// Dans le dossier des thèmes, nom du dossier du thème à utiliser.

// Inclusion d'un éventuel fichier de configuration relatif au thème courant
if( file_exists(JB_AL_ROOT.JB_AL_DOSSIER_THEMES.JB_AL_DOSSIER_THEME_ACTIF.'config_thm.php') )
	require_once( JB_AL_DOSSIER_THEMES.JB_AL_DOSSIER_THEME_ACTIF.'config_thm.php' );

if( !defined('JB_AL_DOSSIER_DATA') )			define( 'JB_AL_DOSSIER_DATA',			'data/' );				// Le dossier racine des données.
if( !defined('JB_AL_DOSSIER_PHOTOS') )			define( 'JB_AL_DOSSIER_PHOTOS',			'photos/' );			// Le nom du dossier qui contiendra vos albums photos publiés (dans le dossier des données).
if( !defined('JB_AL_DOSSIER_MINIATURES') )		define( 'JB_AL_DOSSIER_MINIATURES',		'miniatures/' );		// Le nom du dossier qui contiendra les miniatures générées (dans le dossier des données).
if( !defined('JB_AL_DOSSIER_ORIGINALES') )		define( 'JB_AL_DOSSIER_ORIGINALES',		'originales/' );		// Ce dossier est à utilisé si vous souhaitez mettre en ligne des photos légères mais que soient téléchargées les photos en qualité originale (dans le dossier des données).

if( !defined('JB_AL_FICHIER_ACCUEIL') )			define( 'JB_AL_FICHIER_ACCUEIL',		'texte_accueil.html' );	// Chemin d'accès au fichier qui contiendra le texte d'accueil.
if( !defined('JB_AL_FICHIER_DOSSIER_VIDE') )	define( 'JB_AL_FICHIER_DOSSIER_VIDE',	'texte.html');			// Nom des fichiers textes qui peuvent être placés dans un dossier de photos

// Indiquer si le système de fichier qui héberge votre copie d'Albulle est en Utf-8
if( !defined('JB_AL_FICHIERS_UTF8') )			define( 'JB_AL_FICHIERS_UTF8',			true );

// ================
// TITRE ET SOUS TITRE
//
if( !defined('JB_AL_AFFICHER_ENTETE') ) 		define( 'JB_AL_AFFICHER_ENTETE',		true );		// Affiche / cache l'entête de la page qui contient le titre et le sous-titre.
if( !defined('JB_AL_TITRE_GALERIE') )			define( 'JB_AL_TITRE_GALERIE',			'Ma galerie photos' );
if( !defined('JB_AL_SOUS_TITRE_GALERIE') )		define( 'JB_AL_SOUS_TITRE_GALERIE',		'...contient sûrement des images à découvrir !' );


// ================
// DONNEES A AFFICHER
//

// Copyright
define( 'JB_AL_AFFICHER_VERSION', true );							// Afficher le numéro de version dans le copyright.

// Arborescence
if( !defined('JB_AL_AFFICHER_NB_PHOTOS') )		define( 'JB_AL_AFFICHER_NB_PHOTOS',		true );		// Afficher dans l'arborescence des albums le nombre de photos présentes dans chaque dossier.
if( !defined('JB_AL_AFFICHER_NB_SI_VIDE') )		define( 'JB_AL_AFFICHER_NB_SI_VIDE',	false );	// Affiche le nombre de photos même quand il n'y en a pas.
if( !defined('JB_AL_DEROULER_TOUT') )			define( 'JB_AL_DEROULER_TOUT',          false );	// Ne déroule que le dossier demandé. Mettez à 'true' pour que tous les dossiers soient déroulés.

// Vignettes
if( !defined('JB_AL_AFFICHER_NOMS') )			define( 'JB_AL_AFFICHER_NOMS',			true );		// Afficher le nom de chaque photo dans les vignettes.
if( !defined('JB_AL_REMPLACER_TIRETS_BAS') )	define( 'JB_AL_REMPLACER_TIRETS_BAS',	true );		// Si true, les '_' présents dans les noms seront remplacés par des espaces.
if( !defined('JB_AL_AFFICHER_EXTENSION') )		define( 'JB_AL_AFFICHER_EXTENSION',		false );    // Affiche ou non l'extension du fichier.

if( !defined('JB_AL_AFFICHER_POIDS') )			define( 'JB_AL_AFFICHER_POIDS',			true );		// Afficher la taille de chaque photo.
if( !defined('JB_AL_AFFICHER_DIMENSIONS') )		define( 'JB_AL_AFFICHER_DIMENSIONS',	true );		// Afficher les dimensions.

// Rappel des sous-dossiers
if( !defined('JB_AL_RAPPELER_SOUS_DOSSIERS') )	define( 'JB_AL_RAPPELER_SOUS_DOSSIERS',	true );		// Ceci rappellera les sous-dossiers du dossier courant après les vignettes.
if( !defined('JB_AL_RAPPELER_QUE_SI_VIDE') )	define( 'JB_AL_RAPPELER_QUE_SI_VIDE',	false );	// Permet de n'afficher le rappel des sous-dossiers que si le dossier courant n'a pas de photos.

// Comportement lors d'un dossier vide
if( !defined('JB_AL_AFFICHER_TXT_VIDE') )		define( 'JB_AL_AFFICHER_TXT_VIDE',		false );		// Si vrai le texte par défaut est affiché si le dossier n'a pas de photos

// Tri des dossiers et fichiers
if( !defined('JB_AL_FILTRE_PREFIXES_ACTIF') )	define( 'JB_AL_FILTRE_PREFIXES_ACTIF',	true );	    // Si true, active le filtrage sur les préfixes de tous les noms (dossiers et fichiers).
if( !defined('JB_AL_PREFIXES_SEPARATEUR') )		define( 'JB_AL_PREFIXES_SEPARATEUR',	'_' );		// Séparateur à utiliser pour préfixer vos noms si l'option précédente est active.

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

// Choix du mode d'affichage par défaut
if( !defined('JB_AL_MODE_DIAPO_DEFAUT') )		define( 'JB_AL_MODE_DIAPO_DEFAUT',		false );	// Si true, le mode diaporama sera actif par défaut. Sinon, c'est le mode galerie qui est activé.

// Pour le mode galerie

// Ouvrir les images dans une nouvelle fenêtre sans Javascript. La nouvelle fenêtre ne sera
// pas aux dimensions de l'images et les barres d'outils seront visibles. L'utilisation de cette
// option invalide la compatibilité XHTML Strict à cause du target="_blank".
if( !defined('JB_AL_OUVERTURE_BLK') )			define( 'JB_AL_OUVERTURE_BLK',			false );

// Ouvrir les images dans une nouvelle fenêtre par Javascript.
// Cela permet à l'inverse du paramètre précédent de disposer d'une fenêtre sans barres d'outils
// et aux dimensions de l'images. Vous conservez la validité XHTML, mais vous prenez le risque de
// rendre Albulle inutilisable pour les personnes qui auraient interdit l'exécution de code Javascript.
if( !defined('JB_AL_OUVERTURE_JS') )			define( 'JB_AL_OUVERTURE_JS',			true );
if( !defined('JB_AL_OUVERTURE_LBX') )			define( 'JB_AL_OUVERTURE_LBX',			true );		// Uniquement si l'ouverture javascript est activée : essayer-ce mode, c'est l'adopter !

// Pour le mode diaporama
if( !defined('JB_AL_OUVERTURE_BLK_DIAPO') )		define( 'JB_AL_OUVERTURE_BLK_DIAPO',	false );
if( !defined('JB_AL_OUVERTURE_JS_DIAPO') )		define( 'JB_AL_OUVERTURE_JS_DIAPO',		false );
if( !defined('JB_AL_OUVERTURE_LBX_DIAPO') )		define( 'JB_AL_OUVERTURE_LBX_DIAPO',	false );

// Vous pouvez redimensionner l'image qui s'affichera dans la popup
// Mettez les deux valeurs à 0 pour prendre la taille réelle.
if( !defined('JB_AL_POPUP_LARGEUR') )			define( 'JB_AL_POPUP_LARGEUR',			0 );		// Largeur max de l'image
if( !defined('JB_AL_POPUP_HAUTEUR') )			define( 'JB_AL_POPUP_HAUTEUR',			0 );		// Hauteur max de l'image

// /!\
// L'ouverture des images par popup Javascript est prioritaire sur l'ouverture sans Javascript.
// C'est-à-dire que si vous mettez JB_AL_OUVERTURE_JAVASCRIPT à 'true', quelque soit la valeur de ce
// que vous metterez pour JB_AL_OUVERTURE_BLANK, elle sera ignorée.
// /!\


// ================
// PARAMETRAGE DES VIGNETTES
//
if( !defined('JB_AL_VIGNETTES_PAR_PAGE') )		define( 'JB_AL_VIGNETTES_PAR_PAGE',		20 );		// Nombre de vignettes à afficher par page.

// Vignettes du mode gallerie
if( !defined('JB_AL_VIGNETTES_LARGEUR') )		define( 'JB_AL_VIGNETTES_LARGEUR',		150 );		// Largeur maximum des miniatures, en pixel (ne doit pas valoir 0).
if( !defined('JB_AL_VIGNETTES_HAUTEUR') )		define( 'JB_AL_VIGNETTES_HAUTEUR',		113 );		// Hauteur maximum des miniatures, en pixel (idem).

// Vignettes du mode diaporama
if( !defined('JB_AL_VIGNETTES_DP_LARGEUR') )	define( 'JB_AL_VIGNETTES_DP_LARGEUR', 	78 );		// Idem paramètres précédents
if( !defined('JB_AL_VIGNETTES_DP_HAUTEUR') )	define( 'JB_AL_VIGNETTES_DP_HAUTEUR',	59 );		// Idem

// N.b. : si vous changez les dimensions des vignettes, vous devrez très certainement faire des adaptations dans
// les CSS (fichier structure.css).

// Qualite des vignettes (uniquement pour les images Jpeg)
// Permet de régler la qualité des miniatures sur une échelle de 0 à 100.
// 0 : mauvaise qualité, petit fichier - 100 : meilleure qualité, gros fichier
if( !defined('JB_AL_VIGNETTES_QUALITE') )		define( 'JB_AL_VIGNETTES_QUALITE',		80 );

// /!\
// Si vous changez la largeur ou la hauteur, il faut supprimer toutes les miniatures existantes pour les regénérer
// avec les nouvelles dimensions.
// /!>

// ================
// PARAMETRAGE DU PANIER
//
if( !defined('JB_AL_PANIER_ACTIF') )			define( 'JB_AL_PANIER_ACTIF',			true );		// Activer le panier. Si false, le panier sera désactivé.
if( !defined('JB_AL_PANIER_CAPACITE_MAX') )		define( 'JB_AL_PANIER_CAPACITE_MAX',	0 );		// Nombre maximum de fichiers que peut contenir le panier (0 = désactiver la limitation).
if( !defined('JB_AL_PANIER_POIDS_MAX') )		define( 'JB_AL_PANIER_POIDS_MAX',		20 );		// Poids maximum que peut faire un panier en Mo. 0 = poids infini.
if( !defined('JB_AL_PANIER_NOM_ARCHIVE') )		define( 'JB_AL_PANIER_NOM_ARCHIVE',		'Photos' );	// Le nom que prendra les archives téléchargées.

// Si les zip ( >= 10MB) sont corrompus, passez cette valeur à true, pour éviter d'utiliser "readfile".
// Attention, en mettant true, le script consommera plus de temps d'exécution (souvent limité à 30sec).
define( 'JB_AL_PANIER_NO_READFILE', false);


// ================
// SPECIAL
//

// Si vous souhaitez intégrer Albulle dans votre site Internet, mettez le paramètre suivant
// à 'true'. Cela aura pour effet d'enlever les entêtes HTML lors de la génération des pages
// d'Albulle pour qu'il n'y ait pas de redondance avec votre propre site.
if( !defined('JB_AL_INTEGRATION_SITE') )		define( 'JB_AL_INTEGRATION_SITE',		false );

// Par défaut, Albulle produit du contenu en utf8. En metttant ce paramètre à vrai vous pourrez
// récupérer le contenu en iso-8859-1 si vous n'utilisez pas l'utf8 pour votre site. Cette
// conversion est cependant plus gourmande que de laisser l'utf8.
if( !defined('JB_AL_SORTIE_ISO') )				define( 'JB_AL_SORTIE_ISO',				false );

// Ce paramètre permet d'activer la conservation d'éventuels paramètres utilisé par le site
// hôte dans lequel vous vous insérez. Désactivez-le si vous êtes en mode standard.
if( !defined('JB_AL_CONSERVER_URL_HOTE') )		define( 'JB_AL_CONSERVER_URL_HOTE',		false );

// Utiliser Albulle comme centre de téléchargement.
define( 'JB_AL_MODE_CENTRE',			false );		// Mettez ce paramètre à 'true' pour basculer de mode.
define( 'JB_AL_DOSSIER_CENTRE',			'centre/' );	// Dossier dans lequel se trouvent les fichiers disponibles au téléchargement (relatif au dossier des données).
define( 'JB_AL_EXTENSION_FICHIERS',		'.zip' );		// Extension des fichiers à télécharger.

// Vous pouvez définir une url vers votre site principal pour le cas où Albulle
// ne le serait pas. Le lien s'affichera à gauche de celui qui donne sur la page d'accueil d'Albulle.
if( !defined('JB_AL_HOME_HREF') )				define( 'JB_AL_HOME_HREF',				'' );		// La page de votre site (Laissez vide pour ne pas utiliser cette fonctionnalité).
if( !defined('JB_AL_HOME_TEXTE') )				define( 'JB_AL_HOME_TEXTE',				'' );		// Le texte du lien (Non affiché si paramètre précédent vide).


// ================
// PARAMETRAGE DES CREATIONS DE FICHIERS (pour les miniatures)
//
define( 'JB_AL_CHMOD_FICHIERS',			0644 );

/* EOC (End Of Configuration) ;-) */
?>
