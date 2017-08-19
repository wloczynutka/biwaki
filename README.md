# biwaki

Quick instruction:

1. Clone repository
2. Set up vhost in your evoriment (see vhostConfiguration.conf in main directory)
3. Rename biwaki\app\config\parameters.yml.dist to parameters.yml and edit it entering your settings
4. Open powerShell or command line in folder contain biwaki source then execute composer install 
5. Then build database (using the same command line):
```bash
c:\biwaki> php bin/console doctrine:database:create
c:\biwaki> php bin/console doctrine:schema:update --force
```
6. Obtain google api key and place it in biwaki\app\config\parameters.yml.dist
(this is for displaying maps)
``` 
https://developers.google.com/maps/documentation/javascript/get-api-key
```