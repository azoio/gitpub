<?php
/**
 * @var string $css
 * @var string $lang
 * @var string $path
 **/
?>
@inject('cssLoader','App\Helpers\CssLoader')
<!doctype html>
<html ⚡ lang="{{ !empty($lang) ? $lang : 'en' }}">
<head>
    <meta charset="utf-8">
    <title>Crypto News</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
    <style type="text/css" amp-custom>
        {!! $cssLoader->loadCss('css/bootstrap.css') !!}
    </style>
</head>

<body>
<nav class="navbar "></nav>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('home') }}">Crypto News</a>
        <button class="btn-link navbar-link" on="tap:sidebar.toggle">Langs</button>
    </div>
</nav>

@yield('content')

<amp-sidebar id="sidebar" class="sidepanel" layout="nodisplay" side="left">
    <span class="sidepanel-close-button" on="tap:sidebar.close" role="button" tabindex="0">
        <span class="icon">+</span>
    </span>
    <ul class="sidepanel-menu">
        @if(empty($path))
            <li><a class="navbar-link" href="{{ route('index', ['branch' => 'es']) }}">Spanish</a></li>
            <li><a class="navbar-link" href="{{ route('index', ['branch' => 'ko']) }}">Korean</a></li>
            <li><a class="navbar-link" href="{{ route('index', ['branch' => 'ru']) }}">Russian</a></li>
        @else
            <li><a class="navbar-link" href="{{ route('page', ['branch' => 'es', 'path' => $path]) }}">Spanish</a></li>
            <li><a class="navbar-link" href="{{ route('page', ['branch' => 'ko', 'path' => $path]) }}">Korean</a></li>
            <li><a class="navbar-link" href="{{ route('page', ['branch' => 'ru', 'path' => $path]) }}">Russian</a></li>
        @endif
    </ul>
</amp-sidebar>

<footer class="container">
    © Crypto {{ date('Y') }}
</footer>
</body>
</html>
