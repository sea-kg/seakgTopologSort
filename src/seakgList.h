/*
Copyright (c) 2013 mrseakg@gmail.com
*/

#ifndef _seakgtopologsort_h
#define _seakgtopologsort_h

namespace seakg {


//	QT:
	
	class string
	{
		public:
			string(char *ch) : m_string(ch) {};
			int length();
			std::string toStdString();
		private:
			QString m_string;
		
	}

// C++ BUILDER

// VISUAL STUDIO




} // namespace seakg

#endif // _seakgtopologsort_h
