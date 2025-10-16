#!/bin/sh
set -e

# Install Composer dependencies if vendor folder is empty
if [ ! -d "/var/www/html/vendor" ]; then
  composer install --no-interaction --prefer-dist
fi

# Wait for the database container
until nc -z db 5432; do
  echo "Waiting for database..."
  sleep 1
done

# Run Symfony migrations
echo "Running migrations..."
# 1. Drop database (⚠️ deletes everything!)
# php bin/console doctrine:database:drop --force

# 2. Recreate database
# php bin/console doctrine:database:create

# # 3. Generate a fresh migration from your entities
# php bin/console make:migration
# rm -rf migrations/*
# php bin/console doctrine:migrations:diff
# # 4. Run the migrations
# php bin/console doctrine:migrations:migrate --no-interaction

echo "Generating JWT keys with passphrase..."
rm -rf /var/www/html/config/jwt
mkdir -p /var/www/html/config/jwt

# Generate private key with AES-256 encryption and passphrase
openssl genpkey \
  -out /var/www/html/config/jwt/private.pem \
  -aes256 \
  -algorithm rsa \
  -pkeyopt rsa_keygen_bits:4096 \
  -pass pass:"$JWT_PASSPHRASE"

# Extract public key using same passphrase
openssl pkey \
  -in /var/www/html/config/jwt/private.pem \
  -out /var/www/html/config/jwt/public.pem \
  -pubout \
  -passin pass:"$JWT_PASSPHRASE"
chmod 644 /var/www/html/config/jwt/private.pem
chmod 644 /var/www/html/config/jwt/public.pem

# Ensure permissions
chown -R www-data:www-data /var/www/html/var /var/www/html/vendor /var/www/html/config/jwt

# Execute the container's main command
exec "$@"
