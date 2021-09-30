function form_x() {
	var i = document.form_entete.type.selectedIndex,
	val = document.form_entete.type.options[i].value;

	parent.location.href = val;
}