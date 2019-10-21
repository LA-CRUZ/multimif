encore :
	yarn encore dev
.PHONY: encore

encore-watch : 
	yarn encore dev --watch
.PHONY: encore-watch

server :
	bin/console server:run
.PHONY: server

cc :
	bin/console cache:clear
.PHONY: cc

install :
	composer update
	composer install
	yarn install
.PHONY: install