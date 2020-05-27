<?php

////////////////////////////////////////
// Encodage du fichier : UTF-8
// Utilisation des tabulations : Oui
// 1 tabulation = 4 caractères
// Fins de lignes = LF (Unix)
////////////////////////////////////////

/**
 * @name html.php
 * @author SamRay1024
 * @copyright Bubulles Creations
 * @link http://jebulle.net
 * @since 11/06/2006
 * @version 24/05/2010
 */

if( !defined( '_JB_INCLUDE_AUTH' ) ) {
	header( 'Content-type: text/html; charset=utf-8' );
	exit( 'Vous n\'êtes pas autorisé à afficher cette page.' );
}

try {

// {{{ INITIALISATIONS

	$oRenduPage = new JbParser( $_JB_AL_VARS['s_acces_theme'] .'html/', 'index' );
	
	// Lightbox ?
	$bLightbox = (
		$_JB_AL_VARS['b_mode_diaporama'] === false &&
		JB_AL_OUVERTURE_JS === true &&
		JB_AL_OUVERTURE_LBX === true
	) || (
		$_JB_AL_VARS['b_mode_diaporama'] === true &&
		JB_AL_OUVERTURE_JS_DIAPO === true &&
		JB_AL_OUVERTURE_LBX_DIAPO === true
	);
		
	// Popup JS ?
	$bPopupJs = !$bLightbox && (JB_AL_OUVERTURE_JS === true || JB_AL_OUVERTURE_JS_DIAPO === true);
		
	// Défilement auto ?
	$bDefilementAuto = 
		$_JB_AL_VARS['b_mode_diaporama'] === true &&
		$_JB_AL_VARS['b_defilement_auto'] === true;

	// Accueil ?
	$bAccueil = isset($_JB_AL_VARS['accueil']);
	
	// Nombre de miniatures dans la galerie
	$iNbMiniatures = sizeof($_PHOTOS);
	
	// Le panier contient-il des images ?
	$bPanierRempli = ($_JB_AL_VARS['a_panier']['i_nb_fichiers'] > 0);

// }}}
// {{{ PSEUDOS-VARIABLES

	// Globales
	$oRenduPage->assignVar(array(
		'chemin_theme'		=> $_JB_AL_VARS['s_acces_theme'],
		'chemin_root'		=> JB_AL_ROOT,
		'lien_retour_site'	=> (JB_AL_HOME_HREF !== '' && JB_AL_HOME_TEXTE !== '' ? '<a href="'.JB_AL_HOME_HREF.'">'.JB_AL_HOME_TEXTE."</a> |\n" : ''),
		'rep_courant'		=> $_JB_AL_VARS['s_rep_courant'],
		'version'			=> (JB_AL_AFFICHER_VERSION === true ? ' v'.$_JB_AL_VARS['s_version'] : ''),
		'fil_ariane'		=> $_JB_AL_VARS['s_navigation']
	));
	
// }}}
// {{{ BLOCS ET BOUCLES DEVANT ETRE INITIALISES
	
	$oRenduPage->assignBlock('accueil',				$bAccueil);
	$oRenduPage->assignBlock('contenu_texte',		false);
	$oRenduPage->assignBlock('dossier_vide',		($iNbMiniatures <= 0));
	$oRenduPage->assignBlock('mode_galerie',		!$_JB_AL_VARS['b_mode_diaporama']);
	$oRenduPage->assignBlock('diaporama',			$_JB_AL_VARS['b_mode_diaporama'] && !$bAccueil);
	$oRenduPage->assignBlock('diapo_vide', 			empty($_JB_AL_VARS['s_diapo_courante']));
	$oRenduPage->assignBlock('plusieurs_diapos',	($_JB_AL_VARS['s_url_img_precedente'] !== '' || $_JB_AL_VARS['s_url_img_suivante'] !== ''));
	$oRenduPage->assignBlock('exif',				false);
	$oRenduPage->assignBlock('non_integre',			!JB_AL_INTEGRATION_SITE);
	$oRenduPage->assignBlock('sous_dossiers',		false);
	
	$oRenduPage->assignLoop('vignettes');
	
// }}}
// {{{ TRAITEMENT HEADER HTML
	
	if( JB_AL_INTEGRATION_SITE === false ) {
	
		$oRenduPage->assignBlock('lightbox',			$bLightbox);
		$oRenduPage->assignBlock('popup',				$bPopupJs);
		$oRenduPage->assignBlock('defilement_auto', 	$bDefilementAuto);
		
		$oRenduPage->assignVar(array(
			'charset'		=> (JB_AL_SORTIE_ISO ? 'ISO-8859-1' : 'UTF-8'),
			'titre_page'	=> $_JB_AL_VARS['s_titre_meta']
		));
		
		// Défilement auto
		!$bDefilementAuto or $oRenduPage->assignVar(array(
			'intervalle_temps'		=> $_JB_AL_VARS['i_intervalle_tps'],
			'url_image_suivante'	=> $_JB_AL_VARS['s_url_img_suivante']
		));
	}
	
	// Rendu des metas si intégration à un site active
	else {
	
		$oRenduMetas = new JbParser( $_JB_AL_VARS['s_acces_theme'] .'html/', 'metas' );
		
		$oRenduMetas->assignBlock('accueil',			$bAccueil);
		$oRenduMetas->assignBlock('lightbox',			$bLightbox);
		$oRenduMetas->assignBlock('popup',				$bPopupJs);
		$oRenduMetas->assignBlock('defilement_auto', 	$bDefilementAuto);
		
		$oRenduMetas->assignVar(array(
			'chemin_theme'		=> $_JB_AL_VARS['s_acces_theme'],
			'chemin_root'		=> JB_AL_ROOT,
			'rep_courant'		=> $_JB_AL_VARS['s_rep_courant']
		));
		
		// Défilement auto
		!$bDefilementAuto or $oRenduMetas->assignVar(array(
			'intervalle_temps'		=> $_JB_AL_VARS['i_intervalle_tps'],
			'url_image_suivante'	=> $_JB_AL_VARS['s_url_img_suivante']
		));
		
		$sAlbulleMetas = trim($oRenduMetas->parse());
		unset($oRenduMetas);
	}
	
// }}}
// {{{ TRAITEMENT ENTETE PAGE
	
	$oRenduPage->assignBlock('entete', JB_AL_AFFICHER_ENTETE);

	if( JB_AL_AFFICHER_ENTETE === true )
		$oRenduPage->assignVar(array(
			'titre_galerie'			=> JB_AL_TITRE_GALERIE,
			'sous_titre_galerie'	=> JB_AL_SOUS_TITRE_GALERIE
		));
		
// }}}
// {{{ TRAITEMENT MENU GALERIE
	
	$oRenduPage->assignBlock('menu_galerie', !$bAccueil);
	
	if( $bAccueil === false )
		$oRenduPage->assignVar(array(
			'lien_mode_affichage'	=> $_JB_AL_VARS['s_lien_mode_affichage'],
			'texte_mode_affichage'	=> $_JB_AL_VARS['s_texte_mode_affichage'],
			'panier_tout_ajouter'	=> ( $iNbMiniatures > 0 && !$_JB_AL_VARS['b_voir_panier'] ? '<a href="'.$_JB_AL_VARS['s_lien_panier_tout_ajouter'].'" class="bouton" title="Ajouter toutes les images de la page"><span class="tout"></span></a>' : ''),
			'panier_tout_retirer'	=> ( $iNbMiniatures > 0 && !$_JB_AL_VARS['b_voir_panier'] ? '<a href="'.$_JB_AL_VARS['s_lien_panier_tout_supprimer'].'" class="bouton" title="Retirer toutes les images de la page"><span class="rien"></span></a>' : ''),
			'pagination'			=> $_JB_AL_VARS['s_pagination']
		));
		
// }}}
// {{{ TRAITEMENT CONTENU ACCUEIL
	
	if( $bAccueil ) {
	
		$oRenduPage->assignVar('contenu_texte', $_JB_AL_VARS['accueil']);
	}
	
// }}}
// {{{ TRAITEMENT CONTENU GALERIE
	
	else {
	
		// Eventuel texte associé à la galerie
		if( file_exists($_JB_AL_VARS['s_chemin_rep'] . JB_AL_FICHIER_TEXTE_DOSSIER) )
			$oRenduPage->assignVar(
				'contenu_texte',
				file_get_contents( $_JB_AL_VARS['s_chemin_rep'] . JB_AL_FICHIER_TEXTE_DOSSIER )
			);
			
		// S'il n'y a pas d'images dans le dossier
		if( $iNbMiniatures <= 0 ) {
		
			if( JB_AL_AFFICHER_TXT_VIDE || 
				(!JB_AL_AFFICHER_TXT_VIDE && !$oRenduPage->getBlockStatus('contenu_texte')) )
				$oRenduPage->assignVar('dossier_vide', true);
		}
		
		// Sinon on peut générer le contenu de la galerie
		else {
		
			// Génération des vignettes
			foreach( $_PHOTOS as $sNomPhoto => &$aPhoto ) {
			
				$aVars = array(
					// cadre div
					'classe_vignette'	=> $_JB_AL_VARS['s_classe_css_vignette'],
					'diapo_courante' 	=> (
						$_JB_AL_VARS['b_mode_diaporama'] && $sNomPhoto === $_JB_AL_VARS['s_diapo_courante']
						? ' diapo-courante' : ''
					),
				
					// lien de l'image
					'href_image'		=> $aPhoto['URL'],
					'target_blank'		=> $aPhoto['TARGET'],
					'javascript'		=> $aPhoto['JAVASCRIPT'],
					'lightbox'			=> $aPhoto['LIGHTBOX'],
					'chemin_miniature'	=> $aPhoto['URL_MINIATURE'],
					'classe_miniature'	=> $aPhoto['CLASSE_CSS'],
					'alt_image'			=> $aPhoto['ALT'],
					
					// infos de l'image
					'nom'				=> Util::tronquerChaine($aPhoto['NOM']),
					'legende'			=> $aPhoto['LEGENDE'],
					'dimensions'		=> $aPhoto['DIMENSIONS'],
					'poids'				=> $aPhoto['POIDS'],
					
					// Données EXIF
					'exif_marque'		=> $aPhoto['EXIF']['Make'],
					'exif_modele'		=> $aPhoto['EXIF']['Model'],
					'exif_date'			=> date(JB_AL_DATE_FORMAT, (int) $aPhoto['EXIF']['DateTimestamp']),
					'exif_exposition'	=> $aPhoto['EXIF']['ExposureTime'],
					'exif_sensibilite'	=> $aPhoto['EXIF']['ISOSpeedRatings'],
					'exif_focale'		=> $aPhoto['EXIF']['FocalLength'],
					'exif_ouverture'	=> $aPhoto['EXIF']['ApertureFNumber']
				);
				
				// Si panier actif
				if(JB_AL_PANIER_ACTIF === true)
				{
					$sLienPanierPuce 	= $aPhoto['PANIER']['MODE'] == 'ajout' ? 'puce-ajout' : 'puce-retrait';
					$sLienPanierTitle	= $aPhoto['PANIER']['MODE'] == 'ajout' ? 'Ajouter l\'image' : 'Retirer l\'image';

					$aVars['puce_ajout_panier'] =
						'<a href="'. $aPhoto['PANIER']['URL'] .
						'" class="'. $sLienPanierPuce .'" title="'. $sLienPanierTitle .'">+</a>';
				}
			
				$oRenduPage->assignLoopVars('vignettes', $aVars);
			}
			
			// Génération de la diapositive, en mode diaporama uniquement
			if($_JB_AL_VARS['b_mode_diaporama'] === true) {
				
				// Si une diapo est définie
				if( !empty($_JB_AL_VARS['s_diapo_courante']) ) {
				
					if( $oRenduPage->getBlockStatus('plusieurs_diapos') )
						$oRenduPage->assignVar(array(
							'bouton_precedente'	=> (!empty($_JB_AL_VARS['s_url_img_precedente']) ? '<a href="'.$_JB_AL_VARS['s_url_img_precedente'].'" class="precedente" title="Précedente"><span></span></a>' : ''),
							'bouton_suivante'	=> (!empty($_JB_AL_VARS['s_url_img_suivante']) ? '<a href="'.$_JB_AL_VARS['s_url_img_suivante'].'" class="suivante" title="Suivante"><span></span></a>' : '')
						));

					// variables de la diapositive
					$oRenduPage->assignVar(array(
					
						// lien de l'image
						'href_image'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['URL_DIAPO'],
						'target_blank'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['TARGET'],
						'javascript'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['JAVASCRIPT'],
						'lightbox'			=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['LIGHTBOX'],

						// L'image de la diapositive
						'source_diapo'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['URL_DIAPO'],
						'alt_diapo'			=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['ALT'],

						// La fiche info de la diapo
						'nom_photo'			=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['NOM'],
						'legende'			=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['LEGENDE'],
							
						'dimensions_photo'	=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['DIMENSIONS'],
						'type_mime'			=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['TYPE_MIME'],
						'poids_photo'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['POIDS'],
						
						// Données EXIF
						'exif_marque'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF']['Make'],
						'exif_modele'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF']['Model'],
						'exif_date'			=> date(JB_AL_DATE_FORMAT, $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF']['DateTimestamp']),
						'exif_exposition'	=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF']['ExposureTime'],
						'exif_sensibilite'	=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF']['ISOSpeedRatings'],
						'exif_focale'		=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF']['FocalLength'],
						'exif_ouverture'	=> $_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF']['ApertureFNumber'],
						
						// Le formulaire de défilement automatique
						'form_defilement_action'	=> htmlentities($_SERVER['REQUEST_URI']),
						'intervalle_temps'			=> $_JB_AL_VARS['i_intervalle_tps'],
						'submit_name'				=> $_JB_AL_VARS['s_defilement_submit_name'],
						'submit_value'				=> $_JB_AL_VARS['s_defilement_submit_value']
					));

					// Bloc des données EXIF
					$oRenduPage->assignBlock(
						'exif',
						!empty($_PHOTOS[$_JB_AL_VARS['s_diapo_courante']]['EXIF'])
					);
				}
			}
		}
		
		// Génération du rappel des sous-dossiers si nécessaire
		$oRenduPage->assignBlock(
			'sous_dossiers', 
			JB_AL_RAPPELER_SOUS_DOSSIERS === true &&
			!empty($_JB_AL_VARS['s_rappel_sous_dossiers']) && 
			(( JB_AL_RAPPELER_QUE_SI_VIDE === true && $iNbMiniatures === 0 ) ||
			JB_AL_RAPPELER_QUE_SI_VIDE === false )
		);
			
		if( $oRenduPage->getBlockStatus('sous_dossiers') )
			$oRenduPage->assignVar('rappel_sous_dossiers', $_JB_AL_VARS['s_rappel_sous_dossiers']);
	}
	
// }}}
// {{{ TRAITEMENT BARRE LATERALE
		
	// Arborescence
	$oRenduPage->assignVar('arborescence', $_JB_AL_VARS['s_arborescence']);
		
	// Panier
	$oRenduPage->assignBlock('panier_actif', JB_AL_PANIER_ACTIF);

	if( JB_AL_PANIER_ACTIF === true ) {
	
		// Construction phrase capacité panier
		$sCapacitePanier = 'illimitée';
		if( JB_AL_PANIER_CAPACITE_MAX > 0 && JB_AL_PANIER_POIDS_MAX > 0 )	$sCapacitePanier = JB_AL_PANIER_CAPACITE_MAX.' fichiers ou ~'.JB_AL_PANIER_POIDS_MAX.' Mo';
		if( JB_AL_PANIER_CAPACITE_MAX === 0 && JB_AL_PANIER_POIDS_MAX > 0 )	$sCapacitePanier = '~'.JB_AL_PANIER_POIDS_MAX.' Mo';
		if( JB_AL_PANIER_CAPACITE_MAX > 0 && JB_AL_PANIER_POIDS_MAX === 0 )	$sCapacitePanier = JB_AL_PANIER_CAPACITE_MAX.' fichiers';

		$oRenduPage->assignBlock('panier_rempli', $bPanierRempli);
		
		if( $bPanierRempli )
			$oRenduPage->assignVar(array(
				'panier_url_telecharcher'	=> $_JB_AL_VARS['a_menu_panier']['s_url_download'],
				'panier_url_voir'			=> $_JB_AL_VARS['a_menu_panier']['s_url_voir'],
				'panier_url_vider'			=> $_JB_AL_VARS['a_menu_panier']['s_url_vider']
			));

		$oRenduPage->assignVar(array(
			'panier_capacite'			=> $sCapacitePanier,
			'nombre_fichiers_panier'	=> $_JB_AL_VARS['a_panier']['b_plein'] ? '<span class="plein">'.$_JB_AL_VARS['a_panier']['i_nb_fichiers'].' (Panier plein)</span>' : $_JB_AL_VARS['a_panier']['i_nb_fichiers'],
			'poids_estime'				=> $_JB_AL_VARS['a_panier']['s_poids_estime']
		));
	}

// }}}
// {{{ GENERATION & RETOUR
	
	return trim($oRenduPage->parse());
	
// }}}
}
catch(JbError $oErr) { return $oErr->__toString(); }