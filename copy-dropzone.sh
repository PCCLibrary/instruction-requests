#!/bin/bash

# Create the target directory if it doesn't exist
mkdir -p public/js/components

# Copy the Dropzone component JavaScript to the public directory
cp resources/js/components/dropzone-component.js public/js/components/dropzone-component.js

echo "Copied dropzone-component.js to public directory"
