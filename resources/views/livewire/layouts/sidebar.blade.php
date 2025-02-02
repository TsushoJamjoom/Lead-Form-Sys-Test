<div id="layoutSidenav_nav" class="d-lg-none">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <div class="sb-sidenav-menu-heading">Dashboard</div>
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-dashboard"></i></div>
                    Dashboard
                </a>

                @permission('ticket/list')
                    <div class="sb-sidenav-menu-heading">Ticket Management</div>
                    <a class="nav-link {{ request()->is('ticket/*') ? 'active' : '' }}" href="{{ route('ticket-list') }}"
                        wire:navigate>
                        <div class="sb-nav-link-icon"><i class="fas fa-ticket"></i></div>
                        Tickets
                    </a>
                @endpermission

                @permission('user/list')
                    <div class="sb-sidenav-menu-heading">User Management</div>
                    <a class="nav-link {{ request()->is('user/*') ? 'active' : '' }}" href="{{ route('user-list') }}"
                        wire:navigate>
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Users
                    </a>
                @endpermission

                @permission('customer/list')
                    <div class="sb-sidenav-menu-heading">Company Management</div>
                    <a class="nav-link {{ request()->is('company/*') ? 'active' : '' }}" href="{{ route('company-list') }}"
                        wire:navigate>
                        <div class="sb-nav-link-icon"><i class="fas fa-city"></i></div>
                        Company
                    </a>
                @endpermission

                @permission('sales_lead/list')
                    <div class="sb-sidenav-menu-heading">Sales Lead Management</div>
                    <a class="nav-link {{ request()->is('sales-lead/*') ? 'active' : '' }}" href="{{ route('sales-lead-list') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                        Sales Lead
                    </a>
                    <a class="nav-link {{ request()->is('sales-lead-history') ? 'active' : '' }}" href="{{ route('sales-lead-history') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                        Sales Lead History
                    </a>
                @endpermission


                @permission('calendar/list')
                    <div class="sb-sidenav-menu-heading">Calendar Management</div>
                    <a class="nav-link {{ request()->is('calendar') ? 'active' : '' }}"
                        href="{{ route('calendar') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-calendar"></i></div>
                        Calendar
                    </a>
                @endpermission

                @permission('history/list')
                    <div class="sb-sidenav-menu-heading">History Report</div>
                    <a class="nav-link {{ request()->is('history/*') ? 'active' : '' }}"
                        href="{{ route('history-list') }}" wire:navigate>
                        <div class="sb-nav-link-icon"><i class="fas fa-file-text"></i></div>
                        History
                    </a>
                @endpermission
                @permission('map/list')
                    <div class="sb-sidenav-menu-heading">Map</div>
                    <a class="nav-link {{ request()->is('map') ? 'active' : '' }}" href="{{ route('map') }}"
                        wire:navigate>
                        <div class="sb-nav-link-icon"><i class="fas fa-map"></i></div>
                        Map
                    </a>
                @endpermission
            </div>
        </div>
    </nav>
</div>
