#!/bin/bash
git fetch --all
git reset --hard origin/master
composer update
chmod a+w ./private/logs/
