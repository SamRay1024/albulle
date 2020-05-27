<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="{$charset}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>.: Albulle :. .: {$popup_titre} :.</title>

        <style>
            body { margin: 0px; padding: 0px; }
            a { text-decoration: none; }
            img { border: none; }
        </style>

		<script>
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
		</script>
    </head>

    <body onload="javascript:verifierDimensions();">

        <a href="javascript:window.close();">
            <img id="monImage" src="{$popup_source}" alt="Image de {$popup_source}" />
        </a>

    </body>
</html>