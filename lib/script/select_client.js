function form_x() {
	var i = document.form_devis.type.selectedIndex,
	val = document.form_devis.type.options[i].value;

	parent.location.href = val;
}