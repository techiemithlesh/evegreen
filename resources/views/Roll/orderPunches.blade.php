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
            <div style="text-align: center; max-height:50px" id="orderHistory">
                <div class="example-box movable" style="right: 30px; width:500px;">
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
                <div style="text-align: center;" id="suggestion2">
                    <div class="example-box movable" style="background-color: rgb(181, 217, 220); width:450px;">
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
                <div style="text-align: center;" id="suggestion1">
                    <div class="example-box movable" style="background-color: rgb(119, 163, 202); right: 20px; width:400px;">
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
            </div>

            <form action="" id="myForm" class="row g-3">                
                @csrf
                <div class="row">                    
                    <div class="row mt-3">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="bookingForClientId">Book For Client 
                                    <span onclick="openRollBookingClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                        <i class="bi bi-person-add"></i> Add Client
                                    </span> 
                                </label>
                                <div class="col-md-12">
                                    <select name="bookingForClientId" id="bookingForClientId" class="form-control" onchange="showPrivOrder(event)" >
                                        <option value="">Select</option>
                                        @foreach ($clientList as $val)
                                            <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                        @endforeach
                                    </select><br>
                                    <span class="error-text" id="bookingForClientId-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="orderDate">Order Date</label>
                                <input type="date" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" name="orderDate" id="orderDate" class="form-control" required/>                                  
                                <span class="error-text" id="orderDate-error"></span>
                            </div>
                        </div>                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="bookingEstimatedDespatchDate">Dispatch Date</label>
                                <input type="date" min="{{date('Y-m-d')}}" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" required/>                                  
                                <span class="error-text" id="bookingEstimatedDespatchDate-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3"> 
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="bagQuality">Bag Quality </label>
                                <select name="bagQuality" id="bagQuality" class="form-select" required onchange="showHidePrintingColorDiv()">
                                    <option value="">Select</option>                                    
                                    <option value="NW">NW</option>
                                    <option value="BOPP">BOPP</option>
                                </select>                                                                       
                                <span class="error-text" id="bagQuality-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="gradeId">Roll Grade</label>
                                <select name="gradeId" id="gradeId" class="form-select" required >
                                    <option value="">Select</option>
                                    @foreach($grade as $val)
                                    <option value="{{$val->id}}">{{$val->grade}}</option>
                                    @endforeach
                                </select>                                                                       
                                <span class="error-text" id="bagQuality-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="ratePerUnit">Rate Per Unit</label>
                                <input type="text" name="ratePerUnit" id="ratePerUnit" class="form-control" required onkeypress="return isNumDot(event);" />                                                     
                                <span class="error-text" id="ratePerUnit-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3"> 
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="rateTypeId">Rate Type </label>
                                <select name="rateTypeId" id="rateTypeId" class="form-select" required>
                                    <option value="">Select</option>
                                    @foreach($rateType as $val)
                                    <option value="{{$val->id}}">{{$val->rate_type}}</option>
                                    @endforeach                                    
                                </select>                                                                       
                                <span class="error-text" id="rateTypeId-error"></span>
                            </div>
                        </div> 
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="fareTypeId">Fare</label>
                                <select name="fareTypeId" id="fareTypeId" class="form-select" required>
                                    <option value="">Select</option>
                                    @foreach($fare as $val)
                                    <option value="{{$val->id}}">{{$val->fare_type}}</option>
                                    @endforeach                                    
                                </select>                                                                       
                                <span class="error-text" id="fareTypeId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="stereoTypeId">Stereo</label>
                                <select name="stereoTypeId" id="stereoTypeId" class="form-select" required>
                                    <option value="">Select</option>
                                    @foreach($stereo as $val)
                                    <option value="{{$val->id}}">{{$val->stereo_type}}</option>
                                    @endforeach                                  
                                </select>                                                                       
                                <span class="error-text" id="stereoTypeId-error"></span>
                            </div>
                        </div>       
                    </div>
                    <div class="row mt-3">                            
                        <div class="col-sm-4">
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
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="bookingBagColor">Bag Color </label>
                                <div class="col-md-12">
                                    <select name="bookingBagColor" id="bookingBagColor" class="form-select" required> 
                                        <option value="">Select</option>                                     
                                        @foreach($rollColor as $val)
                                        <option data-color="{{$val->color}}" value="{{$val->color}}">{{$val->color}}</option>
                                        @endforeach
                                    </select>
                                </div>                                                                       
                                <span class="error-text" id="bookingBagColor-error"></span>
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="bookingBagUnits">Bag Unit</label>
                                <select name="bookingBagUnits" id="bookingBagUnits" class="form-select" onchange="emptyTable()">
                                    <option value="">Select</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Piece">Piece</option>
                                </select>                                    
                                <span class="error-text" id="bookingBagUnits-error"></span>
                            </div>
                        </div>                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="totalUnits">QTY</label>
                                <input name="totalUnits" id="totalUnits" class="form-control" required onkeypress="return isNumDot(event);" onchange="getBalance()"/>                                 
                                <span class="error-text" id="bookingBagUnits-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3"> 
                        <div class="col-sm-4">
                            <div class="form-group" id='singleGsm'>
                                <label class="form-label" for="bagGsm">GSM</label>
                                <input name="bagGsm" id="bagGsm" class="form-control" required onkeypress="return isNumDot(event);" />                                 
                                <span class="error-text" id="bagGsm-error"></span>
                            </div>
                            <div class="form-group" id='multipleGsm' style="display: none;">
                                <label class="form-label" for="bagGsmJson">GSM</label>
                                <input name="bagGsmJson" id="bagGsmJson" class="form-control" placeholder="gsm/lamination/boop" required onkeypress="return gsmJson(event); "  onkeyup="setGsm();"/>                                 
                                <span class="error-text" id="bagGsmJson-error"></span>
                            </div>
                        </div>
                       
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="w">W</label>
                                <input name="w" id="w" class="form-control" onkeypress="return isNumDot(event);" required />                                
                                <span class="error-text" id="w-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="l">L </label>
                                <input name="l" id="l" class="form-control" onkeypress="return isNumDot(event);" required />                                                                    
                                <span class="error-text" id="l-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3" id="loopColorDiv"> 
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="looColor">Loop Color</label>
                                <select name="looColor" id="looColor" class="form-control"  required onchange="testLoop()">
                                    <option value="">select</option>
                                    @foreach($loopColor as $val)
                                      <option value="{{$val->loop_color}}" id="{{$val->loop_color}}" data-item="{{json_encode($val)}}">{{$val->loop_color}}</option>
                                    @endforeach
                                </select>
                                <!-- <input name="looColor" id="looColor" class="form-control" required />                                -->
                                <span class="error-text" id="looColor-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4" id="gussetDiv">
                            <div class="form-group">
                                <label class="form-label" for="g">G </label>
                                <input name="g" id="g" class="form-control" onkeypress="return isNumDot(event);" required />                                                                    
                                <span class="error-text" id="g-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">  
                        <div class="col-sm-4" id="bookingPrintingColorDiv">
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
                    <div class="row mt-3" style="text-align:right">
                        <div > Balance <span id="balance" style="color: red;"> </span> </div>
                    </div>
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
                                    <th>Possible Production</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="showRollSuggestion()">Check</button>
                    <button type="submit" class="btn btn-primary" onclick="setHintCollapse();">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <x-client-form/>
