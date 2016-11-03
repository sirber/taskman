#!/bin/bash
hg pull
hg update
composer update
chmod a+w ./private/logs/
