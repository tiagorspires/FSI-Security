# Format Strings

## Task 1
Since printf is compiled in run time and we decide the arguments for the printf on format.c through the stdin, we can make the program crash. If we insert on the stdin a normal string or "%d", "%x" or "%c" the program will behave normally and finish with no errors. But if we insert "%s" or "%n" on the stdin, the printf call will search on the top of the stack for a pointer (in the case of the "%s" will search for the pointer that points to the beggining of the string) and will treat whatever that is there as an adress, which will crash the program.

## Task 2

