<?php

namespace Wpcl\Scaffolding\Timber;

use \Timber\User;

class Author extends User {

	private $_links = [];

	public function links() {

		if ( empty( $this->_links ) ) {

			$networks = [
				'url' => [ 'icon' => '_s_icon _s_icon-chrome' ],
				'facebook' => [ 'icon' => '_s_icon _s_icon-facebook1' ],
				'github' => [ 'icon' => '_s_icon _s_icon-github' ],
				'googleplus' => [ 'icon' => '_s_icon _s_icon-globe' ],
				'instagram' => [ 'icon' => '_s_icon _s_icon-instagram' ],
				'linkedin' => [ 'icon' => '_s_icon _s_icon-linkedin' ],
				'myspace' => [ 'icon' => '_s_icon _s_icon-myspace' ],
				'pinterest' => [ 'icon' => '_s_icon _s_icon-pinterest1' ],
				'soundcloud' => [ 'icon' => '_s_icon _s_icon-soundcloud' ],
				'tumblr' => [ 'icon' => '_s_icon _s_icon-tumblr' ],
				'twitter' => [
					'pattern' => 'https://twitter.com/@%s',
					'icon' => '_s_icon _s_icon-twitter'
				],
				'wikipedia' => [ 'icon' => '_s_icon _s_icon-wikipedia' ],
				'wordpress' => [ 'icon' => '_s_icon _s_icon-wordpress' ],
				'youtube' => [ 'icon' => '_s_icon _s_icon-youtube' ],
			];

			$networks = apply_filters( 'scaffolding/author/networks', $networks );

			foreach ( $networks as $name => $args ) {

				$args = wp_parse_args( $args, [
					'url' => '',
					'pattern' => '',
					'icon' => '',
				] );
				/**
				 * If url is set from filter, don't override
				 */
				$args['url'] = empty( $args['url'] ) ? get_the_author_meta( $name ) : $args['url'];
				/**
				 * Bail if empty at this point
				 */
				if ( empty( $args['url'] ) ) {
					continue;
				}
				/**
				 * Set URL
				 */
				$this->_links[$name]['url'] = empty( $args['pattern'] ) ? esc_url_raw( $args['url'] ) : esc_url_raw( sprintf( $args['pattern'], $args['url'] ) );
				/**
				 * Set Icon
				 */
				$this->_links[$name]['icon'] = $args['icon'];
			}

		}

		return $this->_links;

	}
}