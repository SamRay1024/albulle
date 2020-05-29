{>header}
		<section class="albulle"><!-- cadre principal -->

			{?entete}
			<header>
				<h1>{$titre_galerie}</h1>
				<h2>{$sous_titre_galerie}</h2>
			</header>
			{entete?}

			<section class="droite"><section class="contenu"><!-- cadre droite -->

				<h3 class="cadre breadcrumb">{$lien_retour_site}{$fil_ariane}</h3>

				{?!dossier_vide}{>menu_galerie}{!dossier_vide?}
				{>texte}
				{>diapositive}
				{>galerie}
				{?dossier_vide}{?!accueil}
				<div class="cadre texte">
					<p class="puce puce-dossier-vide">Ce dossier est vide.</p>
				</div>
				{!accueil?}{dossier_vide?}

				<div class="spacer_post_float"></div>

				{>sous_dossiers}

				<div class="copyright">
					Mis en forme par <a href="http://albulle.jebulle.net"
						title="Télécharger Albulle">Albulle</a>
				</div>

			</section></section><!-- cadre droite -->

			<section class="gauche"><!-- cadre gauche -->

				<div class="cadre dossiers"><!-- cadre dossiers photos -->

					<h4 class="cadre-titre"><span>Albums</span></h4>

					<div class="cadre-contenu">
						{?arborescence}
						<ul class="menu">
							{$arborescence}
						</ul>
						{or arborescence}
						<p>Aucun dossier pour l'instant.</p>
						{arborescence?}

						<div class="spacer"></div>
					</div>

				</div><!-- cadre dossiers photos -->

				{?panier_actif}
				<div class="cadre panier"><!-- cadre gestion du panier -->

					<h4 class="cadre-titre"><span>Images sélectionnées</span></h4>

					<div class="cadre-contenu">
						{?nombre_fichiers_panier}
						<p><strong>{$nombre_fichiers_panier}</strong> fichiers pour
						<strong>{$poids_estime}</strong> estimés.<br />
						<em>(Capacité du panier : {$panier_capacite})</em></p>
						
						{>menu_panier}
						{nombre_fichiers_panier?}

						{?!nombre_fichiers_panier}
						<p class="selection-vide">Aucune sélection pour l'instant.</p>
						{!nombre_fichiers_panier?}

						<p class="asterisque">(*) Ces informations sont celles de l'image qui sera téléchargée et non de celle affichée.</p>
						
						<div class="spacer"></div>
					</div>
				</div><!-- cadre gestion du panier -->
				{panier_actif?}

			</section><!-- cadre gauche -->

		</section><!-- cadre principal -->
{?non_integre}
	</body>
</html>
{non_integre?}