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
 * @version 0.1
 */

if( !defined( '_JB_INCLUDE_AUTH' ) ) {
	header( 'Content-type: text/html; charset=utf-8' );
	exit( 'Vous n\'êtes pas autorisé à afficher cette page.' );
}

// ================
// INITIALISATIONS
//

// Ce tableau associatif contiendra l'intégralité des éléments à remplacer dans les fichiers de thèmes.
// Les clés représentent les expressions régulières qu'il faudra remplacer par les valeurs correspondantes.
$aPseudosVariables = array();
$sThmPagination = $sThmSousDossiers = '';

// ================
// ETAPE 1 : Construction des métas
//

// Si lightbox activée
if( JB_AL_OUVERTURE_JAVASCRIPT === true && JB_AL_OUVERTURE_LIGHTBOX === true)
{
	$aPseudosVariables['`<!-- SI LIGHTBOX -->\n\t\t|<!-- FINSI LIGHTBOX -->\n`'] = '';
	$aPseudosVariables['`<!-- SI POPUP -->.*<!-- FINSI POPUP -->\n`s'] = '';
}
else
{
	// Sinon, on efface la section lightbox
	$aPseudosVariables['`<!-- SI LIGHTBOX -->.*<!-- FINSI LIGHTBOX -->\n\n\t\t`s'] = '';

	// Et on regarde si l'ouverture par popup simple est activée
	if(JB_AL_OUVERTURE_JAVASCRIPT) $aPseudosVariables['`<!-- SI POPUP -->\n\t\t|<!-- FINSI POPUP -->`'] = '';
	else $aPseudosVariables['`<!-- SI POPUP -->.*<!-- FINSI POPUP -->\n`s'] = '';
}

// Si en mode diaporama >> activation defilement auto le cas échéant
if($_JB_AL_VARS['b_mode_diaporama'] && $_JB_AL_VARS['b_defilement_auto'])
{
	$aPseudosVariables['`<!-- SI DEFILEMENT_AUTO -->\n\t\t|<!-- FINSI DEFILEMENT_AUTO -->\n`'] = '';
	$aPseudosVariables['`{INTERVALLE_TEMPS}`']		= $_JB_AL_VARS['i_intervalle_tps'];
	$aPseudosVariables['`{URL_IMAGE_SUIVANTE}`']	= $_JB_AL_VARS['s_url_img_suivante'];
}
// SInon on efface la section
else $aPseudosVariables['`<!-- SI DEFILEMENT_AUTO -->.*<!-- FINSI DEFILEMENT_AUTO -->\n`s'] = '';

// Les autres pseudos-variables
$aPseudosVariables['`{CHEMIN_THEME}`']	= $_JB_AL_VARS['s_acces_theme'];
$aPseudosVariables['`{CHEMIN_ROOT}`']	= JB_AL_ROOT;

// Génération des entêtes
$sThmCssMetas = $oOutils->parser($_JB_AL_VARS['s_acces_theme'].'html/metas.thm.php', $aPseudosVariables);
$aPseudosVariables = array();

// ================
// ETAPE 2 : Construction header
//

// Si AlBulle n'est pas intégré dans un site, on génère l'entête Html
if( !JB_AL_INTEGRATION_SITE )
{
	$aPseudosVariables['`{>METAS}`']		= $sThmCssMetas;
	$aPseudosVariables['`{TITRE_PAGE}`']	= $_JB_AL_VARS['s_titre_meta'];

	$sThmHeader = $oOutils->parser( $_JB_AL_VARS['s_acces_theme'].'html/header.thm.php', $aPseudosVariables );
	$aPseudosVariables = array();
}
// Sinon on prends juste les métas pour les link vers les Css et Javascripts
else $sThmHeader = '';

// ================
// ETAPE 3 : Construction du contenu principal : soit le texte d'accueil (ou un autre texte) soit les images du dossier courant
//
$sContenuDroite = '';

// Si pas de répertoire choisi, on affiche l'accueil
if ( empty($_JB_AL_VARS['s_rep_courant']) && !$_JB_AL_VARS['b_voir_panier'] )
{
	ob_start();
	eval('require_once(JB_AL_ROOT.JB_AL_FICHIER_ACCUEIL);');
	$sPageInclue = ob_get_contents();
	ob_end_clean();

	// Lecture patron html page simple
	$sThmTexte = file_get_contents( $_JB_AL_VARS['s_acces_theme'].'html/texte.thm.php' );
	$sThmTexte = str_replace( '{CONTENU_TEXTE}', $sPageInclue, $sThmTexte );

	// Ajout du texte au contenu de droite
	$sContenuDroite .= $sThmTexte;
}

