<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

///////////////////////////////
// LICENCE
///////////////////////////////
//
// © DUCARRE Cédric (SamRay1024), Bubulles Créations, (09/05/2005)
//
// webmaster@jebulle.net
// http://jebulle.net
//
// Ce fichier fait partie d'AlBulle, script de gestion d'albums photos.
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
 * Librairie de fonctions inclassables.
 *
 * @author SamRay1024
 * @copyright Bubulles Creation - http://jebulle.net
 * @since 31/10/2006
 * @version 1.6
 *
 */

class Util {

	/**
	 * Redimensionner une image.
	 *
	 * @param	[RESOURCE]	$img_src		Contenu binaire de l'image à redimensionner.
	 * @param	[INTEGER]	$larg_mini_max	Largeur max de la redimension.
	 * @param	[INTEGER]	$haut_mini_max	Hauteur max de la redimension.
	 *
	 * @return	[RESOURCE]	Contenu binaire de l'image redimensionnée.
	 */
	function redimensionner( $img_src, $larg_mini_max, $haut_mini_max )
	{
		// On recupere les dimensions de l'image que l'on souhaite redimensionner
		$larg = ImageSX ( $img_src );
		$haut = ImageSY ( $img_src );

		$aNouvellesDimensions = $this->calculerDimensions($larg, $haut, $larg_mini_max, $haut_mini_max);

		// Cree une image vierge de la dimension desiree. Cette fonction permet de ne pas etre limite a 256 couleurs contrairement a "ImageCreate"
		$img_dst = ImageCreateTrueColor ( $aNouvellesDimensions['largeur'], $aNouvellesDimensions['hauteur'] );

		// On effectue une copie de l'image source vers la miniture
		ImageCopyResized ( $img_dst, $img_src, 0, 0, 0, 0, $aNouvellesDimensions['largeur'], $aNouvellesDimensions['hauteur'], $larg, $haut);

		return $img_dst;
	}

	/**
	 * Calcul les nouvelles dimensions d'une image.
	 *
	 * @param	[INTEGER]	$iLargeurInitiale		Largeur initiale de l'image.
	 * @param	[INTEGER]	$iHauteurInitiale		Hauteur initiale de l'image.
	 * @param	[INTEGER]	$iLargeurDemandee		Largeur finale souhaitée.
	 * @param	[INTEGER]	$iHauteurDemandee		Hauteur finale souhaitée.
	 * @return	[ARRAY]								Retourne un tableau associatif qui contient les nouvelles dimensions.
	 * 													$aResultat['largeur'] & $aResultat['hauteur']
	 */
	function calculerDimensions( $iLargeurInitiale, $iHauteurInitiale, $iLargeurDemandee, $iHauteurDemandee )
	{
		$aResultat = array( 'largeur' => 0, 'hauteur' => 0 );

		// si l'image est plus petite que les dimensions demandees, on ne redimensionne pas.
		// Pour cela, on force la valeur de la dimension souhaitee a la valeur de la taille de l'image
		// de sorte a creer un ratio de 1 pour la dimension
		if( $iLargeurInitiale < $iLargeurDemandee )	$iLargeurDemandee = $iLargeurInitiale;
		if( $iHauteurInitiale < $iHauteurDemandee )	$iHauteurDemandee = $iHauteurInitiale;

		// On calcule le ratio pour la largeur et la hauteur
		$fRatioLargeur = $iLargeurDemandee / $iLargeurInitiale;
		$fRatioHauteur = $iHauteurDemandee / $iHauteurInitiale;

		// Et on garde le plus petit afin de ne jamais depasser la taille maximale
		$fRatioFinal = ( $fRatioLargeur <= $fRatioHauteur ) ? $fRatioLargeur : $fRatioHauteur;

		// Connaissant le ratio de la miniature, on peut donc obtenir ses dimensions reelles.
		// Ici, on utilise la fonction "round" pour avoir une valeur entiere. Cela nous donne le nombre de pixels que va faire la miniature.
		$aResultat['largeur'] = round ( $iLargeurInitiale * $fRatioFinal );
		$aResultat['hauteur'] = round ( $iHauteurInitiale * $fRatioFinal );

		return $aResultat;
	}

