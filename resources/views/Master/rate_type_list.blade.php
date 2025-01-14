@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Rate Type</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#rateTypeModal" onclick="resetModelForm()">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Rate Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    
    <!-- Modal Form -->
    <div class="modal fade modal-lg" id="rateTypeModal" tabindex="-1" aria-labelledby="rateTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rateTypeModalLabel">Add Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rateTypeForm">
                        @csrf
                        <!-- Hidden field for Client ID -->
                        <input type="hidden" id="id" name="id" value="">

                        <!-- Client Name -->
                        <div class="mb-3">
                            <label class="form-label" for="rateType">Rate Type<span class="text-danger">*</span></label>
                            <input type="text" maxlength="100" id="rateType" name="rateType" class="form-control" placeholder="Rate Type" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-sm-12 text-end">
                                <button type="submit" class="btn btn-success" id="submit">Add</button>
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
            ajax: "{{route('master.rate.type.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "rate_type",
                    name: "rate_type"
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
        });
        $("#rateTypeForm").validate({
            rules: {
                rateType: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addFare();
            }
        });
    });
    function addFare(){
        $.ajax({
                type: "POST",
                'url':"{{route('master.rate.type.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#rateTypeForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        resetModelForm();
                        $("#rateTypeModal").modal('hide');
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
            url: "{{ route('master.rate.type.dtl', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    bagDtl = data.data;
                    console.log(bagDtl); 
                    $("#id").val(bagDtl?.id);
                    $("#rateType").val(bagDtl?.rate_type);
                    $("#rateTypeModal").modal("show");
                    $("#submit").html("Edit");
                    $("#rateTypeModalLabel").html("Edit Rate Type");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function resetModelForm(){
        $("#rateTypeForm").get(0).reset();
        $("#id").val("");
        $("#submit").html("Add");
        $("#rateTypeModalLabel").html("Add Rate Type");
    }

</script>

@include("layout.footer")