@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Stock</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container tableDiv">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5> 
            <div class="panel-control">
                <!-- <a href="{{route('packing.transport.stock')}}" class="btn btn-warning btn-sm">Transport Bag</a> -->
                <button type="button" class="btn btn-sm btn-warning" onclick="openTransportModel('For Godown')">Godown</button>
                <button type="button" class="btn btn-sm btn-success" onclick="openTransportModel('For Delivery')">Client</button>
            </div>           
        </div>
        <div class="panel-body">            
            <table class="table table-bordered  table-responsive table-fixed" id="postsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Packing Date</th>
                        <th>Packing No</th>
                        <th>Client Name</th>
                        <th>Bag Type</th>
                        <th>Bag Unit</th> 
                        <th>Bag Color</th>                      
                        <th>Bag Weight</th>
                        <th>Bag Piece</th>
                        <th>Bag Size </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editBagModal" tabindex="-1" aria-labelledby="editBagModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBagModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="editBagForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="packing_weight" class="control-label">Weight<span class="text-danger">*</span></label>
                            <input name="packing_weight"  id="packing_weight" class="form-control" required onkeypress="return isNum(event);"/>
                            <span class="error-text" id="packing_weight-error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="packing_bag_pieces" class="control-label">Piece<span class="text-danger" style="display:none;">*</span></label>
                            <input name="packing_bag_pieces"  id="packing_bag_pieces" class="form-control" onkeypress="return isNum(event);"/>
                            <span class="error-text" id="packing_bag_pieces-error"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitEditForm">Edit</button>
            </div>
            </div>
        </div>
    </div>

    <!-- transportModel -->
    <div class="modal fade" id="transportModel" tabindex="-1" aria-labelledby="transportModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transportModelLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="transportModelForm">
                        @csrf
                        <!-- Hidden field for Client ID -->
                        <div id="hiddenDiv">

                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="dispatchedDate">Dispatch Date<span class="text-danger">*</span></label>
                                    <input type="date" name="dispatchedDate" id="dispatchedDate" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required />
                                    <span class="error-text" id="dispatchedDate-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="autoId">Auto<span class="text-danger">*</span></label>
                                    <select name="autoId" id="autoId" class="form-select"  required >
                                        <option value="">select</option>
                                        @foreach($autoList as $val)
                                            <option value="{{$val->id}}">{{$val->auto_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="autoId-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4" id="transposerDiv">
                                <div class="form-group">
                                    <label class="form-label" for="transporterId">Transporter<span class="text-danger">*</span></label>
                                    <select name="transporterId" id="transporterId" class="form-select"  required >
                                        <option value="">select</option>
                                        @foreach($transporterList as $val)
                                            <option value="{{$val->id}}">{{$val->transporter_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="transporterId-error"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitTransportModal">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- model chalan -->
    <div class="modal fade" id="chalanModal" tabindex="-1" aria-labelledby="chalanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chalanModalLabel">Chalan Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfPreview" style="width: 100%; height: 500px; display: none;"></iframe>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-primary" id="downloadChalan">Download</button>
                <button type="button" class="btn btn-primary" id="transportBag">Transport</button>
            </div>
            </div>
        </div>
    </div>

</main>
<script>
    $(document).ready(function(){
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{route('packing.stock')}}",// The route where you're getting data from
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
                // { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false ,
                    render: function(data, type, row, meta) {
                            const rowDataEncoded = base64Encode(JSON.stringify(row));
                            return `${meta.row + 1} <input type="checkbox" name="checkbox[]" data-row='${rowDataEncoded}' value="${row?.id}" class="row-select checkbox" >`;
                        }
                },
                { data: "packing_date", name: "packing_date" },
                { data: "packing_no", name: "packing_no" },
                { data: "client_name", name: "client_name" },
                { data: "bag_type", name: "bag_type" },
                { data: "units", name: "units" },
                { data: "bag_color", name: "bag_color" },
                { data: "packing_weight", name: "packing_weight" },
                { data: "packing_bag_pieces", name: "packing_bag_pieces" },
                { data: "bag_size", name: "bag_size",render: function(item) {  return `<pre>${item}</pre>`; }},                
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
            buttons: [{
                extend: 'csv',
                text: 'Export to Excel',
                className: 'btn btn-success',

            }],  
            createdRow: function(row, data, dataIndex) {
                $(row).attr('data-id', data.id);
                $(row).attr('data-item', JSON.stringify(data));
            },          
            initComplete: function () {
                addFilter('postsTable',[0,$('#postsTable thead tr:nth-child(1) th').length - 1]);
            },
        });
        $("#submitEditForm").on("click",function(){
            $("#editBagForm").submit();
        });
        $("#editBagForm").validate({
            rules: {
                packing_weight: {
                    required: true,
                    number: true,
                },
            },
            submitHandler: function(form) {
                showConfirmDialog("Are sure want to edit??",editBagSubmit);
            }
        });

        $("#submitTransportModal").on("click",function(){
            $("#transportModelForm").submit();
        });
        $("#transportModelForm").validate({
            rules: {
                dispatchedDate: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                generateChalan();
            }
        });

    });

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[0]);
        },false);
        
    }

    function editBag(id) {
        $.ajax({
            url: "{{ route('packing.bag.dtl', ['id' => ':id']) }}".replace(':id', id),
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success: function(response) {
                $("#loadingDiv").hide();
                $("#packing_bag_pieces").parent("label").find("span").css("display","none");
                $("#packing_bag_pieces").closest(".form-group").find("label span.text-danger").css("display", "none");
                $("#packing_bag_pieces").attr("required",false);
                if(response.status && response.data.id){
                    let item = response.data;
                    $("#id").val(item.id);
                    $("#packing_weight").val(item.packing_weight);
                    $("#packing_bag_pieces").val(item.packing_bag_pieces);
                    if(item.units!="Kg"){
                        $("#packing_bag_pieces").closest(".form-group").find("label span.text-danger").css("display", "inline"); // Show the asterisk
                        $("#packing_bag_pieces").attr("required",true);
                    }
                    $("#editBagModal").modal("show");
                }
                else{
                    modelInfo("server error!!","warning");
                }
                console.log(response); // Handle the response here
            },
            error: function(errors) {
                $("#loadingDiv").hide();
                console.log(errors);
                modelInfo("server error!!","error");
            }
        });
    }

    function editBagSubmit(){
        $.ajax({
            url:"{{route('packing.bag.edit')}}",
            type:"post",
            dataType:"json",
            data:$("#editBagForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    $("#editBagModal").modal("hide");
                    modelInfo(response?.message);
                    searchData();
                }else{
                    modelInfo("server Error","warning");
                }
            },
            error:function(error){
                $("#loadingDiv").hide();
                console.log(error);
                modelInfo("server Error","error");
            }
        })
    }

    function deleteBag(id){
        $.ajax({
            url:"{{route('packing.bag.delete')}}",
            type:"post",
            dataType:"json",
            data:{id:id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                modelInfo(response.message,response.status?"success":"warning");
                if(response.status){
                    searchData();
                }
            },
            error:function(errors){
                console.log(error);
                modelInfo("server error","error");
            }
        })
    }

    function openTransportModel(transportType) {
        let sequence = [];

        $(".checkbox").each(function () {
            if ($(this).is(":checked")) {
                let jsonData = JSON.parse(base64Decode($(this).attr("data-row")));
                sequence.push(jsonData);
            }
        });

        // ✅ Fix: Check if sequence is empty
        if (sequence.length === 0) {
            alert("Please select at least one bag");
            return;
        }

        let hidden = "";
        let is_local_order=true;
        let rateType = [];
        let client=[];

        // ✅ Fix: Use `forEach` correctly
        sequence.forEach((item) => {
            if(!item.is_local_order && is_local_order){
                is_local_order=false;
            }
            if (!rateType[item.rate_type_id]) {
                rateType[item.rate_type_id] = item.rate_type;
            }
            if (!client[item.client_detail_id]) {
                client[item.client_detail_id] = item.client_name;
            }

            hidden += `<input type='hidden' name="bag[][id]" value="${item.id}" />`;
        });
        hidden+=`<input type='hidden' id='transPortType' name='transPortType' value="${transportType}" />`;
        
        console.log(rateType);
        if (Object.keys(rateType).length > 1 && transportType!="For Godown") {
            alert("Cannot generate different rate type Chalan");
            return;
        }
        if (Object.keys(client).length > 1 && transportType!="For Godown") {
            alert("Cannot generate Chalan for more then one client");
            return;
        }
        $("#transposerDiv").show();
        $("#transporterId").attr("required",true);
        if(is_local_order || transportType=="For Godown"){
            $("#transposerDiv").hide();            
            $("#transporterId").attr("required",false);
        }
        $("#hiddenDiv").html(hidden);
        $("#transportModelLabel").html(transportType);
        $("#transportModel").modal("show");

    }

    function generateChalan(){
        let formData = $("#transportModelForm").serialize();
        $.ajax({
            url:"{{route('packing.generate.chalan')}}",
            type:"post",
            dataType:"json",
            data:formData,
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                console.log(response);
                if(response.status){
                    console.log("Extra Data:", response.data);
                    let chalanNo = response.data.chalanNo;
                    let pdfBase64 = response.data.pdfBase64;
                    let isDownload = false;
                    let pdfDataUri = "data:application/pdf;base64," + pdfBase64;
                    $("#pdfPreview").attr("src", pdfDataUri).show();

                    $("#downloadChalan").show().off("click").on("click", function () {
                        isDownload= true;
                        let pdfBlob = base64ToBlob(pdfBase64, "application/pdf");
                        let link = document.createElement("a");
                        link.href = URL.createObjectURL(pdfBlob);
                        link.download = chalanNo+".pdf";
                        link.click();
                    });

                    $("#transportBag").show().off("click").on("click", function () {
                        let formDataNew = new FormData($("#transportModelForm")[0]); 
                        formDataNew.append("chalanUniqueId", response.data.uniqueId);
                        formDataNew.append("invoiceNo", response.data.chalanNo);

                        $.ajax({
                            url: "{{route('packing.transport.save')}}",
                            type: "post",
                            data: formDataNew,
                            dataType: "json",
                            processData: false, // 🔥 Prevents jQuery from serializing FormData
                            contentType: false, // 🔥 Ensures proper content type for FormData
                            beforeSend: function () {
                                $("#loadingDiv").show();
                                $("#transportBag").prop("disabled", true);
                            },
                            success: function (data) {
                                $("#loadingDiv").hide();

                                if (data.status) {
                                    if(!isDownload){
                                        $("#downloadChalan").click();
                                    }
                                    $("#transportModelForm").get(0).reset();
                                    $("#chalanModal").modal("hide");
                                    $("#transportModel").modal("hide");                                    
                                    $("#transportBag").prop("disabled", false);
                                    modelInfo(data.messages);
                                    searchData();
                                } else {
                                    console.log(data);
                                    modelInfo("Internal Server Error", "warning");
                                }
                            },
                            error: function (errors) {
                                console.log(errors);
                                $("#loadingDiv").hide();
                                modelInfo("Server error", "error");
                            }
                        });
                    });

                    $("#chalanModal").modal("show");
                }
            },
            error: function (xhr, status, error) {
                $("#loadingDiv").hide();
                console.error("AJAX error:", error);
                alert("An error occurred while generating the PDF.");
            }

        });
    }

    function base64ToBlob(base64, mimeType) {
        let byteCharacters = atob(base64);
        let byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        let byteArray = new Uint8Array(byteNumbers);
        return new Blob([byteArray], { type: mimeType });
    }

    $("#chalanModal").on("hidden.bs.modal", function () {
        $("#transportBag").prop("disabled", false);
    });

</script>
@include("layout.footer")