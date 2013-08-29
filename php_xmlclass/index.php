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
		$temp_h;

		$temp_h .= 'class '.$this->classname()." : public _specXMLElement {\n\n".
		"\tpublic:\n\n";
		
		$temp_h .= "
		// _specXMLElement
		virtual QString nameOfElement();
		virtual bool hasBody();
		virtual bool setBody(QString body);
		virtual bool setAttribute(QString name, QString value);
		virtual bool addChildElement(QString name, _specXMLElement *);
\n";
		
		if(count($this->attr) > 0)
		{
			$temp_h .= "\t\t// attributes \n";
			foreach($this->attr as $attrname => $attrval )
			{
				if($attrval > 1)
					$temp_h .= "\t\tQStringList ".$attrname."s; \n";
				else 
					$temp_h .= "\t\tQString ".$attrname.";\n";
			};
			$temp_h .= "\n\t\t// elements\n";
			
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
		
		$temp_h .= ($this->body ? "\t\tQString Body;\n\n" : "");

		foreach($this->elems as $elemname => $elemval )
		{
			if($elemval > 1)
				$temp_h .= "\t\tQList<_".$elemname." *> ".$elemname."s;\n";
			else
				$temp_h .= "\t\t_".$elemname." * ".$elemname."; \n";
			//echo 'Subelement name: '.$elemname.'; Subelement value='.$elemval.';<br>';
		};
		$temp_h .= "};\n";
		
		echo "<pre>".htmlspecialchars($temp_h)."</pre>";
		
		$temp_cpp;
		
		// name of element
		// has body
		
		$classname = $this->classname();
		$name = $this->classname();
		{
			$temp_cpp .= "

//-------------------------------

QString $classname::nameOfElement() {
	return \"".$this->name."\";
};

//-------------------------------

bool $classname::hasBody() {
	return ".($this->body ? "true" : "false").";
};

//-------------------------------

bool $classname::setBody(QString ".($this->body ? "body" : "/*body*/" ).") {
	".($this->body ? "Body = body;
	return true;" : " return false;")."
};

//-------------------------------

bool $classname::setAttribute";
			
			if(count($this->attr) > 0)
			{
				$temp_cpp .= "(QString name, QString value) {\n";

				$temp_sch = 0;
				foreach($this->attr as $attrname => $attrval )
				{
					$temp_cpp .= "\t".($temp_sch > 0 ? "else " : "")."if(name == \"$attrname\")\n\t\t";
					if($attrval > 1)
						$temp_cpp .= $attrname."s << value;";
					else 
						$temp_cpp .= $attrname." = value;";
					$temp_cpp .= "\n";
					$temp_sch++;
				};
				$temp_cpp .= "\telse\n\t\treturn false;\n\treturn true;";
			}
			else
			{
					$temp_cpp .= "(QString /*name*/, QString /*value*/) {\n\treturn false;\n};\n";
			};
			
			$temp_cpp .= "\n}\n\n//-------------------------------\n\nbool $classname::addChildElement"; 
			if(count($this->elems) > 0)
			{
				$temp_cpp .= "(QString strName, _specXMLElement *pElem) {\n";
				$temp_sch = 0;
				foreach($this->elems as $elemname => $elemval )
				{
					$temp_cpp .= "\t".($temp_sch > 0 ? "else " : "")."if(strName == \"$elemname\") {\n\t\t";
					if($elemval > 1)
					{
						$temp_cpp .= $this->classname()." *p = dynamic_cast<_".$elemname." *>(pElem);\n\t\t";
						$temp_cpp .= "if(p == NULL) return false;\n\t\t";
						$temp_cpp .= $elemname."s << p;";
					}
					else
						$temp_cpp .= $elemname." = dynamic_cast<_".$elemname." *>(pElem);";
					$temp_cpp .= "\n\t}\n";
					$temp_sch++;
				};
				$temp_cpp .= "\telse\n\t\treturn false;\n\treturn true;";
				$temp_cpp .= "\n};\n";
			}
			else
			{
				$temp_cpp .= "(QString /*strName*/, _specXMLElement */*pElem*/) {\n\treturn false;\n};";
			}
		}
		
		
		
		$temp_cpp .= "";
		
		echo "<pre>".htmlspecialchars($temp_cpp)."</pre>";
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

// ------------------------------------------ 

// $elements = array();

function parse_xmlclass($elements, $xml, $root = true, $ident = "")
{
	/*if($root) echo "<pre>\n";
	echo $ident."struct ";
	if(!$root) echo "_";
	echo $xml->getName()." { \n";
	*/
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


function print_source($elements)
{
	echo "<hr/><pre>";

	$includes = 
"#include <QFile>
#include <QStack>
#include <QList>
#include <QString>
#include <QStringList>
#include <QXmlStreamReader>
#include <QTextStream>


";
	echo htmlspecialchars($includes); 

	$arr = array_reverse($elements);

	$classnameroot = "";
	$nameroot = "";

	foreach($arr as $elem_name => $elem) {
		$elem->reset();
		echo "class ".$elem->classname().";\n";
		$classnameroot = $elem->classname();
		$nameroot = $elem->name();
	}

echo "
class _specXMLElement {
	public:
		virtual QString nameOfElement() { return \"\"; };
		virtual bool hasBody() { return false; };
		virtual bool setBody(QString /*body*/) { return false; };
		virtual bool setAttribute(QString /*name*/, QString /*value*/) { return false; };
		virtual bool addChildElement(QString /*name*/, _specXMLElement * /*pElem*/) { return false; };
};</pre>
";
	
	foreach($arr as $elem_name => $elem) {
		$elem->print_debug();
		echo "\n//-------------------------------\n";
	}
	
/*	
	foreach($arr as $elem_name => $elem) {
		$cn = $elem->classname();
		$n = $elem->name();
		echo 
$cn." readElement".$n."(QXmlStreamReader &xmlReader) {
	".$cn." elem;
	
	return elem; 
};\n";
	}
*/

 // echo function for create element
echo " <pre>
_specXMLElement * createElement(QString strName) {
";

	foreach($arr as $elem_name => $elem) {
			$cn = $elem->classname();
			$n = $elem->name();
			
			echo "
	if(strName == \"$n\") return new $cn();";
	}
echo "
	return NULL;
};";
		
	echo "
	
bool readFromXML(QString fileXml, ".$classnameroot." &root)
{
	// init xml stream
	QFile file(fileXml);
	QXmlStreamReader xmlReader;
	
	//QString line;
	if ( !file.open(QIODevice::ReadOnly) )
		return false;
	
	{
		QTextStream t( &file );
		// stream.setCodec(\"CP-866\");
		xmlReader.addData(t.readAll());
	}	
	
	// start reading
	QStack<_specXMLElement *> stackElements;
	while(!xmlReader.atEnd()) 
	{
		if(xmlReader.isCharacters() && stackElements.count() != 0)
		{
			_specXMLElement *pElemTop = stackElements.top();
			if(pElemTop->hasBody())
			  pElemTop->setBody(xmlReader.readElementText());
		}
		
		if(xmlReader.isStartElement())
		{ 
			QString strName = xmlReader.name().toString();
			_specXMLElement *elem = createElement(strName);
			
			_specXMLElement *parentElem = (stackElements.count() != 0) ? stackElements.top() : NULL;
			
			if(parentElem != NULL)
				parentElem->addChildElement(strName,elem);

			stackElements.push(elem);
			
			for(int i = 0;  i < xmlReader.attributes().count(); i++)
			{
				QXmlStreamAttribute attr = xmlReader.attributes().at(i);
				elem->setAttribute(attr.name().toString(), attr.value().toString());
			}
		}
		
		if(xmlReader.isEndElement())
		{
			stackElements.pop();
		}
		xmlReader.readNext();		
";
	/*	
	echo "/*";
	foreach($arr as $elem_name => $elem) {
			$cn = $elem->classname();
			$n = $elem->name();
			
			echo "
			if(strName == \"$n\") 
			{
				$cn elem = readElement".$n."(xmlReader);
			};";
		}
	echo "* /";
	*/
echo
"\n\t};
	
	if(xmlReader.hasError())
	{
		return false;
		// std::cout << xmlReader.errorString().toStdString();
	}
	
	return true;
}; </pre>";
	
	echo "</pre>";
}



	echo "Привет, мир!";
	// http://php.net/manual/en/book.simplexml.php
	$xmlfile = "test.xml";	
   $xml = simplexml_load_file($xmlfile);
	//var_dump($xml);
	// $xml->getNamespace();
	echo "</center>";
	
	echo 'XML: <pre>'.htmlspecialchars(file_get_contents($xmlfile)).'</pre>Source code:';
	
	$elements = array();
	$elements = parse_xmlclass($elements, $xml);
	// parse_xmlclass($xml);
	print_source($elements);
	echo "<center>";
	
	
	
?>

</body>
</html>
