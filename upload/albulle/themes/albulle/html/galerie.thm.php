				{%vignettes}
				<div class="{$classe_vignette}"{$diapo_courante}>

					<a href="{$href_image}"{$target_blank}{$lightbox}{$javascript}>
						<img src="{$chemin_miniature}" class="{$classe_miniature}" alt="Photo {$alt_image}" title="{$legende}" />
					</a>

					{?mode_galerie}
					<span class="infosImg">
						{$nom}<br />
						{$dimensions}<br />
						{$poids}
					</span>
					{mode_galerie?}

					{?panier_actif}
					<span class="puce">{$puce_ajout_panier}</span>
					{panier_actif?}

				</div>
				{vignettes%}
