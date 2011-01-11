#мейк файл для основных действий

all:
	php cron/start.php css/gen
	php cron/start.php js/gen
	- compass compile -e production --force
	#- compass compile --output-style compressed --force

daemon:
	- compass watch

clean: compass_clean
	php cron/start.php css/clean
	php cron/start.php js/clean

compass_clean:
	- find application/_common/css -name '*.s[ac]ss' | sed 's/application\/_common/www/' | sed 's/\.s[ac]ss/\.css/' | xargs -I'{}' rm {}

.PHONY:
	clean compass_clean