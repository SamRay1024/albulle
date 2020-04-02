<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

/**
 * Bienvenue dans ce fichier d'exemples !
 *
 * Vous trouverez ici différentes possibilités que vous avez à votre disposition
 * pour utiliser Albulle.
 * 
 * Je vous conseille de lire les descriptions de tous les exemples, chacune d'elles apportant
 * des informations différentes qui peuvent servir dans les autres exemples.
 * 
 * Les exemples qui suivent sont présentés selon le format suivant :
 * 		* Descriptions
 * 		* Code php à copier-coller dans votre fichier index.php
 */

// ==================================================================================================

/**
 * Exemple 1
 *
 * Mode standard (mode par défaut).
 *
 * La première chose à faire est de définir la constante JB_AL_ROOT. Elle est OBLIGATOIRE !
 * Elle définit le chemin d'accès au fichier explore.php, fichier principal d'Albulle, relativement
 * au fichier qui va l'appeler. Quelque soit la façon dont vous allez vous servir d'Albulle, cette
 * constante doit être définie. Dans le cas contraire, vous serez gratifié d'un message d'erreur.
 * 
 * /!\ N'oubliez pas le '/' à la fin du chemin d'accès au dossier racine d'Albulle.
 *
 * JB_AL_ROOT définie, il faut maintenant faire appel au fichier explore.php : c'est ce que fait
 * en partie le require_once(...) de la deuxième ligne. Je dis en partie parce que vous aurez
 * certainement noté le 'echo' présent en début de ligne. Une simple inclusion d'explore.php ne
 * suffit pas ; explore.php n'affiche rien. Vous devez vous même procéder à l'affichage de 
 * la page générée par explore.php : son contenu est renvoyé par un return à la fin du fichier
 * que vous recevez donc par le require_once. C'est une méthode qui pourra vous permettre par
 * exemple de différer l'affichage d'Albulle si vous avez d'autres choses à faire avant, ce qui
 * peut être le cas si vous faites une intégration d'Albulle dans un site.
 */

define( 'JB_AL_ROOT', 'albulle/' );
echo require_once( JB_AL_ROOT.'core/explore.php' );

// ==================================================================================================

/**
 * Exemple 2
 * 
 * Paramétrages supplémentaires.
 * 
 * Si vous le souhaitez, les paramètres que l'on définit normalement dans le fichier
 * config.php peuvent être définis avant l'appel au fichier explore.php. Dans ce cas,
 * ces éléments seront prioritaires sur config.php.
 * 
 * Remarque :
 * 		Ces éléments sont facultatifs et peuvent être définis directement dans le fichier config.php.
 * 		Ils sont accessibles pour le cas où vous souhaiteriez rendre ces paramètres dynamiques selon
 * 		certaines conditions ; chose qu'il n'est pas possible de faire en utilisant config.php.
 * 
 * 		Seule la définition de JB_AL_ROOT reste obligatoire.
 * 
 * 		Cette remarque est valable pour les autres exemples.
 */

define( 'JB_AL_ROOT',					'albulle/' );	// cf. exemple 1

define( 'JB_AL_AFFICHER_ENTETE',		true );			// Afficher / cacher l'entête de page (Tite, sous-titre & logo)
define( 'JB_AL_DOSSIER_THEME_ACTIF',	'albulle/' );	// Choisir un thème : le nom du dossier dans le dossier des thèmes + /

echo require_once( JB_AL_ROOT.'core/explore.php' );

// ==================================================================================================

