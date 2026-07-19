@php $dir = app()->getLocale() === 'ar' ? 'rtl' : 'ltr'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.dashboard')) — Admin · Noor Jordan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 (LTR / RTL) --}}
    @if($dir === 'rtl')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet">

    {{-- Select2 (search-and-select dropdowns): add class="select2" to any <select> --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>

    {{-- Overlay for mobile sidebar --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    @include('admin.includes.sidebar')

    {{-- Main wrapper --}}
    <div class="main-wrapper" id="mainWrapper">

        {{-- Navbar --}}
        @include('admin.includes.navbar')

        {{-- Page content --}}
        <main class="main-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('admin.includes.footer')

    </div>{{-- /main-wrapper --}}

    {{-- Bootstrap JS (must be before panel.js) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/shared/js/panel.js') }}"></script>

    {{-- jQuery + Select2 (search-and-select dropdowns): add class="select2" to any <select> --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/admin/js/select2-init.js') }}"></script>
    @stack('scripts')
</body>
</html>
