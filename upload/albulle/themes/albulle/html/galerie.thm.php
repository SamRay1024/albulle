				{%vignettes}
				<figure class="{$classe_vignette} cadre {$diapo_courante}" title="{$nom}">

					<a href="{$href_image}"{$target_blank}{$lightbox}{$javascript}
						data-fancybox="gallery" data-caption="{$legende}">
						<img
							src="{$chemin_miniature}" class="{$classe_miniature}"
							alt="Photo {$alt_image}" title="{$legende}" />
					</a>

					{?mode_galerie}
					<figcaption class="infosImg">
						<strong>{$nom}</strong><br />
						{$dimensions} | {$poids}
					</figcaption>
					{mode_galerie?}

					{?panier_actif}
					<span class="puce-panier">{$puce_ajout_panier}</span>
					{panier_actif?}
				</figure>
				{vignettes%}
