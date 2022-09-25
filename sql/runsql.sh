#!/usr/bin/env bash

# CREATE THE DATABASE
/opt/ibm/db2/V11.5/bin/db2 create database docker

# Connect to the database
/opt/ibm/db2/V11.5/bin/db2 connect to testdb USER db2inst1 USING ChangeMe1

# Run all the sql files
echo "Looping thru sql"
for file in /opt/ibm/sql/*.sql;
do
  echo "$file"
  /opt/opt/db2/V11.5/bin/db2 -vtf "$file"
done  >  ~/sql/results.out

cat ~/sql/results.out