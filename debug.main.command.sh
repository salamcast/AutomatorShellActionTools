#!/bin/bash

echo -e "Command with out arguments: \n php main.command \n"
php main.command


echo -e "Command with 2 arguments: \n arg1=\"Hello\" arg2=\"World!\" php main.command \n"
arg1="Hello" arg2="World!" php main.command

echo -e "Command with 2 arguments and piped input: \n ls -l | arg1=\"Hello\" arg2=\"World!\" php main.command \n"
ls -l | arg1="Hello" arg2="World!" php main.command

echo -e "Command with 2 arguments and input doc: \n"
cat <<+
arg1="Hello" arg2="World!" php main.command <<+
[new]
one="Hello"
two="World"
+
echo -e "+\n"
arg1="Hello" arg2="World!" php main.command <<+
[new]
one="Hello"
two="World"
+