<?php

namespace Municipio\Customizer\Header;

class CustomizerHeader
{
    public $headers = array();
    public $config = '';
    public $panel = '';

    public static $enabledHeaders = array();

    public function __construct($customizerManager)
    {
        $this->config = $customizerManager->config;
        $this->panel = 'panel_header';

        $this->establishHeaders();

        new \Municipio\Customizer\Header\UserInterface($this);
        new \Municipio\Customizer\Header\Sidebars($this);
    }

    /**
     * Returns list of avalible headers, use filter to add/remove headers
     *
     * Avalible keys for each header:
     * - id (string) - Required key which will be used when registering sidebars, customizer sections etc
     * - enabled (boolean) - enable the header (this ignores the "optional" option)
     * - optional (boolean) - creates an option in backend to enable the header
     * - classes (array/string) - classes to append to the HTML
     * - attributes (array) - attributes to append to the HTML, uses the array key as attribute eg. array('class' => array('class-1', 'class-2')) results in 'class="class-1 class-2"'
     * - container (string) - class name of the container element that wraps the content (defaults to 'container')
     *
     * @return array Avalible headers
     */
    public function avalibleHeaders()
    {
        $avalibleHeaders = array(
            array(
                'id'            => 'top',
                'name'          => 'Top header',
                'optional'      => true,
                'classes'       => ['c-navbar--top']
            ),
            array(
                'id'            => 'primary',
                'name'          => 'Primary header',
                'enabled'       => true
            ),
            array(
                'id'            => 'secondary',
                'name'          => 'Secondary header',
                'classes'       => ['c-navbar--secondary'],
                'optional'      => true
            )
        );

        return apply_filters('Municipio/Customizer/Header/avalibleHeaders', $avalibleHeaders);
    }

    public function establishHeaders()
    {
        $avalibleHeaders = $this->avalibleHeaders();

        if (!is_array($avalibleHeaders) || empty($avalibleHeaders)) {
            return;
        }

        $enabledHeaders = array();

        //Enable by option
        foreach ($avalibleHeaders as $key => $header) {
            if (isset($header['optional']) && $header['optional'] == true) {
                $avalibleHeaders[$key]['enabled'] = false;
            }
        }

        //Map enabled headers
        foreach ($avalibleHeaders as $header) {
            if (isset($header['enabled']) && $header['enabled'] == true && isset($header['id']) && $header['id']) {
                $enabledHeaders[] = $this->mapHeader($header);
            }
        }

        if (is_array($enabledHeaders) && !empty($enabledHeaders)) {
            $this->headers = $enabledHeaders;
            self::$enabledHeaders = $enabledHeaders;

            return true;
        }

        return false;
    }

    public function mapHeader($header)
    {
        if (!isset($header['id']) || !is_string($header['id'])) {
            return;
        }

        //Unset unnecessary vars
        unset($header['enabled']);
        unset($header['optional']);

        $blockClass = apply_filters('Municipio/Customizer/Header/blockClass', 'c-navbar', $header);

        //Append container
        $header['container'] = (isset($header['container']) && is_string($header['container'])) ? $header['container'] : 'container';

        //Append sidebar
        $header['name'] = (isset($header['name']) && is_string($header['name'])) ? $header['name'] : ucfirst($header['id']) . ' header';
        $header['description'] = (isset($header['description']) && is_string($header['description'])) ? $header['description'] : 'Sidebar that sits in the header';
        $header['sidebar'] = 'customizer-header-' . sanitize_title($header['id']);

        //Setup attributes
        $header['attributes'] = (!isset($header['attributes']) || !is_array($header['attributes'])) ? array() : $header['attributes'];

        //Append classes to attributes
        $header['attributes'] = (isset($header['classes'])) ? array_merge(['class' => $header['classes']], $header['attributes']) : array_merge(array('class' => array()), $header['attributes']);
        unset($header['classes']);

        //Append block class
        if (isset($header['attributes']['class']) && is_array($header['attributes']['class'])) {
            array_unshift($header['attributes']['class'], $blockClass);
        } elseif (isset($header['attributes']['class']) && is_string($header['attributes']['class'])) {
            $header['attributes']['class'] =  $blockClass . ' ' . $header['attributes']['class'];
        } else {
            $header['attributes']['class'] = $blockClass;
        }

        //Append ID to attributes
        $header['attributes']['id'] = (isset($header['attributes']['id']) && is_string($header['attributes']['id'])) ? $header['attributes']['id'] : 'customizer-header-' . $header['id'];

        //Map attributes
        $header['attributes'] = (is_array($header['attributes']) && !empty($header['attributes'])) ? \Municipio\Helper\Html::attributesToString($header['attributes']) : '';

        return $header;
    }

    public static function getHeaders()
    {
        if (isset(self::$enabledHeaders) && is_array(self::$enabledHeaders) && !empty(self::$enabledHeaders)) {
            return self::$enabledHeaders;
        }

        return false;
    }
}