	/**
	 * Traitement d'une image postée par formulaire pour écriture dans un dossier.
	 *
	 * Adaptee de http://lecyber.net
	 *
	 * Cette méthode permet de traiter une image envoyée par un formulaire. L'image est redimensionnée aux dimensions
	 * souhaitées (uniquement si elle est plus grande que les dimensions demandées), écrite dans le dossier demandé
	 * et son nom de fichier peut être préfixé.
	 *
	 * @param	[STRING]	$type				Type MIME de l'image envoyée (ie : 'image/gif', 'image.jpg', ...).
	 * @param	[STRING]	$srcFile			Chemin d'accès complet à l'image que l'on souhaite redimensionner.
	 * @param	[STRING]	$destFile			Répertoire de destination de stockage de l'image redimensionnée.
	 * @param	[STRING]	[$larg_mini_max]	Largeur max de la miniature.
	 * @param	[STRING]	[$haut_mini_max]	Lauteur max de la miniature.
	 * @param	[STRING]	[$prefixe]			Si ce champ est indiqué, on génère une miniature préfixé de la chaîne que contient cette variable
	 *											sinon, on écrase le fichier d'entrée si le dossier de destination est le même que celui de l'image originale.
	 * @param	[INTEGER]	[$iQualite]			Uniquement pour les images Jpeg : permet de régler la qualité des miniatures sur une échelle de 0 à 100.
	 * 												75 : par défaut
	 * 												0 : mauvaise qualité, petit fichier
	 * 												100 : meilleure qualité, gros fichier
	 * @return	[STRING]						Chemin d'accès à la miniature qui vient d'être générée.
	 */
	function processImgFile( $type, $srcFile, $destFile, $larg_mini_max = 0, $haut_mini_max = 0, $prefix = '', $iQualite = 75 )
	{

		// Recuperation des infos du fichier de destination
		$sDirDestFile	= dirname( $destFile );			// chemin d'acces
		$nameDestFile	= basename( $destFile);			// nom du fichier

		// on reecrit le fichier de destination avec le prefixe et le chemin complet
		$destFile = $sDirDestFile.'/'.$prefix.$nameDestFile;

		// creation de l'image en fonction du type MIME
		switch( $type )
		{
			case 'image/pjpeg':
			case 'image/jpeg':
				$img_src = ImageCreateFromJpeg( $srcFile );
				break;

			case 'image/x-png':
			case 'image/png':
				$img_src = ImageCreateFromPng( $srcFile );
				break;

			case 'image/gif':
				$img_src = ImageCreateFromGif( $srcFile );
				break;
		}

		// si les deux longeurs max sont nulles, alors on ne redimensionne pas l'image
		if( ($larg_mini_max != 0) && ($haut_mini_max != 0) )
			$img_dst = $this->redimensionner( $img_src, $larg_mini_max, $haut_mini_max );
		else $img_dst = $img_src;

		// Si un second parametre est indique a la fonction ImageJpeg, la miniature est sauvegardee mais elle ne sera pas affichee. Ex : ImageJpeg( $img_src, './miniatures/mini.jpg');
		switch( $type )
		{
			case 'image/pjpeg':
			case 'image/jpeg':
				// ecriture de la miniature au format jpeg
				ImageJpeg( $img_dst, $destFile, $iQualite);
				break;

			case 'image/x-png':
			case 'image/png':
				// ecriture de la miniature au format png
				ImagePng( $img_dst, $destFile );
				break;

			case 'image/gif':
				// ecriture de la miniature au format gif
				ImagePng( $img_dst, $destFile );
				break;
		}

		// destruction du tampon de l'image
		ImageDestroy( $img_dst );

		return $destFile;

	}

