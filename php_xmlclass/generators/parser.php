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
			
		// echo "<h1>$name ".$this->elems[$name]."</h1>";
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

function parse_xmlclass($elements, $xml, $root = true, $ident = "")
{
	/*if($root) echo "<pre>\n";
	echo $ident."struct ";
	if(!$root) echo "_";
	echo $xml->getName()." { \n";
	*/
	$obj = "";
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
		// echo $ident."\tQString Body;\n";
	}
	
	// echo $ident."\tstruct _XMLAttr_".$xml->getName()." {\n";
	if($xml->attributes()->count() > 0)
	{
		foreach($xml->attributes() as $attrname => $attrvalue) {
			$obj->addAttributeName($attrname);
			// echo $ident."\t\tQString ".$attrname.";\n"; // ."; // default value = $attrvalue \n";
		}
	}
	// echo $ident."\t} Attributes;\n";

	foreach($xml->children() as $child)	
		$obj->addSubElement($child->getName());	
	
	foreach($xml->children() as $child)
	{
		// $obj->addSubElement($child->getName());
		$elements = parse_xmlclass($elements, $child, false, $ident."\t");
		// $elements = parse_xmlclass($child, false, $ident."\t");
	}
	// echo $ident."} ";
	
	// $obj->reset();
	// $elements[$obj->name()]->reset();
	$elements[$obj->name()]->merge($obj);
	
	// if(!$root) echo $xml->getName().";\n"; else echo ";\n</pre>";

	if($root)
	{
	   //var_dump($elements);		
	   // var_dump($elements);	
	}
	return $elements;
};

function generateFiles($data_xml, $output_filename)
{
	$files = Array();

	// http://php.net/manual/en/book.simplexml.php
	// $xmlfile = "test.xml";	
	// $xml = simplexml_load_file($xmlfile);
	//var_dump($xml);
	// $xml->getNamespace();
	// echo 'XML: <pre>'.htmlspecialchars(file_get_contents($xmlfile)).'</pre>Source code:';
	$xml = simplexml_load_string($data_xml);
	
	$elements = array();
	$elements = parse_xmlclass($elements, $xml);

	$files[$output_filename.'.h'] = get_header($elements, $output_filename);
	$files[$output_filename.'.cpp'] = get_source($elements, $output_filename);
	
	// parse_xmlclass($xml);
	// print_source($elements);

	return $files;
};
?>
