
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * autoxmlclass © 2013 sea-kg (mrseakg@gmail.com)          *
 * source code of autoxmlclass:                            *
 *        https://github.com/sea-kg/autoxmlclass/          *
 *                                                         * 
 * Attention:                                              *
 *  It's code was generated on server:                     *
 *        http://localhost/xmlclass/index.php
 *  and please NOT CHANGING this code!!!                   *
 *                                                         *  
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


#ifndef _seakgObject_h
#define _seakgObject_h

#include <QFile>
#include <QStack>
#include <QList>
#include <QString>
#include <QStringList>
#include <QXmlStreamReader>
#include <QTextStream>

namespace seakgObject {

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
	
	//-------------------------------

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
	 
	_specXMLElement * createElement(QString strName);

	_Object * readFromXML(QString fileXml);
	
} // namespace seakgObject

#endif // _seakgObject_h
