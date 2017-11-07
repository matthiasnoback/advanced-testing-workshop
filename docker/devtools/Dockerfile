FROM matthiasnoback/php_workshop_tools_base
RUN curl -OL https://phar.phpunit.de/phpcov.phar \
    && chmod +x phpcov.phar \
    && mv phpcov.phar /usr/local/bin/phpcov \
    && phpcov --version
