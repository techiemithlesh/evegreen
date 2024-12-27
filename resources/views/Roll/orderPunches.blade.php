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
            <form action="" id="myForm" class="row g-3">                
                @csrf
                <div class="row">
                    <div style="text-align: center;">
                        <div class="example-box movable">
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
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <x-client-form/>
</main>


<script>
    $(document).ready(function(){
        $('.select22').select2();       

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

    // function showPrivOrder(event){
    //     const clientId = $(event.target).val();
    //     if(clientId!=""){
    //         $.ajax({
    //             url:"{{route('client.old.order')}}",
    //             type:"post",
    //             data:{"clientId":clientId},
    //             dataType:"json",
    //             beforeSend:function(){
    //                 $("#loadingDiv").show();
    //             },
    //             success:function(data){
    //                 $("#loadingDiv").hide();
                    
    //                 $("#history").html(data?.data)
    //             }
    //         })
    //     }

    // }

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
                        $("#history").empty();

                        // Create the table
                        const table = $("<table>").addClass("history-table");
                        const thead = $("<thead>").append(
                            $("<tr>").append(
                                "<th>ID</th>",
                                "<th>Roll No</th>",
                                "<th>Purchase Date</th>",
                                "<th>Roll Color</th>",
                                "<th>Size</th>",
                                "<th>Net Weight</th>",
                                "<th>Gross Weight</th>",
                                "<th>Printing Colors</th>",
                                "<th>Delivered</th>"
                            )
                        );

                        const tbody = $("<tbody>");
                        
                        // Populate the rows
                        response.data.forEach(item => {
                            tbody.append(
                                $("<tr>").append(
                                    `<td>${item.id}</td>`,
                                    `<td>${item.roll_no}</td>`,
                                    `<td>${item.purchase_date}</td>`,
                                    `<td>${item.roll_color || "N/A"}</td>`,
                                    `<td>${item.size || "N/A"}</td>`,
                                    `<td>${item.net_weight || "N/A"}</td>`,
                                    `<td>${item.gross_weight || "N/A"}</td>`,
                                    `<td>${JSON.parse(item.printing_color || "[]").join(", ")}</td>`,
                                    `<td>${item.is_delivered ? "Yes" : "No"}</td>`
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
                        $("#history").html("<p>No records found.</p>");
                    }
                },
                error: function () {
                    $("#loadingDiv").hide();
                    $("#history").html("<p>An error occurred while fetching data.</p>");
                }
            });
        }
    }

</script>


@include("layout.footer")