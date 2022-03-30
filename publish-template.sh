# Script to upload all files from a local dir to the webserver

# 1. copy this file to publish.sh (note: publish.sh is in .gitignore)
# 2. replace USER and PASSWORD and SERVER (just the domain without any protocol)

lftp -c 'set ftp:ssl-allow true ; set ssl:verify-certificate no; open -u USER,PASSWORD -e "cd /; lcd src; mirror -R; quit" SERVER'
