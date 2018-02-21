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
	updateModels( item_id, credentialPath)
}
%- maybe also 'usage' for other objects documented here.
\arguments{
  \item{item_id}{
		Item number of the item that needs updating
	}
  \item{credentialPath}{
		Path to a csv file with fields username, password, host, and database set to the value of an user of the database
	}
}
\details{
%%  ~~ If necessary, more details than the description above ~~
}
\value{
%%  ~Describe the value returned
%%  If it is a LIST, use
%%  \item{comp1 }{Description of 'comp1'}
%%  \item{comp2 }{Description of 'comp2'}
%% ...
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
function (item_id, credentialPath) 
{
    library(car)
    library(MASS)
    library(RMySQL)
    stopifnot(all(item_id == as.integer(item_id)))
    stopifnot(is.character(credentialPath) && file.exists(credentialPath))
    credentials = read.csv(credentialPath, header = TRUE, sep = "\t")
    db = dbConnect(MySQL(), user = toString(credentials$username), 
        password = toString(credentials$password), dbname = toString(credentials$database), 
        host = toString(credentials$host))
    itemQuery = sprintf("SELECT id, name, slope_days, adj_R_squared, estimated_daily_use FROM item WHERE id = \%d", 
        item_id)
    itemData = dbGetQuery(db, itemQuery)
    itemUseQuery = sprintf("SELECT date_nbr, qty FROM item_use WHERE item_id = \%d", 
        item_id)
    useData = dbGetQuery(db, itemUseQuery)
    use = useData$qty
    days = useData$date_nbr
    lm = lm(use ~ days - 1)
    slope_days = coef(summary(lm))["days", "Estimate"]
    adj_R_squared = summary(lm)$adj.r.squared
    nullModel = is.na(itemData$slope_days) | is.na(itemData$adj_R_squared)
    if (nullModel) {
        updateQuery = sprintf("UPDATE item SET slope_days = \%10.7f, adj_R_squared = \%10.7f WHERE id = \%d", 
            slope_days, adj_R_squared * 100, item_id)
        dbSendQuery(db, updateQuery)
    }
    else if (!nullModel) {
        oldlm = lm(use ~ I(itemData$slope_days * days) - 1)
        old_adj_R_squared = summary(oldlm)$adj.r.squared
        if (adj_R_squared >= old_adj_R_squared) {
            updateQuery = sprintf("UPDATE item SET slope_days = \%10.7f, adj_R_squared = \%10.7f WHERE id = \%d", 
                slope_days, adj_R_squared * 100, item_id)
            update = dbSendQuery(db, updateQuery)
            dbClearResult(update)
        }
    }
    all_cons <- dbListConnections(MySQL())
    for (con in all_cons) dbDisconnect(con)
  }
}
% Add one or more standard keywords, see file 'KEYWORDS' in the
% R documentation directory.
\keyword{ ~kwd1 }% use one of  RShowDoc("KEYWORDS")
\keyword{ ~kwd2 }% __ONLY ONE__ keyword per line