</main>


<script>
    $(document).ready(function(){
        getBalance();
        $('.select22').select2(); 
        $("#orderHistory").hide();      

        $('#bookingPrintingColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',
            // dropdownParent: $('#rollBookingModal'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });

        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollBookingModal').css("z-index","");
        });
        showHideLoop();

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
                    required: function(){
                        return $("#bagQuality").val()!=="BOPP"
                    },
                },
                roll_id:{
                    required:true,
                }
            },
            submitHandler: function(form) {
                bookForClient();
            }
        });

        $('#bookingForClientId').select2(); 
    });
    function openRollBookingClineModel(){
        $('#rollBookingModal').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }
    

    function showHideLoop(){
        var bagType = $("#bookingBagTypeId").val();
        console.log(bagType);
        if(["2","4"].includes(bagType)){
            $("#loopColorDiv").show();
            $("#gussetDiv").show();
            if(bagType=="4"){
                $("#gussetDiv").hide();
            }
        }else{
            $("#looColor").val("");
            $("#g").val("");
            $("#loopColorDiv").hide();
            $("#gussetDiv").hide();
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
                                "<th>Bag Size</th>",
                                "<th>Bag Color</th>",
                                "<th>GSM</th>",
                                "<th>Bag Type</th>",
                                "<th>Action</th>",
                            )
                        );

                        const tbody = $("<tbody>");
                        
                        // Populate the rows
                        response.data.forEach((item,index) => {
                            tbody.append(                               
                                $("<tr>").append(
                                    `<td>${parseFloat(item.bag_w) + parseFloat(item.bag_g ? item.bag_g : 0) } X ${ parseFloat(item.bag_l)}</td>`,
                                    `<td>${item.bag_color}</td>`,
                                    `<td>${item.bag_gsm || "N/A"}</td>`,
                                    `<td>${item.bag_type || "N/A"}</td>`,
                                    `<td><button data-item='${JSON.stringify(item)}' id="or${index}" onclick="setOrderValue('or${index}')" class="btn btn-sm btn-info">Place Order</button></td>`,
                                    
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
        console.log(item);

        // Set individual field values
        $("#bookingBagTypeId").val(item?.bag_type_id);
        $("#bookingBagUnits").val(item?.units);
        $("#l").val(item?.bag_l);
        $("#g").val(item?.bag_g);
        $("#w").val(item?.bag_w);
        $("#bookingBagColor").val(item?.bag_color);

        $("#ratePerUnit").val(item?.rate_per_unit);
        $("#totalUnits").val(item?.total_units);
        $("#bookingBagUnits").val(item?.units);
        $("#bagGsm").val(item?.bag_gsm);
        $("#bagQuality").val(item?.bag_quality);

        $("#gradeId").val(item?.grade_id);
        $("#rateTypeId").val(item?.rate_type_id);
        $("#fareTypeId").val(item?.fare_type_id);
        $("#stereoTypeId").val(item?.stereo_type_id);
        $("#looColor").val(item?.bag_loop_color);
        showHidePrintingColorDiv();
        getBalance();
        
        // Set the multi-select field for 'bookingPrintingColor'
        try {
            // Parse the printing_color string to an array
            const printingColors = JSON.parse(item?.bag_printing_color) || [];
            $("#bookingPrintingColor").val(printingColors).trigger("change");; // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#bookingPrintingColor").val([]).trigger("change");; // Clear in case of error
        }
    }

    function showRollSuggestion(){
        let itsOk = true;
        let inputs = [
            { id: "#bookingBagTypeId", name: "Bag Type" },
            { id: "#bookingBagUnits", name: "Bag Units" },
            { id: "#totalUnits", name: "Total Units" },
            { id : "#bagQuality" , name : "bag Quality"},
            { id: "#l", name: "Bag Length" },
            { id: "#w", name: "Bag Width" },
            { id: "#bagGsm", name: "Bag GSM" },
            { id: "#bookingBagColor", name:"Bag Color"},
        ];
        if($("#bookingBagTypeId").val()=="2"){
            inputs.push({ id: "#g", name: "Bag Gusset" });
        }
        if($("#bagQuality").val()=="BOPP"){
            inputs.push({ id: "#bagGsmJson", name: "Bag Gusset" });
        }

        for (let input of inputs) {
            $(input.id).css("border", "1px solid #cbcaca");
            if (!$(input.id).val()) {
                $(input.id).focus();
                $(input.id).css("border", "2px solid red");                
                if(itsOk){
                    itsOk= false;
                }
                console.log(input.id,itsOk);
                //return;  // Exit the function after the first empty field
            }
        }
        if(!itsOk){
            return false;
        }
        $.ajax({
            url:"{{route('client.order.suggestion')}}",
            type:"post",
            data:$("#myForm").serialize(),
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                console.log(response.data.rollTransit.length);
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
                            "<th>Total Possible Product</th>", 
                            "<th>Book For</th>",                            
                            "<th>Add To Book</th>",
                        )
                    );

                    const tbody = $("<tbody style='color:#ffff;'>");
                    
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
                                `<td>${item?.unit || "N/A"}</td>`, 
                                `<td>${item?.client_name || ""}</td>`,                                
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
                            "<th>Total Possible Product</th>",                               
                            "<th>Book For</th>",                          
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
                                `<td>${item.unit || "N/A"}</td>`,
                                `<td>${item?.client_name || ""}</td>`,
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
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                $("#suggestion1").hide();
                $("#suggestion2").hide();
            }
        });
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
                                `<td>${item.result || "N/A"}</td>`,                                
                                `<td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>`,
                            );
        $("#orderRoll tbody").append(tr);
        getBalance();
    }

    function removeTr(element) {
        $(element).closest("tr").remove();
        getBalance();
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
                    getBalance();
                    resetForm("myForm");
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

    function emptyTable(){
        $("#orderRoll tbody").empty();
        getBalance();
    }
    function getBalance() {
        let bookedQtr = 0;
        
        $("#orderRoll tbody tr").each(function () {
            let value = $(this).find('td').eq(8).text(); // Adjust the index to the correct column
            if (!isNaN(value) && value.trim() !== '') {
                bookedQtr += parseFloat(value);
            }
        });
        $("#balance").html(($("#totalUnits").val()-bookedQtr)+" "+$("#bookingBagUnits").val());        
        return bookedQtr;
    }

    function showHidePrintingColorDiv(){
        if($("#bagQuality").val()=="BOPP"){
            $("#bookingPrintingColorDiv").hide();
            $("#singleGsm").hide();
            $("#multipleGsm").show();
            $("#bagGsm").val("");
        }else{
            $("#bookingPrintingColorDiv").show();
            $("#singleGsm").show();
            $("#multipleGsm").hide();
            $("#bagGsmJson").val("");
        }
        setGsm();
    }

    function setGsm(){
        let gsmJson = $("#bagGsmJson").val();
        let gsm = 0;
        const parts = gsmJson.split(/\/+/);
        for (let part of parts) {
            gsm+= parseFloat(part);
        }
        if(gsmJson){
            $("#bagGsm").val(gsm);
        }
        console.log("gsm:",gsm);
    }

    function resetForm(id){
        $("#"+id).get(0).reset();
        $('#'+id+' select').each(function() {
            if ($(this).data('select2')) {
                $(this).val(null).trigger('change');
            }
        });
    }

    function testLoop(){
        let loopColor = $("#looColor").val();
        if(loopColor){
            $.ajax({
                url : "{{route('master.loop.stock.booking.test')}}",
                type : "post",
                data : {"loopColor":loopColor},
                beforeSend:function(){
                    $("loadingDiv").show();
                },
                success:function(data){
                    if(data?.status){
                        modelInfo(data?.message,data?.data);
                    }
                    else{                        
                        testTemp(loopColor);
                    }
                },
                error:function(error){
                    console.log(error);
                    testTemp(loopColor);
                }
            })
        }
    }

    function testTemp(color){
        item = JSON.parse($("#"+color).attr("data-item"));
        if(item?.balance < item?.min_limit){
            modelInfo(item?.loop_color+" is very short","warning");
        }else if(item?.balance < (item?.min_limit+100)){
            modelInfo(item?.loop_color+" is nearly sorted.","info");
        }
    }


</script>


@include("layout.footer")