// Sinon on affiche les images
else
{
	//
	// Génération des miniatures
	//
	$iNbMiniatures = sizeof( $_MINIATURES );

	// Si pas d'images dans le dossier courant
	if( $iNbMiniatures === 0 )
		$sContenuDroite .= file_get_contents( $_JB_AL_VARS['s_acces_theme'].'html/dossier_vide.thm.php' );

	// Sinon, boucle sur les images
	else
	{
		//
		// Génération du formulaire de défilement automatique si on se trouve en mode diaporama
		//
		$sThmFormDefilement = '';

		if($_JB_AL_VARS['b_mode_diaporama'])
		{
			// Génération du formulaire pour le défilement automatique
			$aPseudosVariables['`{FORM_DEFILEMENT_ACTION}`']	= htmlentities($_SERVER['REQUEST_URI']);
			$aPseudosVariables['`{INTERVALLE_TEMPS}`']			= $_JB_AL_VARS['i_intervalle_tps'];
			$aPseudosVariables['`{SUBMIT_NAME}`']				= $_JB_AL_VARS['s_defilement_submit_name'];
			$aPseudosVariables['`{SUBMIT_VALUE}`']				= $_JB_AL_VARS['s_defilement_submit_value'];

			$sThmFormDefilement = $oOutils->parser( $_JB_AL_VARS['s_acces_theme'].'html/form_defilement_auto.thm.php', $aPseudosVariables );
			$aPseudosVariables = array();
		}

		//
		// Génération de la barre de pagination
		//
		$aPseudosVariables['`{LIEN_MODE_AFFICHAGE}`']		= $_JB_AL_VARS['s_lien_mode_affichage'];
		$aPseudosVariables['`{TEXTE_MODE_AFFICHAGE}`']		= $_JB_AL_VARS['s_texte_mode_affichage'];
		$aPseudosVariables['`{PANIER_TOUT_AJOUTER}`']		= $_JB_AL_VARS['s_lien_panier_tout_ajouter'];
		$aPseudosVariables['`{PANIER_TOUT_RETIRER}`']		= $_JB_AL_VARS['s_lien_panier_tout_supprimer'];
		$aPseudosVariables['`{PAGINATION}`']				= $_JB_AL_VARS['s_pagination'];

		$sThmPagination = $oOutils->parser( $_JB_AL_VARS['s_acces_theme'].'html/pagination.thm.php', $aPseudosVariables );
		$aPseudosVariables = array();

		// Génération des vignettes
		for ( $i = 0 ; $i < $iNbMiniatures ; $i++ )
		{
			// cadre div
			$aPseudosVariables[$i]['`{CLASSE_VIGNETTE}`']	= $_JB_AL_VARS['s_classe_css_vignette'];
			$aPseudosVariables[$i]['`{DIAPO_COURANTE}`'] 	= ($_JB_AL_VARS['b_mode_diaporama']) ? $_MINIATURES[$i]['DIAPO_COURANTE'] : '';

			// lien de l'image
			$aPseudosVariables[$i]['`{HREF_IMAGE}`']		= $_MINIATURES[$i]['LIEN_PHOTO']['HREF'];
			$aPseudosVariables[$i]['`{TARGET_BLANK}`']		= $_MINIATURES[$i]['LIEN_PHOTO']['TARGET'];
			$aPseudosVariables[$i]['`{LIGHTBOX}`']			= $_MINIATURES[$i]['LIEN_PHOTO']['LIGHTBOX'];
			$aPseudosVariables[$i]['`{CHEMIN_MINIATURE}`']	= $_MINIATURES[$i]['LIEN_PHOTO']['CHEMIN_MIN'];
			$aPseudosVariables[$i]['`{CLASSE_MINIATURE}`']	= $_MINIATURES[$i]['LIEN_PHOTO']['CLASSE_CSS'];
			$aPseudosVariables[$i]['`{ALT_IMAGE}`']			= $_MINIATURES[$i]['LIEN_PHOTO']['ALT'];

			// Si mode gallerie
			if(!$_JB_AL_VARS['b_mode_diaporama'])
			{
				$aPseudosVariables[$i]['`<!-- SI MODE_GALERIE -->\n\t\t|<!-- FINSI MODE_GALERIE -->\n`'] = '';

				// infos de l'image
				$aPseudosVariables[$i]['`{NOM_PHOTO}`']			= $oOutils->tronquerChaine($_MINIATURES[$i]['NOM_PHOTO']);
				$aPseudosVariables[$i]['`{DIMENSIONS_PHOTO}`']	= $_MINIATURES[$i]['DIM_PHOTO'];
				$aPseudosVariables[$i]['`{POIDS_PHOTO}`']		= $_MINIATURES[$i]['SIZE_PHOTO'];

				// Ajout des sauts de lignes si nécessaire
				if( !empty($aPseudosVariables[$i]['`{NOM_PHOTO}`']) )			$aPseudosVariables[$i]['`{NOM_PHOTO}`'] .= '<br />';
				if( !empty($aPseudosVariables[$i]['`{DIMENSIONS_PHOTO}`']) )	$aPseudosVariables[$i]['`{DIMENSIONS_PHOTO}`'] .= '<br />';
				if( !empty($aPseudosVariables[$i]['`{POIDS_PHOTO}`']) )			$aPseudosVariables[$i]['`{POIDS_PHOTO}`'] .= '<br />';
			}
			// Sinon on efface la fiche de description car mode diaporama actif
			else $aPseudosVariables[$i]['`<!-- SI MODE_GALERIE -->.*<!-- FINSI MODE_GALERIE -->\n`s'] = '';

			$aPseudosVariables[$i]['`{PUCE_AJOUT_PANIER}`'] = $_MINIATURES[$i]['AJOUT_PANIER'];
		}

		$sThmVignettes = $oOutils->parser( $_JB_AL_VARS['s_acces_theme'].'html/vignette.thm.php', $aPseudosVariables, true );
		$aPseudosVariables = array();

		// Si on est en mode diaporama
		if($_JB_AL_VARS['b_mode_diaporama'])
		{
			// Si une diapo est définie
			if( $_JB_AL_VARS['s_diapo_courante'] !== '' )
			{
				// Suppression condition diapo non vide et section diapo vide
				$aPseudosVariables['`<!-- SI DIAPO_NON_VIDE -->\n\t\t|<!-- FINSI DIAPO_NON_VIDE -->\n`']	= '';
				$aPseudosVariables['`<!-- SI DIAPO_VIDE -->.*<!-- FINSI DIAPO_VIDE -->\n`s']				= '';

				// Affichage boutons précédente / suivante
				if( $_JB_AL_VARS['s_href_photo_precedente'] !== '' || $_JB_AL_VARS['s_href_photo_suivante'] !== '' )
				{
					$aPseudosVariables['`<!-- SI PLUSIEURS_DIAPOS -->\n\t\t|<!-- FINSI PLUSIEURS_DIAPOS -->\n`'] = '';

					$aPseudosVariables['`{BOUTON_PRECEDENTE}`']	= $_JB_AL_VARS['s_href_photo_precedente'];
					$aPseudosVariables['`{BOUTON_SUIVANTE}`']	= $_JB_AL_VARS['s_href_photo_suivante'];
				}
				// Sinon on efface la section
				else $aPseudosVariables['`<!-- SI PLUSIEURS_DIAPOS -->.*?<!-- FINSI PLUSIEURS_DIAPOS -->\n`s'] = '';

				// Le formulaire de défilement auto
				$aPseudosVariables['`{>FORM_DEFILEMENT_AUTO}\n`']	= $sThmFormDefilement;

				// L'image de la diapositive
				$aPseudosVariables['`{SOURCE_DIAPO}`']		= $_JB_AL_VARS['s_chemin_diapo'];

				// La fiche info de la diapo
				$aPseudosVariables['`{NOM_PHOTO}`']			= $_MINIATURES[$_JB_AL_VARS['i_diapo_courante']]['NOM_PHOTO'];
				$aPseudosVariables['`{DIMENSIONS_PHOTO}`']	= $_MINIATURES[$_JB_AL_VARS['i_diapo_courante']]['DIM_PHOTO'];
				$aPseudosVariables['`{TYPE_MIME}`']			= $_MINIATURES[$_JB_AL_VARS['i_diapo_courante']]['TYPE_MIME'];
				$aPseudosVariables['`{POIDS_PHOTO}`']		= $_MINIATURES[$_JB_AL_VARS['i_diapo_courante']]['SIZE_PHOTO'];
				$aPseudosVariables['`{DONNEES_EXIF}`']		= $oOutils->afficherExif($_MINIATURES[$_JB_AL_VARS['i_diapo_courante']]['CHEMIN_PHOTO']);
			}
			// Si pas de diapo définie
			else
			{
				// On efface la section de la diapo pour afficher la section du message d'erreur
				$aPseudosVariables['`<!-- SI DIAPO_NON_VIDE -->.*<!-- FINSI DIAPO_NON_VIDE -->\n`s']	= '';
				$aPseudosVariables['`<!-- SI DIAPO_VIDE -->\n\t\t|<!-- FINSI DIAPO_VIDE -->\n`']		= '';
			}

			// En mode diaporama, on place la diapositive avant les vignettes
			$sContenuDroite .= $oOutils->parser( $_JB_AL_VARS['s_acces_theme'].'html/diapo.thm.php', $aPseudosVariables );
			$aPseudosVariables = array();
		}

		// Ajout des vignettes au contenu
		$sContenuDroite .= $sThmVignettes;

	}

	//
	// Génération du rappel des sous-dossiers si nécessaire
	//
	if( JB_AL_RAPPELER_SOUS_DOSSIERS && !empty($_JB_AL_VARS['s_rappel_sous_dossiers']) && (
		( JB_AL_RAPPELER_QUE_SI_VIDE && $iNbMiniatures === 0 )
		||
		!JB_AL_RAPPELER_QUE_SI_VIDE ) ) {

		$aPseudosVariables['`{RAPPEL_SOUS_DOSSIERS}`'] = $_JB_AL_VARS['s_rappel_sous_dossiers'];

		$sThmSousDossiers = $oOutils->parser( $_JB_AL_VARS['s_acces_theme'].'html/rappel_sous_dossiers.thm.php', $aPseudosVariables );
		$aPseudosVariables = array();
	}
}


