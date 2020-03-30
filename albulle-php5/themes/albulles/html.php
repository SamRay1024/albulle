<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
        
		<link rel="stylesheet" href="./<?php echo $sAccesTheme; ?>style.css" type="text/css" />
		
		<title>.: AlBulles :. .: <?php echo $sHeadTitre; ?> :.</title>
 	
	</head>
	
	<body>
	
		<!-- DEBUT droite -->
		<div class="droite">
			
			<h1><?php echo $sBodyTitre; ?></h1>
			
			<?php
			if ( empty($sRep) )
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
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pageAccueil">Accueil</a>
				<?php echo $sNavigation; ?>
			</div>
			<!-- FIN barre de navigation -->
			
			<?php
			$iNbMiniatures = sizeof( $aMiniatures );
			if( $iNbMiniatures === 0 )
			{
			?>
			<div class="accueil"><p class="puceNoPhoto">Il n'y a pas de photos dans ce dossier.</p></div>
			<?php
			}
			else
			{
			for ( $i = 0 ; $i < $iNbMiniatures ; $i++ ) { ?>
			<!-- DEBUT une miniature -->
			<div class="vignette">
			
				<?php echo $aMiniatures[$i]['LIEN_PHOTO']; ?>
				
				<span class="infosImg">
					<?php
					echo $aMiniatures[$i]['DIM_PHOTO'].'<br />';
					echo $aMiniatures[$i]['SIZE_PHOTO'];
					?>
				</span>
				
				<span class="puce"><?php echo $aMiniatures[$i]['AJOUT_PANIER']; ?></span>
				
			</div>
			<!-- FIN une miniature -->
			
			<?php
			} // for
			} // if 2
			} // if 1
			?>
			
			<div class="spacer"></div>
		
		</div>
		<!-- FIN droite -->
	
		<!-- DEBUT gauche -->
		<div class="gauche">
		
			<!-- DEBUT liste dossiers photos -->
			<div class="dossiers">
							
				<ul class="menu">
					<?php echo $sLiensDossiersPhotos; ?>
				</ul>
				
				<div class="spacer"></div>
			
			</div>
			<!-- FIN liste dossiers photos -->
			
			<!-- DEBUT barre de gestion du panier -->
			<div class="panier">
				
				<p>Fichiers dans le panier : <br /><strong><?php echo $sNbFichiersDansLePanier; ?></strong></p>
				
				<?php echo $sMenuPanier,"\n"; ?>
				
				<br />
				
				<div class="center"><?php echo $sPanierLienToutAjouter,"\n",$sPanierLienToutSupprimer,"\n"; ?></div>
				
				<div class="spacer"></div>
			
			</div>
			<!-- FIN barre de gestion du panier -->
			
			<!-- DEBUT Copyright -->
			<div class="copyright">
				
				<!-- Vous avez ici deux liens pour le copyright : un avec une image, un en texte.
					Merci de laisser au moins un lien des deux ;-) -->
				<a href="http://jebulle.net/index.php?rubrique=albulles" title="T&eacute;l&eacute;chargez AlBulles">
					<img src="./<?php echo $sAccesTheme; ?>images/AlBulles_80x15.png" width="80" height="15" title="T&eacute;l&eacute;chargez AlBulles" alt="T&eacute;l&eacute;chargez AlBulles" />
				</a>
				<br />
				AlBulles<?php echo $sVersion; ?> &copy; <a href="http://jebulle.net">Bubulles Creations</a>
			
			</div>
			<!-- FIN Copyright -->
		
		</div>
		<!-- FIN gauche -->
	
	</body>
	
</html>