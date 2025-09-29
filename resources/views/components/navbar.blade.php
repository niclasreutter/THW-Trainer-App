<nav class="navbar fixed-top w-full text-white shadow-lg" style="background-color: #00337F;">
    <div class="container mx-auto flex items-center justify-between py-3 px-4">
        <div class="flex items-center space-x-6">
            <a href="/" class="font-bold text-xl text-yellow-400">THW-Trainer</a>
            <a href="{{ route('dashboard') }}" class="hover:text-yellow-400">Dashboard</a>
            @auth
                @if(auth()->user()->useroll === 'admin')
                    <a href="{{ route('admin.index') }}" class="hover:text-yellow-400">Administration</a>
                @endif
            @endauth
        </div>
        <div>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-yellow-400 text-blue-900 font-bold px-4 py-2 rounded hover:bg-white hover:text-blue-900">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<style>
.navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 50; }
body { padding-top: 64px; }
</style>
