
		<link rel="alternate" href="rss.php?rep={REP_COURANT}" type="application/rss+xml" title="Albulle RSS pour CoolIris" id="gallery" />

		<style type="text/css" media="screen">@import url({CHEMIN_THEME}css/index.css);</style>

		<!--[if IE]>
  		<style type="text/css" media="screen">@import url({CHEMIN_THEME}css/ie_fix.css);</style>
		<![endif]-->

		<!-- SI LIGHTBOX -->
		<style type="text/css" media="screen">@import url({CHEMIN_THEME}css/jquery.lightbox.css);</style>

		<script type="text/javascript" src="{CHEMIN_ROOT}core/includes/js/jquery.js"></script>
		<script type="text/javascript" src="{CHEMIN_ROOT}core/includes/js/jquery.lightbox.js"></script>
		<script type="text/javascript" src="{CHEMIN_THEME}js/main.js"></script>
		<!-- FINSI LIGHTBOX -->

		<!-- SI POPUP -->
		<script type="text/javascript">
		<!--
			function popup( chemin, largeur, hauteur ) {
				window.open( "{CHEMIN_ROOT}core/popup.php?img=" + chemin , "", "menubar=no, status=no, scrollbars=no, menubar=no, width="+ largeur +", height="+ hauteur );
			}
		-->
		</script>
		<!-- FINSI POPUP -->

		<!-- SI DEFILEMENT_AUTO -->
		<meta http-equiv="refresh" content="{INTERVALLE_TEMPS}; URL={URL_IMAGE_SUIVANTE}">
		<!-- FINSI DEFILEMENT_AUTO -->

