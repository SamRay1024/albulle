$(function() {
	 $('[data-fancybox="gallery"]').fancybox({
	 	lang: "en",
	 	i18n: {
	 		en: {
	 			CLOSE: "Fermer",
	 			NEXT: "Suivante",
	 			PREV: "Précédente",
	 			ERROR: "Echec de chargement du contenu. <br/> Veuillez réessayer plus tard.",
	 			PLAY_START: "Démarrer le diaporama",
	 			PLAY_STOP: "Suspendre le diaporama",
	 			FULL_SCREEN: "Plein écran",
	 			THUMBS: "Vignettes",
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
