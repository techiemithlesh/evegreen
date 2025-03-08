@include("layout.header")

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Accounting</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Garbage Verification</li>
                </ol>
            </nav>

        </div>        
    </div>
    <div class="container">
        <div class="panel-body">
            <table id="postsTable" class="table table-striped table-bordered table-fixed">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Cutting Date</th>
                        <th>Machine</th>
                        <th>Shift</th>
                        <th>Operator</th>
                        <th>Helper</th>
                        <th>Garbage</th>
                        <th>%</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
    
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="garbageUpdateModal" tabindex="-1" aria-labelledby="garbageUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="garbageUpdateModalLabel">Enter Remarks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="garbageUpdateForm">
                    <input type="hidden" id="id" name="id">
                    <label for="remarks">Remark</label>
                    <textarea name="remarks" id="remarks" class="form-control"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Accept</button>
            </div>
            </div>
        </div>
    </div>

</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{route('accounting.garbage.verification')}}",
            columns: [
                { data: "DT_RowIndex",  name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "client_name", name: "client_name" },
                { data: "cutting_date", name: "cutting_date"},
                { data: "machine", name: "machine"},
                { data: "shift", name: "shift"},
                { data: "operator_name", name: "operator_name"}, 
                { data: "helper_name", name: "helper_name"},
                { data: "garbage", name: "garbage"},
                { data: "percent", name: "percent"},
                { data: "action", name: "action", orderable: false, searchable: false},
            ],
            initComplete: function () {
                addFilter('postsTable',[0]);
            }, 

        });

        $("#garbageUpdateForm").validate({
            rules: {
                id: {
                    required: true,
                },
                remarks: {
                    required: true,
                    minlength:10,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                showConfirmDialog("Are sure to submit??",garbageUpdateModal);
            }
        });
    });

    function openModel(id){
        resetModelForm("garbageUpdateForm");
        $("#id").val(id);
        $("#garbageUpdateModal").modal("show");        
    }

    function submitForm(){
        $("#garbageUpdateForm").submit();
    }

    function garbageUpdateModal(){
        $.ajax({
            type:"post",
            url:"{{route('accounting.close.garbage')}}",
            data:$("#garbageUpdateForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    modelInfo(response.message);
                    resetModelForm("garbageUpdateForm");
                    $("#garbageUpdateModal").modal("hide");
                    searchData();
                }
                else{
                    modelInfo("Server Error!!","error");
                }
            },
            error:function(errors){
                $("#loadingDiv").hide();
                console.log(errors);
                modelInfo("Server Error!!","error");
            }

        })
    }

    function resetModelForm(id){
        $("#id").val("");
        $("#"+id).get(0).reset();
    }

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[0]);
        },false);
        
    }

</script>

@include("layout.footer")