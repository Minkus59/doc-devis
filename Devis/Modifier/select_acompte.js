function form_x() {
    var i = document.FormAcompte.type.selectedIndex,
    val = document.FormAcompte.type.options[i].value;

    parent.location.href = val;
}