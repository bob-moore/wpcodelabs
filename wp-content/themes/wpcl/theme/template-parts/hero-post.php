<?php

use \Scaffolding\TemplateTags;
?>

<?php TemplateTags::getInstance()->taxonomyList( [ 'tax' => 'post_tag' ] ); ?>

<h1 class="hero-title"><?php TemplateTags::getInstance()->postTitle(); ?></h1>

<div class="entry-meta">
	<?php TemplateTags::getInstance()->postedBy( ['before' => 'Written by '] ); ?>
	<?php TemplateTags::getInstance()->postedOn( ['before' => 'Posted On '] ); ?>
	<?php TemplateTags::getInstance()->taxonomyList( [ 'tax' => 'category', 'before' => 'Published Under ', 'sep' => ', ', 'wrapper' => 'span' ] ); ?>
</div>