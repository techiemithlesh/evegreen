const toggler = document.querySelector(".toggler-btn");
toggler.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("collapsed");
});
  
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM is fully loaded and parsed!');

    // Get all the sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar-link');

    // Event listener for when a sidebar link is clicked
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            // Only prevent default action if it is a collapsible item
            const targetId = e.target.closest('a').getAttribute('data-bs-target').slice(1); // Remove the '#' from the data-bs-target attribute

            // Store the active menu and submenu names in sessionStorage
            const parentMenu = e.target.closest('.sidebar-item');
            if (parentMenu) {
                const menuId = parentMenu.querySelector('a').getAttribute('id').slice(1); // Get the menu id without "p" prefix
                const subMenuId = targetId;

                // Call the function to store active menu and submenu state
                navBarMenuActive(menuId, subMenuId);
            }

            // Get all collapsible items (menus)
            const collapsibleItems = document.querySelectorAll('.sidebar-dropdown');

            collapsibleItems.forEach(item => {
                const itemId = item.id;
                const collapse = new bootstrap.Collapse(item, {
                    toggle: false // Don't toggle automatically
                });

                // Check if the clicked item is a child of the current item
                if (!item.contains(e.target)) {
                    if (itemId !== targetId) {
                        collapse.hide(); // Hide other items
                    } else {
                        // collapse.toggle(); // Toggle the clicked item
                    }
                }
            });

            // Only prevent the default action for links that are collapsible
            if (e.target.closest('a').hasAttribute('data-bs-target')) {
                e.preventDefault(); // Prevent default action for collapsible links
            }
        });
    });

    if (typeof(Storage) !== "undefined") {
        const activeMenuName = sessionStorage.getItem('activeMenuName');
        const activeSubMenuName = sessionStorage.getItem('activeSubMenuName');
        const activeSubSubMenuName = sessionStorage.getItem('activeSubSubMenuName');

        if (activeMenuName && activeSubMenuName) {
            // Set the menu and submenu as active
            const activeMenu = document.getElementById('p' + activeMenuName);
            const activeSubMenu = document.getElementById("p"+activeSubMenuName);
            const activeSubSubMenu = document.getElementById(activeSubSubMenuName);
            console.log("activeMenu:",activeMenu);
            console.log("activeSubMenu:",activeSubMenu);
            console.log("activeSubSubMenu:",activeSubSubMenu);

            if (activeMenu) {
                activeMenu.setAttribute("aria-expanded", "true"); // Ensure the parent is expanded
                const parentDropdown = document.getElementById(activeMenuName);
                if (parentDropdown) {
                    parentDropdown.classList.add("show"); // Ensure the parent dropdown is visible
                }
            }

            // Set the second-level submenu as active
            if (activeSubMenu) {
                activeSubMenu.classList.add("show");
                activeMenu.setAttribute("aria-expanded", "true"); 
                const secondDropdown = document.getElementById(activeSubMenuName);
                if (secondDropdown) {
                    secondDropdown.classList.add("show");
                }
            }

            // Set the third-level submenu as active
            if (activeSubSubMenu) {
                activeSubSubMenu.setAttribute("aria-expanded", "true"); 
                const thirdDropdown = document.getElementById(activeSubSubMenuName);
                if (thirdDropdown) {
                    thirdDropdown.classList.add("show");
                }
            }

            if(activeSubMenu){
                activeSubMenu.classList.add("active-link");
            }
        }
    }
});

function toggleNave(element){
    console.log(element.classList.toggle("collapsed"));
    if(!element.classList.toggle("collapsed")){
        element.classList.add("show");
    }
}

// Function to store the active menu and submenu in sessionStorage
function navBarMenuActive(menuName, subMenuName,subSubMenuName) {
    if (typeof(Storage) !== "undefined") {
        sessionStorage.setItem("activeMenuName", menuName);
        sessionStorage.setItem("activeSubMenuName", subMenuName);
        sessionStorage.setItem("activeSubSubMenuName", subSubMenuName);
        const activeSubMenu = document.getElementById("p"+subMenuName);
        if(activeSubMenu){
            document.querySelectorAll(".active-link").forEach((element) => {
                element.classList.remove("active-link");
            });
            
            activeSubMenu.classList.add("active-link");
        }

    }
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