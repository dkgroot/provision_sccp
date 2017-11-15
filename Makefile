SHELL  := /usr/bin/env bash
PIP    := /usr/bin/env pip
PYTHON := /usr/bin/env python

.PHONY: bootstrap clean
.DEFAULT_GOAL := all

all: 

bootstrap: .requirements_satisfied tools/generate.py
	@echo "running generate"
	@$(PYTHON) tools/generate.py

.requirements_satisfied: tools/requirements.txt
	@echo "checking requirements"
	@$(PIP) install --user -r tools/requirements.txt
	@touch $@

clean:
	@find . -type f -name '*.pyc' -delete
	@find . -type f -name '*~' -delete
	@find . -type f -name '*.bak' -delete
	@find . -type f -name '.requirements_satisfied' -delete
#	@find . -type f -name 'etc/tftpd-hpa/rewrite.rules' -delete
#	@find . -type f -name 'etc/nginx/site-available/tftpboot' -delete
