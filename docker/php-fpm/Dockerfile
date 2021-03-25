FROM ubuntu:18.04
RUN apt update
RUN ln -fs /usr/share/zoneinfo/America/Winnipeg /etc/localtime
RUN apt install -y tzdata php7.2 php7.2-dev php7.2-fpm
RUN apt install -y wget
WORKDIR /root
RUN wget http://pear.php.net/go-pear.phar
RUN php go-pear.phar
RUN pecl channel-update pecl.php.net\
    && pecl install phalcon
RUN mkdir -p /run/php
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"\
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"\
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer\
    && php -r "unlink('composer-setup.php');"
WORKDIR /app
CMD ["php-fpm7.2","-F"]