/**
 * Exemple 3
 * 
 * Mode intégré à un site.
 * 
 * Pour ce faire, vous pouvez définir la constante JB_AL_INTEGRATION_SITE à true. Dans ce cas, ne sera
 * généré que le contenu de la page d'Albulle, càd sans les balises <html>, <head> et <body>. Ce sera
 * à vous de gérer cette partie de la page par l'intermédiaire de votre site. Vous ne récupérerez qu'un
 * <div id="albulle"> qui est le cadre principal de la page.
 * 
 * N'ayant plus d'entête de page, les styles CSS ne seront donc plus présents.
 * 
 * Cependant, vous aller pouvoir récupérer les balises d'inclusion des styles à placer dans l'entête 
 * <head>...</head> de votre site ! Ceci grâce à la variable '$sAlbulleMetas' qui sera disponible une 
 * fois la page explore.php appelée.
 * 
 * Si vous testez l'exemple ci-dessous, vous verrez qu'il y a deux choses qui ne vont pas :
 * 		* La taille de la police,
 * 		* Les caractères accentués qui n'apparaissent pas correctement.
 * 
 * C'est normal ! Etudions les deux points cités :
 * 		* En consultant le fichier style.css du dossier css du thème, vous pourrez trouver la classe
 * 			'.al_body' avec un attribut 'font-size: 62.5%;'. La classe affecte le body d'Albulle qui a
 * 			été volontairement nommé pour justement ne pas affecter un style générique body qui pourrait
 * 			entrer en conflit avec la définition de votre body. Or, en mode intégré, la balise <body> disparait
 * 			comme je l'ai dit plus haut. Vous devrez donc définir dans votre feuille de style l'attribut
 * 			font-size, soit avec la même valeur que celle d'Albulle soit avec une valeur en cohérence 
 * 			avec votre site.
 * 			Plus d'infos sur les ems : http://www.blog-and-blues.org/weblog/2004/05/24/214-font-size-em
 * 
 * 		* Albulle est codé en UTF-8. En supprimant l'entête de page, on supprime l'information d'encodage
 * 			de la page. Vous devez donc dans les métas de votre site indiquer que la page est en UTF-8
 * 			(vous pouvez vous inspirer du fichier header.thm.php du dossier html du thème).
 * 			Oui, cela vous oblige à avoir votre propre site en UTF-8 ; mais cela devrait déjà être le cas ;-)
 * 			Si ça ne l'est pas déjà, ça sera l'occasion de faire la transition !
 * 
 * 			Bon, si vraiment vous ne souhaitez pas passer à l'UTF-8, vous pouvez utiliser le paramètre
 * 			JB_AL_SORTIE_ISO présent dans le fichier de configuration et le mettre à vrai. Le contenu
 *			récupéré sera alors en Iso-8859-1 mais cela prendra un peu plus de ressource pour la
 *			conversion.
 * 
 * Un cas pratique de mise en oeuvre de toutes ces options serait d'avoir une configuration fixe config.php
 * qui permette l'utilisation d'Albulle en mode standard et de pouvoir à la fois l'inclure dans un site en 
 * redéfinissant le dossier du thème pour le changer et utiliser par exemple un thème adapté à votre site.
 * 
 * Elle est pas belle la vie ? ;-)
 *
 * Nouveauté 0.9.2 : la conservation des Url du site hôte.
 *
 * Le site dans lequel vous intégrez Albulle utilise très certainement des paramètres dans les Url. Dans ce
 * cas, il est nécessaire qu'Albulle puisse fonctionner en gérant ces paramètres pour ne pas déstabiliser
 * le site hôte et donc empêcher l'accès à Albulle parce que l'hôte est en vrac...
 *
 * Pour ce faire, un paramètre a été mis en place : JB_AL_CONSERVER_URL_HOTE. A vous de vois s'il faut
 * activer la conservation des paramètres Url de l'hôte où pas. Pour savoir, il suffit de regarder comment l'hôte
 * appelle ses pages : si vous voyez un unique appel au fichier index.php avec une variable qui identifie la page sur
 * laquelle se rendre, il vous faut activer la conservation. Si chaque page appelée est un fichier différent, il n'est
 * alors peut-être pas nécessaire de l'activer...sauf si dans ces pages, les Url sont utilisées pour passer des variables.
 *
 * Avec un minimum de connaissances sur le site hôte, vous verrez vite s'il faut activer ou non la conservation.
 */

define( 'JB_AL_ROOT',					'albulle/' );	// cf. exemple 1

define( 'JB_AL_AFFICHER_ENTETE',		false );		// cf. exemple 2
define( 'JB_AL_DOSSIER_THEME_ACTIF',	'albulle/' );	// cf. exemple 2

define( 'JB_AL_INTEGRATION_SITE',		true );			// Voici la constante qui indique qu'Albulle est intégré à un site.
define( 'JB_AL_CONSERVER_URL_HOTE', 	true );			// Activation de la conservation des paramètres de l'Url de l'hôte.

define( 'JB_AL_SORTIE_ISO',				true );			// Demande le décodage Utf8 pour que le contenu de $sAlbulle soit
														// au charset Iso-8859-1.

define( 'JB_AL_ACCUEIL_ALT'				'<p>Autre texte d\'accueil</p>' );	// Surcharger le texte d'accueil définit dans texte_accueil.html
														
$sAlbulle = require_once( JB_AL_ROOT.'core/explore.php' );

echo $sAlbulleMetas;
echo $sAlbulle;