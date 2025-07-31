<!DOCTYPE html>
<html>
@include('admin::blocks.admin_head')
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="logo" target="_blank">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>Fanky</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Fanky</b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <li><a href="{{ route('admin.feedbacks') }}">
                            <i class="fa fa-fw fa-bell-o"></i>
                            @if ($feedback_new = Fanky\Admin\Models\Feedback::notRead()->count())
                                <span class="label label-danger">{{ $feedback_new }}</span>
                            @endif
                        </a></li>
                    <li><a href="{{ route('admin.users') }}"><i class="fa fa-fw fa-group"></i></a></li>
                    <li><a href="{{ route('admin.pages',['sitemap' => 1]) }}" title="Обновить sitemap.xml"><i
                                    class="fa fa-fw fa-sitemap" title="Обновить sitemap.xml"></i></a></li>
                    <li><a href="{{ route('admin.clear-cache') }}" title="Очистить кеш"
                           onclick="siteClearCache(this, event)"><i
                                    class="fa fa-fw fa-refresh" title="Очистить кеш"></i></a>
                    </li>
                    <li>
                        <a href="{{ route('admin.update-search-index') }}"
                           title="Обновить поисковый индекс" onclick="updateSearchIndex(this, event)">
                            <i class="fa fa-fw search-index" title="Обновить поисковый индекс">S</i>
                        </a>
                    </li>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <p>
                                    {{ Auth::user()->name }}
                                    <small>
                                        Зарегистрирован {{ date('d.m.Y', strtotime(Auth::user()->created_at)) }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ route('admin.users.edit', [Auth::user()->id]) }}"
                                       class="btn btn-default btn-flat"
                                       onclick="popupAjax($(this).attr('href')); return false;">Профиль</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ route('auth') }}" class="btn btn-default btn-flat">Выйти</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            @include('admin::blocks.admin_main_menu')
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            @yield('page_name')
            @yield('breadcrumb')
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('admin::blocks.admin_footer')

    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
</body>
</html>
