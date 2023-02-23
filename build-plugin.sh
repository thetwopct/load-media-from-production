#!/usr/bin/env bash

# run this script with commannd:
# ./build-plugin.sh

plugin_main_file="load-media-from-production.php"

# Get the plugin name by removing the directory and extension parts
plugin_name=$(basename "$plugin_main_file" .php)

# Get version number
version=$(awk '/Version:/ {print $NF; exit}' "$plugin_main_file")

# Create a directory named after the plugin in the releases folder
mkdir -p "releases/$plugin_name"

# Copy the necessary files to the releases folder
cp -R includes "$plugin_main_file" changelog.txt readme.md readme.txt "releases/$plugin_name"

# Generate the zip file inside the plugin directory
cd "releases/"

zip -r "$plugin_name-$version.zip" . -x ".*" -x "__MACOSX"

rm -r "$plugin_name"