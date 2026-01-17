<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>

    {{-- library component siap pakai --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>
    <div class="drawer lg:drawer-open w-full min-h-screen bg-gray-50">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content">
            <!-- Page content -->
            {{ $slot }}
        </div>

        @include('components.admin.sidebar')
    </div>

    <footer class="bg-light text-center py-3">
        <div class="container">
            <p>© {{ date('Y') }} MyLaravelApp. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Section untuk script tambahan --}}
    @stack('scripts')
</body>

</html>