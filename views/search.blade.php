@extends('templates.master')
@section('content')
    @switch($activeSearchEngine)
        @case("google")
            @include('partials.search.google')
            @break
        @case("algoliacustom")
            @include('partials.search.algolia-customsearch')
            @break
        @case("algolia")
            @include('partials.search.algolia')
            @break
        @case("algoliainstant")
            @include('partials.search.algolia-instantsearch')
            @break
        @default
            @include('partials.search.wp')
    @endswitch
@stop
