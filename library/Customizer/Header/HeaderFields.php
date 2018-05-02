<?php

namespace Municipio\Customizer\Header;

class HeaderFields
{
    public $headers = array();
    public $panel = '';
    public $config = '';

    public function __construct($headerPanel)
    {
        $this->headers = $headerPanel->headers;
        $this->panel = $headerPanel->panel;
        $this->config = $headerPanel->config;

        add_action('init', array($this, 'headerFields'), 9);
    }

    public function headerFields()
    {
        if (!is_array($this->headers) || empty($this->headers)) {
            return;
        }

        //Header fields
        foreach ($this->headers as $header) {
            $this->headerBackground($header);
            $this->headerLinkColor($header);
            $this->headerVisibility($header);
            $this->padding($header);
        }
    }

    public function padding($header)
    {
        $choices = apply_filters('Municipio/Customizer/Header/Navbar/Padding', array(
            'default' => __('Default', 'municipio'),
            'c-navbar--hard' => __('No padding', 'municipio'),
            'c-navbar--lg' => __('Large', 'municipio')
        ));

        $defaults = array(
            'top' => 'c-navbar--sm'
        );

        $default = (isset($defaults[$header['id']])) ? $defaults[$header['id']] : 'default';

        \Kirki::add_field($this->config, array(
            'type'        => 'radio',
            'settings'    => $header['id'] . '-header-padding',
            'label'       => __('Header padding', 'municipio'),
            'section'     => $header['section'],
            'default'     => $default,
            'priority'    => 10,
            'multiple'    => 1,
            'choices'     => $choices,
        ));
    }

    public function headerBackground($header)
    {
        $colors = array_merge((array) \Municipio\Helper\Colors::themeColors(), (array) \Municipio\Helper\Colors::neturalColors());
        $default = self::defaultHeaderColors();

        $default = (isset($default[$header['id']]['background'])) ? $default[$header['id']]['background'] : '#000000';

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => $header['id']. '-header-background',
            'label'       => esc_attr__(ucfirst($header['id']) . ' header background', 'municipio'),
            'section'     => $header['section'],
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.c-navbar--' . $header['id'],
                    'property' => 'background-color'
                )
            )
        ));
    }

    public function headerLinkColor($header)
    {
        $colors = array(
            '#000000',
            '#ffffff'
        );

        $default = self::defaultHeaderColors();
        $default = (isset($default[$header['id']]['link'])) ? $default[$header['id']]['link'] : '#000000';

        \Kirki::add_field($this->config, array(
            'type'        => 'color-palette',
            'settings'    => $header['id'] . '-header-link-color',
            'label'       => esc_attr__(ucfirst($header['id']) . ' header link color', 'municipio'),
            'section'     => $header['section'],
            'default'     => $default,
            'choices'     => array(
                'colors' => $colors,
                'style'  => 'round',
            ),
            'output' => array(
                array(
                    'element' => '.c-navbar--customizer.c-navbar--' . $header['id'] . ' a, .c-navbar--customizer.c-navbar--' . $header['id'],
                    'property' => 'color'
                ),
                array(
                    'element' => '.c-navbar--customizer.c-navbar--' . $header['id'] . ' .hamburger-inner, .c-navbar--customizer.c-navbar--' . $header['id'] . ' .hamburger-inner::before, .c-navbar--customizer.c-navbar--' . $header['id'] . ' .hamburger-inner::after',
                    'property' => 'background'
                )
            )
        ));
    }

    public function headerVisibility($header)
    {
        $options = array(
            'hidden-xs' => 'Hide XS',
            'hidden-sm' => 'Hide SM',
            'hidden-md' => 'Hide MD',
            'hidden-lg' => 'Hide LG'
        );

        $default = array(
            'top' => array(
                'hidden-xs',
                'hidden-sm'
            ),
            'bottom' => array(
                'hidden-xs',
                'hidden-sm'
            )
        );

        $default = (isset($default[$header['id']])) ? $default[$header['id']] : array();

        \Kirki::add_field($this->config, array(
            'type'        => 'multicheck',
            'settings'    => $header['id'] . '-header-visibility',
            'label'       => esc_attr__('Visibility settings', 'municipio'),
            'section'     => $header['section'],
            'default'     => $default,
            'priority'    => 10,
            'choices'     => $options,
        ));
    }

    public static function defaultHeaderColors()
    {
        $themeColors = \Municipio\Helper\Colors::themeColors();

        $primary = (isset($themeColors[2])) ? isset($themeColors[2]) : '#000000';

        $colors = array(
            'top' => array(
                'background' => $primary,
                'link' => '#ffffff'
            ),
            'primary' => array(
                'background' => '#ffffff',
                'link' => '#000000'
            ),
            'bottom' => array(
                'background' => $primary,
                'link' => '#ffffff'
            )
        );

        return $colors;
    }
}