	/**
	 * Méthode avancée de lecture de dossiers.
	 *
	 * La méthode reçoit un chemin de dossier, ouvre ce dossier et en donne la liste des éléments, qui
	 * peuvent être ou les dossiers, ou les fichiers, ou les deux.
	 *
	 * Les éléments lus du dossiers sont retournés dans un tableau de deux manières différentes. Pour
	 * les cas où soit les dossiers, soit les fichiers sont demandés, le tableau retourné est un tableau
	 * indexé classique à une dimension. En revanche pour le cas ou les deux types sont demandés, le tableau
	 * retourné est un tableau à deux dimensions. La 1ère est associative et contient deux sous-tableaux :
	 * l'un pour les dossiers, l'autre pour les fichiers.
	 *
	 * 		$aTableauRetour['dir'] contient les dossiers lus.
	 * 		$aTableauRetour['file'] contient les fichiers lus.
	 *
	 * Les deux sous tableaux sont eux indexés classiquement, tout comme les tableaux de retour sur l'un
	 * ou l'autre des types demandés (dossiers/fichiers).
	 *
	 * @param	[STRING]	$sDir				Chemin du dossier à parcourir.
	 * @param	[STRING]	$sMode				Modes de parcours du dossier :
	 * 												'DOSSIERS_SEULEMENT'	=> retourne uniquement les dossiers.
	 * 												'FICHIERS_SEULEMENT'	=> retourne uniquement les fichiers.
	 * 												'TOUT'					=> retourne tous les éléments (dossiers ET fichiers).
	 * @param	[ARRAY]		$aFiltresDossiers	Optionnel. Tableau de dossiers qui ne doivent pas être pris en compte.
	 * @param	[ARRAY]		$aFiltresExtensions	Optionnel. Tableau à utiliser pour ne garder que les fichiers qui correspondent
	 * 												aux extensions données.
	 * 												Les extensions doivent être de la forme 'jpg', 'gif', 'exe', ...
	 * @return	[MIXED]							FALSE en cas d'erreur, tableau des éléments lus sinon.
	*/
	function advScanDir( $sDir, $sMode, $aFiltres = array(), $aFiltresExtensions = array() )
	{
		// creation du tableau qui va contenir les elements du dossier
		$aItemsDir = $aItemsFile = array();

		// ajout du slash a la fin du chemin s'il n'y est pas
		if( !preg_match( "/^.*\/$/", $sDir ) ) $sDir .= '/';

		// Ouverture du repertoire demande
		$handle = @opendir( $sDir );

		// si pas d'erreur d'ouverture du dossier on lance le scan
		if( $handle != false )
		{
			// Parcours du repertoire
			while( $sItem = readdir($handle) )
			{
				if($sItem != '.' && $sItem != '..' && !in_array( $sItem, $aFiltres ) )
				{
					if( is_dir( $sDir.$sItem ) )
						$aItemsDir[] = $sItem;
					else
					{
						$bAjouterFichier = true;

						// Extraction de l'extension si filtrage sur extensions demandé
						if( sizeof($aFiltresExtensions) !== 0 )
						{
							$aExplode = explode('.', $sItem);
							$sExt = strtolower($aExplode[sizeof($aExplode) - 1]);
							if( !in_array($sExt, $aFiltresExtensions) )	$bAjouterFichier = false;
						}

						// Ajout si autorisé
						if($bAjouterFichier)	$aItemsFile[] = $sItem;
					}
				}
			}

			// Fermeture du repertoire
			closedir($handle);

			// Tri des dossiers
			sort( $aItemsDir );
			sort( $aItemsFile );

			// construction tableau retour
			switch( $sMode )
			{
				case 'DOSSIERS_SEULEMENT' : return $aItemsDir; break;
				case 'FICHIERS_SEULEMENT' : return $aItemsFile; break;
				case  'TOUT' : return array( 'dir' => $aItemsDir, 'file' => $aItemsFile );
			}

			return array();
		}
		else return false;
	}

