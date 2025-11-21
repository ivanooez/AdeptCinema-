#!/bin/bash
# create_zip.sh - creates an archive of the project (exclude uploads and secrets)
OUT="adept-cinema-package.zip"
echo "Creating ${OUT} ..."
zip -r "${OUT}" . -x "*.git*" "node_modules/*" "uploads/*" "logs/*" "*.env" "*.env.local"
echo "Done: ${OUT}"
