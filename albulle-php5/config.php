<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

$sDossierPhotos				= 'photos/';					// !! n'oubliez pas le '/' à la fin

$sDossierThemes				= 'themes/';					// dossier où se trouves les thèmes (pareil, ne pas oublier le '/' à la fin
$sDossierThemeUtilise		= 'albulles/';					// dossier du thème à utiliser (remarque idem pour le '/')
$sFichierHtml				= 'html.php';					// Nom que doit prendre le fichier php dans les dossiers de thème

$sFichierDownload			= 'download.php';				// chemin d'accès au fichier de téléchargement

$bAfficherVersion			= true;							// Afficher le numéro de version dans le copyright.
$bAfficherNbPhotos			= true;							// indique si le script affiche le nombre de photos par répertoire
$bPhotoDansNouvellePage		= false;						// si true, les photos seront affichées dans une nouvelle page MAIS vous perdrez
															// la compatibilité XHTML Strict à cause du target="_blank".

$iFichiersMaxDansPanier		= 10;							// mettre à 0 pour désactiver la limitation du panier
$iLargeurMax				= 150;							// valeur en pixels
$iHauteurMax				= 113;							// idem
$iImgParPage				= 10;							// nombre d'images par page

$iChmodDossierMiniatures	= 0755;
$iChmodFichiersMiniatures	= 0644;

?>