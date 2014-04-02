<?php

/**
 * Video Plugin
 *
 * @package        Video
 * @version        1.1
 * @author         Ryan Thompson - AI Web Systems, Inc.
 * @copyright      Copyright (c) 2008 - 2014 AI Web Systems, Inc.
 * @link           https://www.aiwebsystems.com/docs/plugins/video
 */
class Plugin_video extends Plugin
{
    /**
     * Available parameters
     *
     * @var array
     */
    protected $parameters = array(
        'vendor' => null,
        'url'    => null,
    );

    ///////////////////////////////////////////////////////////////////////////////
    // --------------------------      METHODS       ------------------------------ //
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Get a video and it's data
     *
     * @return array
     */
    public function video()
    {
        // Get parameters
        $parameters = $this->getParameters();

        // Vendor?
        if (!isset($parameters['vendor'])) {
            $parameters['vendor'] = self::getVendorFromUrl($parameters);
        }

        // Get the ID
        $parameters['id'] = self::getId($parameters);

        // Check cache
        if (!$return = ci()->cache->get('video:' . $parameters['url'])) {

            // Build the return
            $return = array();

            // Link
            $return['link'] = self::getLink($parameters);

            // Embed
            $return['embed'] = self::getEmbed($parameters);

            // Thumbnails
            $return['thumbnails'] = self::getThumbnails($parameters);

            // Information
            $return['information'] = self::getInformation($parameters);

            // Prep some information
            $return['information']['entry']['title'] = $return['information']['entry']['title']['$t'];

            // Cache
            ci()->cache->put('video:' . $parameters['url'], $return, 86400); // Cache for a day
        }

        return array($return);
    }

    /**
     * Get YouTube video
     *
     * @return array
     */
    public function youtube()
    {
        // Set the vendor
        $this->setAttribute('vendor', 'youtube');

        return $this->video();
    }

    /**
     * Get vendor from URL
     *
     * @param $parameters
     * @return bool|string
     */
    private function getVendorFromUrl($parameters)
    {
        if (isset($parameters['url']) and $url = strtolower($parameters['url'])) {
            if (strpos($url, 'youtube') !== false) {
                return 'youtube';
            } elseif (strpos($url, 'vimeo') !== false) {
                return 'vimeo';
            }
        }

        return false;
    }

    /**
     * Get parameters / defauts
     *
     * @return array
     */
    private function getParameters()
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
     *
     * @return string
     */
    private function getId($parameters)
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
     *
     * @return string
     */
    private function getLink($parameters)
    {
        // Vendors
        switch ($parameters['vendor']) {

            // YouTube
            case 'youtube':
                return 'http://youtu.be/' . $parameters['id'];
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Get embed HTML
     *
     * @return string
     */
    private function getEmbed($parameters)
    {
        $width  = 'width="' . $this->getAttribute('width', '100%') . '"';
        $height = 'height="' . $this->getAttribute('height', '400px') . '"';

        // Vendors
        switch ($parameters['vendor']) {

            // YouTube
            case 'youtube':
                $src = 'src="//www.youtube.com/embed/' . $parameters['id'] . '"';
                break;

            default:
                return null;
                break;
        }

        return '<iframe ' . $width . ' ' . $height . ' ' . $src . ' frameborder="0" allowfullscreen></iframe>';
    }

    /**
     * Get thumbnails
     *
     * @return string
     */
    private function getThumbnails($parameters)
    {
        // Vendors
        switch ($parameters['vendor']) {

            // YouTube
            case 'youtube':
                return array(
                    '0'             => 'http://img.youtube.com/vi/' . $parameters['id'] . '/0.jpg',
                    '1'             => 'http://img.youtube.com/vi/' . $parameters['id'] . '/1.jpg',
                    '2'             => 'http://img.youtube.com/vi/' . $parameters['id'] . '/2.jpg',
                    '3'             => 'http://img.youtube.com/vi/' . $parameters['id'] . '/3.jpg',
                    'default'       => 'http://img.youtube.com/vi/' . $parameters['id'] . '/default.jpg',
                    'hqdefault'     => 'http://img.youtube.com/vi/' . $parameters['id'] . '/hqdefault.jpg',
                    'mqdefault'     => 'http://img.youtube.com/vi/' . $parameters['id'] . '/mqdefault.jpg',
                    'sddefault'     => 'http://img.youtube.com/vi/' . $parameters['id'] . '/sddefault.jpg',
                    'maxresdefault' => 'http://img.youtube.com/vi/' . $parameters['id'] . '/maxresdefault.jpg',
                );
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Get information
     *
     * @param $parameters
     * @return mixed
     */
    private function getInformation($parameters)
    {
        $url = 'https://gdata.youtube.com/feeds/api/videos/' . $parameters['id'] . '?v=2&alt=json';

        return json_decode(file_get_contents($url), true);
    }
}
