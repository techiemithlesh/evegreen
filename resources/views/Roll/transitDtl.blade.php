@include("layout.header")
<!-- Main Component -->

<!-- DataTables SearchPanes -->


<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>                    
                    <li class="breadcrumb-item fs-6"><a href="{{route('roll.transit')}}">Transit Dtl</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">List</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                @if($addToRollInStock??false)
                <button id="addRoll" type="button" class="btn btn-primary fa fa-arrow-right" onclick="transferToRollStock()">
                    Add To Roll Stock <ion-icon name="add-circle-outline"></ion-icon>
                </button>
                @endif
            </div>            
        </div>
        <div class="panel-body">
            
            <table id="postsTable" class="table table-striped table-bordered table-fixed" >
                <thead>
                    <tr>
                        <th >#</th>
                        <th onclick="selectAllCheck()">Select</th>
                        <th>Purchase Date</th>
                        <th>Vendor Name</th>
                        <th>Hardness</th>
                        <th>Roll Type</th>
                        <th>Roll Size</th>
                        <th>GSM</th>
                        <th>Roll Color</th>
                        <th>Length</th>
                        <th>Roll No</th>
                        <th>Gross Weight</th>
                        <th>Net Weight</th>
                        <th>GSM Variation</th>
                        <th>Action</th>
                    </tr>                    
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>    
    <x-roll-booking />
</main>

