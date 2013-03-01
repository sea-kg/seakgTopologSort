#ifndef __SEAKGCHAIN_H__
#define __SEAKGCHAIN_H__

#include <QString>
#include <QList>

class seakgChain
{
	public:
		seakgChain();
		bool isBelongs(QString strParent, QString strChild);
		bool add(QString strParent, QString strChild);
		QString toString();
		QString getStart();
		QString getEnd();
		QString errorString();
		void setIncludeOnce(bool);
		bool hasError();
		bool hasElements();
		bool contains(QString strParent, QString strChild);
		bool canMerge(seakgChain &);
		void merge( seakgChain &chain );
	private:
		bool m_bIncludeOnce;
		bool m_bError;
		QString m_strMsgError;

		void check();
		void checkToFront(QString strSome);
		void checkToEnd(QString strSome);
		QList<QString> m_chain;
};

#endif // __SEAKGCHAIN_H__
