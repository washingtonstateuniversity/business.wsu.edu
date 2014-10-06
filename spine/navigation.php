<section id="spine-navigation" class="spine-navigation">

	<nav id="spine-sitenav" class="spine-sitenav">
		<ul>
			<?php
			$menu_section_args = array(
				'theme_location'  => '', // replaced in loop
				'menu'            => '', // replaced in loop
				'container'       => false,
				'container_class' => false,
				'container_id'    => false,
				'menu_class'      => null,
				'menu_id'         => null,
				'echo'            => true,
				'items_wrap'      => '%3$s',
				'depth'           => 3,
			);

			$x = 1;

			while( $x <= 10 ) {
				if ( has_nav_menu( 'cob-section-' . $x ) ) {
					$menu_section_args['theme_location'] = 'cob-section-' . $x;
					$menu_section_args['menu'] = 'cob-section-' . $x;
					wp_nav_menu( $menu_section_args );
				}
				$x++;
			}
			?>
		</ul>
	</nav>

	<nav id="spine-offsitenav" class="spine-offsitenav">
		<?php
		$offsite = array(
			'theme_location'  => 'offsite',
			'menu'            => 'offsite',
			'container'       => false,
			'container_class' => false,
			'container_id'    => false,
			'menu_class'      => null,
			'menu_id'         => null,
			'echo'            => true,
			'fallback_cb'     => false,
			'items_wrap'      => '<ul>%3$s</ul>',
			'depth'           => 3,
			'walker'          => ''
		);
		wp_nav_menu( $offsite );
		?>
	</nav>

</section>