#!/bin/bash
# Bash script to create MySQL database for CSI3540 Project
# By : William LaRocque (8397424)
# Based on https://stackoverflow.com/questions/33470753/create-mysql-database-and-user-in-bash-script
# get script directory
script_dir=$(pwd)
# echo script_dir
# Create random passwords
DBNAME="RWEBAPPDB"
echo "Generating passwords..."
PASSWRDPHPLN="$(openssl rand -base64 12)\";"
PASSWRDPHP="${PASSWRDPHPLN:0:16}"
PASSWRDR="$(openssl rand -base64 12)"
echo "Creating credentials files..."
# PHP credentials
echo "apiCredentials.php"
cat > apiCredentials.php << EOF
<?php
	\$servername = "localhost";
	\$username = "CSI3540PHP";
	\$password = "$PASSWRDPHPLN
	\$dbname = "$DBNAME";
?>
EOF
# CSV credentials
echo "RDBCredentials.csv"
cat > RDBCredentials.csv << EOF
username;host;database;password
CSI3540R;localhost;$DBNAME;$PASSWRDR
EOF
echo "Moving credentials files..."
# If /root/.my.cnf exists then it won't ask for root password
echo "Creating MySQL Database..."
if [ -f /root/.my.cnf ]; 
then
	echo "No root"
    mysql -e "CREATE DATABASE $DBNAME;"
	# Create the database users 
    mysql -e "CREATE USER 'CSI3540PHP'@'localhost' IDENTIFIED BY '${PASSWRDPHP}';"
	mysql -e "GRANT SELECT, INSERT, UPDATE, DELETE PRIVILEGES ON $DBNAME.* TO 'CSI3540PHP'@'localhost';"
	mysql -e "CREATE USER 'CSI3540R'@'localhost' IDENTIFIED BY '${PASSWRDR}';"
    mysql -e "GRANT SELECT, UPDATE PRIVILEGES ON $DBNAME.* TO 'CSI3540R'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
	# Create tables
	mysql -e "CREATE TABLE user(id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(256) NOT NULL, email VARCHAR(150) NOT NULL, salt CHAR(32) NOT NULL, token CHAR(32), password CHAR(64) NOT NULL, CONSTRAINT emailunique UNIQUE (email));"
	mysql -e "CREATE TABLE item(id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id MEDIUMINT NOT NULL, name VARCHAR(256) NOT NULL, unit VARCHAR(10) NOT NULL, usual_use_size INTEGER NOT NULL, tracked_since CHAR(8), inventory INTEGER NOT NULL CHECK (inventory >= 0), slope_days FLOAT(10,7), adj_R_squared FLOAT(10,7), estimated_daily_use FLOAT(10,7) NOT NULL, CONSTRAINT unq_usr_name UNIQUE (user_id, name), FOREIGN KEY (user_id) REFERENCES user(id) ON UPDATE CASCADE ON DELETE CASCADE);"
	mysql -e "CREATE TABLE item_use(item_id BIGINT NOT NULL, date_nbr MEDIUMINT NOT NULL, date CHAR(8) NOT NULL, qty INTEGER CHECK(qty >= 0), PRIMARY KEY (item_id, date_nbr), FOREIGN KEY (item_id) REFERENCES item(id) ON UPDATE CASCADE ON DELETE CASCADE);"
	# If /root/.my.cnf doesn't exist then it'll ask for root password   
else
    echo "Please enter root user MySQL password!"
    read rootpasswd
	mysql -u root -p ${rootpasswd} -e -e "CREATE DATABASE $DBNAME;"
	# Create the database users 
    mysql -u root -p ${rootpasswd} -e "CREATE USER 'CSI3540PHP'@'localhost' IDENTIFIED BY '${PASSWRDPHP}';"
	mysql -u root -p ${rootpasswd} -e "GRANT SELECT, INSERT, UPDATE, DELETE PRIVILEGES ON $DBNAME.* TO 'CSI3540PHP'@'localhost';"
	mysql -u root -p ${rootpasswd} -e "CREATE USER 'CSI3540R'@'localhost' IDENTIFIED BY '${PASSWRDR}';"
    mysql -u root -p ${rootpasswd} -e "GRANT SELECT, UPDATE PRIVILEGES ON $DBNAME.* TO 'CSI3540R'@'localhost';"
    mysql -u root -p ${rootpasswd} -e "FLUSH PRIVILEGES;"
	# Create tables
	mysql -u root -p ${rootpasswd} -e "CREATE TABLE user(id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(256) NOT NULL, email VARCHAR(150) NOT NULL, salt CHAR(32) NOT NULL, token CHAR(32), password CHAR(64) NOT NULL, CONSTRAINT emailunique UNIQUE (email));"
	mysql -u root -p ${rootpasswd} -e "CREATE TABLE item(id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id MEDIUMINT NOT NULL, name VARCHAR(256) NOT NULL, unit VARCHAR(10) NOT NULL, usual_use_size INTEGER NOT NULL, tracked_since CHAR(8), inventory INTEGER NOT NULL CHECK (inventory >= 0), slope_days FLOAT(10,7), adj_R_squared FLOAT(10,7), estimated_daily_use FLOAT(10,7) NOT NULL, CONSTRAINT unq_usr_name UNIQUE (user_id, name), FOREIGN KEY (user_id) REFERENCES user(id) ON UPDATE CASCADE ON DELETE CASCADE);"
	mysql -u root -p ${rootpasswd} -e "CREATE TABLE item_use(item_id BIGINT NOT NULL, date_nbr MEDIUMINT NOT NULL, date CHAR(8) NOT NULL, qty INTEGER CHECK(qty >= 0), PRIMARY KEY (item_id, date_nbr), FOREIGN KEY (item_id) REFERENCES item(id) ON UPDATE CASCADE ON DELETE CASCADE);"
fi
echo "Finished!"