function form_x() {
	var i = document.form_contact.objet.selectedIndex,
	val = document.form_contact.objet.options[i].value;

	parent.location.href = val;
}