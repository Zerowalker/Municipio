<header id="site-header">
    @navbar([
        'logo' => $logotype->standard['url'], 
        'items' => $topNavigation, 
        'expanded_prev' => $pageParentID, 
        'expanded_current' => $pageID, 
        'expanded_menu' => $topNavigation, 
        'childItemsUrl' => 'wp/wp-json/municipio/v1/navigation', 
        'isDynamic' => true, 
        'pageID' => $pageID,
        'pageParentID' => $pageParentID
    ])
    @endnavbar
    {{-- TAB MENU --}}
    {{-- @includeIf('partials.header.tabs') --}}
    @includeIf('partials.search.search-form')
    {{-- SITE LOGO TYPE --}}
    {{--
    @if (get_field('header_logotype', 'option') === 'negative')
        @includeIf('partials.logo', [
            'logo' => get_field('logotype_negative', 'option'),
            'logoTooltip' => get_field('logotype_tooltip', 'option')
            ])
    @else
        @includeIf('partials.logo', [
            'logo' => get_field('logotype', 'option'),
            'logoTooltip' => get_field('logotype_tooltip', 'option')
            ])
    @endif
    --}}

    {{-- SITE SUB TITLE --}}
    @if (get_field('sub_site_title', 'option') && !empty(get_field('sub_site_title', 'option')))
        @typography([
            "element" => "span",
            "variant" => "h4"
        ])
            {{get_field('sub_site_title', 'option')}}
        @endtypography
    @endif

    {{-- NAVIGATION PRIMARY NAV --}}
    @includeIf('partials.navigation.primary')

    {{-- NAVIGATION MOBILE NAV --}}
    @includeIf('partials.navigation.mobile')

    {{-- NAVIGATION HELPER NAV --}}
    @includeIf('partials.navigation.helper')



  
    {{-- After header body --}}
    @yield('after-header-body')


    {{-- TODO: find out ??? keep or drop.--}}

    @if ($navigation['headerTabsMenu'] || $navigation['headerHelpMenu'] ||
        (is_array(get_field('search_display', 'option')) &&
            in_array('header', get_field('search_display', 'option'))) || (!is_front_page() &&
            is_array(get_field('search_display', 'option')) &&
            in_array('header_sub', get_field('search_display', 'option'))))

        @if ($navigation['headerTabsMenu'] || (is_array(get_field('search_display', 'option')) &&
            in_array('header', get_field('search_display', 'option'))) || (!is_front_page() &&
            is_array(get_field('search_display', 'option')) &&
            in_array('header_sub', get_field('search_display', 'option'))))

            {!! $navigation['headerTabsMenu'] !!}

            @if ( (is_array(get_field('search_display', 'option')) &&
                in_array('header', get_field('search_display', 'option'))) || (!is_front_page() &&
                is_array(get_field('search_display', 'option')) &&
                in_array('header_sub', get_field('search_display', 'option'))) )
                @includeIf('partials.search.search-form')
            @endif

        @endif

        @if ($navigation['headerHelpMenu'])
            {!! $navigation['headerHelpMenu'] !!}
        @endif

    @endif

</header>

{{-- TODO: Segments replace Hero--}}

@includeIf('partials.navigation.helper')

@includeIf('partials.sidebar', ['id' => 'top-sidebar'])
