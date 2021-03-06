#!/bin/bash

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag ${TAG}
box build

#
# Copy executable file into GH pages
#
git checkout gh-pages

cp backup.phar downloads/backup-${TAG}.phar
git add downloads/backup-${TAG}.phar

SHA1=$(openssl sha1 backup.phar)

HASH=$(cut -d ' ' -f 2 <<< "${SHA1}")
echo ${HASH};
JSON='name:"backup.phar"'
JSON="${JSON},sha1:\"${HASH}\""
JSON="${JSON},url:\"http://necrogami.github.io/backup/downloads/backup-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

if [ -f backup.phar.pubkey ]; then
    cp backup.phar.pubkey pubkeys/backup-${TAG}.phar.pubkey
    git add pubkeys/backup-${TAG}.phar.pubkey
    JSON="${JSON},publicKey:\"http://necrogami.github.io/backup/pubkeys/backup-${TAG}.phar.pubkey\""
fi

#
# Update manifest
#
cat manifest.json | jsawk -a "this.push({${JSON}})" | python -mjson.tool > manifest.json.tmp
mv manifest.json.tmp manifest.json
git add manifest.json

git commit -m "Bump version ${TAG}"

#
# Go back to master
#
git checkout master

echo "New version created. Now you should run:"
echo "git push origin gh-pages"
echo "git push ${TAG}"
