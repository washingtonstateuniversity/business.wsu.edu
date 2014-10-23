<?php get_header(); ?>

	<main class="spine-single-template">

		<?php get_template_part('parts/headers'); ?>

		<section class="row side-right gutter pad-ends">

			<div class="column one">

				<article id="post-0" class="post error404 no-results not-found">

					<div class="entry-content">
						<p>Perhaps searching can help.</p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->

				</article>

			</div><!--/column-->
			<div class="column two"></div>

		</section>
	</main>

<?php get_footer(); ?>