// ================
// ETAPE 4 : Construction page finale
//

// Affichage entête
if(JB_AL_AFFICHER_ENTETE)
{
	$aPseudosVariables['`<!-- SI ENTETE -->\n\t\t|<!-- FINSI ENTETE -->\n`']	= '';
	$aPseudosVariables['`{TITRE_GALERIE}`']										= JB_AL_TITRE_GALERIE;
	$aPseudosVariables['`{SOUS_TITRE_GALERIE}`']								= JB_AL_SOUS_TITRE_GALERIE;
}
else $aPseudosVariables['`<!-- SI ENTETE -->.*<!-- FINSI ENTETE -->\n`s'] = '';

// Construction phrase capacité panier
$sCapacitePanier = 'illimitée';
if( JB_AL_PANIER_CAPACITE_MAX > 0 && JB_AL_PANIER_POIDS_MAX > 0 )	$sCapacitePanier = JB_AL_PANIER_CAPACITE_MAX.' fichiers ou ~'.JB_AL_PANIER_POIDS_MAX.' Mo';
if( JB_AL_PANIER_CAPACITE_MAX === 0 && JB_AL_PANIER_POIDS_MAX > 0 )	$sCapacitePanier = '~'.JB_AL_PANIER_POIDS_MAX.' Mo';
if( JB_AL_PANIER_CAPACITE_MAX > 0 && JB_AL_PANIER_POIDS_MAX === 0 )	$sCapacitePanier = JB_AL_PANIER_CAPACITE_MAX.' fichiers';


