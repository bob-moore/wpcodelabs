<div class="dpa-tabs">
	<div class="tab-container">
		<?php foreach( $settings->tabs as $index => $tab ) : ?>
			<div class="tab<?php echo $index === 0 ? ' active' : ''; ?>">
				<a href="#" data-panel="<?php echo $index; ?>"><?php echo $tab->title ?></a>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="content-container">
		<?php foreach( $settings->tabs as $index => $tab ) : ?>
			<div class="panel<?php echo $index === 0 ? ' active' : ''; ?>" data-panel="<?php echo $index; ?>">
				<div class="tab<?php echo $index === 0 ? ' active' : ''; ?>">
					<a href="#" data-panel="<?php echo $index; ?>"><span class="title-wrapper"><?php echo $tab->title ?></span><span class="_s_icon _s_icon-expand_more"></span></a>
				</div>
				<div class="panel-content">
					<?php echo $tab->content; ?>
					<?php if( !empty( $tab->link ) ) : ?>
						<span class="tab-more-link">
							<?php echo _s_flbuilder_link_markup( $tab, 'tablink', 'open' ); ?>
								<span class="link-text"><?php echo $tab->linktext; ?></span>
								<?php if( !empty( $tab->linkicon ) ) : ?>
									<span class="link-icon-container"><span class="<?php echo $tab->linkicon; ?>"></span></span>
								<?php endif; ?>
							</a>
						</span>
					<?php endif; ?>
				</div>

			</div>
		<?php endforeach; ?>
	</div>
</div>

