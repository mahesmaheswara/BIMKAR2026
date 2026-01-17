<div class="navbar bg-base-100 shadow-sm">
    <div class="navbar-start">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                </svg>
            </div>
        </div>

        <img src="{{ asset('assets/images/logo_bengkod.svg') }}" class="h-8 md:h-10 w-auto" />
    </div>

    <div class="navbar-center hidden lg:flex">
        <form action="{{ route('home') }}" method="GET" class="flex gap-2">
            {{-- jaga supaya filter kategori tidak hilang saat search --}}
            @if (request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif

            <input type="text" name="q" value="{{ request('q') }}" class="input w-72"
                placeholder="Cari Event..." />

            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
    </div>


    <div class="navbar-end gap-2">
        @guest
            <a href="{{ route('login') }}" class="btn bg-blue-900 text-white">Login</a>
            <a href="{{ route('register') }}" class="btn text-blue-900">Register</a>
        @else
            <a href="{{ route('pemesanan.riwayat') }}" class="btn btn-outline">Riwayat <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg></a>
            

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline btn-error">
                    Logout
                </button>
            </form>
        @endguest
    </div>
</div>