	/**
	 * Génération d'une liste de liens pour faire une pagination.
	 *
	 * Cette méthode est une adaptation de celle fourni dans PunBB, le
	 * script de forums.
	 *
	 *  ****
	 *  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)
	 *
	 *  This function is part of PunBB.
	 *
	 *  PunBB is free software; you can redistribute it and/or modify it
	 *  under the terms of the GNU General Public License as published
	 *  by the Free Software Foundation; either version 2 of the License,
	 *  or (at your option) any later version.
	 *
	 *  PunBB is distributed in the hope that it will be useful, but
	 *  WITHOUT ANY WARRANTY; without even the implied warranty of
	 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *  GNU General Public License for more details.
	 *
	 *  You should have received a copy of the GNU General Public License
	 *  along with this program; if not, write to the Free Software
	 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
	 *  MA  02111-1307  USA
	 *  ****
	 *
	 * @param	[INTEGER]	$num_pages		Le nombre de pages total.
	 * @param	[INTEGER]	$cur_page		La page courante.
	 * @param	[STRING]	$link_to		La destination des url qu'il faut placer dans les liens.
	 * @return	[STRING]	Retourne la liste des liens au format HTML.
	 */
	function paginer($num_pages, $cur_page, $link_to)
	{
		$pages = array();
		$link_to_all = false;

		// If $cur_page == -1, we link to all pages (used in viewforum.php)
		if ($cur_page == -1)
		{
			$cur_page = 1;
			$link_to_all = true;
		}

		if ($num_pages <= 1)
		$pages = array('<span class="pageActive">1</span>');
		else
		{
			if ($cur_page > 3)
			{
				$pages[] = '<a href="'.$link_to.'&amp;page=1" class="page">1</a>';

				if ($cur_page != 4)
					$pages[] = '<span>&hellip;</span>';
			}

			// Don't ask me how the following works. It just does, OK? :-)
			for ($current = $cur_page - 2, $stop = $cur_page + 3; $current < $stop; ++$current)
			{
				if ($current < 1 || $current > $num_pages)
					continue;
				else if ($current != $cur_page || $link_to_all)
					$pages[] = '<a href="'.$link_to.'&amp;page='.$current.'" class="page">'.$current.'</a>';
				else
					$pages[] = '<span class="pageActive">'.$current.'</span>';
			}

			if ($cur_page <= ($num_pages-3))
			{
				if ($cur_page != ($num_pages-3))
					$pages[] = '<span>&hellip;</span>';

				$pages[] = '<a href="'.$link_to.'&amp;page='.$num_pages.'" class="page">'.$num_pages.'</a>';
			}
		}

		return implode(/*'&#160;'*/'', $pages);
	}

	/**
	 * Extraire à gauche de la n-ième sous-chaîne.
	 *
	 * Extrait d'une chaine tout ce qui se trouve à gauche de la n-ième sous-chaine
	 * spécifiée. Par exemple, pour extraire les chemins parents dans une chaine de
	 * caractères qui contient un chemin :
	 * 		echo SousChaineGauche( 'dossier1/dossier2/dossier3/dossier4', '/', 2 );
	 * 		=> Affiche : dossier1/dossier2
	 *
	 * @param	[STRING]	$sChainePrincipale	La chaine dans laquelle on doit faire l'extraction.
	 * @param	[STRING]	$sSousChaine		La chaine à repérer.
	 * @param	[INTEGER]	$iNbOccurences		Le nombre d'occurences à partir duquel on garde ce qui se trouve
	 *											à gauche de la sous-chaine.
	 * @return	[STRING]						Retourne $sChainePrincipale tronquée.
	 */
	function SousChaineGauche( $sChainePrincipale, $sSousChaine, $iNbOccurences )
	{
		if( $sChainePrincipale !== '' )
		{
			$iOffSet = 0;

			for( $i = 0 ; ( $i < $iNbOccurences) && ( $iOffSet !== false ) ; $i++ )
			{
				$iOffSet = strrpos( $sChainePrincipale, $sSousChaine );

				if( $iOffSet !== false )
					$sChainePrincipale = substr( $sChainePrincipale, 0, $iOffSet );
			}
		}

		return $sChainePrincipale;
	}

