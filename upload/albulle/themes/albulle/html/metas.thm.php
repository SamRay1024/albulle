
		{?!accueil}<link rel="alternate" href="rss.php?rep={$rep_courant}" type="application/rss+xml" title="Albulle RSS pour CoolIris" id="gallery" />{!accueil?}

		<style type="text/css" media="screen">@import url({$chemin_theme}css/index.css);</style>

		<!--[if IE]>
  		<style type="text/css" media="screen">@import url({$chemin_theme}css/ie_fix.css);</style>
		<![endif]-->

		{?lightbox}
		<style type="text/css" media="screen">@import url({$chemin_theme}css/jquery.lightbox.css);</style>

		<script type="text/javascript" src="{$chemin_root}core/includes/js/jquery.js"></script>
		<script type="text/javascript" src="{$chemin_root}core/includes/js/jquery.lightbox.js"></script>
		<script type="text/javascript" src="{$chemin_theme}js/main.js"></script>
		{lightbox?}

		{?popup}
		<script type="text/javascript">
		<!--
			function popup( chemin, largeur, hauteur ) {
				window.open( "{$chemin_root}core/popup.php?img=" + chemin , "", "menubar=no, status=no, scrollbars=no, menubar=no, width="+ largeur +", height="+ hauteur );
			}
		-->
		</script>
		{popup?}

		{?defilement_auto}
		<meta http-equiv="refresh" content="{$intervalle_temps}; URL={$url_image_suivante}">
		{defilement_auto?}

