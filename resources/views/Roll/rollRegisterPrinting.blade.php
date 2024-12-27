@include("layout.header")
<!-- Main Component -->

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Production</a></li>
                    <li class="breadcrumb-item fs-6"><a href="#">Register</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">{{$machine->name??""}} Register</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3">
                    <!-- From Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>

                    <!-- Upto Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-3">
                        <!-- Search Button -->
                        <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                    </div>
                </div>
            </form>

        </div>
        <div class="panel-body">
            <table id="postsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th >#</th>
                        <th>Purchase Date</th>
                        <th>Vendor Name</th>
                        <th>Hardness</th>
                        <th>Roll Type</th>
                        <th>Roll Size</th>
                        <th>GSM <span class="fs-6 fw-light">(gsm/laminate/bopp)</span></th>
                        <th>Roll Color</th>
                        <th>Length</th>
                        <th>Roll No</th>
                        <th>Gross Weight</th>
                        <th>Net Weight</th>
                        <th>GSM Variation</th>

                        <th>W</th>
                        <th>L</th>
                        <th>G</th>
                        <th>Bag Type</th>
                        <th>Unit</th>
                        <th>Customer</th>
                        <th>Printing Color</th>
                        <th>Loop Color</th>
                        <th>Printing Date</th>
                        <th>Cutting Date</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-printing-update-form />
    <x-cutting-update-form />
</main>
<script>
    

    var machineId = window.location.pathname.split('/').pop();
    $(document).ready(function() {  

        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{route('roll.register.printing',':machineId')}}".replace(':machineId', machineId), // The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

                },
                beforeSend: function() {
                    $("#btn_search").val("LOADING ...");
                    $("#loadingDiv").show();
                },
                complete: function() {
                    $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "purchase_date", name: "purchase_date" },
                { data: "vendor_name", name: "vendor_name" },
                { data: "hardness", name: "hardness" },
                { data: "roll_type", name: "roll_type" },
                { data: "size", name: "size" },
                { data: "gsm", name: "gsm" },
                { data: "roll_color", name: "roll_color" },
                { data: "length", name: "length" },
                { data: "roll_no", name: "roll_no" },
                { data: "gross_weight", name: "gross_weight" },
                { data: "net_weight", name: "net_weight" },
                { data: "gsm_variation", name: "gsm_variation" },
                { data : "w", name: "w" },
                { data : "l", name: "l" },
                { data : "g", name: "g" },
                { data : "bag_type", name: "bag_type" },
                { data : "bag_unit", name: "bag_unit" },
                { data : "client_name", name: "client_name" },
                { data : "print_color", name: "print_color" },
                { data : "loop_color", name: "loop_color" },
                { data : "printing_date", name: "printing_date" },
                { data : "cutting_date", name: "cutting_date" },
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [{
                    extend: 'excel',
                    text: 'Export to Excel',
                    className: 'btn btn-success',
            }],            
            initComplete: function () {
                addFilter('postsTable',[0]);
            },
        });

    });

    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }
</script>
@include("layout.footer")