# drupal-8_event_timer
Custom module for displaying time remaining until the event starts.

Custom block which display how many days are left until the event starts, example: ‘12 days left until event starts’.
If the event is going to happen on the current day displays 'This event is happening today'.
If the event date passed, displays ‘This event already passed.’.
Includes custom service which has a method which gets a date as a parameter 
and returns a value, which is then used to display correct string in the block.
