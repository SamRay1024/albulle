				{?menu_galerie}
				<div class="menubar"><!-- actions & pagination -->
					<a href="{$lien_mode_affichage}" class="bouton" title="Basculer le mode d'affichage">
						{$texte_mode_affichage}
					</a>
					
					{?panier_actif}
					{$panier_tout_ajouter}
					{$panier_tout_retirer}
					{panier_actif?}
					
					{$pagination}
					<div class="spacer"></div>
				</div><!-- actions & pagination -->
				{menu_galerie?}