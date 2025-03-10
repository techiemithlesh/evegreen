@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Godown</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>   
            <div class="panel-control">
                <a href="{{route('packing.godown.reiving')}}" class="btn btn-primary btn-sm">Add Bag</a>
                <a href="{{route('packing.transport.for','For Delivery')}}" class="btn btn-warning btn-sm">Transport Bag</a>
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
</main>
<script>
    $(document).ready(function(){
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{route('packing.godown')}}",// The route where you're getting data from
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
</script>
@include("layout.footer")