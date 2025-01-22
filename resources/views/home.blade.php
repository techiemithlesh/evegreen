@include("layout.header")
<!-- Main Component -->

    <main class="p-3">
        <div class="container-fluid"> 
            <div class="mb-3 text-center">
                <h1>Dashboard</h1>
                <div>
                    <x-dashboard.loop-stock-status />
                </div>
                <div>
                @livewireStyles
                @livewire('dashboard.nearest-dispatched-order')
                @livewireScripts
                @stack('scripts') <!-- Include the custom scripts -->
                </div>
            </div>
        </div>
    </main>
@include("layout.footer")