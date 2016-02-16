<?php
/**
 * The template for the front page.
 * Template Name: Pierwsza strona
 *
 */

get_header(); ?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<?php /* The loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
					<div class="entry-thumbnail">
						<?php the_post_thumbnail(); ?>
					</div>
					<?php endif; ?>
					<!-- <h1 class="entry-title"><?php the_title(); ?></h1> -->
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php the_content(); ?>
				</div><!-- .entry-content -->


				<div class="latest-news">
					<?php
					if (is_page()) {
						$cat = "Wydarzenia";
						$posts = get_posts ("category_name=$cat&posts_per_page=3");
						if ($posts) {
							?>
							<header class="entries-header"><h1 class="entry-title"><?php echo $cat ?></h1></header>
							<?php
							foreach ($posts as $post):
								setup_postdata($post); ?>
								
								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> 
									<header class="entry-header">
										<?php if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() ) : ?>
										<div class="entry-thumbnail">
											<?php the_post_thumbnail(); ?>
										</div>
										<?php endif; ?>

										<?php if ( is_single() ) : ?>
										<h1 class="entry-title"><?php the_title(); ?></h1>
										<?php else : ?>
										<h2 class="entry-title">
											<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
										</h2>
										<?php endif; // is_single() ?>

										<div class="entry-meta">
											<?php twentythirteen_entry_meta(); ?>
											<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
										</div><!-- .entry-meta -->
									</header><!-- .entry-header -->

									<div class="entry-summary">
										<?php the_excerpt(); ?>
									</div><!-- .entry-summary -->
								</article><!-- #post -->

							<?php endforeach; ?>
							<?php
							$next = get_adjacent_post( false, '', false );
							if ( ! $next )
								return;
							?>
						<nav class="navigation post-navigation" role="navigation">
						<nav role="navigation">
							<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentythirteen' ); ?></h1>
							<div class="nav-links">
								<a href="/category/wydarzenia" class="alignright">Wszystkie wydarzenia</a>
								<!--<?php // next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'twentythirteen' ), TRUE ); ?>-->
							</div><!-- .nav-links -->
						</nav><!-- .navigation -->
						<?php
						}
					}
					?>
				</div><!-- .latest-news -->

			</article><!-- #post -->
		<?php endwhile; ?>

	</div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
