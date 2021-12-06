<form role="search" method="get" class="search-form" action="/">
	<label class="screen-reader-text">Search for:</label>
	<input type="search" class="search-field" placeholder="Search..." value="<?php the_search_query(); ?>" name="s">
	<button type="submit" class="search-submit-button">
		<span class="_s_icon _s_icon-search"></span><span class="screen-reader-text"><?php esc_html_e( 'Search', 'scaffolding' ); ?></span>
	</button>
</form>