\name{updateModels}
\alias{updateModels}
%- Also NEED an '\alias' for EACH other topic documented here.
\title{
	Update consumption update model
}
\description{
	Get item and item use data from MySQL database
	Create new linear model with the use data
	Update the model in database if its better than the model currently in the database
}
\usage{
	updateModels(item_id)
}
%- maybe also 'usage' for other objects documented here.
\arguments{
  \item{item_id}{
		Item number of the item that needs updating
	}
}
\details{
	Needs a file named csi3540RwebApp.csv (separated with ";" instead ",") containing the credentials needed to access database
}
\value{
	Nothing is returned (void function)
}
\references{
%% ~put references to the literature/web site here ~
}
\author{
	William LaRocque <wlaro029@uottawa.ca>
}
\note{
%%  ~~further notes~~
}

%% ~Make other sections like Warning with \section{Warning }{....} ~

\seealso{
	predict.lm
}
\examples{
##---- Should be DIRECTLY executable !! ----
##-- ==>  Define data, use random,
##--	or do  help(data=index)  for the standard data sets.

## The function is currently defined as
function(item_id){
	# Package import
	# library(car)
	# library(MASS)
	# library(RMySQL)
	

	# Check item_id validity
	stopifnot(all(item_id == as.integer(item_id)))
	
	# Get credentials
	data("RDBCredentials") # credentials will be in data frame RDBCredentials

	# Connect to database
	db = RMySQL::dbConnect(RMySQL::MySQL(), user=toString(RDBCredentials$username), password=toString(RDBCredentials$password), dbname=toString(RDBCredentials$database), host=toString(RDBCredentials$host))
	
	# Get item data (including old model)
	itemQuery = sprintf("SELECT id, name, slope_days, adj_R_squared, estimated_daily_use FROM item WHERE id = %d", item_id)
	itemData = RMySQL::dbGetQuery(db, itemQuery)

	# Get item use data
	itemUseQuery = sprintf("SELECT date_nbr, qty FROM item_use WHERE item_id = %d", item_id)
	useData = RMySQL::dbGetQuery(db, itemUseQuery)

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
		RMySQL::dbSendQuery(db, updateQuery)
	} else if(!nullModel){
		# Create linear model with old model
		oldlm = lm(use ~ I(itemData$slope_days * days) - 1)		
		old_adj_R_squared = summary(oldlm)$adj.r.squared		
		
		if(adj_R_squared >= old_adj_R_squared){
			# Update database
			updateQuery = sprintf("UPDATE item SET slope_days = %10.7f, adj_R_squared = %10.7f WHERE id = %d", slope_days, adj_R_squared*100, item_id)
			update = RMySQL::dbSendQuery(db, updateQuery)
			RMySQL::dbClearResult(update)
		} else {
			updateQuery = sprintf("UPDATE item SET adj_R_squared = %10.7f WHERE id = %d", adj_R_squared*100, item_id)
			update = RMySQL::dbSendQuery(db, updateQuery)
			RMySQL::dbClearResult(update)
		}
	}

	# Close connections
	all_cons <- RMySQL::dbListConnections(RMySQL::MySQL())
    for(con in all_cons) 
      	RMySQL::dbDisconnect(con)
}
% Add one or more standard keywords, see file 'KEYWORDS' in the
% R documentation directory.
\keyword{ ~kwd1 }% use one of  RShowDoc("KEYWORDS")
\keyword{ ~kwd2 }% __ONLY ONE__ keyword per line
