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
		<td>Select language:</td>
		<td>
			<select name='typeview'>
				<option>On Page</option>
				<option>*.zip</option>
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

class Element
{
	var $name;
	var $attr = array();
	
	function setName($name) {
		$this->name = $name;
	}
	
	function name() {
		return $this->name;
	}
	
	function print_debug()
	{
		foreach($this->attr as $attrname => $val )
		{
			echo $attrname;
		};
	}
	
	function reset()
	{
		foreach($this->attr as $attrname)
		{
			$val = $this->attr[$attrname];
			if( $val == 1 || $val == 0)
				$this->attr[$attrname] = 0;
			else
				$this->attr[$attrname] = $val-1;
		};
		
		/*foreach($this->attr as $attrname)
			$this->attr[$attrname] = 0;*/
	}
	
	function addAttributeName($name) {
		if(isset($attr[$name]))
			$attr[$name] = $attr[$name] + 1;
		else
			$attr[$name] = 0;
	}
	
	function addChildName($name) {
		
	}
	
	function write($elements)
	{
	
	}
};

$elements = array();

function parse_xmlclass($xml, $root = true, $ident = "")
{
	if($root) echo "<pre>\n";
	echo $ident."struct ";
	if(!$root) echo "_";
	echo $xml->getName()." { \n";
	
	$obj;
	
	if(!isset($elements[$xml->getName()]))
	{
		$obj = new Element();
		$obj->setName($xml->getName());
		$elements[$obj->name()] = $obj;
	}
	else
		$obj = $elements[$xml->getName()];
	
	$obj->reset();
	
	if($xml->children()->count() == 0)
	{
		echo $ident."\tQString Body;\n";
	}
	
	echo $ident."\tstruct _Attr_".$xml->getName()." {\n";
	if($xml->attributes()->count() > 0)
	{
		foreach($xml->attributes() as $attrname => $attrvalue) {
			$obj->addAttributeName($attrname);
			echo $ident."\t\tQString ".$attrname.";\n"; // ."; // default value = $attrvalue \n";
		}
	}
	echo $ident."\t} Attributes;\n";
	
	foreach($xml->children() as $child)
	{
		parse_xmlclass($child, false, $ident."\t");
	}
	echo $ident."} ";
	if(!$root) echo $xml->getName().";\n"; else echo ";\n</pre>";
	
	if($root)
	{
		foreach($elements as $elem_name => $elem_) {
			echo "Element name: ".$elem_name."<br>";
			
		}
		var_dump($elements);	
	}
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
