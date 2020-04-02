{>HEADER}

		<div id="albulle"><!-- cadre principal -->

			<!-- SI ENTETE -->
			<div id="entete">
				<h1><span>{TITRE_GALERIE}</span></h1>
				<h2>{SOUS_TITRE_GALERIE}</h2>
			</div>
			<!-- FINSI ENTETE -->

			<div class="droite"><div class="contenu"><!-- cadre droite -->

				<h3>{LIEN_RETOUR_SITE}{NAVIGATION}</h3>

				{>BARRE_MENU}
				{>TEXTE_DOSSIER}
				{>CONTENU_DROITE}

				<div class="spacer_post_float"></div>

				{>SOUS_DOSSIERS}

			</div></div><!-- cadre droite -->

			<div class="gauche"><!-- cadre gauche -->

				<div class="dossiers"><!-- cadre dossiers photos -->

					<h4><span class="titre">Dossiers disponibles</span></h4>

					<ul class="menu">
						{ARBORESCENCE}
					</ul>

					<div class="spacer"></div>

				</div><!-- cadre dossiers photos -->

				<!-- SI PANIER_ACTIF -->
				<div class="panier"><!-- cadre gestion du panier -->

					<h4><span class="titre">Photos dans le panier</span></h4>

					<p>Fichiers dans le panier : <strong>{NOMBRE_FICHIERS_PANIER}</strong><br />
					Estimation poids final de l'archive : <strong>{POIDS_ESTIME}</strong><br />
					<em>(Capacité du panier : {PANIER_CAPACITES})</em></p>

					{MENU_PANIER}

					<p class="asterisque">(*) Ces informations sont celles de l'image qui sera téléchargée et non de celle affichée.</p>
					
					<div class="spacer"></div>
				</div><!-- cadre gestion du panier -->
				<!-- FINSI PANIER_ACTIF -->

				<div class="copyright"><!-- copyright -->

					<!-- Merci de laisser au moins le lien vers le site d'Albulle ;-) -->
					<a href="http://albulle.jebulle.net" title="Site officiel d'Albulle">
						<img src="./{CHEMIN_THEME}images/btn-albulle.png" width="80" height="15" title="T&eacute;l&eacute;chargez Albulle, Galerie Photos" alt="T&eacute;l&eacute;chargez Albulle, Galerie Photos" />
					</a><br />
					<a href="http://www.cooliris.com" title="Site officiel de Cooliris">
						<img src="./{CHEMIN_THEME}images/btn-cooliris.jpg" width="80" height="20" title="Visualiser autrement les photos grâce à Cooliris" alt="Logo Cooliris" />
					</a><br />
					Albulle{VERSION} &copy; <a href="http://jebulle.net" title="Retrouvez Albulle, Galerie Photos et d'autres scripts">Bubulles Creations</a>

				</div><!-- copyright -->

			</div><!-- cadre gauche -->

		</div><!-- cadre principal -->

<!-- SI NON_INTEGRE -->
	</body>

</html>
<!-- FINSI NON_INTEGRE -->
