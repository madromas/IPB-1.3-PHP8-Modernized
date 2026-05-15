function toggleShareMenu(event) {
    // Prevent the click from bubbling up to the document
    if (event) event.stopPropagation();
    
    var menu = document.getElementById('share-menu-id');
    
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
    } else {
        menu.style.display = 'none';
    }
}

// Close the menu if the user clicks anywhere else on the page
document.onclick = function(event) {
    var menu = document.getElementById('share-menu-id');
    if (menu && menu.style.display === 'block') {
        menu.style.display = 'none';
    }
};

function toggleSearchBox() {
    var row = document.getElementById("search-dropdown-row");
    var input = row.querySelector('.modern-input');

    if (row.style.display === "block") {
        row.style.display = "none";
    } else {
        row.style.display = "block";
        // Focus the input so user can type immediately
        input.focus();
    }
}