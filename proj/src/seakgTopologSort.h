#ifndef __SEAKGTOPOLOGSORT_H__
#define __SEAKGTOPOLOGSORT_H__

#include "seakgChain.h"

class seakgTopologSort
{
	public:
		seakgTopologSort();
		void push(QString strParent, QString strChild);
		QString errorString();
		bool hasError();
		void std_print();
		void finish();
		bool contains(QString strParent, QString strChild);
	private:
		bool m_bError;
		bool refactoring();
		
		QList<seakgChain> m_Chains;
		QString m_strError;
		struct mPair
		{
			QString strParent;
			QString strChild;
			QString toString()
			{
				return strParent + " -> " + strChild;
			};
		};
		
		QList<mPair> m_Pairs;
};

#endif // __SEAKGTOPOLOGSORT_H__
