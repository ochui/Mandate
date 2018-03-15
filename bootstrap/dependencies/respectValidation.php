<?php

#Respect validation
use Respect\Validation\Validator as v;

#custom rules
v::with('App\\Validation\\Rules\\');