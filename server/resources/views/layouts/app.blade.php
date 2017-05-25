<!doctype html>
<html lang="en">
  <head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="广州明朝,明朝互动">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('css')
  </head>
  <body>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header header">
          <a class="nav-navbar-brand" href="{{url('/')}}" >
            <img height="40" src="/images/logo.png">
          </a>
        </div>
      </div>
    </div>
    <div class="container">
      @yield('content')
    </div>
    <footer class="footer">
      Copyright © 2016 MingChao.All Rights Reserved.广州明朝互动科技股份有限公司  版权所有
    </footer>
    @yield('js')
  </body>
</html>