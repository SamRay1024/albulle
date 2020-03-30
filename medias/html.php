<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://wwww.w3.org/1999/xhtml">
	<head>
		
		<link rel="stylesheet" href="./medias/style.css" type="text/css" />
		
		<title>.: AlBulles :. .: <?php echo $sHeadTitre; ?> :.</title>
 	
	</head>
	
	<body>
	
		<!-- DEBUT droite -->
		<div class="droite">
			
			<h1><?php echo $sBodyTitre; ?></h1>
			
			<?php
			if ( empty( $sRep ) )
			{
			?>
			
			<div class="accueil">
			
				<strong>Bienvenue dans AlBulles !</strong>
				
				<br /><br />
				
				AlBulles est un programme de galerie photos.<br /><br />
				
				Ce texte est un exemple. Vous pouvez le remplacer par ce que vous souhaitez. Pour consulter les photos
				disponibles, utilisez la liste des dossiers.<br /><br />
				
				Un panier virtuel est &agrave; votre disposition. Il vous suffit d'ajouter les images &agrave; votre panier pour
				ensuite les t&eacute;l&eacute;charger sous forme d'archive zip.
				
			</div>
			
			<?php
			}
			else {
			?>
			
			<!-- DEBUT barre de navigation -->
			<div class="navigation">
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="page">Accueil</a>
				Pages : <?php echo $sNavigation; ?>
			</div>
			<!-- FIN barre de navigation -->
			
			<?php
			for ( $i = 0 ; $i < sizeof( $aMiniatures ) ; $i++ ) { ?>
			<!-- DEBUT une miniature -->
			<div class="miniature">
			
				<?php echo $aMiniatures[$i]['LIEN_PHOTO']; ?>
				
				<div class="infosImg">
					<?php
					echo $aMiniatures[$i]['DIM_PHOTO'].'<br />';
					echo $aMiniatures[$i]['SIZE_PHOTO'];
					?>
				</div>
				
				<div class="puce"><?php echo $aMiniatures[$i]['AJOUT_PANIER']; ?></div>
				
			</div>
			<!-- FIN une miniature -->
			
			<?php
			} // for
			} // if
			?>
			
			<div class="spacer"></div>
		
		</div>
		<!-- FIN droite -->
	
		<!-- DEBUT gauche -->
		<div class="gauche">
		
			<!-- DEBUT liste dossiers photos -->
			<div class="dossiers">
			
				<img src="./medias/images/albulles_dossiers_dispos.jpg" alt="Dossiers des photos" />
				
				<ul class="liens">
					<?php echo $sLiensDossiersPhotos; ?>
				</ul>
				
				<div class="spacer"></div>
			
			</div>
			<!-- FIN liste dossiers photos -->
			
			<!-- DEBUT barre de gestion du panier -->
			<div class="panier">
			
				<img src="./medias/images/albulles_panier.jpg" alt="Panier" /><br /><br />
				
				Fichiers dans le panier : <br /><strong><?php echo $sNbFichiersDansLePanier; ?></strong>
				
				<br /><br />
				
				<div class="actions">
					<?php echo $sPanierLienArchive,"\n",$sPanierLienVider,"\n"; ?>
				</div>
				
				<br />
				
				<?php echo $sPanierLienToutAjouter,"\n",$sPanierLienToutSupprimer,"\n"; ?>
			
			</div>
			<!-- FIN barre de gestion du panier -->
			
			<!-- DEBUT Copyright -->
			<div class="copyright">
			
				<a href="http://www.mozilla.eu.org/fr/products/firefox/" title="Ce site s'affiche mieux avec un navigateur respectant les normes">
					<img src="./medias/images/firefox_80x15.png" width="80" height="15" title="Ce site s'affiche mieux avec un navigateur respectant les normes" alt="T&eacute;l&eacute;chargez FireFox" />
				</a>
				
				<br />
				
				<a href="http://jebulle.net/index.php?rubrique=albulles" title="T&eacute;l&eacute;chargez AlBulles">
					<img src="./medias/images/AlBulles_80x15.png" width="80" height="15" title="T&eacute;l&eacute;chargez AlBulles" alt="T&eacute;l&eacute;chargez AlBulles" />
				</a>
				
				<br />
				
				AlBulles <?php echo $sVersion; ?> &copy; <a href="http://jebulle.net">Bubulles Creations</a> - 2005
			
			</div>
			<!-- FIN Copyright -->
		
		</div>
		<!-- FIN gauche -->
	
	</body>
	
</html>