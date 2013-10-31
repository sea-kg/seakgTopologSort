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

	$obj->reset();
	
	if($xml->children()->count() == 0)
	{
		$obj->setBody(true);
	}
	
	if($xml->attributes()->count() > 0)
	{
		foreach($xml->attributes() as $attrname => $attrvalue) {
			$obj->addAttributeName($attrname);
			// echo $ident."\t\tQString ".$attrname.";\n"; // ."; // default value = $attrvalue \n";
		}
	}

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
	}
	return $elements;
};

?>
