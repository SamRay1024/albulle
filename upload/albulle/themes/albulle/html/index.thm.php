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

				<div class="panier"><!-- cadre gestion du panier -->

					<h4><span class="titre">Photos dans le panier</span></h4>

					<p>Fichiers dans le panier : <strong>{NOMBRE_FICHIERS_PANIER}</strong><br />
					Estimation poids final de l'archive : <strong>{POIDS_ESTIME}</strong><br />
					<em>(Capacité du panier : {PANIER_CAPACITES})</em></p>

					{MENU_PANIER}

					<br />

					<div class="spacer"></div>
				</div><!-- cadre gestion du panier -->

				<div class="copyright"><!-- copyright -->

					<!-- Vous avez ici deux liens pour le copyright : un avec une image, un en texte.
						Merci de laisser au moins un lien des deux ;-) -->
					<a href="http://jebulle.net/?rubrique=albulle" title="T&eacute;l&eacute;chargez Albulle, Galerie Photos">
						<img src="./{CHEMIN_THEME}images/AlBulle_80x15.png" width="80" height="15" title="T&eacute;l&eacute;chargez Albulle, Galerie Photos" alt="T&eacute;l&eacute;chargez Albulle, Galerie Photos" />
					</a>
					<br />
					Albulle{VERSION} &copy; <a href="http://jebulle.net" title="Retrouvez Albulle, Galerie Photos et d'autres scripts">Bubulles Creations</a>

				</div><!-- copyright -->

			</div><!-- cadre gauche -->

		</div><!-- cadre principal -->

<!-- SI NON_INTEGRE -->
	</body>

</html>
<!-- FINSI NON_INTEGRE -->
