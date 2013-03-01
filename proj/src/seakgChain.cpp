#include "seakgChain.h"

#include <iostream>
seakgChain::seakgChain() : m_bError(false), m_strMsgError(""), m_bIncludeOnce(false) {
};

// ---------------------------------------------------------------------------

bool seakgChain::hasElements() {
	return m_chain.count() > 0;
}

bool seakgChain::isBelongs(QString strParent, QString strChild) {
	return (getStart() == strChild) || (getEnd() == strParent);
};

// ---------------------------------------------------------------------------

QString seakgChain::getStart() {
	if(hasElements()) return m_chain[0];
	return QString();
};

// ---------------------------------------------------------------------------

QString seakgChain::getEnd() {
	if(hasElements()) return m_chain[m_chain.count()-1];
	return QString();
};

// ---------------------------------------------------------------------------

void seakgChain::setIncludeOnce(bool b)
{
	m_bIncludeOnce = b;
};

// ---------------------------------------------------------------------------

QString seakgChain::errorString() {
	return m_strMsgError;
};

// ---------------------------------------------------------------------------

bool seakgChain::hasError() {
	return m_bError;
}

// ---------------------------------------------------------------------------

void seakgChain::check() {
	if(getStart() != getEnd()) return;
	m_bError = true;
	m_strMsgError = "fixated sequence: " + toString();
}

// ---------------------------------------------------------------------------

void seakgChain::checkToEnd(QString strSome) {
	if(!m_chain.contains(strSome)) return;
	m_bError = true;
	m_strMsgError = "fixated sequence: " + toString() + " -> " + strSome;
}

// ---------------------------------------------------------------------------

void seakgChain::checkToFront(QString strSome) {
	if(!m_chain.contains(strSome)) return;
	m_bError = true;
	m_strMsgError = "fixated sequence: " + strSome + " -> " + toString();
}

// ---------------------------------------------------------------------------

bool seakgChain::add(QString strParent, QString strChild) {
	if(!hasElements()) {
		m_chain.push_front(strParent);
		m_chain.push_back(strChild);
		return true;
	}
	
	bool bResult = false;
	if(hasError()) return false;
	
	if(getStart() == strChild)
	{
		checkToFront(strParent);
		if(hasError()) return false;
		
		m_chain.push_front(strParent);
		bResult = true;
	};
	
	check();
	if(hasError()) return false;
	
	if(getEnd() == strParent)
	{
		checkToEnd(strChild);
		if(hasError()) return false;
		
		m_chain.push_back(strChild);
		bResult = true;
	};
	
	check();
	if(hasError()) return false;
	
	return bResult;
};

// ---------------------------------------------------------------------------

QString seakgChain::toString() {
	QString strChain;
	for (int i = 0; i < m_chain.count(); i++) 
		if(i == 0) strChain += m_chain[i]; else strChain += " -> " + m_chain[i];
	return strChain;
}

// ---------------------------------------------------------------------------

bool seakgChain::contains(QString strParent, QString strChild) {
	
	return (m_chain.indexOf(strParent) >= 0 && m_chain.indexOf(strChild) >= 0 && m_chain.indexOf(strParent) < m_chain.indexOf(strChild));
}

// ---------------------------------------------------------------------------

bool seakgChain::canMerge( seakgChain &chain ) {
	
	for(int i = 0; i < chain.m_chain.count(); i++)
		if(m_chain.contains(chain.m_chain[i])) return true;
	return false;
}

// ---------------------------------------------------------------------------

void seakgChain::merge( seakgChain &chain ) {
	// int lastInd1 = 0;
	int lastInd2 = 0;
	
	// std::cout << "start\n";
	
	QList<QString> newChain;
	
	for(int i1 = 0; i1 < m_chain.count(); i1++)
	{
		// std::cout << "\ti1=" << i1 << "\n";
		
		QString str1 = m_chain[i1];
		int ind2 = chain.m_chain.indexOf(str1, lastInd2);
		/*std::cout << "\tind2=" << ind2 << "\n";
		std::cout << "\tlastInd2=" << lastInd2 << "\n";
		std::cout << "\tstr1=" << str1.toStdString() << "\n";
		std::cout << "\tchain.m_chain.count()=" << chain.m_chain.count() << "\n";*/
		
		
		if(lastInd2 < chain.m_chain.count() && m_chain[i1] == chain.m_chain[lastInd2])
		{
			lastInd2++;
			newChain.push_back(str1);
		}
		else if(newChain.contains(str1))
		{
			
		}
		else if(ind2 >= 0 && ind2 < chain.m_chain.count() && lastInd2 < chain.m_chain.count())
		{
			for(int i2 = lastInd2; i2 <= ind2; i2++)
			{
				if(!newChain.contains(chain.m_chain[i2]))
					newChain.push_back(chain.m_chain[i2]);
			}
				
			lastInd2 = ind2;
		}
		else	
			newChain.push_back(str1);
	};
	
	if(lastInd2 < chain.m_chain.count())
		for(int i2 = lastInd2; i2 < chain.m_chain.count(); i2++)
			if(!newChain.contains(chain.m_chain[i2]))
				newChain.push_back(chain.m_chain[i2]);
	
	m_chain.clear();
	
	for(int i = 0; i < newChain.count(); i++)
		m_chain.push_back(newChain[i]);
		
	// std::cout << "end\n";
}

// ---------------------------------------------------------------------------
