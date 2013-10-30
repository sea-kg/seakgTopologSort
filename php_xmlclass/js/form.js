function getComboA(sel) {
	 // var value = sel.options[sel.selectedIndex].value;

	 var value = {
		0 : "<input type='file' name='data_xml' value=''/>",
		1 : "<textarea name='data_xml' />"
	 };
	 document.getElementById("xml_i").innerHTML = value[sel.selectedIndex];
};

function init_v()
{
	getComboA(document.getElementById("xml_input"));
};
