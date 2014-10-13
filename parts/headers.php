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
			<h1></h1>
			<div class="subtitle"></div>
			<div class="call-to-action"></div>
		</div>
	</header>
<?php endif; ?>
