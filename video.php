<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Video Plugin
 *
 * @package		Video
 * @version		1.1
 * @author		Ryan Thompson - AI Web Systems, Inc.
 * @copyright	Copyright (c) 2008 - 2014 AI Web Systems, Inc.
 * @link		https://www.aiwebsystems.com/docs/plugins/video
 */
class Plugin_video extends Plugin
{
	/**
	 * Available parameters
	 * @var array
	 */
	$parameters = array(
		'vendor',
		'url',
		);

	///////////////////////////////////////////////////////////////////////////////
	// --------------------------	  METHODS 	  ------------------------------ //
	///////////////////////////////////////////////////////////////////////////////

	/**
	 * Get a video and it's data
	 * @return array
	 */
	public function video()
	{
		// Get parameters
		$parameters = sef::getParameters();

		// Vendor?
		if (! $parameters['vendor']) {
			$parameters['vendor'] = self::getVendorFromUrl($parameters);
		}

		// Get the ID
		$parameters['id'] = self::getId($parameters);


		/**
		 * Build the return
		 */
		
		$return = array();

		// Link
		$return['link'] = self::getLink($parameters);

		// Embed
		$return['embed'] = self::getEmbed($parameters);

		// Thumbnails
		$return['thumbnails'] = self::getThumbnails($parameters);

		// GO!
		return $return;
	}

	/**
	 * Get YouTube video
	 * @return array
	 */
	public function youtube()
	{
		// Set the vendor
		$this->setAttribute('vendor', 'youtube');

		return $this->video();
	}

	///////////////////////////////////////////////////////////////////////////////
	// --------------------------	 UTILITIES 	  ------------------------------ //
	///////////////////////////////////////////////////////////////////////////////

	/**
	 * Get parameters / defauts
	 * @return array
	 */
	private static function getParameters()
	{
		/**
		 * Get parameters / default values
		 */
		$parameters = array();

		foreach ($this->parameters as $parameter => $default) {
			$parameters[$parameter] = $this->getAttribute($parameter, $default);
		}

		return $parameters;
	}

	/**
	 * Get video ID
	 * @return string
	 */
	private static function getId($parameters)
	{
		// Parse the URL
		parse_str(parse_url($parameters['url'], PHP_URL_QUERY), $variables);

		// Vendors
		switch ($parameters['vendor']) {

			// YouTube
			case 'youtube':
				return $variables['v'];
				break;
			
			default:
				return null;
				break;
		}
	}

	/**
	 * Get video link
	 * @return string
	 */
	private static function getLink($parameters)
	{
		// Vendors
		switch ($parameters['vendor']) {

			// YouTube
			case 'youtube':
				return 'http://youtu.be/'.$parameters['id'];
				break;
			
			default:
				return null;
				break;
		}
	}

	/**
	 * Get embed HTML
	 * @return string
	 */
	private static function getEmbed($parameters)
	{
		// Vendors
		switch ($parameters['vendor']) {

			// YouTube
			case 'youtube':
				return '<iframe width="'.$this->getAttribute('width', '100%').'" height="'.$this->getAttribute('height', 'auto').'" src="//www.youtube.com/embed/'.$parameters['id'].'" frameborder="0" allowfullscreen></iframe>';
				break;
			
			default:
				return null;
				break;
		}
	}

	/**
	 * Get thumbnails
	 * @return string
	 */
	private static function getEmbed($parameters)
	{
		// Vendors
		switch ($parameters['vendor']) {

			// YouTube
			case 'youtube':
				return array(
					'0' => 'http://img.youtube.com/vi/'.$parameters['id'].'/0.jpg',
					'1' => 'http://img.youtube.com/vi/'.$parameters['id'].'/1.jpg',
					'2' => 'http://img.youtube.com/vi/'.$parameters['id'].'/2.jpg',
					'3' => 'http://img.youtube.com/vi/'.$parameters['id'].'/3.jpg',
					'default' => 'http://img.youtube.com/vi/'.$parameters['id'].'/default.jpg',
					'hqdefault' => 'http://img.youtube.com/vi/'.$parameters['id'].'/hqdefault.jpg',
					'mqdefault' => 'http://img.youtube.com/vi/'.$parameters['id'].'/mqdefault.jpg',
					'sddefault' => 'http://img.youtube.com/vi/'.$parameters['id'].'/sddefault.jpg',
					'maxresdefault' => 'http://img.youtube.com/vi/'.$parameters['id'].'/maxresdefault.jpg',
					);
				break;
			
			default:
				return null;
				break;
		}
	}
}
