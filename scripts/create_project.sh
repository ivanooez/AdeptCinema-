#!/bin/bash
# simple helper to create folders used by project (run from repo root)
set -e
mkdir -p public admin admin/common common uploads/{banners,payments,subtitles,posters} sql scripts logs
chmod -R 755 uploads
echo "Project folders created."
