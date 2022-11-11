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


##Task 3

###Task 3.A
In this task we need to change number variable.


Like we have done in task 2B, we have to access the address by making it the beginning of our string into it. The address is 0x080e5068.


We can access it by using "%x" 64 times and changing the values for 63 times plus "%n".The reason for this is the same we used to crash the server and we can write to the address the pointer is pointing to.In this case pointing to where the secret variable was located.


###Task 3.B
The difference between task 3B and 3A is in this one we have to change the secret variable like task 3A but also change it to a specific value: 0x5000

We know "%n" print the number of characters printed in the string so far, and it will be able to write 0x5000. We must make it so that our string has 5000 characters before it.


From this task we understand how printf() is used to acess or change memory from an attacked device, how it can be exploited and what cautions we need to have when using it.

We have also learned how an attacker can use the side effects of "%x", "%s", "%n" in a formatted string to his/her advantage performing an attack.
