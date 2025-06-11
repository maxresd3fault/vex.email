#!/bin/sh
git config filter.hidekey.clean "sed -E \"s/(define\\('[^']+',')[^']*('\\);)/\\1***REDACTED***\\2/g\""
git config filter.hidekey.smudge "cat"