	/**
	 * Extraire à droite de la n-ième sous-chaîne.
	 *
	 * Extrait d'une chaine tout ce qui se trouve à droite de la n-ième sous-chaîne
	 * spécifiée. Par exemple, pour extraire les dossiers enfants dans une chaîne de
	 * caractères qui contient un chemin :
	 * 		echo sousChaineDroite( 'dossier1/dossier2/dossier3/dossier4', '/', 2 );
	 * 		=> Affiche : dossier3/dossier4
	 *
	 * @param	[STRING]	$sChainePrincipale	La chaine dans laquelle on doit faire l'extraction.
	 * @param	[STRING]	$sSousChaine		La chaine à repérer.
	 * @param	[INTEGER]	$iNbOccurences		Le nombre d'occurences à partir duquel on garde ce qui se trouve
	 *											à droite de la sous-chaine.
	 * @return	[STRING]						Retourne $sChainePrincipale tronquée.
	 */
	function sousChaineDroite( $sChainePrincipale, $sSousChaine, $iNbOccurences = 0 )
	{
		if( $sChainePrincipale !== '' )
		{
			$iOffSet = 0;

			for( $i = 0 ; ( $i < $iNbOccurences) && ( $iOffSet !== false ) ; $i++ )
			{
				$iOffSet = strpos( $sChainePrincipale, $sSousChaine );

				if( $iOffSet !== false )
					$sChainePrincipale = substr( $sChainePrincipale, $iOffSet + 1, strlen($sChainePrincipale));
			}
		}

		return $sChainePrincipale;
	}

	/**
	 * Nettoyer une chaine de chemin d'accès qui provient d'une URL.
	 *
	 * Cette méthode permet le nettoyage d'une chaine qui représente un chemin vers un dossier quelconque.
	 * Elle est destinée à eviter les failles d'accès à des dossiers interdits par une URL. Elle se charge
	 * donc de repérer les dossiers vides ('//'), les noms tels './' et '../'. En plus, il est possible
	 * de demander à la méthode d'interdire des dossiers spécifiques dont vous passez le nom par
	 * l'intermédiaire d'un tableau.
	 *
	 * La chaine étant passée par référence, elle n'est pas retournée par la méthode et est directement
	 * utilisable dans le fichier appelant.
	 *
	 * @param	[STRING]	$sChaine				Passée par référence, la chaine est nettoyée des dossiers interdits.
	 * @param	[ARRAY]		$aDossiersInterdits		Optionnel. Contient une liste de dossiers interdits.
	 * @return	[ARRAY]								Retourne la chaine d'accès sous forme de tableau (pour des traitements ultérieurs)
	 */
	function nettoyerCheminURL( &$sChaine, $aDossiersInterdits = array() )
	{
		$sExpRegDossiersInterdits = '';

		// On eclate le chemin dans un tableau extraire chaque dossier
		$aDossiers = explode( '/', $sChaine );

		// on calcul le nombres de dossiers passés dans l'url
		$iNbDossiers = sizeof($aDossiers);

		// Si des dossiers sont interdits, on construit la fin de l'expression régulière
		$iNbDossiersInterdits = sizeof($aDossiersInterdits);
		if( $iNbDossiersInterdits !== 0 )
			for( $i = 0 ; $i < $iNbDossiersInterdits ; $i++ )
				$sExpRegDossiersInterdits .= '|'.$aDossiersInterdits[$i];

		// nettoyage des dossiers, pour enlever les chaines vides, les accès du type ./ et ../
		// et les dossiers interdits
		for( $i = 0 ; $i < $iNbDossiers ; $i++ )
			if( $aDossiers[$i] === '' || preg_match('/^[.]+$'.$sExpRegDossiersInterdits.'/', $aDossiers[$i]) !== 0 )
				unset( $aDossiers[$i] );

		// reconstruction des index du tableau (utilisation d'un tableau vide pour corriger un bogue intervenant occasionnellement)
		$aTemp = array();
		$aDossiers = array_merge($aDossiers, $aTemp);

		// Reconstruction de la chaine d'accès nettoyée
		$sChaine = implode( '/', $aDossiers );

		return $aDossiers;
	}

