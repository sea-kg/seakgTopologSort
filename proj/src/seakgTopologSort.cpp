#include "seakgTopologSort.h"
#include <iostream>

seakgTopologSort::seakgTopologSort(bool bIgnoreError)
:
	m_bError(false)
	,m_strError("")
	,m_bIgnoreError(bIgnoreError)
{

};

// ---------------------------------------------------------------------------

void seakgTopologSort::push(QString strParent, QString strChild) {
	{
		mPair pair;
		pair.strParent = strParent;
		pair.strChild = strChild;
		m_Pairs.push_back(pair);
	}

	if(hasError()) return;

	int nCount = 0;
	bool bContains = false;
	for(int i = 0; i < m_Chains.count(); i++)
	{
		if(m_Chains[i].add(strParent, strChild))
			nCount++;

		if(m_Chains[i].contains(strParent, strChild)) bContains = true;
			
		if(m_Chains[i].hasError())
		{
			m_bError = true;
			m_strError += m_Chains[i].errorString() + "\n";
			return;
		};
	};
	
	if(nCount == 0 && !bContains)
	{
		seakgChain chain;
		chain.add(strParent, strChild);
		m_Chains.push_back(chain);
	};
	
	if(hasError()) return;
	
	/*std::cout << "before refactoring: \n";
	std_print();
	refactoring();
	std::cout << "after refactoring: \n";
	std_print();*/
};

// ---------------------------------------------------------------------------

QString seakgTopologSort::errorString() {
	return m_strError;
};

// ---------------------------------------------------------------------------

bool seakgTopologSort::hasError() {
	return m_bError;
};

// ---------------------------------------------------------------------------

void seakgTopologSort::finish() {
	
	if(!hasError())
		while(refactoring() && !hasError()) {};
	
	// check
	
	for(int i = 0; i < m_Pairs.count(); i++)
	{
		if(! contains(m_Pairs[i].strParent, m_Pairs[i].strChild))
		{
			m_bError = true;
			QString str = "error: it is not contains pair " + m_Pairs[i].toString() + "\n";
			m_strError += str;
		}
	}	
};

// ---------------------------------------------------------------------------

bool seakgTopologSort::refactoring() {
	
	QList<int> needRemoved;
	QList<seakgChain> newList;
	
	// std::cout << ".";
	for(int x = 0; x < m_Chains.count(); x++)
	{
		// std::cout << ".\n";
		for(int y = x + 1; y < m_Chains.count(); y++)
		{
			
			if( !needRemoved.contains(x) && m_Chains[x].canMerge(m_Chains[y]))
			{
				
				m_Chains[x].merge(m_Chains[y]);
				needRemoved.push_back(y);
				
			};
		};
	};
	
	// std::cout << ".\n";
	
	for(int i = 0; i < m_Chains.count(); i++)
		if( !needRemoved.contains(i) ) newList.push_back(m_Chains[i]);
	
	m_Chains.clear();
	
	for(int i = 0; i < newList.count(); i++)
		m_Chains.push_back(newList[i]);
	
	return needRemoved.count() > 0;
};

// ---------------------------------------------------------------------------

void seakgTopologSort::std_print() {
	for(int i = 0; i < m_Chains.count(); i++)
		std::cout << i << ") " << m_Chains[i].toString().toStdString() + "\n";
}

// ---------------------------------------------------------------------------

bool seakgTopologSort::contains(QString strParent, QString strChild)
{
	for(int i = 0; i < m_Chains.count(); i++)
		if(m_Chains[i].contains(strParent,strChild)) return true;
	return false;
};
