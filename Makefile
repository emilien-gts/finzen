.PHONY: help install start stop

install:
	$(MAKE) -C api install

start:
	$(MAKE) -C api start

stop:
	$(MAKE) -C api stop
