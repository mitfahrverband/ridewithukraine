# Script to upload all files from a local dir to the webserver

# 1. copy this file to publish.sh (note: publish.sh is in .gitignore)
# 2. replace USER and PASSWORD and SERVER (just the domain without any protocol)

# How it works
# - the local directory is changed to src
# - the remote directory is /, which is the webserver root for the domain
# - mirror -R updates all files on the remote dir

lftp -c 'set ftp:ssl-allow true ; set ssl:verify-certificate no; open -u USER,PASSWORD -e "cd /; lcd src; mirror -R; quit" SERVER'
