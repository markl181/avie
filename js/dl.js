function myFunction() {
    /* Get the text field */
    var copyText = document.getElementsByClassName("copyelement")[0].innerText;

    /* Select the text field */
    //copyText.select();

    navigator.clipboard.writeText(copyText);

    /* Copy the text inside the text field */
    //document.execCommand("copy");

    /* Alert the copied text */
    //alert("Copied the text: " + copyText.value);
}