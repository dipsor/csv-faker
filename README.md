# DIPSOR/CSV-FAKER #
[![Build Status](https://travis-ci.org/dipsor/csv-faker.svg?branch=master)](https://travis-ci.org/dipsor/csv-faker)
Creates commands for laravel.
### csv:faker:new // uses default option --load=a ###
    - User has to interact in the console.
### csv:faker:new {--load=s} {filename?} {rows?} {columns?} ### 
    - User uses the command with arguments.
    - Ex: csv:faker:new {--load=s} table 15 name:firstname,surname:lastname,email:email
        - will create csv file table.csv with 15 rows and 3 columns. 
        - columns need to be in form: columnName:fakerPropertyName.


