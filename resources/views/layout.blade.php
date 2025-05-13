<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="/favicon.ico">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">
    @if (session('success') || session('error') || $errors->any())
        <div class="alert-container" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
            data-errors="{{ json_encode($errors->all()) }}" style="display: none;">
        </div>
    @endif

    <div class="min-h-screen">
        @include('components.navbar')

        <main class="container-xl py-8 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.alert-container');
            if (!container) return;

            const success = container.dataset.success;
            const error = container.dataset.error;
            const validationErrors = JSON.parse(container.dataset.errors);

            if (success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: success,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000
                });
            }

            if (error || validationErrors.length > 0) {
                console.error(error, validationErrors);
                const errorMessage = error || validationErrors.join('\n');

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMessage.replace(/\n/g, '<br>'),
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000
                });
            }
        });
    </script>
</body>

</html>
