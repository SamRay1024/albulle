{>header}

		<div id="albulle"><!-- cadre principal -->

			{?entete}
			<div id="entete">
				<h1><span>{$titre_galerie}</span></h1>
				<h2>{$sous_titre_galerie}</h2>
			</div>
			{entete?}

			<div class="droite"><div class="contenu"><!-- cadre droite -->

				<h3>{$lien_retour_site}{$fil_ariane}</h3>

				{>menu_galerie}
				{>texte}
				{>diapositive}
				{>galerie}
				{?dossier_vide}
				<div class="texte">
					<p class="puceNoPhoto">Il n'y a pas de photos dans ce dossier.</p>
				</div>
				{dossier_vide?}

				<div class="spacer_post_float"></div>

				{>sous_dossiers}

			</div></div><!-- cadre droite -->

			<div class="gauche"><!-- cadre gauche -->

				<div class="dossiers"><!-- cadre dossiers photos -->

					<h4><span class="titre">Dossiers disponibles</span></h4>

					{?arborescence}
					<ul class="menu">
						{$arborescence}
					</ul>
					{or arborescence}
					<p>Aucun dossier pour l'instant.</p>
					{arborescence?}

					<div class="spacer"></div>

				</div><!-- cadre dossiers photos -->

				{?panier_actif}
				<div class="panier"><!-- cadre gestion du panier -->

					<h4><span class="titre">Photos dans le panier</span></h4>

					<p>Fichiers dans le panier : <strong>{$nombre_fichiers_panier}</strong><br />
					Estimation poids final de l'archive : <strong>{$poids_estime}</strong><br />
					<em>(Capacité du panier : {$panier_capacite})</em></p>

					{>menu_panier}

					<p class="asterisque">(*) Ces informations sont celles de l'image qui sera téléchargée et non de celle affichée.</p>
					
					<div class="spacer"></div>
				</div><!-- cadre gestion du panier -->
				{panier_actif?}

				<div class="copyright"><!-- copyright -->

					<!-- Merci de laisser au moins le lien vers le site d'Albulle ;-) -->
					<a href="http://albulle.jebulle.net" title="Site officiel d'Albulle">
						<img src="./{$chemin_theme}images/btn-albulle.png" width="80" height="15" title="T&eacute;l&eacute;chargez Albulle, Galerie Photos" alt="T&eacute;l&eacute;chargez Albulle, Galerie Photos" />
					</a><br />
					<a href="http://www.cooliris.com" title="Site officiel de Cooliris">
						<img src="./{$chemin_theme}images/btn-cooliris.jpg" width="80" height="20" title="Visualiser autrement les photos grâce à Cooliris" alt="Logo Cooliris" />
					</a><br />
					Albulle{$version} &copy; <a href="http://jebulle.net" title="Retrouvez Albulle, Galerie Photos et d'autres scripts">Bubulles Creations</a>

				</div><!-- copyright -->

			</div><!-- cadre gauche -->

		</div><!-- cadre principal -->

{?non_integre}
	</body>

</html>
{non_integre?}