// Remplacement du reste des pseudos-variables
$aPseudosVariables['`{>HEADER}`']					= $sThmHeader;
$aPseudosVariables['`{NAVIGATION}`']				= $_JB_AL_VARS['s_navigation'];
$aPseudosVariables['`{>BARRE_MENU}`']				= $sThmPagination;
$aPseudosVariables['`{>CONTENU_DROITE}`']			= $sContenuDroite;
$aPseudosVariables['`{>SOUS_DOSSIERS}`']			= $sThmSousDossiers;
$aPseudosVariables['`{ARBORESCENCE}`']				= $_JB_AL_VARS['s_arborescence'];
$aPseudosVariables['`{NOMBRE_FICHIERS_PANIER}`']	= $_JB_AL_VARS['s_nb_fichiers_panier'];
$aPseudosVariables['`{POIDS_ESTIME}`']				= $_JB_AL_VARS['s_poids_estime'];
$aPseudosVariables['`{PANIER_CAPACITES}`']			= $sCapacitePanier;
$aPseudosVariables['`{MENU_PANIER}`']				= $_JB_AL_VARS['s_menu_panier'];
$aPseudosVariables['`{LIEN_RETOUR_SITE}`']			= $_JB_AL_VARS['s_lien_retour_site'];
$aPseudosVariables['`{CHEMIN_THEME}`']				= $_JB_AL_VARS['s_acces_theme'];
$aPseudosVariables['`{VERSION}`']					= $_JB_AL_VARS['s_version'];

// Affichage pied de page si pas en mode intégration
if( !JB_AL_INTEGRATION_SITE )
	$aPseudosVariables['`<!-- SI NON_INTEGRE -->\n\t\t|<!-- FINSI NON_INTEGRE -->\n`'] = '';
else $aPseudosVariables['`<!-- SI NON_INTEGRE -->.*<!-- FINSI NON_INTEGRE -->`s'] = '';

$sThmIndex = $oOutils->parser( $_JB_AL_VARS['s_acces_theme'].'html/index.thm.php', $aPseudosVariables );


// =================
// Envoi de la page générée
//
return $sThmIndex;

?>
