<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
        <meta http-equiv="Content-Style-Type" content="text/css" />

        <title>.: AlBulle :. .: {$popup_titre} :.</title>

        <style>
            body { margin: 0px; padding: 0px; }
            a { text-decoration: none; }
            img { border: none; }
        </style>

		<script type="text/javascript">
 		<!--
 	    function verifierDimensions() {

			var iLargeur = (document.body.clientWidth);
			var iHauteur = (document.body.clientHeight);
			var fRatio = iLargeur / iHauteur;

 	    	var iLargeurEcran = screen.width;
 	    	var iHauteurEcran = screen.height;
 	    	var bRedimensionner = false;

 	    	if( iLargeur > iLargeurEcran )
 	    	{
 	    		iLargeur = iLargeurEcran - 60;
				iHauteur = iLargeur * (1/fRatio);
				document.images["monImage"].width = iLargeur;
				bRedimensionner = true;
			}

			if( iHauteur > iHauteurEcran )
			{
				iHauteur = iHauteurEcran - 60;
				iLargeur = iHauteur * fRatio;
				document.images["monImage"].height = iHauteur;
				bRedimensionner = true;
			}

			parent.window.moveTo(0,0);

			if( bRedimensionner )
				parent.window.resizeTo(iLargeur,iHauteur);
		}
		-->
		</script>

    </head>

    <body onload="javascript:verifierDimensions();">

        <a href="javascript:window.close();">
            <img id="monImage" src="{$popup_source}" alt="Image de {$popup_source}" />
        </a>

    </body>

</html>
