# biwaki

Quick instruction:

1. clone repository
2. set up vhost in your evoriment (see vhostConfiguration.conf in main directory)
3. open powerShell or command line in folder contain biwaki source then execute composer install 
4. And build database:
```bash
c:\biwaki> php bin/console doctrine:database:create
c:\biwaki> php bin/console doctrine:schema:update --force
```
