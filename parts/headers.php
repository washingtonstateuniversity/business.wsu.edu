<?php

//Retrieve an array of values to be used in the header.
$spine_main_header_values = spine_get_main_header();

if ( spine_get_option( 'main_header_show' ) == 'true' ) : ?>
	<header class="main-header">
		<div class="header-group hgroup guttered padded-bottom short">

			<sup class="sup-header"><span class="sup-header-default"><?php echo strip_tags( $spine_main_header_values['sup_header_default'], '<a>' ); ?></span></sup>
			<sub class="sub-header"><span class="sub-header-default"><?php echo strip_tags( $spine_main_header_values['sub_header_default'], '<a>' ); ?></span></sub>

		</div>
		<section id="cob-header-navigation" class="cob-header-navigation">
			<?php
			$header_menu_args = array(
				'theme_location'  => 'cob-header',
				'menu'            => 'cob-header',
				'container'       => 'nav',
				'container_class' => 'cob-headernav',
				'container_id'    => 'cob-headernav',
				'menu_class'      => null,
				'menu_id'         => null,
				'echo'            => true,
				'items_wrap'      => '<ul>%3$s</ul>',
				'depth'           => 0,
			);
			wp_nav_menu( $header_menu_args );
			unset( $header_menu_args );
			?>
			<div style="clear:both;"></div>
		</section>
		<div class="cob-headline-container">
			<?php if ( $cob_page_headline = cob_get_page_headline() ) : ?><h1 class="page-headline"><?php echo esc_html( $cob_page_headline ); ?></h1><?php endif; ?>
			<?php if ( $cob_page_subtitle = cob_get_page_subtitle() ) : ?><div class="page-subtitle"><?php echo wp_kses_post( $cob_page_subtitle ); ?></div><?php endif; ?>
			<?php if ( $cob_page_cta = cob_get_page_call_to_action() ) : ?>
				<div class="page-call-to-action">
				<?php if ( $cob_page_cta_url = cob_get_page_call_to_action_url() ) : ?>
					<a href="<?php esc_url( $cob_page_cta_url ); ?>"><?php echo esc_html( $cob_page_cta ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $cob_page_cta ); ?>
				<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</header>
<?php endif; ?>