	/**
	 * Prépare un chemin d'accès à un fichier pour être utilisé comme Url (href ou src).
	 *
	 * @param	[STRING]	$sUrl			Le chemin à formater.
	 * @param	[BOOLEAN]	$bSansSlashs	Si True, les slashs ne seront pas encodés.
	 * @return	[STRING]					L'Url formatée.
	 */
	function preparerUrl( $sUrl, $bSansSlashs = false )
	{
		$sUrl = rawurlencode($sUrl);
		return ( $bSansSlashs === true ) ? str_replace( '%2F', '/', $sUrl ) : $sUrl;
	}

	/**
	 * Enlèver le préfixe d'une chaine.
	 *
	 * Permet d'effacer tout ce qui se trouve avant un séparateur dans une chaine.
	 * Le séparateur est lui aussi effacé. Si des espaces se trouvent après le séparateur,
	 * ils sont de même supprimés.
	 *
	 * @param	[STRING]	$sChaineANettoyer		La chaîne dans laquelle enlever un préfixe.
	 * @param	[STRING]	$sMarqueur				La chaîne qui marque la fin du préfixe.
	 * @return	[STRING]							La chaîne nettoyées.
	 */
	function enleverPrefixe( $sChaineANettoyer, $sSeparateur )
	{
		// Recherche du séparateur
		$iPosSeparateur = strpos($sChaineANettoyer, $sSeparateur);

		// Extraction de ce qui ce trouve à gauche de la position trouvée
		$sGaucheSeparateur = substr($sChaineANettoyer, 0, $iPosSeparateur);

		// Si la chaine lue à gauche du séparateur est bien un entier, on peut alors l'enlever
		// car on est certain qu'il représente le préfixe.
		if( ctype_digit($sGaucheSeparateur) )
			return trim( substr($sChaineANettoyer, $iPosSeparateur, strlen($sChaineANettoyer)) );
		else
		    return $sChaineANettoyer;
	}

	/**
	 * Parse un fichier pour remplacer ses pseudos-variables.
	 *
	 * Le tableau à passer en paramètres doit être associatif. Les clés de ce tableau doivent
	 * représenter les éléments à remplacer dans le fichier. Ces clés doivent alors être des
	 * expression régulières.
	 *
	 * De plus, le tableau peut être soit à une dimension soit à deux. Un tableau à une dimension
	 * signifie que vous parsez un fichier simple. Un tableau à deux dimensions signifie que
	 * vous devez parser le fichier autant de fois qu'il y a de valeurs dans la première dimension.
	 *
	 * En d'autres termes, le remplacement de données dans un fichier simple conduit à présenter un
	 * tableau de la façon suivante :
	 * 		$aElements['/{MON_ELEMENT_1}/']	= 'ce qui va être mis à la place';
	 * 		...
	 *
	 *  Et pour boucler sur le fichier :
	 *  	$aElements[0]['/{MON_ELEMENT_1}/']	= 'ce qui va être mis à la place';
	 *  	$aElements[0]['/{MON_ELEMENT_2}/']	= '...';
	 *  	...
	 *  	$aElements[1]['/{MON_ELEMENT_1}/']	= '...';
	 *  	$aElements[1]['/{MON_ELEMENT_2}/']	= '...';
	 *  	...
	 *
	 * /!\ Si le tableau passé en paramètre est à deux dimensions, il faut définir le dernier
	 * paramètre à True.
	 *
	 * @param	[String]	$sCheminFichier			Le chemin d'accès au fichier à parser.
	 * @param	[String]	$aElementsARemplacer	Le tableau dont les clés sont les expressions régulières d'accès aux pseudo-variables
	 * 												et les valeurs le contenu à remplacer.
	 * @param	[Boolean]	$bBoucler				Optionnel. Si vrai, c'est que le tableau est bi-dimensionnel, donc qu'il s'agit de boucler
	 * 												sur plusieurs éléments.
	 * @return	[String]							Le contenu du fichier parsé.
	 */
	function parser( $sCheminFichier, $aElementsARemplacer, $bBoucler = false )
	{
		// Lecture fichier et remplacement des pseudos-variables
		$sContenuFichier = file_get_contents($sCheminFichier);

		if($bBoucler === false)
		{
			// Extraction des patterns à remplacer
			$aPatterns = array_keys($aElementsARemplacer);
			$aRemplacements = array_values($aElementsARemplacer);

			return preg_replace($aPatterns, $aRemplacements, $sContenuFichier);
		}
		else
		{
			$sContenuFinal = '';
			$iNbElements = sizeof($aElementsARemplacer);
			for( $i = 0 ; $i < $iNbElements ; $i++ )
			{
				$aPatterns = array_keys($aElementsARemplacer[$i]);
				$aRemplacements = array_values($aElementsARemplacer[$i]);
				$sContenuFinal .= preg_replace($aPatterns, $aRemplacements, $sContenuFichier);
			}
			return $sContenuFinal;
		}
	}

