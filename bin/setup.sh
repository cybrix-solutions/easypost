#!/bin/sh

cp .env.example .env
composer install

# Generate a random app key
key="base64:$(openssl rand -base64 32)"

# Replace the key in the .env file
awk -v newkey="$key" '/^APP_KEY=/{$0="APP_KEY="newkey}1' .env > temp && mv temp .env
