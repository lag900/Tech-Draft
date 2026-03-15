#!/bin/bash

# setup_wkhtmltopdf.sh
# This script downloads and sets up wkhtmltopdf binary locally within the project.

TOOLS_DIR="./tools/wkhtmltopdf"
BINARY_PATH="$TOOLS_DIR/wkhtmltopdf"

# Create directories
mkdir -p "$TOOLS_DIR"

if [ -f "$BINARY_PATH" ]; then
    echo "wkhtmltopdf binary already exists at $BINARY_PATH"
    exit 0
fi

echo "Detecting OS..."
OS_TYPE=$(uname -s)
ARCH_TYPE=$(uname -m)

echo "OS: $OS_TYPE, ARCH: $ARCH_TYPE"

# URL for wkhtmltopdf - targeting 0.12.6.1 generic linux 64-bit
# Note: In a real world scenario, you might want more robust URL selection
DOWNLOAD_URL="https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.amazonlinux2_amd64.rpm" # Just an example, let's use a more generic one if possible

# Actually, let's use the github releases for a generic linux version if available
# For Linux x86_64:
if [[ "$OS_TYPE" == "Linux" && "$ARCH_TYPE" == "x86_64" ]]; then
    # Using a generic binary from a trusted source or a direct download
    # Since official static binaries are harder to find now, we'll try to find a stable one.
    # We will use the GitHub release download.
    echo "Downloading wkhtmltopdf for Linux x64..."
    
    # We'll download the deb/rpm and extract only the binary if we want it "standalone"
    # Or download a pre-built static binary.
    # For this exercise, I'll simulate the download/extract logic.
    
    # Let's try to find a portable tarball.
    # wkhtmltopdf doesn't always provide simple tarballs for all distros anymore.
    # However, we can use 'curl -O' to get the package.
    
    # FALLBACK: If we can't find a simple binary, we might advise the user, 
    # but the request asks for an AUTO-setup.
    
    # Let's use a known stable link for a generic linux binary.
    URL="https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.noarch.rpm"
    # Extraction from RPM/DEB usually requires tools that might not be there.
    
    # For simplicity in this environment, I'll try to use a direct binary link if possible.
    # Otherwise, I will provide the script structure to handle it.
    
    # Let's use the "generic" version if we can find it.
    # Assuming we have access to common tools like wget/curl.
    curl -L -o "$TOOLS_DIR/wkhtmltox.deb" "https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.focal_amd64.deb"
    
    echo "Extracting binary..."
    cd "$TOOLS_DIR"
    ar x wkhtmltox.deb
    tar xf data.tar.xz
    mv usr/local/bin/wkhtmltopdf wkhtmltopdf
    chmod +x wkhtmltopdf
    
    # Clean up
    rm -rf usr wkhtmltox.deb debian-binary control.tar.gz data.tar.xz
    cd ../..
    
    echo "Setup complete. wkhtmltopdf is at $BINARY_PATH"
else
    echo "Automatic setup not supported for $OS_TYPE $ARCH_TYPE yet. Please install wkhtmltopdf manually."
    exit 1
fi
