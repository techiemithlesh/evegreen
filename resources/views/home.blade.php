@include("layout.header")

<!-- Add Custom Styles -->
<style>
    .card {
        /* background: #fff; */
        /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);  */
        /* Optional shadow for design */
        border: none; /* Remove border */
        border-radius: 10px; /* Keep rounded corners */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        padding: 15px; /* Adjust padding */
        text-align: center;
        /* overflow: hidden;  */
        /* Prevent content overflow */
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        /* overflow-y: auto;  */
        /* Add scrolling for overflowing content */
    }
    .card-body-title {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        padding: 5px 0;
        /* background-color: #007BFF;
        color: white; */
        margin: 0;
    }


    .row.g-3 {
        gap: 1.5rem; /* Adjust the gap between rows */
    }

    /* Scrollbar hidden but allow scrolling */
    body::-webkit-scrollbar {
        display: none;
    }
</style>

<!-- Main Component -->
<main class="p-3">
    <div class="container">
        <div class="panel-body" style="overflow: hidden; height: 100vh;">
            <div class="row g-3">
                <!-- Stock Status Card -->
                <div class="col-md-4 d-flex flex-column">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-body-title">Loop Stock</h6>
                            <x-dashboard.loop-stock-status />
                        </div>
                    </div>
                </div>

                <!-- Nearest Dispatched Order Card -->
                <div class="col-md-4 d-flex flex-column">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-body-title">Nearest Dispatched Order</h6>
                            @livewireStyles
                            @livewire('dashboard.nearest-dispatched-order')
                            @livewireScripts
                            @stack('scripts') <!-- Include the custom scripts -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include("layout.footer")
