$(function() {
	 $('[data-fancybox="gallery"]').fancybox({
	 	lang: "fr",
		buttons: ["arrowLeft","arrowRight","slideShow","fullScreen","thumbs","download","share","close"],
	 	i18n: {
	 		fr: {
	 			CLOSE: "Fermer",
	 			NEXT: "Avancer",
	 			PREV: "Revenir",
	 			ERROR: "Echec de chargement du contenu. <br/> Veuillez réessayer plus tard.",
	 			PLAY_START: "Démarrer le diaporama",
	 			PLAY_STOP: "Suspendre le diaporama",
	 			FULL_SCREEN: "Afficher en plein écran",
	 			THUMBS: "Afficher les vignettes",
	 			DOWNLOAD: "Télécharger",
	 			SHARE: "Partager",
	 			ZOOM: "Agrandir"
	 		}
	 	},
	 	caption : function(instance, item) {
	 		var caption = $(this).data('caption') || '';
	
	 		if (item.type === 'image' && caption === '')
	 			caption = $(this).attr('title');
	
	 		return caption;
	 	}
	 });
});