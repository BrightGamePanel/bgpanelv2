#!/bin/sh
# This file checks the current panel version.
cat ../index.php | grep *@version > temp
cat temp | sed 's/\<*@version\>//g' > version
cat version