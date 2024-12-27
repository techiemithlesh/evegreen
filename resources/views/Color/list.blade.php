@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Roll Color</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#rollColorModal" onclick="resetModelForm()">
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
                    <th>Color Name</th>
                    <th>Color</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
     <x-roll-color-form />
</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('roll.color.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "color",
                    name: "color"
                },
                {
                    data: "html",
                    name: "html",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
        });
        $("#rollColorForm").validate({
            rules: {
                color: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addRollColor();
            }
        });
    });
    function addRollColor(){
        $.ajax({
                type: "POST",
                'url':"{{route('roll.color.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#rollColorForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#rollColorForm").get(0).reset();
                        $("#rollColorModal").modal('hide');
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
            url: "{{ route('roll.color.edit', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    bagDtl = data.data;
                    console.log(bagDtl); 
                    $("#id").val(bagDtl?.id);
                    $("#color").val(bagDtl?.color);
                    $("#rollColorModal").modal("show");
                
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

</script>

@include("layout.footer")