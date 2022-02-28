.PHONY: build push

build:
	docker image build -f ./Dockerfile -t gearsacr.azurecr.io/pbsgears-gears-backend:latest --target fpm-k8 .

push:
	docker push gearsacr.azurecr.io/pbsgears-gears-backend:latest
