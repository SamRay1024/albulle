<?php

///////////////////////////////
// LICENCE
///////////////////////////////
//
// © DUCARRE Cedric, Bubulles Creations, (09/05/2005) 
// 
// webmaster@jebulle.net
// http://jebulle.net
// 
// Ce logiciel est une bibliothèque de fonctions utilitaires diverses.
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
//	INFOS FICHIER
///////////////////////////////
//
// Fichier : util.class.php
// Creation : 06/07/2004
// Auteur : SamRay1024
// Bubulles Creations - http://jebulle.net
//
///////////////////////////////
//
// DESCRIPTION
// > contient differentes methodes inclassables qui servent d'utilitaires
//
///////////////////////////////

class Util {

	//
	// redimensionner une image
	// 
	// >> Parametres d'entree :
	// > $img_src : RESOURCE / identifiant de l'image a redimensionner
	// > $larg_mini_max : INTEGER / largeur max de la redimension
	// > $haut_mini_max : INTEGER / hauteur max de la redimension
	//
	// >> Parametre de sortie : RESOURCE / identifiant de l'image redimensionnee
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

	//
	// traitement d'une image postee par formulaire pour ecriture dans un dossier
	//
	// Adaptee de http://lecyber.net
	//
	// DESCRIPTION
	// > cette methode permet de traiter une image envoyee par un formulaire. L'image est redimensionnee aux dimensions
	// > souhaitees (uniquement si elle est plus grande que les dimensions demandees), ecrite dans le dossier demande
	// > et son nom de fichier peut etre prefixe.
	// 
	// >> Parametre d'entree :
	// > $type : STRING / type MIME de l'image envoyee (ie : 'image/gif', 'image.jpg', ...)
	// > $srcFile : STRING / chemin d'acces complet a l'image que l'on souhaite redimensionner
	// > $destFile : STRING / repertoire de destination de stockage de l'image redimensionnee
	// > [$larg_mini_max] : INTEGER / largeur max de la miniature
	// > [$haut_mini_max] : INTEGER / hauteur max de la miniature
	// > [$prefixe] : STRING / si ce champ est indique, on genere une miniature prefixe de la chaine que contient cette variable
	// >				sinon, on ecrase le fichier d'entree si le dossier de destination est le meme que celui de l'image originale
	// 	
	// >> Parametre de sortie :
	// > renvoie le chemin d'acces a la miniature qui vient d'etre generee
	function processImgFile( $type, $srcFile, $destFile, $larg_mini_max = 0, $haut_mini_max = 0, $prefix = '' )
	{
		
		// Recuperation des infos du fichier de destination
		$dirDestFile		= dirname( $destFile );			// chemin d'acces
		$nameDestFile	= basename( $destFile);			// nom du fichier
		
		// on reecrit le fichier de destination avec le prefixe et le chemin complet
		$destFile = $dirDestFile.'/'.$prefix.$nameDestFile;		
		
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

	//
	// traitement d'une image pour la redimensionner
	//
	// Adaptee de http://lecyber.net
	//
	// DESCRIPTION
	// > Cette methode ne travaille que sur le contenu binaire d'une image.
 	// > Pour redimensionner une image binaire, il faut cracher l'image sur le disque pour travailler sur ce fichier
	// > temporaire.
	// 
	// >> Parametres d'entree :
	// > $type : STRING / type MIME de l'image envoyee (ie : 'image/gif', 'image.jpg', ...)
	// > $srcBin : BINARY / contenu binaire de l'image
	// > $nom : STRING / nom du fichier temporaire qui permettra la redimension
	// > $larg_mini_max : INTEGER / largeur max de la miniature
	// > $haut_mini_max : INTEGER / hauteur max de la miniature
	// 	
	// >> Parametre de sortie : aucun
	function miniature( $type, $srcBin, $nom, $larg_mini_max, $haut_mini_max )
	{
		
		$temporaire = CONT_PATH.$nom;
		
		// ecriture d'un fichier temporaire a partir de la donnee binaire
		$handle = fopen( $temporaire, 'w' );
		fwrite( $handle, $srcBin );
		fclose( $handle );
		
		// On cree une image correspondant a l'image source
		switch( $type )
		{
			case 'image/pjpeg':
			case 'image/jpeg':
				$img_src = ImageCreateFromJpeg( $temporaire );
				break;
			
			case 'image/x-png':
			case 'image/png':
				$img_src = ImageCreateFromPng( $temporaire );
				break;
				
			case 'image/gif':
				$img_src = ImageCreateFromGif( $temporaire );
				break;
		}			
		
		$img_dst = $this->redimensionner( $img_src, $larg_mini_max, $haut_mini_max );	
		
		// Si un second parametre est indique a la fonction ImageJpeg, la miniature est sauvegardee mais elle ne sera pas affichee. Ex : ImageJpeg( $img_src, './miniatures/mini.jpg');
		Header ('Content-type: $type');
		switch ( $type )
		{
			case 'image/pjpeg':
			case 'image/jpeg':
				// Envoie l'image vers le navigateur.
				ImageJpeg( $img_dst );
				break;
				
			case 'image/x-png':
			case 'image/png':
				ImagePng( $img_dst );
				break;
				
			case 'image/gif':
				ImageGif( $img_dst );
				break;	
		}
		
		// On detruit l'image afin de liberer la memoire
		ImageDestroy( $img_dst );
		ImageDestroy( $img_src );
				
		// on supprime le fichier temporaire
		unlink( $temporaire );
	}

	function afficheArray( $array )
	{
		echo '<pre>';
		print_r( $array );
		echo '</pre>';
	}


	//
	// generation d'une entete de page HTML
	//
	// DESCRIPTION
	// > genere l'entete d'un fichier HTML jusqu'a la balise ouvrante <body>.
	// > Indique le fichier css ainsi que le titre de la page
	//
	// >> Parametre d'entree :
	// > $cheminTpl : STRING / chemin du template utilise
	// > $nomTpl : STRING / nom du template utilise
	// > $titre : STRING / titre de la page parsee
	//
	// >> Parametre de sortie : aucun
	function enteteHTML( $cheminTpl, $nomTpl, $titre )
	{
		
		$tpl = new template( $cheminTpl );
		$tpl->set_file( 'entete', 'index_header.html' );
		
		$tpl->set_var( array(
			'CHEMIN_TEMPLATE'	=> $cheminTpl,
			'NOM_TEMPLATE'			=> $nomTpl,
			'TITRE_PAGE'				=> $titre
			) );
			
		$tpl->pparse( 'affichage', 'entete' );
	
	}
	
	//
	// Generation d'un pied de page HTML
	//
	// DESCRIPTION
	// > genere la fin d'une page HTML cad les balises fermante </body> et </html>.
	// > Affiche un texte d'informations avant de fermer la page (copyright, temps d'execution, ...)
	//
	// >> Parametre d'entree :
	// > $cheminTpl : STRING / chemin du template utilise
	// > $infos : STRING / texte libre a afficher en bas de page
	//
	// >> Parametre de sortie : aucun
	function piedHTML( $cheminTpl, $infos )
	{
		
		$tpl = new template( $cheminTpl );
		$tpl->set_file( 'pied', 'index_footer.html' );
		
		$tpl->set_var( 'INFOS', $infos  );
			
		$tpl->pparse( 'affichage', 'pied' );		
			
	}

	//
	// creation d'une balise <style> a partir d'un fichier css
	//
	// DESCRIPTION :
	// > ouvre un fichier css pour le lire et concatener une balise <style>...</style>
	//
	// >> Parametre d'entree :
	// > $chemin : STRING / chemin d'acces au fichier
	//
	// >> Parametre de sortie : STRING / chaine complete avec la balise CSS
	function cssToHTML( $chemin )
	{
		
		// ouverture du fichier
		$file			= fopen( $chemin, 'r' );
		$contenu	= fread( $file, filesize( $chemin ) );
		
		return "<style>\n<!--\n$contenu\n-->\n</style>";
			
	}
	
	//
	// prepare un blob pour l'enregistrement en base
	//
	// DESCRIPTION
	// >> Parametre d'entree :
	// > $file : STRING / nom du fichier a preparer
	//
	// >> Parametre de sortie : STRING / la chaine preparee	
	function prepareBlob( $file ) { 
		$blob = file_get_contents($file);
		$blob = addslashes($blob);
		$blob = addcslashes($blob, "");
		return $blob;
	}
	
	//
	// permet de fermer certaines balises HTML dans un texte qui sont restees ouvertes
	//
	// DESCRIPTION
	// >> Parametre d'entree :
	// > $texte : STRING / texte dans lequel chercher les balises non fermees
	//
	// >> Parametre de sortie : STRING / le texte avec ses balises fermees	
	function fermerBalises( $texte )
	{
			
		// expressions regulieres des balises ouvrantes
		$expregOuvrantes = array(
			"/\<strong\>/",
			"/\<em\>/",
			"/\<u\>/",
			"/\<strike\>/",
			"/\<a .*\>/",
			"/\<font .*\>/",
			"/\<span .*\>/" );

		// expressions regulieres des balises fermantes respectivement aux balises ouvertes
		$expregFermantes = array(
			"/\<\/strong\>/",
			"/\<\/em\>/",
			"/\<\/u\>/",
			"/\<\/strike\>/",
			"/\<\/a\>/",
			"/\<\/font\>/",
			"/\<\/span\>/" );
			
		// balises fermantes
		$balisesFermantes = array(
			"</strong>",
			"</em>",
			"</u>",
			"</strike>",
			"</a>",
			"</font>",
			"</span>" );
						
		// pour chaque balise ouvrante
		foreach( $expregOuvrantes as $i => $pattern )
		{
				
			// on regarde combien de fois elle apparait
			$nbOuvrantes = preg_match_all( $pattern, $texte, $inutile = array() );
			
			// si des occurences ont �t� trouv�es
			if( $nbOuvrantes != false )
			{
				
				// on regarde combien de fois la balise fermante correspondante apparait
				$nbFermantes = preg_match_all( $expregFermantes[$i], $texte, $inutile = array() );
			
				// si ce nombre n'est pas egal au nombre de balises ouvrantes c'est qu'il faut fermer la balise
				if( $nbOuvrantes != $nbFermantes )
					// on rajoute autant de balises fermantes qu'il faut
					for( $j = 0 ; $j < $nbOuvrantes - $nbFermantes ; $j++ )
						$texte .= $balisesFermantes[$i];
			}
			
		}
			
		return $texte;
				
	}
	
	//
	// liste le contenu d'un dossier
	//
	// DESCRIPTION
	// > La methode recoit un chemin de dossier, ouvre ce dossier et en donne la liste des elements ; ces elements peuvent etre
	// > tout le contenu, seulement les dossiers ou seulement les fichiers.
	// > La methode retourne un tableau contenant les elements du dossiers ou FALSE si le chemin est incorrect.
	// 
	// >> Parametres d'entree :
	// > $dir : STRING / Chemin d'acces au dossier
	// > $mode : STRING / codes des differentes methodes de recherche
	// >				'DOSSIERS_SEULEMENT'	=> donne uniquement les dossiers
	// >				'FICHIERS_SEULEMENT'		=> donne uniquement les fichiers
	// >				'TOUT'								=> donne tous les elements (dossiers ET fichiers)
	//
	// >> Parametre de sortie : ARRAY / retourne un tableau contenant les elements demandes OU False en cas d'erreur
	function advScanDir( $dir, $mode )
	{
		
		// creation du tableau qui va contenir les elements du dossier
		$items = array();
		
		// ajout du slash a la fin du chemin s'il n'y est pas
		if( !preg_match( "/^.*\/$/", $dir ) ) $dir .= '/';
		
		// Ouverture du repertoire demande
		$handle = opendir( $dir );
		
		// si pas d'erreur d'ouverture du dossier on lance le scan
		if( $handle != false )
		{
			
			// Parcours du repertoire
			while( $item = readdir($handle) )
			{
				if($item != '.' && $item != '..')
				{
					// selon le mode choisi
					switch( $mode )
					{
						case 'DOSSIERS_SEULEMENT' :
							if( is_dir( $dir.$item ) )
								$items[] = $item;
							break;
						
						case 'FICHIERS_SEULEMENT' :
							if( !is_dir( $dir.$item ) )
								$items[] = $item;
							break;
						
						case  'TOUT' :
							$items[] = $item;
					
					}
				}
			}
			
			// Fermeture du repertoire
			closedir($handle);
			
			return $items;
			
		}
		else return false;
		
	}

	//
	// suppression avancee d'un repertoire
	//
	// DESCRIPTION
	// > permet de supprimer un dossier meme s'il n'est pas vide. Le script explore tout le contenu et le supprime
	// > avant de supprimer le dossier.
	//
	// >> Parametres d'entree :
	// > $dir : STRING / chemin d'acces au dossier a supprimer
	//
	// >> Parametre de sortie : BOOLEEN / TRUE si dossier supprime, FALSE en cas d'erreur
	function advRmDir( $dir )
	{
		
		// ajout du slash a la fin du chemin s'il n'y est pas
		if( !preg_match( "/^.*\/$/", $dir ) ) $dir .= '/';
		
		// Ouverture du repertoire demande
		$handle = @opendir( $dir );
		
		// si pas d'erreur d'ouverture du dossier on lance le scan
		if( $handle != false )
		{
			
			// Parcours du repertoire
			while( $item = readdir($handle) )
			{
				if($item != "." && $item != "..")
				{
					if( is_dir( $dir.$item ) )
						$this->advRmDir( $dir.$item );
					else unlink( $dir.$item );
				}
			}
			
			// Fermeture du repertoire
			closedir($handle);
			
			// suppression du repertoire
			$res = rmdir( $dir );
			
		}
		else $res = false;
		
		return $res;
		
	}

}

?>