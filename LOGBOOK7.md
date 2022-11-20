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



--

## Challenge 1
To login as the admin, we just need to do the same thing as in the Task 2. So, all that was needed was to insert on the username: admin' Or'
And we also needed to insert something on the password field since the website does not allow this field to be left empty, but the content it's not relevant at all.
By logging in as the admin, we could see the flag{44e9441ad346fcdb0f0850669532ec10}.

## Challenge 2



