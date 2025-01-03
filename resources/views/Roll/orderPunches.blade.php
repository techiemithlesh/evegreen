@include("layout.header")
<!-- Main Component -->

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Order Punches</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">Order Punches</h5>            
        </div>
    </div>
    <div class="container">
        <div class="panel-body">
            <div style="text-align: center;" id="orderHistory">
                <div class="example-box movable" style="right: 30px; width:500px">
                    <div class="header">
                        <span>Old Orders</span>
                        <span class="icons">▼</span>
                    </div>
                    <div class="content">
                        <div id="history">
                        </div>
                    </div>
                </div>
            </div>

            <div id="suggestion" style="display: none;">
                <div style="text-align: center;" id="suggestion1">
                    <div class="example-box movable" style="background-color: rgb(238, 80, 96); right: 20px; width:450px">
                        <div class="header">
                            <span>Roll Stock</span>
                            <span class="icons">▼</span>
                        </div>
                        <div class="content">
                            <div id="suggestionRoll"> a
                            </div>
                        </div>
                    </div>
                </div>
                <div style="text-align: center;" id="suggestion2">
                    <div class="example-box movable" style="background-color: rgb(89, 199, 208); width:400px">
                        <div class="header">
                            <span>Roll Transit</span>
                            <span class="icons">▼</span>
                        </div>
                        <div class="content">
                            <div id="suggestionRollTransit">b
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" id="myForm" class="row g-3">                
                @csrf
                <div class="row">                    
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="bookingForClientId">Book For Client 
                                    <span onclick="openRollBookingClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                        <i class="bi bi-person-add"></i> Add Client
                                    </span> 
                                </label>
                                <select name="bookingForClientId" id="bookingForClientId" class="form-select select-option" onchange="showPrivOrder(event)">
                                    <option value="">Select</option>
                                    @foreach ($clientList as $val)
                                        <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="bookingForClientId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="bookingEstimatedDespatchDate">Dispatch Date</label>
                                <input type="date" min="{{date('Y-m-d')}}" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" required/>                                  
                                <span class="error-text" id="bookingEstimatedDespatchDate-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">                            
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="bookingBagTypeId">Bag Type </label>
                                <select name="bookingBagTypeId" id="bookingBagTypeId" class="form-select" onchange="showHideLoop()">
                                    <option value="">Select</option>
                                    @foreach ($bagType as $val)
                                        <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                                    @endforeach
                                </select>                                                                       
                                <span class="error-text" id="bookingBagTypeId-error"></span>
                            </div>
                        </div>
                        
                        <div class="col-sm-6" id="loopColorDiv">
                            <div class="form-group">
                                <label class="form-label" for="looColor">Loop Color</label>
                                <input name="looColor" id="looColor" class="form-control" required />                               
                                <span class="error-text" id="looColor-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="bookingBagUnits">Bag Unit</label>
                                <select name="bookingBagUnits" id="bookingBagUnits" class="form-select">
                                    <option value="">Select</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Pice">Pice</option>
                                </select>                                    
                                <span class="error-text" id="bookingBagUnits-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">                            
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="l">L </label>
                                <input name="l" id="l" class="form-control" onkeypress="return isNumDot(event);" required />                                                                    
                                <span class="error-text" id="l-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="w">W</label>
                                <input name="w" id="w" class="form-control" onkeypress="return isNumDot(event);" required />                                
                                <span class="error-text" id="w-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">                            
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="g">G </label>
                                <input name="g" id="g" class="form-control" onkeypress="return isNumDot(event);" required />                                                                    
                                <span class="error-text" id="g-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="bookingPrintingColor">Printing Color</label>
                                <div class="col-md-12">
                                    <select name="bookingPrintingColor[]" id="bookingPrintingColor" class="form-select select22" multiple="multiple" required> 
                                        <option value="">Select</option>                                     
                                        @foreach($color as $val)
                                        <option data-color="{{$val->color}}" value="{{$val->color}}" style="background-color:{{$val->color}};">{{$val->color}}</option>
                                        @endforeach
                                    </select>
                                </div>                                                                                                          
                                <span class="error-text" id="bookingPrintingColor-error"></span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="row">
                    <div class="row mt-3">
                        <table class="table table-bordered  table-responsive " id="orderRoll">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>GSM</th>
                                    <th>Roll Color</th>
                                    <th>Length</th>
                                    <th>Size</th>
                                    <th>Net Weight</th>
                                    <th>roll Type</th>
                                    <th>Hardness</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="showRollSuggestion()"><i class="bi bi-camera-reels"></i></button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <x-client-form/>
    <x-confirmation />
