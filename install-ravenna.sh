#!/usr/bin/env bash

if [ ! -x "$(command -v apt)" ]; then
        echo "Looks like it's not an Ubuntu- or Debian-based system."
        exit 1
fi

if [ "$(whoami)" != "root" ]; then
        echo "You must run this script as root"
        exit 1
fi

apt update
apt upgrade
apt install -y screen git apache2 php libapache2-mod-php php-cli php-gd php-common php-imagick php-xml php-intl

apt install unattended-upgrades
dpkg-reconfigure unattended-upgrades

a2enmod ssl
a2enmod rewrite

cp /etc/apache2/apache2.conf /etc/apache2/apache2.conf.old
cat <<EOF >>/etc/apache2/apache2.conf
<Directory /var/www/html>
        AllowOverride All
</Directory>
EOF

mkdir /etc/apache2/certificate
cd /etc/apache2/certificate
openssl req -new -newkey rsa:4096 -x509 -sha256 -days 365 -nodes -out apache-certificate.crt -keyout apache.key

mv /etc/apache2/sites-enabled/000-default.conf /etc/apache2/sites-enabled/000-default.conf.old
cat <<EOF >/etc/apache2/sites-enabled/000-default.conf
<VirtualHost *:80>
        RewriteEngine On
        RewriteCond %{HTTPS} !=on
        RewriteRule ^/?(.*) https://%{SERVER_NAME}/$1 [R=301,L]
</virtualhost>
<VirtualHost *:443>
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/html
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
        SSLEngine on
        SSLCertificateFile /etc/apache2/certificate/apache-certificate.crt
        SSLCertificateKeyFile /etc/apache2/certificate/apache.key
</VirtualHost>
EOF

service apache2 restart

cd /var/www/html/
git clone https://github.com/dmpop/ravenna.git
rm index*
git clone https://github.com/dmpop/ravenna.git .
