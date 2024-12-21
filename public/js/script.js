const toggler = document.querySelector(".toggler-btn");
toggler.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("collapsed");
});

function navBarMenuActive(menuName, subMenuName){
  
  if (typeof(Storage) !== "undefined") {
      sessionStorage.setItem("activeMenuName", menuName);
      sessionStorage.setItem("activeSubMenuName", subMenuName);
  }
}
if(typeof(Storage) !== "undefined") {

      // var activeMenuName = sessionStorage.getItem('activeMenuName');
      // var activeSubMenuName = sessionStorage.getItem('activeSubMenuName');
      // $("#p"+activeMenuName).attr("aria-expanded","true");
      // $("#"+activeMenuName).addClass("show");
      // console.log($("#"+activeMenuName));
      // $("#ul_"+activeSubMenuName).addClass("active-link");
}

function addFilter(tableName,indexNo=[]){
  // Define the table and first row headers
  var table = $('#'+tableName);

  // Dynamically create the second row in <thead> for dropdown filters
  var filterRow = $('<tr></tr>'); // Create a new <tr> for filters

  table.find('thead tr:nth-child(1) th').each(function (index) {
      // Create <th> and <select> dynamically for each header
      if (indexNo.includes(index)) {
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