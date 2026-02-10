#!/usr/bin/env sh

set -e

echo Authenticating...

echo $GITHUB_ACCESS_TOKEN | docker login ghcr.io -u $GITHUB_USERNAME --password-stdin

echo Building Nginx image...

docker build -t overlay-mgr_nginx:latest -t ghcr.io/jckf/overlay-mgr_nginx:latest . -f app.dockerfile

echo Building PHP image...

docker build -t overlay-mgr_php:latest -t ghcr.io/jckf/overlay-mgr_php:latest . -f api.dockerfile

echo Pushing images...

docker push ghcr.io/jckf/overlay-mgr_nginx:latest
docker push ghcr.io/jckf/overlay-mgr_php:latest
