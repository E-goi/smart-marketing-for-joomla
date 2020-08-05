#!/bin/bash

VERSION=$1

#remove temporary old files
rm -Rf tmp

#echo $VERSION

#create new tmp dir
mkdir tmp

#start deploy
rm -f ./packages/*.zip
rm -f ./Files/smart-marketing-for-joomla-3.9-$VERSION.zip
cp -R ./language ./tmp/languages
cp  ./LICENSE ./tmp/
cp  ./pkg_egoi.xml ./tmp/
mkdir ./tmp/packages

#start zipping packages
cd ./packages/com_user_egoi_plugin/
zip -r com_user_egoi_plugin_$VERSION.zip *
cd ..
cd ..
mv ./packages/com_user_egoi_plugin/com_user_egoi_plugin_$VERSION.zip ./tmp/packages/

cd ./packages/com_content_egoi_plugin/
zip -r com_content_egoi_plugin_$VERSION.zip *
cd ..
cd ..
mv ./packages/com_content_egoi_plugin/com_content_egoi_plugin_$VERSION.zip ./tmp/packages/

cd ./packages/com_egoi_component/
zip -r com_egoi_component_$VERSION.zip *
cd ..
cd ..
mv ./packages/com_egoi_component/com_egoi_component_$VERSION.zip ./tmp/packages/

cd ./packages/com_egoi_file/
zip -r com_egoi_file_$VERSION.zip *
cd ..
cd ..
mv ./packages/com_egoi_file/com_egoi_file_$VERSION.zip ./tmp/packages/

cd ./packages/com_egoi_module/
zip -r com_egoi_module_$VERSION.zip *
cd ..
cd ..
mv ./packages/com_egoi_module/com_egoi_module_$VERSION.zip ./tmp/packages/

cd ./tmp

sed -i -e "s|\com_user_egoi_plugin.zip|com_user_egoi_plugin_${VERSION}.zip|g" "pkg_egoi.xml"
sed -i -e "s|\com_egoi_component.zip|com_egoi_component_${VERSION}.zip|g" "pkg_egoi.xml"
sed -i -e "s|\com_egoi_file.zip|com_egoi_file_${VERSION}.zip|g" "pkg_egoi.xml"
sed -i -e "s|\com_egoi_module.zip|com_egoi_module_${VERSION}.zip|g" "pkg_egoi.xml"
sed -i -e "s|\com_content_egoi_plugin.zip|com_content_egoi_plugin_${VERSION}.zip|g" "pkg_egoi.xml"

#final zip
zip -r smart-marketing-for-joomla-3.9-$VERSION.zip languages packages LICENSE pkg_egoi.xml
cd ..
mv ./tmp/smart-marketing-for-joomla-3.9-$VERSION.zip ./Files/

#remove temporary old files again
rm -Rf tmp