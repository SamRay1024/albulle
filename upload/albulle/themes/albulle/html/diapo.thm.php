				<div id="diapo"><!-- diapositive du mode diaporama -->

					<!-- SI DIAPO_NON_VIDE -->
					<a name="marqueur"></a>

					<!-- SI PLUSIEURS_DIAPOS -->
					<div class="navDiapos">
						{BOUTON_SUIVANTE}
						{BOUTON_PRECEDENTE}
					</div>
					<!-- FINSI PLUSIEURS_DIAPOS -->

					<img src="{SOURCE_DIAPO}" id="image" alt="Image de {SOURCE_DIAPO}" />

					<!-- SI PLUSIEURS_DIAPOS -->
					<div class="navDiapos">
						{BOUTON_SUIVANTE}
						{BOUTON_PRECEDENTE}
					</div>
					<!-- FINSI PLUSIEURS_DIAPOS -->

					<div class="informations">
						<div class="fiche">
							<span>Informations :</span>
							<ul>
								<li><span>Nom / description : </span>{NOM_PHOTO}</li>
								<li>&nbsp;</li>
								<li><span>Type MIME : </span>{TYPE_MIME}</li>
								<li><span>Dimensions : </span>{DIMENSIONS_PHOTO} pixels</li>
								<li><span>Poids : </span>{POIDS_PHOTO}</li>
							</ul>
						</div>

						<!-- SI EXIF -->
						<div class="fiche">
							<span>Données EXIF :</span>
							{DONNEES_EXIF}
						</div>
						<!-- FINSI EXIF -->
					</div>

					{>FORM_DEFILEMENT_AUTO}

					<div class="spacer"></div>
					<!-- FINSI DIAPO_NON_VIDE -->

					<!-- SI DIAPO_VIDE -->
					<p>Choisissez une image dans la liste pour la visualiser !</p>
					<!-- FINSI DIAPO_VIDE -->

				</div><!-- diapositive du mode diaporama -->

