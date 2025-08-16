@extends('layouts.app')

@section('content')
<div id="customer-dashboard" class="min-h-screen flex flex-col">

    {{-- Top Bar --}}
    <div class="w-full bg-white flex items-center justify-between px-4 py-2 border-b border-gray-200 text-sm font-semibold relative">
        {{-- Left: Logo --}}
        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="flex flex-col justify-between w-6 h-5 mr-4 focus:outline-none">
                <span class="block h-0.5 bg-black"></span>
                <span class="block h-0.5 bg-black"></span>
                <span class="block h-0.5 bg-black"></span>
            </button>
            <img src="/images/washwiselogo2.png" alt="WashWise Logo" class="h-10 w-auto" draggable="false" />
        </div>

        {{-- Right: User --}}
        <div class="relative">
            <button onclick="toggleProfileMenu()" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                <img src="{{ auth()->user()->avatar ?? '/images/default-avatar.png' }}" alt="Profile" class="w-8 h-8 rounded-full border border-gray-300" />
                <span class="text-gray-700 font-medium">{{ auth()->user()->name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown --}}
            <div id="profile-menu" class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                <div class="py-2 space-y-1">
                    <a href="#" class="flex items-center gap-2 px-4 py-2 w-full text-left text-gray-700 hover:bg-gray-100 transition rounded-lg">
                        👤 Edit Profile
                    </a>
                    <a href="#" class="flex items-center gap-2 px-4 py-2 w-full text-left text-gray-700 hover:bg-gray-100 transition rounded-lg">
                        ⚙️ Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-2 px-4 py-2 w-full text-left text-red-600 hover:bg-red-50 transition rounded-lg">
                            🚪 Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white text-black shadow-lg z-50 transform -translate-x-full transition-transform duration-300">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h2 class="text-lg font-bold">Menu</h2>
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        </div>
        <nav class="flex-1 px-3 mt-4 space-y-2">
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white font-semibold shadow-md transition-all duration-300">
                📅 Book a Service
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white font-semibold shadow-md transition-all duration-300">
                📜 Booking History
            </a>
        </nav>
    </aside>

    {{-- Hero Section --}}
    <section class="relative flex flex-col items-center justify-center min-h-[40vh] bg-cover bg-center" style="background-image: url('/images/hero-carwash.jpg');">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="relative z-10 text-center text-white p-8">
            <h1 class="text-4xl font-bold mb-4">Find and Book Your Car Wash</h1>
            <p class="text-lg">Choose from our trusted partner shops</p>
        </div>
    </section>

    {{-- Shop List --}}
    <div class="container mx-auto py-10 px-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($shops as $shop)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                @if($shop->logo)
                    <img src="{{ asset('storage/'.$shop->logo) }}" alt="{{ $shop->name }}" class="w-full h-40 object-cover" />
                @endif
                <div class="p-4">
                    <h3 class="text-lg font-semibold">{{ $shop->name }}</h3>
                    <p class="text-gray-600">{{ $shop->address }}</p>
                    <a href="{{ route('customer.booking.create', $shop->id) }}" class="mt-3 inline-block px-4 py-2 bg-[#FF2D2D] text-white rounded-lg hover:bg-red-600">
                        Book Now
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>

{{-- Scripts --}}
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
}
function toggleProfileMenu() {
    document.getElementById('profile-menu').classList.toggle('hidden');
}
</script>
@endsection
