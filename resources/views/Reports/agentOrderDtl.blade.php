@include("layout.header")

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Report</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Agent Order</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">   
        <div class="panel-body">
            <form action="" id="searchForm">
                <div class="row">                    
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="fromDate">From Date<span class="text-danger">*</span></label>
                                <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$fromDate??false ? $fromDate : date('Y-m-d')}}" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="uptoDate">Upto Date<span class="text-danger">*</span></label>
                                <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$uptoDate??false ? $uptoDate : date('Y-m-d')}}"  />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="agentId">Agent</label>
                                <select id="agentId" name="agentId" class="form-select"  >
                                    <option value="">Select</option>
                                    @foreach($agentList as $val)
                                        <option value="{{$val->id}}" {{($agentId??false) == $val->id ? 'selected' :''}}>{{$val->broker_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>                  
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-success" id="btn_search" onclick="searchData()">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>         
        <div class="panel-body"> 
            <table id="postTable" class="table table-responsive table-striped table-fixed">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Order Date</th>
                        <th>Bag Size</th>
                        <th>Unit</th>
                        <th>Total Qty</th>
                        <th>Is Delivered</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    $(document).ready(function(){
        $("#agentId").select2({
            width:"100%",
            display:"block"
        });
        history.replaceState(null, "", window.location.pathname);
        $("#postTable").DataTable({
            processing: true,
            serverSide: false,
            searching:true,
            ajax: {
                url: "{{route('report.agent.order.dtl')}}", // The route where you're getting data from  
                data: function(d) {
                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

                },              
                beforeSend: function() {
                    if ($("#btn_search").is("button")){
                        $("#btn_search").html("LOADING ...");
                    }else if($("#btn_search").is("input")){
                        $("#btn_search").val("LOADING ...");
                    }
                    $("#loadingDiv").show();
                },
                complete: function() {
                    if ($("#btn_search").is("button")){
                        $("#btn_search").html("SEARCH");
                    }else if($("#btn_search").is("input")){
                        $("#btn_search").val("SEARCH");
                    }
                    // $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },

            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "client_name", name: "client_name" },
                { data: "order_date", name: "order_date" },
                { data: "bag_size", name: "bag_size" },
                { data: "units", name: "units" },                 
                { data: "total_units", name: "total_units" },
                { data: "is_delivered", name: "is_delivered" }, 
                
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel-fill text-success"></i> ',
                    className: 'btn btn-success',
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf-fill text-danger"></i>',
                    title: 'Data Export',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: [0, 1,2, 3,4,5,6,7,8,9,10]  // Export only Name, Position, and Age columns
                    // }

                },
            ],  
            initComplete: function () {
                addFilter('postTable',[0]);
            },
        })
    });

    function searchData(){
        $("#postTable").DataTable().ajax.reload();
    }
</script>

@include("layout.footer")