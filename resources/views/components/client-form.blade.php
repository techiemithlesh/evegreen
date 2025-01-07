<!-- Modal Form -->
<div class="modal fade modal-lg" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="clientForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <div class="row">
                        <!-- Client Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="clientName">Client Name<span class="text-danger">*</span></label>
                                <input type="text" maxlength="100" id="clientName" name="clientName" class="form-control" placeholder="Enter Client Name" required>
                                <span class="error-text" id="clientName-error"></span>
                            </div>
                        </div>
                        
                        <!-- Client Email -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="email">Email</label>
                                <input type="email" maxlength="100" id="email" name="email" class="form-control" placeholder="client@example.com">
                                <span class="error-text" id="email-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="city">City<span class="text-danger">*</span></label>
                                <input type="text" id="city" name="city" class="form-control" placeholder="Enter city" required >
                                <span class="error-text" id="city-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="state">State<span class="text-danger">*</span></label>
                                <input type="text" id="state" name="state" class="form-control" placeholder="Enter state" required >
                                <span class="error-text" id="state-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="sectorId">Sector<span class="text-danger">*</span></label>
                                <select type="text" id="sectorId" name="sectorId" class="form-select" placeholder="Enter state" required >
                                    <option value="">Select</option>
                                    @foreach($sector as $val)
                                        <option value="{{$val->id}}">{{$val->sector}}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="sectorId-error"></span>
                            </div>
                        </div>
                        <!-- Client Mobile Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="mobileNo">Mobile Number<span class="text-danger">*</span></label>
                                <input type="text" maxlength="15" id="mobileNo" name="mobileNo" class="form-control" placeholder="Enter Mobile Number" required onkeypress="return isNum(event);">
                                <span class="error-text" id="mobileNo-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="secondaryMobileNo">Secondary Mobile Number</label>
                                <input type="text" maxlength="15" id="secondaryMobileNo" name="secondaryMobileNo" class="form-control" placeholder="Enter Secondary Mobile Number" onkeypress="return isNum(event);">
                                <span class="error-text" id="secondaryMobileNo-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="temporaryMobileNo">Temporary Mobile Number<span class="text-danger">*</span></label>
                                <input type="text" maxlength="15" id="temporaryMobileNo" name="temporaryMobileNo" class="form-control" placeholder="Enter Temporary Mobile Number" onkeypress="return isNum(event);">
                                <span class="error-text" id="temporaryMobileNo-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <!-- Client Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="address">Address<span class="text-danger">*</span></label>
                                <textarea id="address" name="address" class="form-control" placeholder="Enter Client Address" rows="3" required></textarea>
                                <span class="error-text" id="address-error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add Client</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
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
                addClint();
            }
        });
        addEventListenersToForm("clientForm");
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
                        var newOptionValue = data?.data?.client?.id;
                        var clientName = data?.data?.client?.client_name;
                        if (newOptionValue !== "") {
                            // Check if the option already exists
                            if ($('#forClientId option[value="' + newOptionValue + '"]').length === 0) {
                                // Add the new option to the select list
                                $('#forClientId').append('<option value="' + newOptionValue + '">' + clientName + '</option>');                                
                            } 
                            if ($('#bookingForClientId option[value="' + newOptionValue + '"]').length === 0) {
                                // Add the new option to the select list
                                $('#bookingForClientId').append('<option value="' + newOptionValue + '">' + clientName + '</option>');                                
                            } 
                        } 
                        modelInfo(data.messages);
                    }else if (data?.errors) {
                        let errors = data?.errors;
                        modelInfo(data.message);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    }
</script>
