#!/bin/bash


# xml_input=fromfile
# data_xml=filename == text.xml
# lang=cpp_qt_useqxml
# outputtype=zipfile
# outputfilename='autocode'

#	-o temp.txt \

echo ">>>>>>> start test <<<<<<<<";

FILENAME_XML=`pwd`"/object.xml"

echo "+++++++ fullpath to xmlfile: "
echo "    " $FILENAME_XML

if [ -f src/temp.zip ]; then
	rm src/temp.zip
fi

echo "";
echo "";
echo "";

echo "------- start download sources -------";

curl -X POST \
	-o src/temp.zip \
	-F xml_input=fromfile \
	-F data_xml=@$FILENAME_XML \
	-F output_filename=seakgObject \
	-F output_lang=cpp_qt_useqxml \
	-F output_type=zipfile \
	"http://localhost/xmlclass/index.php"

echo "------- end download sources -------";

echo "";
echo "";
echo "";

echo "+++++ unpack zipfile: "
cd src

if [ -f seakgObject.h ]; then 
	rm seakgObject.h 
fi

if [ -f seakgObject.cpp ]; then 
	rm seakgObject.cpp 
fi

unzip temp.zip
rm temp.zip
cd ..

echo "";
echo "";
echo "";

echo "------- start compile -------";

if [ -f php_xmlclass_test ]; then 
	rm php_xmlclass_test 
fi

qmake php_xmlclass_test.pro
make

echo "------- end compile -------";

echo "";
echo "";
echo "";
echo "------- start program -------";

if [ -f php_xmlclass_test ]; then 
  ./php_xmlclass_test object.xml	
fi

echo "------- end program -------";
echo ">>>>>>> end test <<<<<<<<";


#	 \
# 

# SOURCE_FOLDER="../proj/src/"

# cp temp/* $SOURCE_FOLDER




# curl -o src.zip -X POST -d "data_xml@test.xml" http://localhost/xmlclass/index.php


