
function isNum(e){
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
}
function isAlpha(e){
    var keyCode = (e.which) ? e.which : e.keyCode
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
        return false;

    return true;
}
function isNumDot(e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode == 46) {
        var txt = e.target.value;
        if ((txt.indexOf(".") > -1) || txt.length == 0) {
            return false;
        }
    } else {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    }
}
jQuery.validator.addMethod("dateFormatYYYMMDD", function(value, element) {
    return this.optional(element) || /^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))+$/i.test(value);
}, "Invalid format (YYYY-MM-DD)"); 

jQuery.validator.addMethod("alphaSpace", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]+$/i.test(value);
}, "Letters only please (a-z, A-Z )");

$(".toggler-btn").on("click", function() {
    document.querySelector("#sidebar").classList.toggle("collapsed");
});
toastr.options = {
    "closeButton": true, // Show close (dismiss) button
    "debug": false, 
    "newestOnTop": true,
    "progressBar": true, // Display a progress bar
    "positionClass": "toast-top-right", // Top right corner
    "preventDuplicates": true, // Avoid duplicate toasts
    "onclick": null,
    "showDuration": "300", // How long the show animation lasts
    "hideDuration": "1000", // How long the hide animation lasts
    "timeOut": "3000", // Auto-hide delay (5 seconds)
    "extendedTimeOut": "1000", // Extra time for mouse hover
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
function modelInfo(msg,className="success") {
    

    if(className=="error") {
        toastr.error(msg);
    }
    else if(className=="info") {
        toastr.info(msg);
    }
    else if(className=="warning") {
        toastr.warning(msg);
    }else{
        toastr.success(msg);
    }

}


function addEventListenersToForm(id) {
    const form = document.getElementById(id);
    // Loop through all elements in the form
    Array.from(form.elements).forEach((element) => {
        // Add event listeners based on input type
        if (element.tagName === "INPUT" || element.tagName === "SELECT" || element.tagName === "TEXTAREA") {
            element.addEventListener("input", hideErrorMessage);
            element.addEventListener("change", hideErrorMessage);
        }
    });
}

function hideErrorMessage(event) {
    const element = event.target;
    // Find and hide the error message associated with the element
    const errorMessage = document.getElementById(`${element.id}-error`);
    if (errorMessage) {
        errorMessage.innerHTML = "";
    }
}



// add search option 

//=============================================

function addSearch(id) {
    const select = $("#" + id);
    const options = getOptions(select);

    const searchInput = $('<input type="text" class="search-input" placeholder="Search...">');
    const dropdown = $('<div class="dropdown"></div>');

    select.before(searchInput);
    select.before(dropdown);

    searchInput.css({
        width: select.outerWidth() + "px",
        height: select.outerHeight() + "px",
        padding: select.css("padding"),
        fontSize: select.css("font-size")
    });

    dropdown.css({
        width: select.outerWidth() + "px",
    });

    searchInput.hide();
    dropdown.hide();

    select.on("click", function () {
        toggleSelectAndSearch(select, searchInput, dropdown);
    });

    populateDropdown('', options, dropdown, searchInput, select);

    searchInput.on("input", function () {
        const filter = $(this).val().toLowerCase();
        populateDropdown(filter, options, dropdown, searchInput, select);
    });

    observeSelectChanges(select, options, dropdown, searchInput); // Observe changes in the <select>
}

function getOptions(select) {
    return Array.from(select.find("option")).map(option => ({
        text: option.textContent || option.innerText,
        value: option.value
    }));
}

function populateDropdown(filter = '', options, dropdown, searchInput, select) {
    dropdown.empty();
    const filteredOptions = options.filter(option =>
        option.text.toLowerCase().includes(filter.toLowerCase())
    );

    if (filteredOptions.length === 0) {
        dropdown.append('<div>No results found</div>');
        return;
    }

    filteredOptions.forEach(option => {
        const div = $("<div>")
            .text(option.text)
            .attr("data-value", option.value)
            .css("cursor", "pointer")
            .on("click", function () {
                setSelect(option.value, option.text, dropdown, searchInput, select);
            });
        dropdown.append(div);
    });

    const offset = select.offset();
    
}

function setSelect(value, text, dropdown, searchInput, select) {
    select.val(value);
    dropdown.hide();
    searchInput.val(text);
    searchInput.hide();
    select.show();
}

function toggleSelectAndSearch(select, searchInput, dropdown) {
    if (searchInput.is(":visible")) {
        searchInput.hide();
        dropdown.hide();
        select.show();
    } else {
        searchInput.show();
        searchInput.focus();
        dropdown.show();
        select.hide();
    }
}

function observeSelectChanges(select, options, dropdown, searchInput) {
    const observer = new MutationObserver(() => {
        // Update options array and redraw dropdown
        options.length = 0;
        options.push(...getOptions(select));
        populateDropdown('', options, dropdown, searchInput, select);
    });

    observer.observe(select[0], { childList: true });
}



