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
MySQL has a multileoption that prevents more than one sql statement. Basically with this option enabled, everytime we put a ';' (essencial to end a statement and start a new one) all of our input that goes after that gets commented, which will lead to a semantic error because our query will lose some important content and specific chars, ending up not corresponding to a correct sql statement when complemented with the sql code from the webpage.

## Task 3

### Task 3.1
To modify Alice's salary we need to, on the 'Edit Profile' page, insert in one box (for example in the username one) the following sentence: ',salary = '40000
This will include the variable 'salary' on the update statement and we can assign any value we want (int this case we updated the value from 20000 to 40000, doubling Alice's salary).

### Task 3.2
As Alice, to change Boby's salary we need to change the 'id' on the 'where' clause. So to change Boby's salary to 1 dollar we just need to insert the following sentence on any box (for example in the username one): ',salary = '1' WHERE ID = '2';#
This will insert the 'salary' variable on the update statement (just like in task 3.1) and the id used will be 2 (Boby's id, instead of Alice's). The inputed sentence also needs to have a '#' in order to comment the rest of the update statement, so it is overwritten by our code, otherwise we won't be able to change Boby's salary.

--

## Challenge 1
To login as the admin, we just need to do the same thing as in the Task 2. So, all that was needed was to insert on the username: admin' Or'
And we also needed to insert something on the password field since the website does not allow this field to be left empty, but the content it's not relevant at all.
By logging in as the admin, we could see the flag{44e9441ad346fcdb0f0850669532ec10}.

## Challenge 2



