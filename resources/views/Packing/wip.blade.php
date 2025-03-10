@include('layout.header')
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Entry</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">WIP List</h5>            
        </div>        
        <div class="panel-body">            
            <form action="" id="entryForm">
                <div class="row">
                    <table class="table table-bordered table-responsive table-fixed" id="orderRoll">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Bag Size</th>
                                <th>Bag Type</th>
                                <th>Bag Color</th>
                                <th>Bag GSM</th>
                                <th>Total Bag Weight</th>
                                <th>Unit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary" id="submit" style="display:none;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    $(document).ready(function(){
        $('#orderRoll').DataTable({
            responsive: false,
            processing: false,
            ordering: false,
            ajax: {
                url: "{{route('packing.wip')}}", // The route where you're getting data from
                data: function(d) {
                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
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
                { data: "client_name", name: "client_name" },
                { data: "bag_size", name: "bag_size" ,render: function(item) {  return `<pre>${item}</pre>`; }},
                { data: "bag_type", name: "bag_type" },
                { data: "bag_color", name: "bag_color" },
                { data: "bag_gsm", name: "bag_gsm" },
                { data: null,name: "balance",render: function(row, type, data) { return parseFloat(data.balance).toFixed(2);}},
                { data: "units", name: "units" },
                { data: null, orderable: false, searchable: false, render: function(row, type, data) {
                        return `
                            <table class="mt-2 table table-bordered table-fixed" style="display:none;" id="table_${data.id}">
                            </table>
                            <button type="button" data-item='${JSON.stringify(data)}' 
                                    id="button_${data.id}" 
                                    onclick="addTr('${data.id}')" 
                                    class="btn btn-sm btn-primary">+</button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="disburseOrderConform(${data.id},'${parseFloat(data.balance).toFixed(2)} Kg')">D</button>
                        `;
                    }
                }
            ],

            dom: 'lBfrtip',
            language: {
                lengthMenu: "Show _MENU_"
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ["10 Rows", "25 Rows", "50 Rows", "100 Rows", "All"]
            ],
            buttons: [
                {
                    extend: 'csv',
                    text: 'Export to Excel',
                    className: 'btn btn-success'
                }
            ],
            initComplete: function () {
                addFilter('orderRoll',[0,$("#orderRoll th").length-1]);
            }, 
        });

        $("#entryForm").validate({
            rules: {
                "roll[][id]": {
                    required: true,
                    number: true,
                },
                "roll[][weight]":{
                    required:true,
                    number: true,
                },
            },
            submitHandler: function(form) {
                entryFormSubmit();
            }
        });
    });

    let sl = 0;
    function addTr(id) {
        sl++;
        let table = $("#table_" + id);
        let buttonElement = document.getElementById("button_" + id); 
        console.log(buttonElement.getAttribute('data-item'));
        let item = JSON.parse(buttonElement.getAttribute('data-item'));
        if (table.find("tbody").length === 0) {
            table.append("<tbody></tbody>");
        }
        let formula = item?.formula_ideal_weight;
        let valueObj = item?.valueObject;
 

        let tr = $("<tr>").attr("data-id", item.id)
            .append(`
                <td>
                    <input type='text' class="form-control" style="width:100px" placeholder="Piece" id='roll[${sl}][pieces]' name='roll[${sl}][pieces]' ${item.units === 'Piece' ? 'required' : ''} onkeypress="return isNumDot(event);" />
                    <span class="error-text" id="roll[${sl}][pieces]-error"></span>
                </td>
                <td>
                    <input type='text' class="form-control" style="width:100px" placeholder="IdealWeight" id='roll[${sl}][idealWeight]' name='roll[${sl}][idealWeight]' readonly value='0' />
                </td>
                <td>
                    <input type='hidden' name='roll[${sl}][id]' value='${item.id}' />
                    <input type='text' class="form-control" style="width:100px" placeholder="Weight" id='roll[${sl}][weight]' name='roll[${sl}][weight]' required onkeypress="return isNumDot(event);" />
                    <span class="error-text" id="roll[${sl}][weight]-error"></span>
                </td>                
                <td><button type='button' onclick='removeTr(this, ${id})' class='btn btn-sm btn-warning'>X</button></td>
            `);
        
        table.find("tbody").append(tr);
        table.show();
        $("#submit").show();
    }

    function removeTr(element, id) {
        $(element).closest("tr").remove();
        let table = $("#table_" + id);
        
        if (table.find("input[type='hidden']").length === 0) {
            table.hide();
        }

        if ($("input[type='hidden'][name^='roll']").length === 0) {
            $("#submit").hide();
        }
    }

    function entryFormSubmit(){
        if ($("input[type='hidden'][name^='roll']").length === 0) {
            return false;
        }
        $.ajax({
            url:"{{route('packing.entry.wip.add')}}",
            type:"post",
            dataType:"json",
            data:$("#entryForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    modelInfo(data?.message);
                    $("#entryForm").get(0).reset();
                    searchData();
                    // $("#orderRoll").DataTable().ajax.reload();
                    sl=0;
                }else{
                    modelInfo("Server Error","error");
                    console.log(data);
                }
            },
            error:function(errors){
                $("#loadingDiv").hide();
                modelInfo("Server Error","error");
                console.log(data);
            }
        });
    }

    function disburseOrderConform(orderId,message){
        showConfirmDialog("Are You Sure Discard "+message+"??",function(){ 
            disburseOrder(orderId);
        });
    }

    function disburseOrder(orderId){
        let buttonElement = document.getElementById("button_" + orderId); 
        let item = JSON.parse(buttonElement.getAttribute('data-item'));
        $.ajax({
            url:"{{route('packing.wip.disburse.order')}}",
            type:"post",
            dataType:"json",
            data:{"id":orderId,"balance":item.balance},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                console.log(response);                
                $("#loadingDiv").hide();
                if(response.status){
                    modelInfo(response.message);
                    searchData();
                }else{
                    modelInfo("server error!!","error");
                }
            },
            error:function(errors){
                console.log(errors);
                modelInfo("server error!!","error");
                $("#loadingDiv").hide();
            }
        })
    }

    function searchData(){
        $('#orderRoll').DataTable().ajax.reload(function(){
            addFilter('orderRoll',[0]);
        },false);
    }
</script>

@include('layout.footer')
