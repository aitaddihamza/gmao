<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center text-gray-700 hover:text-gray-900 focus:outline-none">
                    <span class="mr-2">{{ Auth::user()->name }}</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" 
                     @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    {{ Auth::user()->role  }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
