<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
       
        <style>
            .notifyjs-corner {
                position: fixed;
                margin: 5px;
                z-index: 999999 !important;
            }

            .youtube-icon {
                width: 30px;
                /* Adjust the width as needed */
                height: auto;
                /* Maintain aspect ratio */
                margin-right: 5px;
                /* Add some spacing between the icon and the text */
            }

            .app-brand-logo img,
            .app-brand-logo svg {
                display: inline;
            }
        </style>
        @yield('head')
    </head>

    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <div class="layout-page">
                    <div class="content-wrapper">
                        @yield('content')
                        <!-- Footer -->
                        {{-- <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                           
                        </div>
                    </footer> --}}
                        <!-- / Footer -->
                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        
    </body>
    <script>
        @auth
        var source = new EventSource("{{ URL('/sse-updates') }}");

        source.onmessage = function(event) {
            let ac = JSON.parse(event.data);
            $.notify(ac.message, 'success');
        }
        @endauth
    </script>
    @yield('script')

</html>