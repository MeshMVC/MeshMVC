#!/bin/bash

set -e

if [ "$1" = "-c" ]; then
    echo "Clearing caches..."
    # clear cache
    shopt -s dotglob
    rm -rf cache/*
    echo "Caches cleared."
else
echo "./mesh.sh [OPTIONS]...
  -h, --help                   this help message
  -u, --start                  alias of 'docker compose up'
  -d, --stop                   alias of 'docker compose down'
  -e, --env                    set current environment
  -c, --clear-cache            clear caches
  -l, --lint                   lint webapp code"
fi
