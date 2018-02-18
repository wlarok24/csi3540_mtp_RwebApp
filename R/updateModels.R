# CSI3540 Projet
# Par : William LaRocque
# R script to update models
# Based on https://github.com/rwebapps/tvscore/blob/master/R/tv.R

updateModels <- function(item_id, credentialPath){
	# Package import
	library(car)
	library(MASS)
	library(RMySQL)

	# Check item_id validity
	stopifnot(all(item_id == as.integer(item_id)))
	stopifnot(is.character(credentialPath) && file.exists(credentialPath))
	
	# Connect to database
	credentials = read.csv("C:/Projects/csi3540_mtp_RwebApp/data/RDBCredentials.csv", header=TRUE, sep="\t")
	db = dbConnect(MySQL(), user=toString(credentials$username), password=toString(credentials$password), dbname=toString(credentials$database), host=toString(credentials$host))
	
	# Get item data (including old model)
	itemQuery = sprintf("SELECT id, name, slope_days, adj_R_squared, estimated_daily_use FROM item WHERE id = %d", 7)
	itemQueryResult = dbSendQuery(db, itemQuery)
	itemData = fetch(itemQueryResult, n=1)

	# Get item use data
	itemUseQuery = sprintf("SELECT date_nbr, qty FROM item_use WHERE item_id = %d", 7)
	useQueryResult = dbSendQuery(db, itemUseQuery)
	useData = fetch(useQueryResult, n=-1)

	# Create new linear model with data (if n > ??)


	# Compare to old model and update model if its better
	

}

