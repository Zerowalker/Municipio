<!DOCTYPE html>
<html {!! $languageAttributes !!}>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $pageTitle }}</title>

        <meta name="pubdate" content="{{ $pagePublished }}">
        <meta name="moddate" content="{{ $pageModified }}">

        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="format-detection" content="telephone=yes">
        <meta name="HandheldFriendly" content="true"/>

        <script>
            var ajaxurl = '{!! $ajaxUrl !!}';
        </script>

        {{-- Wordpress required call --}}
        {!! wp_head() !!}

    </head>

    <body class="{{ $bodyClass }}">

    {{-- Site header --}}

    @includeIf('partials.header.default')

    <main id="main">

        {{-- Before page layout --}}
        @yield('before-layout')

        {{-- Page layout --}}
        @section('layout')
            <div class="container main-container">

                {{-- Above --}}
                @hasSection('above')
                        @yield('above')
                @endif

                {{-- Sidebar left --}} {{-- TODO: RENAME TO "SIDEBAR" --}}
                @hasSection('sidebar-left')
                    @includeIf('partials.sidebar.default', ['id' => 'sidebar-left'])
                @endif

                {{-- Content --}}
                <div class="{{$layout['content']}} s-content">
                    @yield('content')
                </div>

                {{-- Below --}}
                @hasSection('below')
                    @yield('below')
                @endif
            </div>

        @show

        {{-- After page layout --}}
        @yield('after-layout')
    </main>


    {{-- Site footer --}}
    @section('footer')
        @includeIf('partials.footer.default')
    @show
    
    {{-- Wordpress required call --}}
    {!! wp_footer() !!}

    </body>
</html>