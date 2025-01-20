@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Transport</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container"> 
        <div class="panel-body">
            <form action="" id="searchForm" >
                <div class="row">                    
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="fromDate">From Date<span class="text-danger">*</span></label>
                                <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required />
                                <span class="error-text" id="fromDate-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="uptoDate">Upto Date<span class="text-danger">*</span></label>
                                <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required />
                                <span class="error-text" id="uptoDate-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="autoId">Auto</label>
                                <select type="text" id="autoId" name="autoId" class="form-select">
                                    <option value="">Select</option>
                                    @foreach($autoList as $val)
                                        <option value="{{$val->id}}">{{$val->auto_name}}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="transPortType-error"></span>
                            </div>
                        </div> 
                                                
                        <div class="col-sm-3">
                            <div class="form-group">
                                    <label class="form-label" for="transporterId">Transporter</label>
                                    <select type="text" id="transporterId" name="transporterId" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($transporterList as $val)
                                            <option value="{{$val->id}}">{{$val->transporter_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="transporterId-error"></span>
                                </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="billNo">Bill No.</label>
                                <input type="text" name="billNo" id="billNo" class="form-control"  />
                                <span class="error-text" id="billNo-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="invoiceNo">Chalan No.</label>
                                <input type="text" name="invoiceNo" id="invoiceNo" class="form-control"  />
                                <span class="error-text" id="invoiceNo-error"></span>
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
            <table class="table table-bordered table-responsive table-fixed" id="postTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transition Type</th>
                        <th>Delivery Date</th>
                        <th>Auto Name</th>
                        <th>Transporter Name</th>
                        <th>Chalan No</th>
                        <th>Bill No</th>
                        <th>Bags</th>
                        <th>Client Name</th>
                        <th>View</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</main>
<script>
    $(document).ready(function(){

        const table = $('#postTable').DataTable({
            processing: true,
            serverSide: false,
            searching:false,
            ajax: {
                url: "{{route('packing.transport.register')}}", // The route where you're getting data from  
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
                { data: "transition_type", name: "transition_type", orderable: false, searchable: false },
                { data: "transport_date", name: "transport_date", orderable: false, searchable: false },                
                { data: "auto_name", name: "auto_name", orderable: false, searchable: false },                
                { data: "transporter_name", name: "transporter_name", orderable: false, searchable: false },
                { data: "invoice_no", name: "invoice_no", orderable: false, searchable: false },
                { data: "bill_no", name: "bill_no", orderable: false, searchable: false },
                { data: "bag_no", name: "bag_no", orderable: false, searchable: false },
                { data: "client_name", name: "client_name", orderable: false, searchable: false },
                { data: "action", name: "action", orderable: false, searchable: false },
                
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [],    
        });
    });

    function searchData(){
        $('#postTable').DataTable().ajax.reload();
    }
</script>
@include("layout.footer")