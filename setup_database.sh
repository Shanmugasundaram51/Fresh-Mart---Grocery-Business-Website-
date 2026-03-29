#!/bin/bash

# Database setup script for MAMP
# This script creates the database and imports the schema

echo "Setting up Fresh Mart database..."

# MAMP MySQL path
MYSQL="/Applications/MAMP/Library/bin/mysql"
MYSQL_USER="root"
MYSQL_PASS="root"

# Check if MAMP MySQL exists
if [ ! -f "$MYSQL" ]; then
    echo "MAMP MySQL not found at $MYSQL"
    echo "Using system MySQL instead..."
    MYSQL="mysql"
fi

# Create database
echo "Creating database 'onlinesale'..."
$MYSQL -u $MYSQL_USER -p$MYSQL_PASS -e "CREATE DATABASE IF NOT EXISTS onlinesale;"

# Import schema
echo "Importing database schema..."
$MYSQL -u $MYSQL_USER -p$MYSQL_PASS onlinesale < onlinesale.sql

echo ""
echo "✅ Database setup complete!"
echo ""
echo "Database: onlinesale"
echo "Tables created:"
$MYSQL -u $MYSQL_USER -p$MYSQL_PASS onlinesale -e "SHOW TABLES;"
echo ""
echo "Products with stock:"
$MYSQL -u $MYSQL_USER -p$MYSQL_PASS onlinesale -e "SELECT id, name, stock_quantity FROM products LIMIT 5;"
echo ""
echo "🚀 You can now access the website!"
