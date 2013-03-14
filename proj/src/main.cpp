
#include <QObject>
#include <QMap>
#include <QDateTime>
#include <iostream>

#include "seakgTopologSort.h"

// ---------------------------------------------------------------------------
int main(int, char ** )
{
	qsrand (QDateTime::currentMSecsSinceEpoch());

	int max = 20;
	seakgTopologSort topsort;
	
	std::cout << "digraph graphname {\n";
	for(int i = 0; i < 15; i++)
	{
		int t1 = qrand() % max;
		int t2 = qrand() % max;
		std::cout << "\t" << t1 << " -> " << t2 << ";\n";
		topsort.push(QString::number(t1), QString::number(t2));
	}
	std::cout << "}\n";
	
	/*
		topsort.push("a", "b");
		topsort.push("a", "d");
		topsort.push("a", "c");
		topsort.push("a", "e");
		topsort.push("b", "d");
		topsort.push("c", "d");
		topsort.push("d", "e");
	*/
	
	/*
		topsort.push("3", "6");
		topsort.push("6", "12");
		topsort.push("12", "16");
		topsort.push("0", "6");
	*/
	
	topsort.finish();
	
	if( topsort.hasError() )
	{
		topsort.std_print();
		std::cout << topsort.errorString().toStdString() << "\n";
		return -2;
	}
	
	topsort.std_print();
	
	std::cout << "\r\n";
	return 0;
};