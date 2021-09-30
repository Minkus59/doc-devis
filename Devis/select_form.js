function form_x() {
	var i = document.form_statue.statue.selectedIndex,
	val = document.form_statue.statue.options[i].value;

	parent.location.href = val;
}