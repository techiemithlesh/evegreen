@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Vendor</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">Vendor List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#vendorModal" onclick="resetModelForm('vendorForm')">
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
                    <th>Vendor Name</th>
                    <th>GST NO</th>
                    <th>Email</th>
                    <th>Mobile No</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <x-vendor-form />
</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('vendor.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "vendor_name",
                    name: "vendor_name"
                },
                {
                    data: "gst_no",
                    name: "gst_no"
                },
                {
                    data: "email",
                    name: "email"
                },
                {
                    data: "mobile_no",
                    name: "mobile_no",
                },
                {
                    data: "address",
                    name: "address",
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
        });

        $('button[data-bs-target="#vendorModal"]').on("click",()=>{
            $("#vendorForm").get(0).reset();
        });

        $("#vendorForm").validate({
            rules: {
                vendorName: {
                    required: true,
                    minlength: 2
                },
                vendorMobileNo: {
                    // required: true,
                    number: true,
                    minlength:10,
                    minlength:10
                },
                vendorAddress: {
                    // required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addVendor();
            }
        });
    });
    function addVendor(){
        $.ajax({
                type: "POST",
                'url':"{{route('vendor.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#vendorForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#vendorForm").get(0).reset();
                        $("#vendorModal").modal('hide');
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
            url: "{{ route('vendor.edit', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    vendorDtl = data.data;
                    console.log(vendorDtl); 
                    $("#id").val(vendorDtl?.id);
                    $("#vendorName").val(vendorDtl?.vendor_name);
                    $("#email").val(vendorDtl?.email);
                    $("#mobileNo").val(vendorDtl?.mobile_no);
                    $("#gstNo").val(vendorDtl?.gst_no);
                    $("#address").val(vendorDtl?.address);
                    $("#vendorModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function deactivateMenu(id){
        $.ajax({
            type:"post",
            url: "{{ route('vendor.deactivate', ':id') }}".replace(':id', id),
            dataType: "json",
            data:{lock_status:true},
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    $('#postsTable').DataTable().draw();
                    modelInfo(data?.messages,"success");
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }
    function resetModelForm(){
        $("#id").val("");
        $("#vendorForm").get(0).reset();
    }
</script>

@include("layout.footer")