</main>


<script>
    $(document).ready(function(){
        $('.select22').select2(); 
        $("#orderHistory").hide();      

        $('#bookingPrintingColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            // dropdownParent: $('#rollBookingModal'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });

        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollBookingModal').css("z-index","");
        });
        showHideLoop();
        addSearch("bookingForClientId");

        $("#myForm").validate({
            rules: {
                bookingForClientId: {
                    required: true,
                },
                bookingEstimatedDespatchDate:{
                    required:true
                },
                bookingBagUnits: {
                    required: true,
                },
                bookingBagTypeId: {
                    required: true,
                },
                bookingPrintingColor: {
                    required: true,
                },
                roll_id:{
                    required:true,
                }
            },
            submitHandler: function(form) {
                bookForClient();
            }
        });

    });
    function openRollBookingClineModel(){
        $('#rollBookingModal').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }
    

    function showHideLoop(){
        var bagType = $("#bookingBagTypeId").val();
        console.log(bagType);
        if(["2"].includes(bagType)){
            $("#loopColorDiv").show();
        }else{
            $("#looColor").val("");
            $("#loopColorDiv").hide();
        }
    }

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px; z-index:40000">' + option.text + '</span>');
    }
    function showPrivOrder(event) {
        const clientId = $(event.target).val();
        if (clientId != "") {
            $.ajax({
                url: "{{route('client.old.order')}}",
                type: "post",
                data: { "clientId": clientId },
                dataType: "json",
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (response) {
                    $("#loadingDiv").hide();

                    if (response.status && response.data.length > 0) {
                        // Clear previous content
                        $("#orderHistory").show();                        
                        setHintDefault("orderHistory");
                        $("#history").empty();

                        // Create the table
                        const table = $("<table>").addClass("history-table");
                        const thead = $("<thead>").append(
                            $("<tr>").append(
                                "<th>Sl</th>",
                                "<th>Order</th>",
                                "<th>GSM</th>",
                                "<th>Roll Color</th>",
                                "<th>Length</th>",
                                "<th>Size</th>",
                                "<th>Net Weight</th>",
                                "<th>roll Type</th>",
                                "<th>Hardness</th>",
                                "<th>Bag</th>",
                                "<th>Bag Unit</th>",
                                "<th>W</th>",
                                "<th>L</th>",
                                "<th>G</th>",
                                "<th>Printing Colors</th>",
                            )
                        );

                        const tbody = $("<tbody>");
                        
                        // Populate the rows
                        response.data.forEach((item,index) => {
                            tbody.append(                               
                                $("<tr>").append(
                                    `<td>${index+1}</td>`,
                                    `<td><button data-item='${JSON.stringify(item)}' id="or${index}" onclick="setOrderValue('or${index}')" class="btn btn-sm btn-info">Place Order</button></td>`,
                                    `<td>${item.gsm}</td>`,
                                    `<td>${item.roll_color || "N/A"}</td>`,
                                    `<td>${item.length || "N/A"}</td>`,
                                    `<td>${item.size || "N/A"}</td>`,
                                    `<td>${item.net_weight || "N/A"}</td>`,
                                    `<td>${item.roll_type || "N/A"}</td>`,
                                    `<td>${item.hardness || "N/A"}</td>`,
                                    `<td>${item.bag_type || "N/A"}</td>`,
                                    `<td>${item.bag_unit || "N/A"}</td>`,
                                    `<td>${item.w || "N/A"}</td>`,
                                    `<td>${item.l || "N/A"}</td>`,
                                    `<td>${item.g || "N/A"}</td>`,
                                    `<td>${JSON.parse(item.printing_color || "[]").join(", ")}</td>`,
                                )
                            );
                        });

                        // Append the table structure
                        table.append(thead).append(tbody);
                        $("#history").append(table);

                        // Optionally, style the table
                        $(".history-table").css({
                            width: "100%",
                            borderCollapse: "collapse",
                            margin: "20px 0",
                            fontSize: "16px",
                            textAlign: "left"
                        });

                        $(".history-table th, .history-table td").css({
                            border: "1px solid #ddd",
                            padding: "8px"
                        });

                        $(".history-table th").css({
                            backgroundColor: "#f2f2f2",
                            fontWeight: "bold"
                        });
                    } else {
                        $("#orderHistory").hide();
                        $("#history").html("<p>No records found.</p>");
                    }
                },
                error: function () {
                    $("#loadingDiv").hide();
                    $("#history").html("<p>An error occurred while fetching data.</p>");
                }
            });
        }else{
            $("#orderHistory").hide();
        }
    }


    function setOrderValue(id) {        
        const item = JSON.parse($(event.target).attr("data-item"));

        // Set individual field values
        $("#bookingBagTypeId").val(item?.bag_type_id);
        $("#bookingBagUnits").val(item?.bag_unit);
        $("#l").val(item?.l);
        $("#g").val(item?.g);
        $("#w").val(item?.w);

        
        // Set the multi-select field for 'bookingPrintingColor'
        try {
            // Parse the printing_color string to an array
            const printingColors = JSON.parse(item?.printing_color) || [];
            $("#bookingPrintingColor").val(printingColors).trigger("change");; // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#bookingPrintingColor").val([]).trigger("change");; // Clear in case of error
        }
    }

    function showRollSuggestion(){
        $.ajax({
            url:"{{route('client.order.suggestion')}}",
            type:"post",
            data:$("#myForm").serialize(),
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                console.log( response.data.rollTransit.length);
                $("#loadingDiv").hide();
                $("#suggestion").show();

                if (response.status && response.data.roll.length > 0) {
                    // Clear previous content
                    $("#suggestion1").show();
                    setHintDefault("suggestion1");
                    $("#suggestionRoll").empty();

                    // Create the table
                    const table = $("<table>").addClass("history-table");
                    const thead = $("<thead>").append(
                        $("<tr>").append(
                            "<th>Sl</th>",
                            "<th>Roll No</th>",
                            "<th>GSM</th>",
                            "<th>Roll Color</th>",
                            "<th>Length</th>",
                            "<th>Size</th>",
                            "<th>Net Weight</th>",
                            "<th>roll Type</th>",
                            "<th>Hardness</th>",                            
                            "<th>Add To Book</th>",
                        )
                    );

                    const tbody = $("<tbody>");
                    
                    // Populate the rows
                    response.data.roll.forEach((item,index) => {
                        tbody.append(                               
                            $("<tr>").append(
                                `<td>${index+1}</td>`,
                                `<td>${item.roll_no}</td>`,
                                `<td>${item.gsm}</td>`,
                                `<td>${item.roll_color || "N/A"}</td>`,
                                `<td>${item.length || "N/A"}</td>`,
                                `<td>${item.size || "N/A"}</td>`,
                                `<td>${item.net_weight || "N/A"}</td>`,
                                `<td>${item.roll_type || "N/A"}</td>`,
                                `<td>${item.hardness || "N/A"}</td>`,                                
                                `<td><button data-item='${JSON.stringify(item)}' id="rl${index}" onclick="addToBook('rl${index}')" class="btn btn-sm btn-info">Place Order</button></td>`,
                            )
                        );
                    });

                    // Append the table structure
                    table.append(thead).append(tbody);
                    $("#suggestionRoll").append(table);

                    // Optionally, style the table
                    $(".history-table").css({
                        width: "100%",
                        borderCollapse: "collapse",
                        margin: "20px 0",
                        fontSize: "16px",
                        textAlign: "left"
                    });

                    $(".history-table th, .history-table td").css({
                        border: "1px solid #ddd",
                        padding: "8px"
                    });

                    $(".history-table th").css({
                        backgroundColor: "#f2f2f2",
                        fontWeight: "bold"
                    });
                    
                } else {
                    $("#suggestion1").hide();
                    $("#suggestionRoll").html("<p>No records found.</p>");
                }
                if (response.status && response.data.rollTransit.length > 0) {
                    // Clear previous content
                    $("#suggestion2").show();                    
                    setHintDefault("suggestion2");
                    $("#suggestionRollTransit").empty();

                    // Create the table
                    const table = $("<table>").addClass("history-table");
                    const thead = $("<thead>").append(
                        $("<tr>").append(
                            "<th>Sl</th>",
                            "<th>Roll No</th>",
                            "<th>GSM</th>",
                            "<th>Roll Color</th>",
                            "<th>Length</th>",
                            "<th>Size</th>",
                            "<th>Net Weight</th>",
                            "<th>roll Type</th>",
                            "<th>Hardness</th>",                            
                            "<th>Add To Book</th>",
                        )
                    );

                    const tbody = $("<tbody>");
                    
                    // Populate the rows
                    response.data.rollTransit.forEach((item,index) => {
                        tbody.append(                               
                            $("<tr>").append(
                                `<td>${index+1}</td>`,
                                `<td>${item.roll_no}</td>`,
                                `<td>${item.gsm}</td>`,
                                `<td>${item.roll_color || "N/A"}</td>`,
                                `<td>${item.length || "N/A"}</td>`,
                                `<td>${item.size || "N/A"}</td>`,
                                `<td>${item.net_weight || "N/A"}</td>`,
                                `<td>${item.roll_type || "N/A"}</td>`,
                                `<td>${item.hardness || "N/A"}</td>`,                                
                                `<td><button data-item='${JSON.stringify(item)}' id="tl${index}" onclick="addToBook('tl${index}')" class="btn btn-sm btn-info">Place Order</button></td>`,
                            )
                        );
                    });

                    // Append the table structure
                    table.append(thead).append(tbody);
                    $("#suggestionRollTransit").append(table);

                    // Optionally, style the table
                    $(".history-table").css({
                        width: "100%",
                        borderCollapse: "collapse",
                        margin: "20px 0",
                        fontSize: "16px",
                        textAlign: "left"
                    });

                    $(".history-table th, .history-table td").css({
                        border: "1px solid #ddd",
                        padding: "8px"
                    });

                    $(".history-table th").css({
                        backgroundColor: "#f2f2f2",
                        fontWeight: "bold"
                    });
                } else {
                    $("#suggestion2").hide();
                    $("#suggestionRollTransit").html("<p>No records found.</p>");
                }
            }
        })
    }

    function addToBook(id){
        const item = JSON.parse($(event.target).attr("data-item"));
        const existingRow = $(`#orderRoll tbody tr[data-id="${item.id}"]`);
        if (existingRow.length > 0) {
            alert("This item is already added.");
            return; // Exit the function if the item already exists
        }
        const tr = $("<tr>")
                    .attr("data-id", item.id)
                    .addClass((item.stock=="stock" ?"table-info" : "table-danger"))
                    .append(
                                `<td>${item.roll_no} <input type='hidden' name='roll[${item.id}][id]' value='${item.id}' /></td>`,
                                `<td>${item.gsm}</td>`,
                                `<td>${item.roll_color || "N/A"}</td>`,
                                `<td>${item.length || "N/A"}</td>`,
                                `<td>${item.size || "N/A"}</td>`,
                                `<td>${item.net_weight || "N/A"}</td>`,
                                `<td>${item.roll_type || "N/A"}</td>`,
                                `<td>${item.hardness || "N/A"}</td>`,                                
                                `<td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>`,
                            );
        $("#orderRoll tbody").append(tr);
        
    }

    function removeTr(element) {
        $(element).closest("tr").remove();
    }

    function bookForClient(){
        if ($("input[type='hidden'][name^='roll']").length === 0) {
            (showConfirmDialog('Are you sure you want to add On Pending Order?', saveOrder));
        }
        else{
            saveOrder();
        }
        return false;
    }
    function saveOrder(){
        $.ajax({
            url:"{{route('order.punches.save')}}",
            type:"post",
            data:$("#myForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data.status){
                    $("#myForm").get(0).reset();
                    $("#orderHistory").hide();
                    $("#suggestion").hide();
                    $("#orderRoll tbody").empty();
                    modelInfo(data.messages);
                    setHintDefault();
                }else{
                    console.log(data);
                    modelInfo("Internal Server Error","warning");
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                modelInfo("server error","error")
            }
        })
    }


</script>


@include("layout.footer")