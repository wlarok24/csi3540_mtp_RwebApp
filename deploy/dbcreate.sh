#!/bin/bash
# Bash script to create MySQL database for CSI3540 Project
# By : William LaRocque (8397424)
# Based on https://stackoverflow.com/questions/33470753/create-mysql-database-and-user-in-bash-script
# get script directory
script_dir=$(pwd)
#cd script_dir;
echo "Credentials will be found here : ${script_dir}"
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
	\$username = "RWEBAPPPHP";
	\$password = "$PASSWRDPHPLN
	\$dbname = "$DBNAME";
?>
EOF
# CSV credentials
echo "RDBCredentials.csv"
cat > RDBCredentials.csv << EOF
username;host;database;password
RWEBAPPR;localhost;$DBNAME;$PASSWRDR
EOF
echo "Creating MySQL Database..."
read -s -p "Enter mysql root user password : " rootpasswd 
mysql -u root -p$rootpasswd -e "CREATE DATABASE IF NOT EXISTS $DBNAME;"
# Create the database users 
mysql -u root -p$rootpasswd -e "CREATE USER IF NOT EXISTS 'RWEBAPPPHP'@'localhost' IDENTIFIED BY '${PASSWRDPHP}';"
mysql -u root -p$rootpasswd -e "GRANT SELECT, INSERT, UPDATE, DELETE ON $DBNAME.* TO 'RWEBAPPPHP'@'localhost';"
mysql -u root -p$rootpasswd -e "CREATE USER IF NOT EXISTS 'RWEBAPPR'@'localhost' IDENTIFIED BY '${PASSWRDR}';"
mysql -u root -p$rootpasswd -e "GRANT SELECT, UPDATE ON $DBNAME.* TO 'RWEBAPPR'@'localhost';"
mysql -u root -p$rootpasswd -e "FLUSH PRIVILEGES;"
# Create tables
mysql -u root -p$rootpasswd -e "CREATE TABLE IF NOT EXISTS $DBNAME.user(id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(256) NOT NULL, email VARCHAR(150) NOT NULL, salt CHAR(32) NOT NULL, token CHAR(32), password CHAR(64) NOT NULL, CONSTRAINT emailunique UNIQUE (email));"
mysql -u root -p$rootpasswd -e "CREATE TABLE IF NOT EXISTS $DBNAME.item(id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id MEDIUMINT NOT NULL, name VARCHAR(256) NOT NULL, unit VARCHAR(10) NOT NULL, usual_use_size INTEGER NOT NULL, tracked_since CHAR(8), inventory INTEGER NOT NULL CHECK (inventory >= 0), slope_days FLOAT(10,7), adj_R_squared FLOAT(10,7), estimated_daily_use FLOAT(10,7) NOT NULL, CONSTRAINT unq_usr_name UNIQUE (user_id, name), FOREIGN KEY (user_id) REFERENCES user(id) ON UPDATE CASCADE ON DELETE CASCADE);"
mysql -u root -p$rootpasswd -e "CREATE TABLE IF NOT EXISTS $DBNAME.item_use(item_id BIGINT NOT NULL, date_nbr MEDIUMINT NOT NULL, date CHAR(8) NOT NULL, qty INTEGER CHECK(qty >= 0), PRIMARY KEY (item_id, date_nbr), FOREIGN KEY (item_id) REFERENCES item(id) ON UPDATE CASCADE ON DELETE CASCADE);"
echo "Finished!"