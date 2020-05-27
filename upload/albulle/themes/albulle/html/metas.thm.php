		
		{?!accueil}<link rel="alternate" href="rss.php?rep={$rep_courant}" type="application/rss+xml" title="Albulle RSS" id="gallery">{!accueil?}
		
		<style media="screen">@import url({$chemin_theme}css/style.css);</style>
		
		<!--[if IE]>
		<style media="screen">@import url({$chemin_theme}css/ie_fix.css);</style>
		<![endif]-->
		
		{?lightbox}
		<style media="screen">@import url({$chemin_theme}css/jquery.fancybox.min.css);</style>
		
		<script src="{$chemin_root}core/includes/js/jquery.js"></script>
		<script src="{$chemin_root}core/includes/js/jquery.fancybox.min.js"></script>
		<script src="{$chemin_theme}js/main.js"></script>
		{lightbox?}
		
		{?popup}
		<script>
			function popup( chemin, largeur, hauteur ) {
				window.open( "{$chemin_root}core/popup.php?img=" + chemin , "", "menubar=no, status=no, scrollbars=no, menubar=no, width="+ largeur +", height="+ hauteur );
			}
		</script>
		{popup?}
		
		{?defilement_auto}
		<meta http-equiv="refresh" content="{$intervalle_temps}; URL={$url_image_suivante}">
		{defilement_auto?}
		
