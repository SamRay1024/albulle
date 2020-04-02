$(function() {
	$('a[@rel*=lightbox]').lightBox({
		
		// Chemin des images de la fenêtre lightbox
		imageLoading:	'albulle/themes/albulle/images/lbox_loading.gif',
		imageBtnPrev:	'albulle/themes/albulle/images/lbox_prevlabel.png',
		imageBtnNext:	'albulle/themes/albulle/images/lbox_nextlabel.png',
		imageBtnClose:	'albulle/themes/albulle/images/lbox_closelabel.png',
		imageBlank:		'albulle/themes/albulle/images/lbox_blank.gif',

		overlayBgColor: 		'#000',		// Couleur du fond qui se superpose à la page
		overlayOpacity:			0.9,		// Opacité du fond (0.x où x = [0..9])
		
		fixedNavigation:		false,		// Mettre à vrai pour que les boutons de navigation restent affichés
		
		containerBorderSize:	10,			// Epaisseur de la bordure (à ajuster si vous modifiez le padding de #lightbox-container-image-box dans jquery.lightbox.css)		
		containerResizeSpeed:	400,		// Temps de l'animation de redimensionnement (en millisecondes, soit 500 = 5 secondes)
		
		// Textes
		txtImage:				'Image',
		txtOf:					'sur',
		
		// Raccourcis clavier
		keyToClose:				'c',		// Fermeture ou 'x' ou Echap
		keyToPrev:				'p',		// Image précédente ou flèche de gauche
		keyToNext:				'n'			// Image suivante ou flèche de droite
	
	});
});
