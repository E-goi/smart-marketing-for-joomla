deploy:
	@echo "-- START BUILD -- \n"

	@echo buiding version $(version)
	@./scripts/deploy.sh $(version)	
	
	@echo "-- END BUILD -- \n"
