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