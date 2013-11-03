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
	
	function get_header()
	{	
		$temp_h = "\n\t";

		$temp_h .= 'class '.$this->classname()." : public _specXMLElement {\n".
		"\t\tpublic:\n";
		
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
			$temp_h .= "\t\t\t// attributes \n";
			foreach($this->attr as $attrname => $attrval )
			{
				if($attrval > 1)
					$temp_h .= "\t\t\tQStringList ".$attrname."s; \n";
				else 
					$temp_h .= "\t\t\tQString ".$attrname.";\n";
			};
			$temp_h .= "\n\t\t\t// elements\n";
			
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
		
		$temp_h .= ($this->body ? "\t\t\tQString Body;\n\n" : "");

		foreach($this->elems as $elemname => $elemval )
		{
			if($elemval > 1)
				$temp_h .= "\t\t\tQList<_".$elemname." *> ".$elemname."s;\n";
			else
				$temp_h .= "\t\t\t_".$elemname." * ".$elemname."; \n";
			//echo 'Subelement name: '.$elemname.'; Subelement value='.$elemval.';<br>';
		};
		$temp_h .= "\t};\n";
		return $temp_h;
	}
	
	function get_source()
	{	
		$temp_cpp = "";
		
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
				$temp_cpp .= "(QString name, QString value) {\n\t";

				$temp_sch = 0;
				foreach($this->attr as $attrname => $attrval )
				{
					$temp_cpp .= "\t".($temp_sch > 0 ? "else " : "")."if(name == \"$attrname\")\n\t\t\t";
					if($attrval > 1)
						$temp_cpp .= $attrname."s << value;";
					else 
						$temp_cpp .= $attrname." = value;";
					$temp_cpp .= "\n\t";
					$temp_sch++;
				};
				$temp_cpp .= "\telse\n\t\t\treturn false;\n\t\treturn true;";
			}
			else
			{
					$temp_cpp .= "(QString /*name*/, QString /*value*/) {\n\treturn false;\n};\n\t";
			};
			
			$temp_cpp .= "\n\t}\n\n\t//-------------------------------\n\n\tbool $classname::addChildElement"; 
			if(count($this->elems) > 0)
			{
				$temp_cpp .= "(QString strName, _specXMLElement *pElem) {\n\t";
				$temp_sch = 0;
				foreach($this->elems as $elemname => $elemval )
				{
					$temp_cpp .= "\t".($temp_sch > 0 ? "else " : "")."if(strName == \"$elemname\") {\n\t\t\t";
					if($elemval > 1)
					{
						$temp_cpp .= $this->classname()." *p = dynamic_cast<_".$elemname." *>(pElem);\n\t\t\t";
						$temp_cpp .= "if(p == NULL) return false;\n\t\t\t";
						$temp_cpp .= $elemname."s << p;";
					}
					else
						$temp_cpp .= $elemname." = dynamic_cast<_".$elemname." *>(pElem);";
					$temp_cpp .= "\n\t\t}\n\t";
					$temp_sch++;
				};
				$temp_cpp .= "\telse\n\t\t\treturn false;\n\t\treturn true;";
				$temp_cpp .= "\n\t};\n";
			}
			else
			{
				$temp_cpp .= "(QString /*strName*/, _specXMLElement */*pElem*/) {\n\t\treturn false;\n\t};";
			}
		}
		
		
		
		$temp_cpp .= "";
		
		return $temp_cpp;
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

function copyright()
{
	return 
"
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * autoxmlclass © 2013 sea-kg (mrseakg@gmail.com)          *
 * source code of autoxmlclass:                            *
 *        https://github.com/sea-kg/autoxmlclass/          *
 *                                                         * 
 * Attention:                                              *
 *  It's code was generated on server:                     *
 *        http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]."
 *  and please NOT CHANGING this code!!!                   *
 *                                                         *  
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
";
}


function get_header($elements, $output_filename)
{
	$temp_h = copyright()."

#ifndef _".$output_filename."_h
#define _".$output_filename."_h

#include <QFile>
#include <QStack>
#include <QList>
#include <QString>
#include <QStringList>
#include <QXmlStreamReader>
#include <QTextStream>

namespace ".$output_filename." {

";
	
	// echo htmlspecialchars($includes); 

	$arr = array_reverse($elements);

	$classnameroot = "";
	$nameroot = "";

	foreach($arr as $elem_name => $elem) {
		$elem->reset();
		$temp_h .= "	class ".$elem->classname().";\n";
		$classnameroot = $elem->classname();
		$nameroot = $elem->name();
	}

$temp_h .= "
	class _specXMLElement {
		public:
			virtual QString nameOfElement() { return \"\"; };
			virtual bool hasBody() { return false; };
			virtual bool setBody(QString /*body*/) { return false; };
			virtual bool setAttribute(QString /*name*/, QString /*value*/) { return false; };
			virtual bool addChildElement(QString /*name*/, _specXMLElement * /*pElem*/) { return false; };
	};
	
	//-------------------------------
";
	
	foreach($arr as $elem_name => $elem) {
		$temp_h .= $elem->get_header();
		$temp_h .= "\n\t//-------------------------------\n\t";
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
 $temp_h .= " 
	_specXMLElement * createElement(QString strName);
";
	
/*$temp_h .= " 
	_specXMLElement * createElement(QString strName) {
		_specXMLElement *elem = NULL;	
";

	foreach($arr as $elem_name => $elem) {
			$cn = $elem->classname();
			$n = $elem->name();
			
			$temp_h .= "
		if(strName == \"$n\") elem = new $cn();";
	}
$temp_h .= "
		return elem;
	};";
*/
	$temp_h .= "
	".$classnameroot." * readFromXML(QString fileXml);
	
} // namespace ".$output_filename."

#endif // _".$output_filename."_h\r\n";

	return $temp_h;
}

function get_source($elements, $output_filename)
{
	$temp_cpp = copyright()."
#include \"".$output_filename.".h\"

namespace ".$output_filename." {
	";
	
	$arr = array_reverse($elements);

	$classnameroot = "";
	$nameroot = "";

	foreach($arr as $elem_name => $elem) {
		$elem->reset();
		$classnameroot = $elem->classname();
		$nameroot = $elem->name();
	}
	
	foreach($arr as $elem_name => $elem) {
		$temp_cpp .= $elem->get_source();
		$temp_cpp .= "
		
	//-------------------------------
";
	}
	
$temp_cpp .= " 
	_specXMLElement * createElement(QString strName) {
		_specXMLElement *elem = NULL;	
";

	foreach($arr as $elem_name => $elem) {
			$cn = $elem->classname();
			$n = $elem->name();
			
			$temp_cpp .= "
		if(strName == \"$n\") elem = new $cn();";
	}
$temp_cpp .= "
		return elem;
	};
	
	//-------------------------------
";	
	
	$temp_cpp .= "
	
	".$classnameroot." * readFromXML(QString fileXml) {
		".$classnameroot." *root = NULL;

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

				if(stackElements.count() == 0)
					root = (".$classnameroot." *)elem;
								
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
$temp_cpp .= "
		};
	
		if(xmlReader.hasError())
		{
			return NULL;
			// std::cout << xmlReader.errorString().toStdString();
		}
	
		return root;
	};

} // namespace ".$output_filename."
";
	
	return $temp_cpp;
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