	/**
	 * Créé une liste HTML dont chaque puce représente une information EXIF d'une image au format JPEG.
	 *
	 * Les informations retournées par cette méthode sont les suivantes :
	 * 		Marque et modèle de l'appareil photo, date et heure de la photo, temps d'exposition,
	 * 		sensibilité ISO, ouverture et longueur de la focale.
	 *
	 * @param	[STRING]	$sCheminImage	Le chemin d'accès à l'image dont on souhaite obtenir les infos EXIF.
	 * @return	[STRING]					Si la photo ne possède pas de données EXIF, une simple phrase est retournée. Sinon
	 * 										la méthode retourne une liste <ul>...</ul> avec un élément <li>...</li> par donnée.
	 * 										La liste <ul> possède un identifiant, "exif", pour repérer la liste avec une feuille
	 * 										de style CSS.
	 */
	function afficherExif( $sCheminImage )
	{
		$sDonneesExif		= "<ul id=\"exif\">\n\t";
		$sDonneeInconnue	= 'inconnue';

		if( function_exists('exif_imagetype') ) {

			if( file_exists($sCheminImage) && exif_imagetype($sCheminImage) === IMAGETYPE_JPEG ) {
				if( ($aExif = @exif_read_data($sCheminImage, 'ANY_TAG', true)) !== false ) {

					if( key_exists('IFDO', $aExif) )
					{
						// Marque appareil
						$sDonneesExif .= '<li><span>Marque de l\'appareil : </span>';
						$sDonneesExif .= ( key_exists('Make', $aExif['IFD0']) && $aExif['IFD0']['Make'] !== '' ) ? $aExif['IFD0']['Make'] : $sDonneeInconnue;
						$sDonneesExif .= '</li>';

						// Modèle appareil
						$sDonneesExif .= '<li><span>Modèle de l\'appareil : </span>';
						$sDonneesExif .= ( key_exists('Model', $aExif['IFD0']) && $aExif['IFD0']['Model'] !== '' ) ? $aExif['IFD0']['Model'] : $sDonneeInconnue;
						$sDonneesExif .= '</li>';
					}

					if( key_exists('EXIF', $aExif) )
					{
						// Date/heure
						$sDonneesExif .= '<li><span>Date/Heure de la prise de vue : </span>';
						$sDonneesExif .= ( key_exists('DateTimeOriginal', $aExif['EXIF']) && $aExif['EXIF']['DateTimeOriginal'] !== '' ) ? $aExif['EXIF']['DateTimeOriginal'] : $sDonneeInconnue;
						$sDonneesExif .= '</li>';

						// Temps exposition
						$sDonneesExif .= '<li><span>Temps d\'exposition : </span>';
						$sDonneesExif .= ( key_exists('ExposureTime', $aExif['EXIF']) && $aExif['EXIF']['ExposureTime'] !== '' ) ? $aExif['EXIF']['ExposureTime'] : $sDonneeInconnue;
						$sDonneesExif .= '</li>';

						// ISO
						$sDonneesExif .= '<li><span>Sensibilité ISO : </span>';
						$sDonneesExif .= ( key_exists('ISOSpeedRatings', $aExif['EXIF']) && $aExif['EXIF']['ISOSpeedRatings'] !== '' ) ? $aExif['EXIF']['ISOSpeedRatings'] : $sDonneeInconnue;
						$sDonneesExif .= '</li>';

						// Longueur focale
						$sDonneesExif .= '<li><span>Longueur de la focale : </span>';
						$sDonneesExif .= ( key_exists('FocalLength', $aExif['EXIF']) && $aExif['EXIF']['FocalLength'] !== '' ) ? $aExif['EXIF']['FocalLength'] : $sDonneeInconnue;
						$sDonneesExif .= '</li>';
					}

					if( key_exists('COMPUTED', $aExif) )
					{
						// Ouverture focale
						$sDonneesExif .= '<li><span>Ouverture de la focale : </span>';
						$sDonneesExif .= ( key_exists('ApertureFNumber', $aExif['COMPUTED']) && $aExif['COMPUTED']['ApertureFNumber'] !== '' ) ? $aExif['COMPUTED']['ApertureFNumber'] : $sDonneeInconnue;
						$sDonneesExif .= '</li>';
					}
				}
				else return '';
			}
			else return '';
		}
		else return '';

		$sDonneesExif .= "</ul>\n";

		return $sDonneesExif;
	}

