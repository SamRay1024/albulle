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
// © DUCARRE Cedric, Bubulles Creations, (09/05/2005) 
// 
// webmaster@jebulle.net
// http://jebulle.net
// 
// Ce fichier fait partie d'AlBulles, script de gestion d'albums photos. 
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
 * @since 25/08/2005
 * @version 1.1
 * 
 */

class Util {

	/**
	 * Redimensionner une image.
	 *
	 * @param	[RESOURCE]	$img_src		Identifiant de l'image à redimensionner.
	 * @param	[INTEGER]	$larg_mini_max	Largeur max de la redimension.
	 * @param	[INTEGER]	$haut_mini_max	Hauteur max de la redimension.
	 * 
	 * @return	[RESOURCE]	Identifiant de l'image redimensionnée.
	 */
	function redimensionner( $img_src, $larg_mini_max, $haut_mini_max )
	{
		// On recupere les dimensions de l'image que l'on souhaite redimensionner
		$larg = ImageSX ( $img_src );
		$haut = ImageSY ( $img_src );
		
		// si l'image est plus petite que les dimensions demandees, on ne redimensionne pas.
		// Pour cela, on force la valeur de la dimension souhaitee a la valeur de la taille de l'image
		// de sorte a creer un ratio de 1 pour la dimension
		if( $larg < $larg_mini_max )	$larg_mini_max = $larg;
		if( $haut < $haut_mini_max )	$haut_mini_max = $haut;
		
		// On calcule le ratio pour la largeur et la hauteur
		$ratio_l = $larg_mini_max / $larg;
		$ratio_h = $haut_mini_max / $haut;
		
		// Et on garde le plus petit afin de ne jamais depasser la taille maximale
		( $ratio_l <= $ratio_h ) ? $ratio = $ratio_l : $ratio = $ratio_h ;
		
		// Connaissant le ratio de la miniature, on peut donc obtenir ses dimensions reelles.
		// Ici, on utilise la fonction "round" pour avoir une valeur entiere. Cela nous donne le nombre de pixels que va faire la miniature.
		$mini_larg = round ( $larg * $ratio );
		$mini_haut = round ( $haut * $ratio );
		
		// Cree une image vierge de la dimension desiree. Cette fonction permet de ne pas etre limite a 256 couleurs contrairement a "ImageCreate"
		$img_dst = ImageCreateTrueColor ( $mini_larg, $mini_haut ); 
		
		// On effectue une copie de l'image source vers la miniture
		ImageCopyResized ( $img_dst, $img_src, 0, 0, 0, 0, $mini_larg, $mini_haut, $larg, $haut); 
		
		return $img_dst;
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
	 * 	
	 * @return	[STRING]						Chemin d'accès à la miniature qui vient d'être générée.
	 */
	function processImgFile( $type, $srcFile, $destFile, $larg_mini_max = 0, $haut_mini_max = 0, $prefix = '' )
	{
		
		// Recuperation des infos du fichier de destination
		$sDirDestFile		= dirname( $destFile );			// chemin d'acces
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
		if( $larg_mini_max != 0 && $haut_mini_max != 0 )
			$img_dst = $this->redimensionner( $img_src, $larg_mini_max, $haut_mini_max );
		else $img_dst = $img_src;		
		
		// Si un second parametre est indique a la fonction ImageJpeg, la miniature est sauvegardee mais elle ne sera pas affichee. Ex : ImageJpeg( $img_src, './miniatures/mini.jpg');
		switch( $type )
		{
			case 'image/pjpeg':
			case 'image/jpeg':
				// ecriture de la miniature au format jpeg
				ImageJpeg( $img_dst, $destFile);
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
	 * @param	[STRING]	$sDir		Chemin du dossier à parcourir.
	 * @param	[STRING]	$sMode		Modes de parcours du dossier :
	 * 										'DOSSIERS_SEULEMENT'	=> retourne uniquement les dossiers.
	 * 										'FICHIERS_SEULEMENT'	=> retourne uniquement les fichiers.
	 * 										'TOUT'					=> retourne tous les éléments (dossiers ET fichiers).
	 * @return	[MIXED]					FALSE en cas d'erreur, tableau des éléments lus sinon.
	*/
	function advScanDir( $sDir, $sMode, $aFiltres = array() )
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
						$aItemsFile[] = $sItem;
					
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
				case 'DOSSIERS_SEULEMENT' :
						return $aItemsDir;
					break;
				
				case 'FICHIERS_SEULEMENT' :
						return $aItemsFile;
					break;
				
				case  'TOUT' :
					return array( 'dir' => $aItemsDir, 'file' => $aItemsFile );
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
					$pages[] = '&hellip;';
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
					$pages[] = '&hellip;';
	
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
	 * @return	[STRING]	Retourne $sChainePrincipale tronquée.
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
	
}

?>