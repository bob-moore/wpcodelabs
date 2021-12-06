<div class="contact-vcard">
	<?php if( !empty( $settings->photo_src ) ) : ?>
		<div class="contact-vcard-header" style="background-image: url( <?php echo $settings->photo_src; ?> )"></div>
	<?php endif; ?>
	<div class="contact-vcard-body">
		<ul class="contact-vcard-meta">

			<?php if( !empty( $settings->phone ) ) : ?>
				<li class="phone">
					<a href="<?php echo $settings->phone_link; ?>"><?php echo $settings->phone; ?></a>
				</li>
			<?php endif; ?>
			<?php if( !empty( $settings->email ) ) : ?>
				<li class="email">
					<a href="<?php echo $settings->email_link; ?>"><?php echo $settings->email; ?></a>
				</li>
			<?php endif; ?>
			<?php if( !empty( $settings->address ) ) : ?>
				<li class="address">
					<?php if( !empty( $settings->address_link ) ) : ?>
						<a href="<?php echo $settings->address_link; ?>" target="<?php echo $settings->address_link_target; ?>" rel="<?php echo $settings->address_link_rel; ?>">
					<?php endif; ?>

						<address>

							<?php echo nl2br( $settings->address ); ?>

						</address>

						<?php if( !empty( $settings->address_link ) ) : ?>
							</a>
						<?php endif; ?>

					</a>
				</li>
			<?php endif; ?>
		</ul>

	</div>

</div>