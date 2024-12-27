@include("layout.header")
<!-- Main Component -->

<!-- DataTables SearchPanes -->


<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Transit Dtl</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <button id="addRoll" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#rollModal">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
                <button id="addRollImport" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#fileImportModal">
                    Add Roll Import Excel <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
        <div class="panel-body">
            
            <table id="example" class="table table-striped table-bordered" >
                <thead>
                    <tr>
                        <th>Vendor Name</th>
                        <th>Purchase Date</th>
                        <th>Total Roll</th>
                        <th>Action</th>
                    </tr>
                    
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->vendor_name }}</td>
                        <td>{{ $item->purchase_date }}</td>
                        <td>{{ $item->total_count }}</td>
                        <td> <a href="{{ url('roll/transit/dtl/' . $item->vender_id.'?purchase_date='.$item->purchase_date) }}">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <x-roll-form />
    <x-import-file />
</main>
<script>
    $(document).ready(function(){
        $("#rollForm").validate({
            rules: {
                vendorId: {
                    required: true,
                },
                purchaseDate: {
                    required: true,
                },
                rollSize: {
                    required: true,
                },
                rollGsm: {
                    required: true,
                },
                rollColor: {
                    required: true,
                },
                netWeight: {
                    required: true,
                },
                grossWeight: {
                    required: true,
                },
                estimatedDespatchDate:{
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                },
                bagUnits: {
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                },
                bagTypeId: {
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                },
                "printingColor[]": {
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                }
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addRoll();
            }
        });
        $("#importForm").validate({
            rules: {
                csvFile: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                importFile();
                return false;
            }
        });
    });

    function addRoll() {
        $.ajax({
                type: "POST",
                'url': "{{route('roll.transit.add')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#rollForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#rollForm").get(0).reset();
                        $("#rollModal").modal('hide');
                        modelInfo(data.messages);
                        window.location.reload();
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        )
    }

    function importFile() {
        var formData = new FormData($("#importForm")[0]);
        $.ajax({
                type: "POST",
                'url': "{{route('roll.import')}}",
                "deferRender": true,
                processData: false, // Do not process data (let FormData handle it)
                contentType: false, // Do not set content type (let the browser handle it)
                dataType: "json",

                'data': formData,
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        document.getElementById("importForm").reset();
                        $("#fileImportModal").modal('hide');
                        modelInfo(data.messages);
                        window.location.reload();
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
                error: function(error) {
                    $("#loadingDiv").hide();
                    console.log(error);
                }
            }

        )
    }
</script>
@include("layout.footer")
