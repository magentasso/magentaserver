# MagentaServer

The reference implementation of a standalone MagentaSSO server.

## Installation


Assuming you have your web roots in `/var/www`,
and you want to keep MagentaServer
in `/var/www/magentaserver`:

```shell
% # Clone the repo and install dependencies
% git clone https://github.com/magentasso/magentaserver.git /var/www/magentaserver
% cd /var/www/magentaserver
% composer install

% # Edit the configuration environment variables
% cp .env.dist .env
% $EDITOR .env
```

Point your web server's document root to
`/var/www/magentaserver/public` and
rewrite all requests to `index.php` -
this will work for nginx:

```nginx
server {
	server_name magentaserver.test;
	root /var/www/magentaserver/public;

	# Or, however you enable PHP
	include /etc/nginx/fastcgi_php.conf;

	location / {
		try_files $uri /index.php;
	}
}
```

## Documentation

The `docs/` directory contains the majority
of the MagentaServer documentation.

## License

The MagentaServer is licensed under the
GNU Affero General Public License, version 3
(or, at your option, any later version).

See [LICENSE](./LICENSE) for details.
