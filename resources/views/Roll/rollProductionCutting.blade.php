@include("layout.header")

<style>
    .modal-body {
        max-height: calc(100vh - 150px); /* Adjust height as needed */
        overflow-y: auto; /* Enable vertical scrolling */
    }
</style>
<!-- Main Component -->

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">{{$machine->name??""}}</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">  
        <div class="panel-heading">
            <h5 class="panel-title">Roll List</h5>                    
        </div>       
        <div class="panel-body">
            <form id="cuttingRoll">
                @csrf
                <!-- Hidden field for Client ID -->
                <input type="hidden" id="id" name="id" value="{{$machine->id}}">

                <div class="row panel-body">
                    <div class="row mt-3">                    
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="rollNo">Roll No</label>
                                <input type="text" id="rollNo" name="rollNo" class="form-control">
                                <span class="error-text" id="rollNo-error"></span>
                            </div>
                        </div>
                        
                        <div class="col-sm-4 d-flex align-items-end">
                            <button type="button" id="search" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row panel-body">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="cuttingUpdate">Date <span class="text-danger">*</span></label>
                            <input type="date" name="cuttingUpdate" id="cuttingUpdate" class="form-control" max="{{date('Y-m-d')}}" required />
                            <span class="error-text" id="cuttingUpdate-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="shift">Shift <span class="text-danger">*</span></label>
                            <select type="shift" name="shift" id="shift" class="form-select" required>
                                <option value="">select</option>
                                <option value="Day">Day</option>
                                <option value="Night">Night</option>
                            </select>
                            <span class="error-text" id="shift-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="operatorId">Operator <span class="text-danger">*</span></label>
                            <select name="operatorId" id="operatorId" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($operator as $val)
                                    <option value="{{$val->id}}">{{$val->name}}</option>
                                @endforeach
                            </select>
                            <span class="error-text" id="operatorId-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="helperId">Helper <span class="text-danger">*</span></label>
                            <select type="date" name="helperId" id="helperId" class="form-select"  required >
                                <option value="">Select</option>
                                    @foreach($helper as $val)
                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                            </select>
                            <span class="error-text" id="helperId-error"></span>
                        </div>
                    </div>
                </div>
                <div class="row panel-body">
                    <table class="table table-striped table-bordered table-responsive table-fixed">
                        <thead>
                            <th>Roll No</th>
                            <th>Punches Date</th>
                            <th>Roll Size</th>
                            <th>Roll Color</th>
                            <th>Client Name</th>
                            <th>Printing Color</th>
                            <th>Garbage In Kg</th>                        
                            <th>Remove</th>
                        </thead>
                        <tbody id="rollTableBody">
                        </tbody>
                    </table>
                </div>

                <!-- Submit Button -->
                <div class="row mt-4">
                    <div class="col-sm-12 text-end">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
</main>
<script>
    let sl = 0;
    $(document).ready(function () {
        // Handle Search Button Click
        $("#search").on("click", function () {
            let rollNo = $("#rollNo").val();
            if (rollNo != "") {
                $.ajax({
                    url: "{{ route('roll.search.cutting') }}",
                    type: "POST",
                    dataType: "json",
                    data: { "rollNo": rollNo },
                    beforeSend: function () {
                        $("#loadingDiv").show();
                    },
                    success: function (data) {
                        $("#loadingDiv").hide();
                        if (data.status && data.data) {
                            $("#rollNo").val("");
                            let roll = data.data;
                            const rowId = `row${sl}`;
                            const colors = roll.printing_color.split(",");
                            let row = `
                                <tr id='${rowId}'>
                                    <td>
                                        <input type='hidden' name='roll[${sl}][id]' value='${roll.id}' />${roll.roll_no}
                                    </td>
                                    <td>${roll.purchase_date}</td>
                                    <td>${roll.size}</td>
                                    <td>${roll.roll_color}</td>
                                    <td>${roll.client_name}</td>
                                    <td>${roll.printing_color}</td>
                                    <td>
                                        <input type='text' name='roll[${sl}][totalQtr]' id='totalQtr${rowId}' class='form-control dynamic-field' onkeypress="return isNumDot(event);" required />
                                    </td>
                                    <td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>
                                </tr>`;
                            $("#rollTableBody").append(row);
                            sl = sl+1;
                            applyValidationRules(rowId);
                        } else {
                            modelInfo(data.message || "Invalid Roll No", "warning");
                        }
                    }
                });
            }
        });

        // Handle Form Validation
        $("#cuttingRoll").validate({
            ignore: [],
            rules: {
                id: { required: true },
                "roll[][id]": { required: true },
                "roll[][totalQtr]": { required: true },
            },
            submitHandler: function (form) {
                // If form is valid, submit it
                if ($("input[type='hidden'][name^='roll']").length === 0) {
                    alert("Please add at least one roll before submitting.");
                    return false;
                }
                updateRollCutting();
            }
        });
    });

    // Apply validation rules to dynamically added fields
    function applyValidationRules(rowId) {
        $(`#${rowId} input.dynamic-field`).each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "This field is required.",
                },
            });
        });
    }

    // Remove Row from Table
    function removeTr(element) {
        $(element).closest("tr").remove();
    }

    // Update Roll Printing (Submit form via AJAX)

    function updateRollCutting() {
        $.ajax({
            type: "POST",
            url: "{{ route('roll.cutting.update') }}",
            dataType: "json",
            data: $("#cuttingRoll").serialize(),
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data.status) {
                    sl =0;
                    // Reset form and clear the table
                    $("#cuttingRoll")[0].reset();
                    $("#rollTableBody").empty();
                    $("#UpdateCuttingModel").modal('hide');
                    modelInfo(data.messages || "Update Successful!");
                } else if (data.errors) {
                    let errors = data.errors;
                    for (field in errors) {
                        $(`#${field}-error`).text(errors[field][0]);
                    }
                    modelInfo(data.messages || "Validation Error", "error");
                } else {
                    modelInfo("Something Went Wrong!!", "error");
                }
            },            
        });
    }

</script>
@include("layout.footer")