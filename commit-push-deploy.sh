#!/bin/bash
git add -A
git commit -am "${1:-'dev'}"
git push origin master
dep deploy production
