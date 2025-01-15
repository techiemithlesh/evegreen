@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Clint</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">Clint List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#clientModal" onclick="resetModelForm()">
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
                    <th>Clint Name</th>
                    <th>Mobile No</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <x-client-form />
    <x-confirmation />
</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('client.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "client_name",
                    name: "client_name"
                },
                {
                    data: "mobile_no",
                    name: "mobile_no"
                },
                {
                    data: "email",
                    name: "email"
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
        $('button[data-bs-target="#clientModal"]').on("click",()=>{
            $("#clientForm").get(0).reset();
        });

        $("#clientForm").validate({
            rules: {
                clientName: {
                    required: true,
                    minlength: 3
                },

                clientMobileNo: {
                    required: true,
                    number: true,
                    minlength:10,
                    minlength:10
                },
                clientAddress: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addClint();
            }
        });
    });
    function addClint(){
        $.ajax({
                type: "POST",
                'url':"{{route('client.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#clientForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#clientForm").get(0).reset();
                        $("#clientModal").modal('hide');
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
            url: "{{ route('client.edit', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    clientDtl = data.data;
                    console.log(clientDtl); 
                    $("#id").val(clientDtl?.id);
                    $("#clientName").val(clientDtl?.client_name);
                    $("#email").val(clientDtl?.email);
                    $("#mobileNo").val(clientDtl?.mobile_no);
                    $("#address").val(clientDtl?.address);
                    $("#secondaryMobileNo").val(clientDtl?.secondary_mobile_no);
                    $("#temporaryMobileNo").val(clientDtl?.temporary_mobile_no);
                    $("#city").val(clientDtl?.city);
                    $("#state").val(clientDtl?.state);
                    $("#sectorId").val(clientDtl?.sector_id);
                    $("#clientModal").modal("show");
                
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
    }

    function deactivate(id){
        
    }

</script>

@include("layout.footer")