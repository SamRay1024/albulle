				{?diaporama}
				<div id="diapo"><!-- diapositive du mode diaporama -->

					{?diapo_vide}
					<p class="puceNoPhoto">Choisissez une image dans la liste pour la visualiser !</p>
					{or diapo_vide}
					<a name="marqueur"></a>

					{?plusieurs_diapos}
					<div class="navDiapos">
						{$bouton_suivante}
						{$bouton_precedente}
					</div>
					{plusieurs_diapos?}

					<a href="{$href_image}"{$target_blank}{$lightbox}{$javascript}>
						<img src="{$source_diapo}" id="image" alt="Image de {$alt_diapo}" />
					</a>
					
					{?plusieurs_diapos}
					<div class="navDiapos">
						{$bouton_suivante}
						{$bouton_precedente}
					</div>
					{plusieurs_diapos?}

					<div class="informations">
						<div class="fiche">
							<span>Informations :</span>
							<ul>
								<li><span>Nom : </span>{$nom_photo}</li>
								<li><span>Légende : </span>{$legende}</li>
								<li>&nbsp;</li>
								<li><span>Type MIME : </span>{$type_mime}</li>
								<li><span>Dimensions : </span>{$dimensions_photo} pixels</li>
								<li><span>Poids : </span>{$poids_photo}</li>
							</ul>
						</div>

						{?exif}
						<div class="fiche">
							<span>Données EXIF :</span>
							
							<ul id="exif">
								<li><span>Marque de l'appareil :</span> {?exif_marque}{$exif_marque}{or exif_marque}-{exif_marque?}</li>
								<li><span>Modèle de l'appareil :</span> {?exif_modele}{$exif_modele}{or exif_modele}-{exif_modele?}</li>
								<li><span>Date/Heure de la prise de vue :</span> {?exif_date}{$exif_date}{or exif_date}-{exif_date?}</li>
								<li><span>Temps d'exposition :</span> {?exif_exposition}{$exif_exposition}{or exif_exposition}-{exif_exposition?}</li>
								<li><span>Sensibilité ISO :</span> {?exif_sensibilite}{$exif_sensibilite}{or exif_sensibilite}-{exif_sensibilite?}</li>
								<li><span>Longueur de la focale :</span> {?exif_focale}{$exif_focale}{or exif_focale}-{exif_focale?}</li>
								<li><span>Ouverture de la focale :</span> {?exif_ouverture}{$exif_ouverture}{or exif_ouverture}-{exif_ouverture?}</li>
							</ul>
						</div>
						{exif?}
					</div>

					{>form_defilement_auto}

					<div class="spacer"></div>
					{diapo_vide?}

				</div><!-- diapositive du mode diaporama -->
				{diaporama?}