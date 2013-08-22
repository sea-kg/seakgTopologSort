class _SQLSelect;
class _Field;
class _Object;

class _SQLSelect {

	public:

		// attributes 
		QString table;

		// elements
		QString Body;

};


//-------------------------------

class _Field {

	public:

		// attributes 
		QString name;
		QString value;
		QString id;
		QString Attr1;
		QString Attr2;
		QString Attr5;

		// elements
		QString Body;

		QList<_Field> Fields;
};


//-------------------------------

class _Object {

	public:

		// attributes 
		QString id;

		// elements
		_Field Field; 
		_SQLSelect SQLSelect; 
};


//-------------------------------

