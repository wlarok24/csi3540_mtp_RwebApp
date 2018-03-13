# CSI3540 Projet
# Par : William LaRocque
# R script to update models
# Based on https://github.com/rwebapps/tvscore/blob/master/R/tv.R

updateModels <- function(item_id){
	# Package import
	# library(car)
	# library(MASS)
	library(RMySQL)
	

	# Check item_id validity
	stopifnot(all(item_id == as.integer(item_id)))
	
	# Get credentials
	data("RDBCredentials") # credentials will be in data frame RDBCredentials

	# Connect to database
	db = dbConnect(MySQL(), user=toString(RDBCredentials$username), password=toString(RDBCredentials$password), dbname=toString(RDBCredentials$database), host=toString(RDBCredentials$host))
	
	# Get item data (including old model)
	itemQuery = sprintf("SELECT id, name, slope_days, adj_R_squared, estimated_daily_use FROM item WHERE id = %d", item_id)
	itemData = dbGetQuery(db, itemQuery)

	# Get item use data
	itemUseQuery = sprintf("SELECT date_nbr, qty FROM item_use WHERE item_id = %d", item_id)
	useData = dbGetQuery(db, itemUseQuery)

	# Create new linear model with data

	# For this project, I will only have a variable (days)
	# to create the regression model. In future versions, more
	# variables could be added for better accuracy.

	use = useData$qty
	days = useData$date_nbr
	lm = lm(use ~ days - 1) #'-1' is to remove the intercept from the model
	slope_days = coef(summary(lm))["days", "Estimate"] # Get the model slope
	adj_R_squared = summary(lm)$adj.r.squared # Get model adjusted R squared

	# Compare to old model and update model if its better
	nullModel = is.na(itemData$slope_days) | is.na(itemData$adj_R_squared)
	if(nullModel){
		# Update database
		updateQuery = sprintf("UPDATE item SET slope_days = %10.7f, adj_R_squared = %10.7f WHERE id = %d", slope_days, adj_R_squared*100, item_id)
		dbSendQuery(db, updateQuery)
	} else if(!nullModel){
		# Create linear model with old model
		oldlm = lm(use ~ I(itemData$slope_days * days) - 1)		
		old_adj_R_squared = summary(oldlm)$adj.r.squared		
		
		if(adj_R_squared >= old_adj_R_squared){
			# Update database
			updateQuery = sprintf("UPDATE item SET slope_days = %10.7f, adj_R_squared = %10.7f WHERE id = %d", slope_days, adj_R_squared*100, item_id)
			update = dbSendQuery(db, updateQuery)
			dbClearResult(update)
		} else {
			updateQuery = sprintf("UPDATE item SET adj_R_squared = %10.7f WHERE id = %d", adj_R_squared*100, item_id)
			update = dbSendQuery(db, updateQuery)
			dbClearResult(update)
		}
	}

	# Close connections
	all_cons <- dbListConnections(MySQL())
    for(con in all_cons) 
      	dbDisconnect(con)
}

