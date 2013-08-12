<html>
<head>
<title> generate class from xml </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
</head>
<body>
<center>
<form action='form-handler.php' method='post' enctype='multipart/form-data'>
<table>
	<tr>
		<td>XML-file: </td>
		<td><input type='file' value=''/></td>
	</tr>
	<tr>
		<td>Select language:</td>
		<td>
			<select name='lang'>
				<option>C++ && Qt 5.1 (QXmlReader, QXmlWriter)</option>
				<option>C++ Builder XE3</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>rename class <br> or namespace: </td>
		<td><input name='rename' type='text' value=''/></td>
	</tr>
	<tr>
		<td></td>
		<td><input type='submit' value='OK'/></td>
	</tr>	
	
</table>
</form>

<?

function parse_xmlclass($xml, $root = true, $ident = "")
{
	if($root) echo "<pre>\n";
	echo $ident."struct ";
	if(!$root) echo "_";
	echo $xml->getName()."{ \n";
	
	if($xml->children()->count() == 0)
	{
		echo $ident."\tQString Body;\n";
	}
	
	echo $ident."\tstruct _Attr_".$xml->getName()." {\n";
	if($xml->attributes()->count() > 0)
	{
		foreach($xml->attributes() as $attrname => $attrvalue) {
		    echo "1";
			echo $ident."\t\tQString ".$attrname."; // default value = $attrvalue \n";
		}
	}
	echo $ident."\t} Attributes;\n";
	
	foreach($xml->children() as $child)
	{
		parse_xmlclass($child, false, $ident."\t");
	}
	echo "} ";
	if(!$root) echo $xml->getName().";\n"; else echo ";\n</pre>";
};

	echo "Привет, мир!";
	// http://php.net/manual/en/book.simplexml.php
	$xmlfile = "test.xml";
    $xml = simplexml_load_file($xmlfile);
	//var_dump($xml);
	// $xml->getNamespace();
	echo "</center>";
	parse_xmlclass($xml);
	echo "<center>";
?>

</body>
</html>
