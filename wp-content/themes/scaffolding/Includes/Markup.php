<?php
/**
 * Class to assist in dynamic markup
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

defined( 'ABSPATH' ) || exit;

class Markup extends Lib\Framework
{
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void
	{
		Subscriber::addFilter( 'timber/twig', [$this, 'twigFunctions'] );
		Subscriber::addFilter( 'scaffolding/markup/atts', [$this, 'commonAttributes'], 8, 3 );
	}
	/**
	 * Add functions to twig
	 *
	 * @param object $twig Twig instance to add function to
	 */
	public function twigFunctions( object $twig ) : object
	{
		$twig->AddFunction( new \Timber\Twig_Function( 'markup_atts', [$this, 'attributes'] ) );
		return $twig;
	}
	/**
	 * Convert array values to string
	 *
	 * Used when getting classes and other attributes from filters
	 *
	 * @param  array  $array [description]
	 */
	protected function valuesToString( array $array ) : array
	{
		foreach ( $array as $index => $value )
		{
			if ( is_string( $value ) )
			{
				continue;
			}
			$array[$index] = implode( ' ', (array)$value );
		}

		return $array;
	}
	/**
	 * Merge attributes from several sources together
	 *
	 * @param  array  $original First array to merge
	 * @param  array  $additional [description]
	 * @return [type]             [description]
	 */
	protected function mergeAttributes( array $original, array $additional ) : array
	{
		foreach ( $additional as $index => $value )
		{
			if ( isset( $original[$index] ) && ! empty( $original[$index] ) )
			{
				$original[$index] .= ' ' . trim( $value );
			}
			else
			{
				$original[$index] = trim( $value );
			}
		}
		return $original;
	}
	public function attributes( string $context = '', array $additions = [], $ref = [] )
	{
		$settings = $this->valuesToString( Subscriber::getInstance( 'Settings' )->get( "components/{$context}/atts", [] ) );

		if ( ! empty( $additions ) )
		{
			$settings = $this->mergeAttributes( $settings, $this->valuesToString( $additions ) );
		}

		$settings = apply_filters( 'scaffolding/markup/atts', $settings, $context, $ref );

		$settings = apply_filters( "scaffolding/markup/atts/{$context}", $settings, $ref );

		$atts = array_reduce( array_keys( $settings ), function( $carry, $key ) use ( $settings )
		{
			if ( ! empty( $settings[$key] ) )
			{
				$carry .= sprintf( '%s="%s" ', $key, trim( $settings[$key] ) );
			}
			return $carry;
		}, '' );

		$atts = apply_filters( 'scaffolding/markup/atts/string', $atts, $context, $ref );

		return apply_filters( "scaffolding/markup/atts/string/{$context}", trim( $atts ) );

	}

	public function commonAttributes( array $atts, string $context, array $ref = [] ) : array
	{
		/**
		 * Maybe add breakpoint
		 */
		$settings = Subscriber::getInstance( 'Settings' )->get( "components/{$context}", [] );

		if ( isset( $settings['breakpoint'] ) && ! isset( $atts['data-breakpoint'] ) )
		{
			$atts['data-breakpoint'] = $settings['breakpoint'];
		}
		/**
		 * Add context specific attributes
		 */
		switch ( $context )
		{
			case 'entry' :
				$atts['class'] = esc_attr( implode( ' ', get_post_class( $atts['class'] ?? '' ) ) );
				break;
			default:
				// Reserve for future
				break;
		}
		return $atts;
	}

	public function atts( string $context = '', array $additions = [], $ref = [] )
	{
		$settings = apply_filters( 'scaffolding/atts', $this->getAttributes( $context, $additions ), $context, $ref );

		$settings = apply_filters( "scaffolding/atts/{$context}", $settings, $ref );

		$atts = array_reduce( array_keys( $settings ), function( $carry, $key ) use ( $settings )
		{
			if ( ! empty( $settings[$key] ) )
			{
				$carry .= sprintf( '%s="%s" ', $key, trim( $settings[$key] ) );
			}
			return $carry;
		}, '' );

		return apply_filters( "scaffolding/atts_string/{$context}", trim( $atts ) );
	}
}