#!/bin/bash
 
WEB_PATH=/data/web/onlineExam
 
echo "Start deployment"
cd $WEB_PATH
echo "pulling source code..."
/usr/local/bin/git reset --hard origin/master
/usr/local/bin/git clean -f
/usr/local/bin/git pull
/usr/local/bin/git checkout master
echo "Finished."