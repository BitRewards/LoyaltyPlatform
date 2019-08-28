<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/favicon.png" rel="icon" type="image/x-icon" />

    {{-- Encrypted CSRF token for Laravel, in order for Ajax requests to work --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>
        <?php
            $mainTitle = ($user = \Auth::user()) && $user->partner && $user->partner->isBitrewardsEnabled() ? 'Bitrewards' : config('backpack.base.project_name');
        ?>
      {{ isset($title) ? $title.' :: '. $mainTitle.' Admin' : $mainTitle.' Admin' }}
    </title>

    <script>
        window.LANGUAGE = '{{ HLanguage::getCurrent() }}';
    </script>

    @yield('before_styles')

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/plugins/pace/pace.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/pnotify/pnotify.custom.min.css') }}">

    <!-- BackPack Base CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/backpack/backpack.base.css') }}">

    <link rel="stylesheet" href="{{ asset('css/tipped.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rouble.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hotfixes.css') }}">

    @yield('after_styles')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition {{ config('backpack.base.skin') }} sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper" id="admin-app">

      <header class="main-header">
        <!-- Logo -->
        <a href="{{ route('admin') }}" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini">{!! config('backpack.base.logo_mini') !!}</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">{!! (($user = \Auth::user()) && $user->partner && $user->partner->isBitrewardsEnabled() ? 'Bitrewards Merchant\'s Dashboard' : config('backpack.base.logo_lg')) !!}</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ trans('backpack::base.toggle_navigation') }}</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>

          @include('backpack::inc.menu')
        </nav>
      </header>

      <!-- =============================================== -->

      @include('backpack::inc.sidebar')

      <!-- =============================================== -->

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
         @yield('header')

        <!-- Main content -->
        <section class="content">

          @yield('content')

        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

      {{--<footer class="main-footer" style="display: none;">
        @if (config('backpack.base.show_powered_by'))
            <div class="pull-right hidden-xs">
              {{ trans('backpack::base.powered_by') }} <a target="_blank" href="http://laravelbackpack.com">Laravel BackPack</a>
            </div>
        @endif
        {{ trans('backpack::base.handcrafted_by') }} <a target="_blank" href="{{ config('backpack.base.developer_link') }}">{{ config('backpack.base.developer_name') }}</a>.
      </footer>--}}
    </div>
    <!-- ./wrapper -->


    @yield('before_scripts')

    <!-- jQuery 2.2.0 -->
    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script>window.jQuery || document.write('<script src="{{ asset('vendor/adminlte') }}/plugins/jQuery/jQuery-2.2.0.min.js"><\/script>')</script>
    <!-- Bootstrap 3.3.5 -->
    <script src="{{ asset('vendor/adminlte') }}/bootstrap/js/bootstrap.min.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/plugins/pace/pace.min.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/plugins/fastclick/fastclick.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/dist/js/app.min.js"></script>
    <script src="{{ asset('js/jsoneditor.min.js') }}"></script>
    <script src="{{ asset('js/tipped.js') }}"></script>

    @if (config('app.env') === 'production')
        <script src="https://cdn.ravenjs.com/3.16.0/raven.min.js" crossorigin="anonymous"></script>
        <script>
            Raven.config('https://53f10974ce1948778a7a074fadbb83e0@sentry.io/180370', {
                release: '{{ $sentryBackendRelease }}',
            }).install();
        </script>
    @endif

    <script src="{{ asset('admin-static/js/main.js') }}"></script>

    <!-- page script -->
    <script type="text/javascript">
        // To make Pace works on Ajax calls
        $(document).ajaxStart(function() {
            Pace.restart();
        });

        // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                error: function(response) {
                    let message = '';

                    if (response.responseJSON && response.responseJSON.data) {
                        let data = response.responseJSON.data;
                        for (let property in data) {
                            if (data.hasOwnProperty(property)) {
                                message += data[property] + "\n";
                            }
                        }
                    }

                    if (!message) {
                        message = 'Unknown error';
                    }

                    PNotify.removeAll();

                    new PNotify({
                        text: message,
                        type: 'error',
                        icon: false
                    });
                },
            });

        // Set active state on menu element
        let current_url = '{{ url(Route::current()->uri()) }}';

        $('ul.sidebar-menu li a').each(function() {
            const href = $(this).attr('href')

            if (href.startsWith(current_url) || current_url.startsWith(href)) {
                $(this).parents('li').addClass('active');
            }
        });
    </script>

    @include('backpack::inc.alerts')

    @yield('after_scripts')

    <!-- JavaScripts -->
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

    <script>
      function initTipped()
      {
        $('[data-tooltip]').each(function(i, item) {
          $item = $(item);
          $item.addClass('tipped');
          $item.attr('title', $item.data('tooltip'));
          $item.removeData('tooltip');
          $item.removeAttr('tooltip');
        });

        Tipped.create('.tipped');
      }

      $(function() {
        initTipped();

        $(document).ajaxComplete(initTipped);
      })
    </script>
    <script src="{{ asset('admin-app/admin.js') }}<?= '?rev='.time(); ?>"></script>
</body>
</html>
