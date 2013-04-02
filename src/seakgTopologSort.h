/*
Copyright (c) 2013 mrseakg@gmail.com
*/

#ifndef _seakgtopologsort_h
#define _seakgtopologsort_h

#include <QString>
#include <QList>

namespace seakg {

	namespace topologSort
	{
		template<typename T> class link;
		template<typename T> class chain;
		template<typename T> QString toString(T &t);
		template<typename T> QString toString(seakg::topologSort::link<T> &lnk);
		template<typename T> QString toString(seakg::topologSort::chain<T> &chn);
		
		enum releshionshipLink {
			rlOneParent, // t1->t2, t1->t3
			rlOneChild, // t2->t4, t3->t4
			rlEqual, // t1->t2, t1->t2
			rlFather, // t1->t2 is father t2->t3
			rlChild, // t2->t3 is child t1->t2			
			rlUnknown // t1->t2, t3->t4
		}
		
		template<typename T>
		class link
		{
			public:
				link(T parent, T child) : m_tParent(parent), m_tChild(child) {
				};

				T getParent() {
					return m_tParent;
				};

				T getChild() {
					return m_tChild;
				};
			
						bool isNeighbor(seakg::topologSort::link<T> &l) {
					return isParentNeighbor(l) || isChildNeighbor(l);
				};
				
				seakg::topologSort::releshionshipLink getRelationshipLink(seakg::topologSort::link<T> &l) {
					if(l.getParent() == m_tParent && l.getChild() == m_tChild)
						return rlEqual;
					else if(l.getParent() == m_tParent)
						return rlOneParent;
					else if(l.getChild() == m_tChild)
						return rlOneChild;
					else if(l.getChild() == m_tParent)
						return rlFather;
					else if(l.getParent() == m_tChild)
						return rlChild;

					return rlUnknown;
				};

				bool operator == (seakg::topologSort::link<T> &l) {
					return  getRelationshipLink(l) == rlEqual;
				};
				
			private:
				T m_tParent, m_tChild;
		};
		
		template<typename T>
		class chain
		{
			public:
				chain() : m_bError(false), m_strMsgError(""), m_bIncludeOnce(false) {
				};
				
				bool canBeGlued(seakg::topologSort::link<T> &lnk) {
					return (getStart() == lnk.getChild()) || (getEnd() == lnk.getParent());
				};
				
				bool add(seakg::topologSort::link<T> &lnk) {
						if(!hasElements()) {
						m_chain.push_front(lnk.getParent());
						m_chain.push_back(lnk.getChild());
						return true;
					}
					
					bool bResult = false;
					if(hasError()) return false;
					
					if(getStart() == lnk.getChild())
					{
						checkToFront(lnk.getParent());
						if(hasError()) return false;
						
						m_chain.push_front(lnk.getParent());
						bResult = true;
					};
					
					check();
					if(hasError()) return false;
					
					if(getEnd() == lnk.getParent())
					{
						checkToEnd(lnk.getChild());
						if(hasError()) return false;
						
						m_chain.push_back(lnk.getChild());
						bResult = true;
					};
					
					check();
					if(hasError()) return false;
					
					return bResult;

				};
				
				QString toString() {
					QString strChain = "digraph chain_" + (int)this + " { ";
					for (int i = 0; i < m_chain.count(); i++) 
						if(i == 0) strChain += "'" + toString<T>(m_chain[i]) + "'"; else strChain += " -> '" + toString<T>(m_chain[i]) + "'";
					strChain += " }; ";
					return strChain;
				};
				
				T getStart() {
					if(hasElements()) 
						return m_chain[0];
					return T();
				};
				
				T getEnd() {
					if(hasElements()) 
						return m_chain[m_chain.count()-1];
					return T();
				};
				
				QString errorString() {
					return m_strMsgError;
				};
				
				void setIncludeOnce(bool b) {
					m_bIncludeOnce = b;
				};
				
				bool hasError() {
					return m_bError;
				};
				
				bool hasElements() {
					return m_chain.count() > 0;
				};
				
				bool contains(seakg::topologSort::link<T> &lnk) {
					return (m_chain.indexOf(strParent) >= 0 && m_chain.indexOf(strChild) >= 0 && m_chain.indexOf(strParent) < m_chain.indexOf(strChild)); 
				};
				
				bool canTryMerge(seakg::topologSort::chain<T> &chn) {
					for(int i = 0; i < chn.m_chain.count(); i++)
						if(m_chain.contains(chn.m_chain[i])) return true;
					return false;
				};
				
				void merge( seakg::topologSort::chain<T> &chn ) {
						// int lastInd1 = 0;
						int lastInd2 = 0;
						
						seakg::topologSort::chain<T> newChain;
						
						for(int i1 = 0; i1 < m_chain.count(); i1++)
						{						
							T t1 = m_chain[i1];
							int ind2 = chain.m_chain.indexOf(t1, lastInd2);
							/*std::cout << "\tind2=" << ind2 << "\n";
							std::cout << "\tlastInd2=" << lastInd2 << "\n";
							std::cout << "\tstr1=" << str1.toStdString() << "\n";
							std::cout << "\tchain.m_chain.count()=" << chain.m_chain.count() << "\n";*/
							
							
							if(lastInd2 < chain.m_chain.count() && m_chain[i1] == chain.m_chain[lastInd2])
							{
								lastInd2++;
								newChain.push_back(t1);
							}
							else if(newChain.contains(t1))
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
								newChain.push_back(t1);
						};
						
						if(lastInd2 < chain.m_chain.count())
							for(int i2 = lastInd2; i2 < chain.m_chain.count(); i2++)
								if(!newChain.contains(chain.m_chain[i2]))
									newChain.push_back(chain.m_chain[i2]);
						
						m_chain.clear();
						
						for(int i = 0; i < newChain.count(); i++)
							m_chain.push_back(newChain[i]);
							
						// std::cout << "end\n";
				};
			private:
				bool m_bIncludeOnce;
				bool m_bError;
				QString m_strMsgError;

				void check();
				void checkToFront(QString strSome);
				void checkToEnd(QString strSome);
				QList<T> m_chain;
				QList<seakg::topologSort::link<T> > m_links;
		};
		
		class sorter
		{
			public:
				sorter(bool bIgnoreError = false);
				void add(seakg::topologSort::link<T> l);

				QString errorString() {
					return m_strError;
				};

				bool hasError();
				void std_print();
				void start();
				void finish();
				bool contains(seakg::topologSort::link<T> &l);
			private:
				bool m_bError;
 				bool m_bFinish;
				bool refactoring();				
				QString m_strError;
				bool m_bIgnoreError;

				QList<seakg::topologSort::link<T> > m_Pairs;
				QList<seakg::topologSort::chain<T> > m_Chains;				
		};


	} // topologSort

} // namespace seakg

#endif // _seakgtopologsort_h
