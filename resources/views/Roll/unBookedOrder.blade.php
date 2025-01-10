@include("layout.header")
<!-- Main Component -->


<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Order</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Un-Book Order</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>            
        </div>
        <!-- <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-3">
                        <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                    </div>
                </div>
            </form>

        </div> -->
        <div class="panel-body">
            <table id="postsTable" class="table table-striped table-bordered text-center table-fixed">
                <thead>
                    <tr>
                        <th >#</th>
                        <th>Booking Date</th>
                        <th>Client Name</th>
                        <th>Estimate Delivery Date</th>
                        <th>Bag Type</th>
                        <th>Bag Unit</th>
                        <th>Qtr</th>
                        <th>Book Qtr</th>
                        <th>Bag Color</th>
                        <th>Roll No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-unbooked-order-roll-suggestion />
    <x-custom-alert />
</main>
<script>
    
    $(document).ready(function() {        
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            searching:false,
            ajax: {
                url: "{{route('order.unbook')}}", // The route where you're getting data from
                data: function(d) {
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; 
                    });
                },
                beforeSend: function() {
                    $("#btn_search").val("LOADING ...");
                    $("#loadingDiv").show();
                },
                complete: function() {
                    $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },

            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                // { data: "order_no", name: "order_no" },
                { data: "created_at", name: "created_at" },
                { data: "client_name", name: "client_name" },
                { data: "estimate_delivery_date", name: "estimate_delivery_date" },
                { data: "bag_type", name: "bag_type" },
                { data: "units", name: "units" },
                { data: "total_units", name: "total_units" },
                { data: "booked_units", name: "booked_units" },
                { data: "bag_color", name: "bag_color" },
                { data: "roll_no", name: "roll_no" },
                { data: "action", name: "action" },
                
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [{
                extend: 'csvHtml5', // Correct button extension for exporting to CSV
                text: 'Export to Excel', // Button label
                className: 'btn btn-success', // Custom button class
            }],                        
            initComplete: function () {
                addFilter('postsTable',[0,($('#postsTable thead tr:nth-child(1) th').length - 1)]);
            },
        });

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
                roll_id:{
                    required:true,
                }
            },
            submitHandler: function(form) {
                bookForClient();
            }
        });

    });
    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }

    function openBookingModel(id){
        emptyTable();
        $.ajax({
            url:"{{route('order.rebook')}}",
            type:"post",
            data:{"id":id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                console.log(data);
                $("#loadingDiv").hide();
                if(data?.status){
                    modelInfo(data?.message);
                    setOrderValue(data?.data?.order);
                    showRollSuggestion(data);
                    showHideLoop();
                }else{
                    console.log(data);
                    modelInfo("Server Error","error");
                }
            },
            error:function(errors){
                $("#loadingDiv").hide();
                console.log(error);
            }
        });
    }


    function setOrderValue(item) {  

        // Set individual field values
        $("#bookingBagTypeId").val(item?.bag_type);
        $("#bookingEstimatedDespatchDate").val(item?.estimate_delivery_date);
        $("#clientName").val(item?.client_name);
        $("#bookingBagTypeId").val(item?.bag_type);
        $("#bookingBagUnits").val(item?.units);
        $("#l").val(item?.bag_l);
        $("#g").val(item?.bag_g);
        $("#w").val(item?.bag_w);

        $("#ratePerUnit").val(item?.rate_per_unit);
        $("#totalUnits").val(item?.total_units);
        $("#bookingBagUnits").val(item?.units);
        $("#bagGsm").val(item?.bag_gsm);
        $("#bagQuality").val(item?.bag_quality);
        $("#looColor").val(item?.bag_loop_color);
        $("#id").val(item?.id);
        $("#booked").val(item?.booked_units);
        $("#bagGsmJson").val(item?.bag_gsm_json);
        showHidePrintingColorDiv();
        getBalance();
        
        // Set the multi-select field for 'bookingPrintingColor'
        try {
            // Parse the printing_color string to an array
            const printingColors = JSON.parse(item?.bag_color) || [];
            $("#bookingPrintingColor").val(printingColors).trigger("change");; // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#bookingPrintingColor").val([]).trigger("change");; // Clear in case of error
        }
    }

    function showRollSuggestion(response){
        
        $("#loadingDiv").hide();
        $("#suggestion").show();
        
        if (response.status && response.data.roll.length > 0) {
            $("#rollBookForUnbookedOrderModel").modal("show");
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
            $("#rollBookForUnbookedOrderModel").modal("show");
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
            alert("Book Attlist One roll");
            return false;
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
                    $("#rollBookForUnbookedOrderModel").modal("hide");
                    $('#postsTable').DataTable().ajax.reload();
                    modelInfo(data.messages);
                    setHintDefault();
                    getBalance();
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

    function showHideLoop(){
        var bagType = $("#bookingBagTypeId").val();
        console.log(bagType);
        if(["2","Box"].includes(bagType)){
            $("#loopColorDiv").show();
        }else{
            $("#looColor").val("");
            $("#g").val("");
            $("#loopColorDiv").hide();
        }
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
        console.log(gsm);
    }


    function emptyTable(){
        $("#orderRoll tbody").empty();
        getBalance();
    }
    function getBalance() {
        let bookedQtr = parseFloat($("#booked").val());
        
        $("#orderRoll tbody tr").each(function () {
            let value = $(this).find('td').eq(8).text(); // Adjust the index to the correct column
            if (!isNaN(value) && value.trim() !== '') {
                bookedQtr += parseFloat(value);
            }
        });
        $("#balance").html(($("#totalUnits").val()-bookedQtr)+" "+$("#bookingBagUnits").val());        
        return bookedQtr;
    }

    function disbursedOrder(id){
        $.ajax({
            url:"{{route('order.disabused')}}",
            type:"post",
            data:{"id":id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data.status){
                    modelInfo(data?.message);
                    $('#postsTable').DataTable().ajax.reload();

                }else if(data?.errors){
                    modelInfo(data?.message,"warning");
                }
                else{
                    modelInfo("server Error","error");
                }
            },
            error:function(error){
                console.log(error);
                modelInfo("server Error","error");
                $("#loadingDiv").hide();
            }
        });
    }

    function deactivate(id){
        $.ajax({
            url:"{{route('order.deactivate')}}",
            type:"post",
            data:{"id":id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data.status){
                    modelInfo(data?.message);
                    $('#postsTable').DataTable().ajax.reload();

                }else if(data?.errors){
                    modelInfo(data?.message,"warning");
                }
                else{
                    modelInfo("server Error","error");
                }
            },
            error:function(error){
                console.log(error);
                modelInfo("server Error","error");
                $("#loadingDiv").hide();
            }
        });
    }
    

</script>
@include("layout.footer")