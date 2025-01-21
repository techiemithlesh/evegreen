@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Loop Stock</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <!-- <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#gradeModal" onclick="resetModelForm()">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div> -->
        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Loop Color</th>
                    <th>Stock</th>
                    <th>Min Limit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div class="modal fade modal-lg" id="loopStockModal" tabindex="-1" aria-labelledby="loopStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loopStockModalLabel">Edit Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loopStockForm">
                        @csrf
                        <!-- Hidden field for Client ID -->
                        <input type="hidden" id="id" name="id" value="">

                        <!-- Client Name -->
                        <div class="mb-3">
                            <label class="form-label" for="loopColor">Loop Color</label>
                            <input type="text"  id="loopColor" name="loopColor" class="form-control" readonly />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="balance"> Balance<span class="text-danger">*</span></label>
                            <input type="text"  id="balance" name="balance" class="form-control" placeholder="Enter Stock Balance" required  onkeypress="return isNumDot(event);" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="minLimit"> Min Limit<span class="text-danger">*</span></label>
                            <input type="text"  id="minLimit" name="minLimit" class="form-control" placeholder="Enter Stock Min Limit" required  onkeypress="return isNumDot(event);" />
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-sm-12 text-end">
                                <button type="submit" class="btn btn-success" id="submit">Edit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('master.loop.stock.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "loop_color",
                    name: "loop_color"
                },
                {
                    data: "balance",
                    name: "balance"
                },
                {
                    data: "min_limit",
                    name: "min_limit"
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
        });

        $("#loopStockForm").validate({
            rules: {
                id: {
                    required: true,
                },
                balance: {
                    required: true,
                },
                min_limit: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                editStock();
            }
        });
    });

    function editStock(){
        $.ajax({
                type: "POST",
                'url':"{{route('master.loop.stock.edit')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#loopStockForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        resetModelForm();
                        $("#loopStockModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    }  

    function openModelEdit(id){
        $.ajax({
            type:"get",
            url: "{{ route('master.loop.stock.dtl', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    bagDtl = data.data;
                    console.log(bagDtl); 
                    $("#id").val(bagDtl?.id);
                    $("#loopColor").val(bagDtl?.loop_color);
                    $("#balance").val(bagDtl?.balance);
                    $("#minLimit").val(bagDtl?.min_limit);
                    $("#loopStockModal").modal("show");
                    $("#submit").html("Edit");
                    $("#gradeModalLabel").html("Edit Stock");                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function resetModelForm(){
        $("#loopStockForm").get(0).reset();
        $("#id").val("");
        $("#submit").html("Add");
        $("#gradeModalLabel").html("Add Stock");
    }

    function deactivate(id){
        $.ajax({
            type:"post",
            url: "{{ route('master.loop-stock.deactivate', ':id') }}".replace(':id', id),
            dataType: "json",
            data:{lock_status:true},
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    $('#postsTable').DataTable().draw();
                    modelInfo(data?.message,"success");
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

</script>

@include("layout.footer")