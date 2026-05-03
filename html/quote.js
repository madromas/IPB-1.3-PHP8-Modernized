function ins(name) {
    // Insert bold name with a trailing space and line break
    var htmlName = "<b>" + name + "</b>, &nbsp;";

    if (window.tinyMCE && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, htmlName);
        return;
    }

    if (document.REPLIER && document.REPLIER.Post) {
        var input = document.REPLIER.Post;
        input.value += htmlName;
        input.focus();
    }
}

function paste(text) {
    if (window.tinyMCE && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
        return;
    }

    if (document.REPLIER && document.REPLIER.Post) {
        var textarea = document.REPLIER.Post;
        textarea.value += text;
        textarea.focus();
    }
}

function Insertranged(text, autorpost, datapost) {
    if (text && text !== "") {
        // The exact HTML structure from your $class->dump_quote setup
        var formattedQuote = 
            "<div class='quote-wrapper'>" +
                "<b>" + autorpost + "</b> (" + datapost + ")" +
                "<div class='quote-content'>" + text + "</div>" +
            "</div><p>&nbsp;</p>\n";
            
        paste(formattedQuote);
    } else {
        alert("Please select some text first!");
    }
}

function Insert(text) { 
    if (text != "") {
        // Simple quote wrapper for selections without author metadata
        var simpleQuote = 
            "<div class='quote-wrapper'>" +
                "<div class='quote-content'>" + text + "</div>" +
            "</div><p>&nbsp;</p>\n";
        paste(simpleQuote);
    }
}

function get_selection() {
    var selectionText = "";
    if (window.getSelection) {
        selectionText = window.getSelection().toString(); 
    } else if (document.selection) {
        selectionText = document.selection.createRange().text;
    }

    if (selectionText !== "") {
        selectionText = selectionText.replace(/\r\n/gi, " ");
        while (selectionText.indexOf("  ") != -1) {
            selectionText = selectionText.replace(/  /gi, " ");
        }
        window.txt = selectionText;
    }
}

function CopyQuote() {
    window.txt = "";
    if (window.getSelection) {
        window.txt = window.getSelection().toString();
    } else if (document.selection) {
        window.txt = document.selection.createRange().text;
    }
}