# work-time-monitor
simple work time monitor so i don't mess up with my work time

## configure program rights

the program will automatically create ./var/database.sqlite file
make sure the program has user rights to alter the folder / file

## usage

the program simply saves the your events into the .sqlite database
and then provides a summary for a particular work day

### first start a work day
to start a work day just press the blue button at the top of the screen
which says "Workday Start"

>>> when you start workday - no events are started
>>> when you end workday - all the events for the workday are stoped

### create events
press event buttons to register events' initiation time
>>> when one event starts - the previous one ends
>>> there could only be one event active at a time

### finish your day
to finish your workday just press the blue button at the top of the screen
which says "Workday Stop"

## authentication
create file ./var/users.php
the file should return a php array, which credentials

file example:
```
<?php
return array('myusername' => 'mypassword');
```
>>> if the file is empty - no authorization would be asked