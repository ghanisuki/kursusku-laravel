<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            KursusKu
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('courses.*')) active @endif"
                       href="{{ route('courses.index') }}">Kursus</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('enrollments.create', 'enrollments.store')) active @endif"
                       href="{{ route('enrollments.create') }}">
                        Pendaftaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('enrollments.index')) active @endif"
                       href="{{ route('enrollments.index') }}">
                        Laporan Enrollment
                    </a>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('login')) active @endif"
                           href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-success btn-sm" href="{{ route('register') }}">Daftar</a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('profile')) active @endif"
                           href="{{ route('profile') }}">Profil Saya ({{ auth()->user()->name }})</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
