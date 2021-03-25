# phalcon-crawler

## Bring up the containers
Go into the folder of the project root and:
```bash
$: docker-compose up -d
```

## Part one - the command
In the docker container do:
```bash
$: cd /app/app
$: php cli.php craw [number_of_pages_to_crawl]
```
Note that the index page also count as one.

## Part two - see the crawl result
Visit: http://diphuaji.sytes.net