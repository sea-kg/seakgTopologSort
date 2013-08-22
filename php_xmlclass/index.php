<html>
<head>
<title> generate class from xml </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">

<script type="text/javascript">
	function getComboA(sel) {
		 // var value = sel.options[sel.selectedIndex].value;

		 var value = {
		 	0 : "<input type='file' value=''/>",
		 	1 : "<textarea />"
		 };
		 document.getElementById("xml_i").innerHTML = value[sel.selectedIndex];
	};
	
	function init_v()
	{
		getComboA(document.getElementById("xml_input"));
	
	};
	
</script>

</head>
<body onload="init_v();">
<center>
<form action='form-handler.php' method='post' enctype='multipart/form-data'>
<table>
	<tr>
		<td>XML: </td>
		<td>
			<select id='xml_input' name='xml_input' id="comboA" onchange="getComboA(this)">
				<option id='fromfile'>from file</option>
				<option id='fromtext'>text</option>
			</select><br>		
		</td>
	</tr>
	<tr> <td/> <td> <div id='xml_i'/> </td> </tr>
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
	var $elems = array();
	var $body = false;
	
	// ------------------------------------------ 
	
	function setName($name) {
		$this->name = $name;
	}
	
	// ------------------------------------------ 
	
	function name() {
		return $this->name;
	}

	// ------------------------------------------ 
		
	function classname() {
		return "_".$this->name;
	}

	// ------------------------------------------ 
	
	function print_debug()
	{	
		$temp;

		$temp .= 'class '.$this->classname()." {\n\n".
		"\tpublic:\n\n";
		
		if(count($this->attr) > 0)
		{
			$temp .= "\t\t// attributes \n";
			foreach($this->attr as $attrname => $attrval )
			{
				if($attrval > 1)
					$temp .= "\t\tQStringList ".$attrname."s; \n";
				else 
					$temp .= "\t\tQString ".$attrname.";\n";
			};
			$temp .= "\n\t\t// elements\n";
			
			/*
			$temp .= "\t\tstruct _XMLAttr_".$this->name." {\n";
			foreach($this->attr as $attrname => $attrval )
			{
				if($attrval > 1)
					$temp .= "\t\t\tQStringList ".$attrname."s; \n";
				else 
					$temp .= "\t\t\tQString ".$attrname.";\n";
			};
			$temp .= "\t\t} Attributes;\n\n";
			*/
		};
		
		$temp .= ($this->body ? "\t\tQString Body;\n\n" : "");

		foreach($this->elems as $elemname => $elemval )
		{
			if($elemval > 1)
				$temp .= "\t\tQList<_".$elemname."> ".$elemname."s;\n";
			else
				$temp .= "\t\t_".$elemname." ".$elemname."; \n";
			//echo 'Subelement name: '.$elemname.'; Subelement value='.$elemval.';<br>';
		};
		$temp .= "};\n";
		
		echo "<pre>".htmlspecialchars($temp)."</pre>";
	}
	
	// ------------------------------------------ 
	
	function reset()
	{
		foreach($this->attr as $attrname => $attrval)
		{
			// $val = $this->attr[$attrname];
			if( $attrval == 1 || $attrval == 0)
			   $this->attr[$attrname] = 0;
			else
				$this->attr[$attrname] = 2;
		};
		
		
		foreach($this->elems as $elemname => $elemval)
		{
			if( $elemval == 0 || $elemval == 1)
				$this->elems[$elemname] = 0;
			else if($elemval > 1)
				$this->elems[$elemname] = 2;
		};
		/*foreach($this->attr as $attrname)
			$this->attr[$attrname] = 0;*/
	}
	
	// ------------------------------------------ 
	
	function merge($elem)
	{
		foreach($elem->elems as $elemname => $elemval)
		{
			if(isset($this->elems[$elemname]))
			{
				$our_elemval = $this->elems[$elemname];
				$this->elems[$elemname] = ($our_elemval >= $elemval) ? $our_elemval : $elemval;
			}
			else
				$this->elems[$elemname] = $elemval;
		};	
	}
	
	// ------------------------------------------ 
	
	function addAttributeName($name) {
		if(isset($this->attr[$name]))
			$this->attr[$name]++;
		else
			$this->attr[$name] = 1;
	}
	
	// ------------------------------------------ 
		
	function addSubElement($name) {

		if(isset($this->elems[$name]))
			$this->elems[$name] = $this->elems[$name] + 1;
		else
			$this->elems[$name] = 1;	
			
		echo "<h1>$name ".$this->elems[$name]."</h1>";
	}
	
	// ------------------------------------------ 
	
	function setBody($b)
	{
		$this->body = $b;
	}
	
	// ------------------------------------------ 
	
	function write($elements)
	{
	
	}
};

// ------------------------------------------ 

// $elements = array();

function parse_xmlclass($elements, $xml, $root = true, $ident = "")
{
	if($root) echo "<pre>\n";
	echo $ident."struct ";
	if(!$root) echo "_";
	echo $xml->getName()." { \n";
	
	$obj;
	$xmlName = $xml->getName();
	if(isset($elements[$xmlName]))
	  $obj = $elements[$xmlName];
	else
	{
		$obj = new Element();
		$obj->setName($xmlName);
		$elements[$xmlName] = $obj;
	}

	// var_dump($elements);

	$obj->reset();
	
	if($xml->children()->count() == 0)
	{
		$obj->setBody(true);
		echo $ident."\tQString Body;\n";
	}
	
	echo $ident."\tstruct _XMLAttr_".$xml->getName()." {\n";
	if($xml->attributes()->count() > 0)
	{
		foreach($xml->attributes() as $attrname => $attrvalue) {
			$obj->addAttributeName($attrname);
			echo $ident."\t\tQString ".$attrname.";\n"; // ."; // default value = $attrvalue \n";
		}
	}
	echo $ident."\t} Attributes;\n";

	foreach($xml->children() as $child)	
		$obj->addSubElement($child->getName());	
	
	foreach($xml->children() as $child)
	{
		// $obj->addSubElement($child->getName());
		$elements = parse_xmlclass($elements, $child, false, $ident."\t");
		// $elements = parse_xmlclass($child, false, $ident."\t");
	}
	echo $ident."} ";
	
	// TODO: merge objects
	// $obj->reset();
	// $elements[$obj->name()]->reset();
	$elements[$obj->name()]->merge($obj);
	
	if(!$root) echo $xml->getName().";\n"; else echo ";\n</pre>";

	if($root)
	{
	   //var_dump($elements);
	   	
	   echo "<hr/><pre>";

		$arr = array_reverse($elements);

	   foreach($arr as $elem_name => $elem) {
		   $elem->reset();
			echo "class ".$elem->classname().";\n";
		}
		
		
		foreach($arr as $elem_name => $elem) {
			$elem->print_debug();
			echo "\n//-------------------------------\n\n";
		}
		echo "</pre>";
		
		// var_dump($elements);	
	}
	return $elements;
};

	echo "Привет, мир!";
	// http://php.net/manual/en/book.simplexml.php
	$xmlfile = "test.xml";	
   $xml = simplexml_load_file($xmlfile);
	//var_dump($xml);
	// $xml->getNamespace();
	echo "</center>";
	
	echo 'XML: <pre>'.htmlspecialchars(file_get_contents($xmlfile)).'</pre>Structure:';
	
	$elements = array();
	$elements = parse_xmlclass($elements, $xml);
	// parse_xmlclass($xml);
	echo "<center>";
	
?>

</body>
</html>
