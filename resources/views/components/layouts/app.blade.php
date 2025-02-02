<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>{{ $title ?? 'Lead form generator' }}</title>
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" />
    <style>
        .invalid-feedback {
            display: block;
        }

        .cursor-pointer {
            cursor: pointer;
        }
        .breadcrumb{
            display: none;
        }
        .map-outer{
        overflow: hidden;
        }
        .map-outer #map {
            height: 600px;
            width: 100%;
            margin-top: 20px;
            padding: 2px;
            position: absolute !important;
            border: 0.5px solid black;
        }
    </style>
    @stack('styles')
</head>

<body class="sb-nav-fixed">
    @livewire('layouts.header')
    <div id="layoutSidenav">
        @livewire('layouts.sidebar')
        <div id="layoutSidenav_content" class="ps-lg-0">
            <main>
                {{ $slot }}
            </main>
            @livewire('layouts.footer')
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src="{{asset('assets/js/jsQR.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        window.addEventListener('alert', ({
            detail: {
                type,
                message
            }
        }) => {
            Toast.fire({
                icon: type,
                title: message
            });
        })

        @if (Session::has('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ Session('error') }}"
            });
        @elseif (Session::has('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ Session('success') }}"
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>
