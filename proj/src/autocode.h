#include <QFile>
#include <QStack>
#include <QList>
#include <QString>
#include <QStringList>
#include <QXmlStreamReader>
#include <QTextStream>


class _SQLSelect;
class _Field;
class _Object;

class _specXMLElement {
	public:
		virtual QString nameOfElement() { return ""; };
		virtual bool hasBody() { return false; };
		virtual bool setBody(QString /*body*/) { return false; };
		virtual bool setAttribute(QString /*name*/, QString /*value*/) { return false; };
		virtual bool addChildElement(QString /*name*/, _specXMLElement * /*pElem*/) { return false; };
};

class _SQLSelect : public _specXMLElement {

	public:


		// _specXMLElement
		virtual QString nameOfElement();
		virtual bool hasBody();
		virtual bool setBody(QString body);
		virtual bool setAttribute(QString name, QString value);
		virtual bool addChildElement(QString name, _specXMLElement *);

		// attributes 
		QString table;

		// elements
		QString Body;

};


//-------------------------------

QString _SQLSelect::nameOfElement() {
	return "SQLSelect";
};

//-------------------------------

bool _SQLSelect::hasBody() {
	return true;
};

//-------------------------------

bool _SQLSelect::setBody(QString body) {
	Body = body;
	return true;
};

//-------------------------------

bool _SQLSelect::setAttribute(QString name, QString value) {
	if(name == "table")
		table = value;
	else
		return false;
	return true;
}

//-------------------------------

bool _SQLSelect::addChildElement(QString /*strName*/, _specXMLElement */*pElem*/) {
	return false;
};

//-------------------------------

class _Field : public _specXMLElement {

	public:


		// _specXMLElement
		virtual QString nameOfElement();
		virtual bool hasBody();
		virtual bool setBody(QString body);
		virtual bool setAttribute(QString name, QString value);
		virtual bool addChildElement(QString name, _specXMLElement *);

		// attributes 
		QString name;
		QString value;
		QString id;
		QString Attr1;
		QString Attr2;
		QString Attr5;

		// elements
		QString Body;

		QList<_Field *> Fields;
};


//-------------------------------

QString _Field::nameOfElement() {
	return "Field";
};

//-------------------------------

bool _Field::hasBody() {
	return true;
};

//-------------------------------

bool _Field::setBody(QString body) {
	Body = body;
	return true;
};

//-------------------------------

bool _Field::setAttribute(QString name, QString value) {
	if(name == "name")
		name = value;
	else if(name == "value")
		value = value;
	else if(name == "id")
		id = value;
	else if(name == "Attr1")
		Attr1 = value;
	else if(name == "Attr2")
		Attr2 = value;
	else if(name == "Attr5")
		Attr5 = value;
	else
		return false;
	return true;
}

//-------------------------------

bool _Field::addChildElement(QString strName, _specXMLElement *pElem) {
	if(strName == "Field") {
		_Field *p = dynamic_cast<_Field *>(pElem);
		if(p == NULL) return false;
		Fields << p;
	}
	else
		return false;
	return true;
};

//-------------------------------

class _Object : public _specXMLElement {

	public:


		// _specXMLElement
		virtual QString nameOfElement();
		virtual bool hasBody();
		virtual bool setBody(QString body);
		virtual bool setAttribute(QString name, QString value);
		virtual bool addChildElement(QString name, _specXMLElement *);

		// attributes 
		QString id;

		// elements
		_Field * Field; 
		_SQLSelect * SQLSelect; 
};


//-------------------------------

QString _Object::nameOfElement() {
	return "Object";
};

//-------------------------------

bool _Object::hasBody() {
	return false;
};

//-------------------------------

bool _Object::setBody(QString /*body*/) {
	 return false;
};

//-------------------------------

bool _Object::setAttribute(QString name, QString value) {
	if(name == "id")
		id = value;
	else
		return false;
	return true;
}

//-------------------------------

bool _Object::addChildElement(QString strName, _specXMLElement *pElem) {
	if(strName == "Field") {
		Field = dynamic_cast<_Field *>(pElem);
	}
	else if(strName == "SQLSelect") {
		SQLSelect = dynamic_cast<_SQLSelect *>(pElem);
	}
	else
		return false;
	return true;
};

//-------------------------------

_specXMLElement * createElement(QString strName) {

	if(strName == "SQLSelect") return new _SQLSelect();
	if(strName == "Field") return new _Field();
	if(strName == "Object") return new _Object();
	return NULL;
};
	
bool readFromXML(QString fileXml, _Object &root)
{
	// init xml stream
	QFile file(fileXml);
	QXmlStreamReader xmlReader;
	
	//QString line;
	if ( !file.open(QIODevice::ReadOnly) )
		return false;
	
	{
		QTextStream t( &file );
		// stream.setCodec("CP-866");
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

	};
	
	if(xmlReader.hasError())
	{
		return false;
		// std::cout << xmlReader.errorString().toStdString();
	}
	
	return true;
}; 
