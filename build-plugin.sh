#!/usr/bin/env bash

# run this with commannd
# ./build-plugin.sh
zip -r9 ../performance-youtube.zip . --exclude "/.git*" "*node_modules*" "*src*" "*package-lock.json" "*package.json" "*.zip" "*.sh" "*.editorconfig*" "*gulpfile.js" "*.eslintrc.js" "*.gitignore" "*.DS_Store"
