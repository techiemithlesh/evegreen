@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Bag History</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container"> 
        <div class="panel-body">
            <form action="" id="searchForm" >
                <div class="row">                    
                    <div class="row mt-3">                        

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="bagStatusId">Transport Type</label>
                                <select type="text" id="bagStatusId" name="bagStatusId[]" multiple class="form-select">
                                    <option value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    
                                </select>
                                <span class="error-text" id="bagStatusId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="bagStatusId">Transport Type</label>
                                <select type="text" id="bagStatusId3" name="bagStatusId3[]" multiple class="form-select">
                                    <option value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    
                                </select>
                                <span class="error-text" id="bagStatusId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                iii <input type="text" name="inputs">
                            </div>

                        </div>
                    </div>                   
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-success" id="btn_search" onclick="searchData()">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="searchForm2" >
                    <div class="row">                    
                        <div class="row mt-3">                        

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label" for="bagStatusId">Transport Type</label>
                                    <select type="text" id="bagStatusId2" name="bagStatusId[]" multiple class="form-select">
                                        <option value="">Select</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        
                                    </select>
                                    <span class="error-text" id="bagStatusId-error"></span>
                                </div>
                            </div>
                            
                        </div>                   
                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-sm-12 text-end">
                                <button type="button" class="btn btn-success" id="btn_search" onclick="searchData2()">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function () {
        $("#bagStatusId2").select2({
            placeholder: "Select tags",
            allowClear: false,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',
            dropdownParent: $('#exampleModal'),            
            width:"100%",
        });

        $("#bagStatusId").select2();
        $("#bagStatusId3").select2();
    });

    // Copy selected values from bagStatusId to bagStatusId2
    function searchData() {
        let selectedValues = $("#bagStatusId").val(); // Get selected values
        $("#bagStatusId2").val(selectedValues).trigger('change'); // Set values & update Select2
    }

    // Serialize form data correctly
    // function searchData2() {
    //     let form1 = $("#searchForm").serializeArray();
    //     let form2 = $("#searchForm2").serializeArray();
    //     let formData = {};

    //     $.each(form1, function (_, field) {
    //         $.each(form2, function (_, field2) {
    //             if(field.name.match(/\[\]$/)){
    //                 if(field.name==field2.name){

    //                 }
    //                 else{
                        
    //                     if(formData[field.name]){
    //                         formData[field.name].push(field.value);
    //                     }
    //                     else{
    //                         formData[field.name] = [field.value];
    //                     }
    //                 }
    //             }
    //             else{
    //                 if(field.name==field2.name){

    //                 }else{
    //                     formData[field.name] = field.value;
    //                 }
    //             }
    //         });
            
    //     });

    //     $.each(form2, function (_, field) {
    //         if(field.name.match(/\[\]/)){
    //             if(formData[field.name]){
    //                 formData[field.name].push(field.value);
    //             }else{
    //                 formData[field.name] = [field.value];
    //             }
    //         }
    //         else{
    //             formData[field.name] = field.value;
    //         }
    //     });

    //     console.log(formData); // Check the combined form data
    //     $.ajax({
    //         url:"hello.com",
    //         type:"post",
    //         data:formData,
    //         success:function(res){

    //         }
    //     })
    // }


    function searchData2() {
    let form1 = $("#searchForm").serializeArray();
    let form2 = $("#searchForm2").serializeArray();
    let formData = {};

    // Convert form2 into a quick lookup map for field existence check
    let form2Keys = {};
    $.each(form2, function (_, field) {
        form2Keys[field.name] = true;
    });

    // Process form1 first (Only if the key does NOT exist in form2)
    $.each(form1, function (_, field) {
        if (field.name.match(/\[\]$/)) { // Handle multi-value fields (Array)
            if (!form2Keys[field.name]) { // Only add if NOT in form2
                if (!formData[field.name]) {
                    formData[field.name] = [];
                }
                if (!formData[field.name].includes(field.value)) { // Prevent duplicates
                    formData[field.name].push(field.value);
                }
            }
        } else { // Single-value fields
            if (!form2Keys[field.name] && !(field.name in formData)) { 
                formData[field.name] = field.value;
            }
        }
    });

    // Process form2 (Overwrite values from form1)
    $.each(form2, function (_, field) {
        if (field.name.match(/\[\]$/)) { // Handle multi-value fields
            if (!formData[field.name]) {
                formData[field.name] = [];
            }
            if (!formData[field.name].includes(field.value)) { // Prevent duplicates
                formData[field.name].push(field.value);
            }
        } else { // Single-value fields
            formData[field.name] = field.value; // Overwrite if exists
        }
    });

    console.log(formData); // Debugging: Check the final result

    $.ajax({
        url: "hello.com",
        type: "POST",
        data: formData,
        success: function (res) {
            console.log("Success:", res);
        }
    });
}


//     function searchData2() {
//     let form1 = $("#searchForm").serializeArray();
//     let form2 = $("#searchForm2").serializeArray();
//     let formData = {};

//     function processForm(form, overwrite = false) {
//         $.each(form, function (_, field) {
//             if (field.name.match(/\[\]$/)) { // Handle multiple values (fields with [])
//                 if (!formData[field.name] || overwrite) {
//                     formData[field.name] = []; // Initialize or overwrite
//                 }
//                 if (!formData[field.name].includes(field.value)) { // Prevent duplicates
//                     formData[field.name].push(field.value);
//                 }
//             } else { // Handle single-value fields
//                 if (!formData[field.name] || overwrite) {
//                     formData[field.name] = field.value;
//                 }
//             }
//         });
//     }

//     // Process form1 first (lower priority)
//     processForm(form1);

//     // Process form2 second (higher priority, overwrites form1 values)
//     processForm(form2, true);

//     console.log(formData); // Debugging: Check the combined form data

//     $.ajax({
//         url: "hello.com",
//         type: "POST",
//         data: formData,
//         success: function (res) {
//             console.log("Success:", res);
//         }
//     });
// }


    


</script>
@include("layout.footer")