	/**
	 * Tronque la chaîne de catactères passée en paramètre pour la réduire à iNbCaracteres.
	 * La chaine est complétée avec '...' pour indiquer qu'elle n'est pas complète.
	 *
	 * @param	[STRING]	$sChaineATronquer	La chaîne que l'on souhaite tronquer.
	 * @param	[STRING]	$iNbCaracteres		Le nombres de caractères maximals que doit faire la chaine depuis le début.
	 * @return	[STRING]						La chaîne tronquée + '...'.
	 */
	function tronquerChaine( $sChaineATronquer, $iNbCaracteres = 20 )
	{
		if( !empty($sChaineATronquer) )
		{
			$sChaineATronquer = trim($sChaineATronquer);

			if( strlen($sChaineATronquer) > $iNbCaracteres )
				return substr($sChaineATronquer, 0, $iNbCaracteres).'...';
			else return $sChaineATronquer;
		}
		else return '';
	}

	/**
	 * Lit le type MIME d'une image.
	 *
	 * La fonction fonctionne de deux façons différentes :
	 *  - si les extensions pour le support EXIF sont chargées : utilisation de la fonction exif_imagetype.
	 * 		Tous les formats d'images sont alors supportés.
	 * 	- si les extensions ne sont pas chargées : code perso qui lit l'extension du fichier pour en déterminer
	 *      le type MIME. Seul sont détectées les jpg, gif et png.
	 *
	 * Les valeurs de retour possibles sont les suivantes :
	 * 	- image/jpeg
	 *  - image/gif
	 *  - image/png
	 *
	 * @param	[STRING]	$sCheminImg		Chemin d'accès au fichier dont on veut le type MIME.
	 * @return	[STRING]					Type MIME.
	 */
	function imageTypeMime( $sCheminImg )
	{
		if( function_exists('exif_imagetype') )
			return image_type_to_mime_type(exif_imagetype($sCheminImg));
		else {

			$aExplode = explode( '.', $sCheminImg );
			$sExt = strtolower( $aExplode[sizeof( $aExplode ) - 1] );

			switch ( $sExt )
			{
				case 'jpg':
				case 'jpeg':
				case 'jpe': $sTypeMime = 'image/jpeg'; break;
				case 'gif': $sTypeMime = 'image/gif'; break;
				case 'png': $sTypeMime = 'image/png'; break;
			}

			return $sTypeMime;
		}
	}

}

?>
