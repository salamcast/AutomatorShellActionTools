## Shell Action Tools

In my research into building Automator Actions with Shell Scripts (such as bash/sh/ksh), I notices that hasn't been much written about the subject.  So I have based my work on these urls:

 - https://macosxautomation.com/automator/shellacaction/index.html
 - https://developer.apple.com/library/mac/documentation/AppleApplications/Conceptual/AutomatorConcepts/Articles/ShellScriptActions.html


This is a small effort to show that PHP can be used in more places than just Websites; this class is aimed a processing command input and enviroment variables passes to the script from the prevous Automator Action.  Much like a pipe ( | ) when using the command line

This class will process the variables set and passed before the command (set as values in the enviroment variable `$_ENV`) and the document sent to standard input (`{command} < STDIN`).  


#### Using the Code in main.command

> \#!/usr/bin/php
> <?php
>
> require 'ShellActionTools.class.php';
>
> $s=new ShellActionTools();
>
> echo "arg is: " . $s->arg . "\n";
> echo "arg2 is " . $s->arg2 . "\n";
>
> exit();

#### Debuging Class on console


> $> arg="one" arg2="two" php main.command < config-file


or like this

> $> ls | arg="one" arg2="two" php main.command


As you can see with the example above, you can use this class to help you process to types of input for a shell tool; but the main focus is building an Mac OS X Automator.app Action in XCode using shell scripting -- PHP in this case.  
 
**Ignored ENV keys**

- LDFLAGS
- CPPFLAGS
- TMPDIR
- LANG
- PKG_CONFIG_PATH
- TERM_PROGRAM
- TERM
- SHELL
- TERM_PROGRAM_VERSION
- TERM_SESSION_ID
- USER
- SSH_AUTH_SOCK
- LOGNAME
- SECURITYSESSIONID
- DBUS_LAUNCHD_SESSION_BUS_SOCKET
- XPC_FLAGS
- XPC_SERVICE_NAME
- HOME
- PATH
- PWD
- OLDPWD
- PYTHONPATH
- SHLVL
- _
- __CF_USER_TEXT_ENCODING
- Apple_PubSub_Socket_Render

These were removed so that it would be easyer to process the enviroment variables passed or set on the command line

#### XCode Interface

The XCode interface builder isn't that hard to use once you become fimilar with the tools and the items you can add to the interface.  The links above will give you a short guide on how to build an automator shell script action; the pages are dated, showing screen shots from Mac OS X 10.6 or 10.5.  

I created my [VLC-Cutter](https://github.com/salamcast/VLC-cutter) action with Mac OS X 10.11.3

Please take a look at [macosxautomation.com](https://macosxautomation.com/) for more ideas on what you can do with Automator workflows.


#### \*NIX Users, turn that frown upside down

If you take a look at the Debugging Class on the console section, you'll notice that this class can be used in normal Linux/Unix shell scripts with PHP using the same format; I'm not going to leave you linux heads empty handed, apple isn't the only *fruit* computer I use ;) ... Raspbery Pi's could also benifit from our PHP skills!  

This class will only be focussed on dealing with the piped input and enviroment variables.  I'm most likely going to build something that will deal with getops related stuff later on, maybe an extention of this class.