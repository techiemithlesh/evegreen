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
                                <th>Bag Type</th>
                                <th>Bag Size</th>
                                <th>Unit</th>
                                <th>Client Requires</th>
                                <th>Total Roll Weight</th>
                                <th>Total Garbage Weight</th>
                                <th>Total Bag Weight</th>
                                <th>Balance</th>
                                <th>Add</th>
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
            responsive: true,
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
                { data: "bag_type", name: "bag_type" },
                { data: "bag_size", name: "bag_size",render:function(row,type,data){ return `${parseFloat (data.bag_w) + parseFloat(data.bag_g ? data.bag_g :0.00)} X ${data.bag_l}`} },
                { data: "units", name: "units" },
                { data: "total_units", name: "total_units" },
                { data: "roll_weight", name: "roll_weight" },
                { data: "total_garbage", name: "total_garbage" },
                { data: "packing_weight", name: "packing_weight" },
                { 
                    data: null,
                    name: "balance",
                    render: function(row, type, data) {
                        return data.roll_weight - data.packing_weight - data.total_garbage;
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(row, type, data) {
                        return `
                            <table class="mt-2 table table-bordered table-fixed" style="display:none;" id="table_${data.id}">
                            </table>
                            <button type="button" data-item='${JSON.stringify(data)}' 
                                    id="button_${data.id}" 
                                    onclick="addTr('${data.id}')" 
                                    class="btn btn-sm btn-primary">+</button>
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
            ]
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
        let item = JSON.parse(buttonElement.getAttribute('data-item'));
        if (table.find("tbody").length === 0) {
            table.append("<tbody></tbody>");
        }
 

        let tr = $("<tr>").attr("data-id", item.id)
            .append(`
                <td>
                    <input type='hidden' name='roll[${sl}][id]' value='${item.id}' />
                    <input type='text' class="form-control" style="width:100px" placeholder="Weight" id='roll[${sl}][weight]' name='roll[${sl}][weight]' required onkeypress="return isNumDot(event);" />
                    <span class="error-text" id="roll[${sl}][weight]-error"></span>
                </td>
                <td>
                    <input type='text' class="form-control" style="width:100px" placeholder="Piece" id='roll[${sl}][pieces]' name='roll[${sl}][pieces]' ${item.units === 'Piece' ? 'required' : ''} onkeypress="return isNumDot(event);" />
                    <span class="error-text" id="roll[${sl}][pieces]-error"></span>
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
                    $("#orderRoll").DataTable().ajax.reload();
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
</script>

@include('layout.footer')