<script>
    let isCheckBox = '<?=($addToRollInStock??false);?>';
    
    $(document).ready(function () {
        // Get vendor_id from the URL path
        let vendor_id = window.location.pathname.split('/').pop(); 
        let url = window.location.href; // Current URL with query parameters
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: url,
                data: function (d) {
                    let formData = $("#searchForm").serializeArray();
                    $.each(formData, function (i, field) {
                        d[field.name] = field.value;
                    });
                },
                beforeSend: function () {
                    $("#btn_search").val("LOADING ...");
                    $("#loadingDiv").show();
                },
                complete: function () {
                    $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "check", name: "check", orderable: false, searchable: false },
                { data: "purchase_date", name: "purchase_date" },
                { data: "vendor_name", name: "vendor_name" },
                { data: "hardness", name: "hardness" },
                { data: "roll_type", name: "roll_type" },
                { data: "size", name: "size" },
                { data: "gsm", name: "gsm" },
                { data: "roll_color", name: "roll_color" },
                { data: "length", name: "length" },
                { data: "roll_no", name: "roll_no" },
                { data: "gross_weight", name: "gross_weight" },
                { data: "net_weight", name: "net_weight" },
                { data: "gsm_variation", name: "gsm_variation" },
                { data: "action", name: "action", orderable: false, searchable: false },
            ],
            dom: 'lBfrtip', // Updated dom configuration
            language: {
                lengthMenu: "Show _MENU_",
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"],
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export to Excel',
                    className: 'btn btn-success',
                },
            ],
            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(7); 
                td.attr("title", data?.gsm_json); 
                if (data.row_color) {
                    $(row).addClass(data.row_color);
                }
            },
            initComplete: function () {
                addFilter('postsTable',[0,1,$('#postsTable thead tr:nth-child(1) th').length - 1]);
            },
        });

        if (!isCheckBox) {
            table.column(1).visible(false);
        }


    });
    
        

    // Booking form validation
    $("#rollBookingForm").validate({
        rules: {
            bookingForClientId: { required: true },
            bookingEstimatedDespatchDate: { required: true },
            bookingBagUnits: { required: true },
            bookingBagTypeId: { required: true },
            bookingPrintingColor: { required: true },
        },
        submitHandler: function () {
            bookForClient();
        },
    });

    

    function addFilter(tableName){
        // Define the table and first row headers
        var table = $('#'+tableName);

        // Dynamically create the second row in <thead> for dropdown filters
        var filterRow = $('<tr></tr>'); // Create a new <tr> for filters

        table.find('thead tr:nth-child(1) th').each(function (index) {
            // Create <th> and <select> dynamically for each header
            if (index === 0 || index === 1 || index === $('#' + tableName + ' thead tr:nth-child(1) th').length - 1) {
                filterRow.append('<th></th>'); // Empty header for non-filterable columns
            } else {
                var filterCell = $(`
                    <th>
                        <select class="filter-select" data-column="${index}" style="width: 100%" multiple="multiple">
                            <option value="">All</option>
                        </select>
                    </th>
                `);

                // Append <th> with dropdown to the filter row
                filterRow.append(filterCell);

            }
        });

        // Append the filter row to the <thead>
        table.find('thead').append(filterRow);

        // Initialize DataTable
        var dataTable = table.DataTable();

        // Populate dropdowns with unique values for each column
        dataTable.columns().every(function () {
            var column = this;
            var select = $('.filter-select[data-column="' + column.index() + '"]');

            // Get unique values for the column and add them as options
            column.data().unique().sort().each(function (d) {
                select.append('<option value="' + d + '">' + d + '</option>');
            });

            // Initialize Select2 for the dropdown
            select.select2({
                placeholder: 'Select one or more values',
                allowClear: true,
                width: '100%'
            });
        });

        // Add filtering functionality for multi-select
        $('.filter-select').on('change', function () {
            var columnIndex = $(this).data('column'); // Get column index
            var selectedValues = $(this).val(); // Get selected values (array)

            // Build regex to match any of the selected values
            var regex = selectedValues && selectedValues.length > 0
                ? selectedValues.join('|') // Join selected values with "|" for OR regex
                : '';

            // Apply the filter using regex
            dataTable.column(columnIndex).search(regex, true, false).draw(); // Regex-based search
        });
    }

    function openModelBookingModel(id) {
        if (id) {
            $("#rollId").val(id);
            $("#rollBookingModal").modal("show");

        }
    }

    // Book for client
    function bookForClient() {
        $.ajax({
            type: "POST",
            url: "{{route('roll.transit.book')}}",
            dataType: "json",
            data: $("#rollBookingForm").serialize(),
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data.status) {
                    $("#rollBookingForm").get(0).reset();
                    $("#rollBookingModal").modal('hide');
                    var table = $('#postsTable').DataTable();
                    table.ajax.reload(null, false);  
                    modelInfo(data.messages);
                } else if (data?.errors) {
                    let errors = data.errors;
                    for (let field in errors) {
                        $(`#${field}-error`).text(errors[field][0]);
                    }
                } else {
                    modelInfo("Something Went Wrong!!");
                }
            },
        });
    }

    // Trigger table redraw on search
    function searchData() {
        $('#postsTable').DataTable().ajax.reload(null, false);
    }

    function openModelBookingModel(id) {
        if (id) {
            $("#rollId").val(id);
            $("#rollBookingModal").modal("show");

        }
        return;
    }

    function openCloseClientMode(){
        forClientId = $("#forClientId").val();
        if(forClientId!=""){
            $("div[client='client']").show();
        }
        else{
            $("div[client='client']").hide();
        }
    }

    let selectAll = false;

    function selectAllCheck(){
        if(selectAll)
        {
            $('input[name="transitId[]"]').prop("checked",false);             
            selectAll = false;
        }
        else
        {
            $('input[name="transitId[]"]').prop("checked",true);
            selectAll = true;
        }
    }
    function transferToRollStock(){
        var selectitem = [];
        $('input[name="transitId[]"]').each(function() { 
            if ($(this).is(':checked')) {
                selectitem.push($(this).val());
            }
        });
        
        console.log(selectitem);

        $.ajax({
            type: "POST",
            url: "{{route('roll.transit.rll.stock')}}",
            dataType: "json",
            data:{
                items: selectitem, 
            },
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data.status) {
                    var table = $('#postsTable').DataTable();
                    table.ajax.reload(null, false);  
                    modelInfo(data.messages);
                } else if (data?.errors) {
                    let errors = data.errors;
                    for (let field in errors) {
                        $(`#${field}-error`).text(errors[field][0]);
                    }
                } else {
                    modelInfo("Something Went Wrong!!");
                }
            },
        });
    }
</script>

@include("layout.footer")
