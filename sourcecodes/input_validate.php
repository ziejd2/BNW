<?php
//This file will contain input validation functions.

function valid_keyval($keyval)
{
  $keyval = trim($keyval);
  $keyval = stripslashes($keyval);
  $keyval = htmlspecialchars($keyval);
  //check if keyval contains only uppercase or lowercase letters.
  if (!preg_match('/^[a-zA-Z]+$/',$keyval)) {
    header("Location: keyval_error.php");
  }
  if (strlen($keyval)!=3) {
    header("Location: keyval_error.php");
  }
  return $keyval;
}


function valid_input($input)
{
  $input = trim($input);
  $input = stripslashes($input);
  $input = htmlspecialchars($input);
  //check if keyval contains only letters, numbers, hyphens, underscore, periods, or whitespace.
  if (!preg_match('/^[-A-Za-z\d\ \t\r\n_\.]+$/',$input)) {
    header("Location: input_error.php");
  }
  return $input;
}
