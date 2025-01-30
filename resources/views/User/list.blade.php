@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">User</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetModelForm()">
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
                    <th>User Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
     <x-user-form />
</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('user.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "name",
                    name: "name"
                },
                {
                    data: "email",
                    name: "email",
                },
                {
                    data: "user_type",
                    name: "user_type",
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
        });
        $("#userForm").validate({
            rules: {
                color: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX                
                addUser();
            }
        });
        addEventListenersToForm("userForm");
    });
    function addUser(){
        let url = "{{route('user.add')}}";
        let userId = $("#id").val();
        if(userId!="" && userId!="undefined"){
            url = "{{route('user.edit')}}"
        }
        $.ajax({
                type: "POST",
                'url':url,            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#userForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#userForm").get(0).reset();
                        $("#userModal").modal('hide');
                        $('#postsTable').DataTable().ajax.reload();
                        modelInfo(data.messages);
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    }   

    function userEditModal(id){
        $.ajax({
            type:"get",
            url: "{{ route('user.id', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    userDtl = data.data;
                    console.log(userDtl); 
                    $("#passwordDiv").hide();
                    $("#id").val(userDtl?.id);
                    $("#name").val(userDtl?.name);                    
                    $("#email").val(userDtl?.email);                    
                    $("#user_type_id"+userDtl?.user_type_id).attr("checked",true);
                    $("#userModal").modal("show");
                
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
        $("#userForm").get(0).reset();
        document.querySelectorAll("#userForm input, #userForm select, #userForm textarea").forEach(field => {
            if (field.type === "checkbox" || field.type === "radio") {
                field.checked = false;
            } else {
                field.value = "";
            }
        });

    }

</script>

@include("layout.footer")