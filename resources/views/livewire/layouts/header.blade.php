<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark justify-content-lg-between">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="{{ route('dashboard') }}">Lead Form</a>
    <!-- Sidebar Toggle-->
    <button class="menu-btn-icon btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 d-lg-none" id="customSidebarToggle"
        data-event="true"><i class="fas fa-bars"></i></button>

    <nav class="navbar navbar-expand-sm bg-body-tertiary">
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-dashboard"></i></div>
                            Dashboard
                        </a>
                    </li>

                    @permission('ticket/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('ticket/list') ? 'active' : '' }}"
                                href="{{ route('ticket-list') }}" wire:navigate>
                                <div class="sb-nav-link-icon"><i class="fas fa-ticket"></i></div>
                                Tickets
                            </a>
                        </li>
                    @endpermission

                    @permission('user/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('user/*') ? 'active' : '' }}"
                                href="{{ route('user-list') }}" wire:navigate>
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Users
                            </a>
                        </li>
                    @endpermission


                    @permission('customer/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('company/*') ? 'active' : '' }}"
                                href="{{ route('company-list') }}" wire:navigate>
                                <div class="sb-nav-link-icon"><i class="fas fa-city"></i></div>
                                Company
                            </a>
                        </li>
                    @endpermission

                    @permission('sales_lead/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('sales-lead/*') ? 'active' : '' }}"
                                href="{{ route('sales-lead-list') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                                Sales Lead
                            </a>
                        </li>
                    @endpermission

                    @permission('sales_lead/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('sales-lead-history') ? 'active' : '' }}"
                                href="{{ route('sales-lead-history') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                Lead History
                            </a>
                        </li>
                    @endpermission

                    @permission('calendar/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('calendar') ? 'active' : '' }}"
                                href="{{ route('calendar') }}" >
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Calendar
                            </a>
                        </li>
                    @endpermission

                    @permission('history/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('history/*') ? 'active' : '' }}"
                                href="{{ route('history-list') }}" wire:navigate>
                                <div class="sb-nav-link-icon"><i class="fas fa-file-text"></i></div>
                                History
                            </a>
                        </li>
                    @endpermission

                    @permission('map/list')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('map/*') ? 'active' : '' }}" href="{{ route('map') }}"
                                wire:navigate>
                                <div class="sb-nav-link-icon"><i class="fas fa-map"></i></div>
                                Map
                            </a>
                        </li>
                    @endpermission

                </ul>
            </div>
        </div>
    </nav>
    <!-- Navbar-right-->
    <ul class="navbar-nav d-md-inline-block form-inline ms-auto ms-lg-0  me-0 me-md-3 my-2 my-md-0" x-data="{ expanded: false }">
        <li class="nav-item dropdown" @mouseover.away = "expanded = false">
            <a class="nav-link dropdown-toggle" id="navbarDropdown"
                href="javascript:void(0)" x-on:click="expanded = ! expanded">{{ optional(auth()->user())->name }}</a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" :class="expanded ? 'show' : ''">
                <li><a class="dropdown-item" href="{{route('profile')}}" wire:navigate>Profile</a></li>
                <li class="d-none">
                    <hr class="dropdown-divider" />
                </li>
                <li><button type="button" class="dropdown-item" wire:click="logout">Logout</button></li>
            </ul>
        </li>
    </ul>
</nav>
@push('scripts')
    <script>
        document.getElementById('customSidebarToggle').addEventListener("click", function(e) {
            const mydivclass = document.querySelector('body');
            if (mydivclass.classList.contains('sb-sidenav-toggled')) {
                mydivclass.classList.remove("sb-sidenav-toggled");
            } else {
                mydivclass.classList.add("sb-sidenav-toggled");
            }
        });
        // document.getElementById('navbarDropdown').addEventListener("click", function(e) {
        //     const mydivclass = document.querySelector('.dropdown-menu-end');
        //     if (mydivclass.classList.contains('show')) {
        //         mydivclass.classList.remove("show");
        //     } else {
        //         mydivclass.classList.add("show");
        //     }
        // });
    </script>
@endpush
