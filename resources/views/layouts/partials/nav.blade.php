@auth
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse d-print-none">
        <div class="position-sticky pt-3">
            <ul class="nav flex-column">

                <!--CHAGAS-->
                {{-- @canany(['Developer', 'Administrator', 'Chagas:', 'Chagas: Administrador', 'Chagas: Solicitud', 'Chagas: Toma', 'Chagas: Seguimiento', 'Chagas: Bandejas', 'Chagas: Ficha', 'Chagas: Reportes'])
            <li class="nav-item">
                <a class="nav-link {{ active('chagas.*') }}" href="{{ route('chagas.welcome') }}">
                    <i class="fas fa-bug"></i> CHAGAS
                </a>
            </li>
            @endcanany --}}

                <!--CHAGAS-->
                {{-- @canany(['Developer', 'Administrator', 'Lab: Administrador'])
            <li class="nav-item">
                <a class="nav-link {{ active('labs.*') }}" href="{{ route('labs.welcome') }}">
                    <i class="fas fa-vial"></i> LABORATORIO
                </a>
            </li>
            @endcanany --}}

                <!--SAMU-->
                @canany(['Developer', 'Administrator', 'SAMU'])
                    <li class="nav-item">
                        <a class="nav-link {{ active('samu.*') }}" href="{{ route('samu.welcome') }}">
                            <i class="fas fa-ambulance fa-fw"></i> SAMU
                        </a>
                    </li>
                @endcanany

                <!--PROGRAMADOR MÉDICO-->
                @canany(['Mp: user'])
                    <li class="nav-item">
                        <a class="nav-link {{ active('medical_programmer.*') }}"
                            href="{{ route('medical_programmer.welcome') }}">
                            <i class="fa fa-user-md"></i> PROGRAMADOR MÉDICO
                        </a>
                    </li>
                @endcanany

                <!--ESTADISTICAS REM-->
                @canany(['STATS: REM'])
                    <li class="nav-item">
                        <a class="nav-link {{ active('samu.rem') }}" href="{{ route('samu.rem') }}">
                            <i class="fas fa-chart-bar fa-fw"></i> ESTADÍSTICAS REM
                        </a>
                    </li>
                @endcanany
            </ul>

            @can('Administrator')
                <h6
                    class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>ADMINISTRADOR</span>
                    <a class="link-secondary" href="#" aria-label="Add a new report">
                        <span data-feather="plus-circle" class="align-text-bottom"></span>
                    </a>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link {{ active('user.*') }}" href=" {{ route('user.index') }}">
                            <i class="fas fa-user"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ active('parameter.permission.index') }}"
                            href="{{ route('parameter.permission.index') }}">
                            <span data-feather="lock"></span>
                            Permisos<span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ active('user.edit') }}" href="{{ route('user.edit', auth()->id()) }}">
                            <span data-feather="unlock"></span>
                            Mis permisos
                        </a>
                    </li>
                @endcan

                <ul class="nav flex-column">
                    <li class="nav-item border-top">
                        @if (session()->has('god'))
                            <a class="nav-link" href="{{ route('user.switch', session('god')) }}">
                                <span class="text-danger" data-feather="eye"></span>
                                God Like
                            </a>
                        @endif
                        <a class="nav-link" href="{{ route('claveunica.logout') }}">
                            <span data-feather="log-out"></span>
                            Cerrar sesión
                        </a>
                    </li>
                </ul>
        </div>
    </nav>
@endauth
