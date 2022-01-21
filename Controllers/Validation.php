<?php

namespace AutomatedRoster\Controllers;

/**
 * Handles form sanitizing and validation
 */
trait Validation
{
  public $error = [];
  public $success = [];
  public $flag = true;

  /**
   * Validate text form input
   *
   * @param string $input Form text input
   * @param string $error_name Name of the error
   * @return string
   **/
  public function validate_text(string $input = null, string $error_name = null, bool $confirm = true)
  {
    $input = \filter_var($input, FILTER_SANITIZE_STRING);
    if (empty($input)) {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " is required";
      $this->flag = false;
    } elseif ($confirm == false) {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " not found/does not exist";
      $this->flag = false;
    } else {
      return $input;
    }
  }

  /**
   * Validate text form input
   *
   * @param string $input Form text input
   * @param string $error_name Name of the error
   * @return string
   **/
  public function validate_username(string $input = null, string $error_name = null, bool $confirm = false)
  {
    $input = \filter_var($input, FILTER_SANITIZE_STRING);
    if (empty($input)) {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " is required";
      $this->flag = false;
    } elseif ($confirm == true) {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " already in use";
      $this->flag = false;
    } else {
      return $input;
    }
  }

  /**
   * Validate form input
   *
   * @param string $input Form text input
   * @param string $error_name Name of the error
   * @return string
   **/
  public function validate_old_password(bool $confirm = true, string $error_name = 'password')
  {
    if ($confirm == false) {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " is incorrect";
      $this->flag = false;
    }
  }

  /**
   * Validate email form input
   *
   * @param string $input Form text input
   * @param string $error_name Name of the error
   * @param string $email_check check if an email is already existing, 
   * An emailCheck() can be pass as a paramater 
   * @return string
   **/
  public function validate_email(string $input = null, bool $email_check = false, string $error_name = 'email')
  {
    $input = \filter_var($input, FILTER_SANITIZE_EMAIL);
    if (!empty($input)) {
      if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
        $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " format is invalid";
        $this->flag = false;
      } elseif ($email_check == true) {
        $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " already in use";
        $this->flag = false;
      } else {
        return $input;
      }
    } else {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " is required";
      $this->flag = false;
    }
  }

  /**
   * Validate password form input
   *
   * @param string $input_1 Password input
   * @param string $input_2 Confirm Password input
   * @return string
   **/
  public function validate_password(string $input_1 = null, string $input_2 = null, string $error_name = 'password')
  {
    $input_1 = \filter_var($input_1, FILTER_SANITIZE_STRING);
    $input_2 = \filter_var($input_2, FILTER_SANITIZE_STRING);
    if (!empty($input_1)) {
      if ($input_1 != $input_2) {
        $this->error[str_replace(' ', '_', 'confirm_' . $error_name) . '_err'] = "Confirm password does not match";
        $this->flag = false;
      } else {
        return $input_1;
      }
    } else {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " is required";
      $this->flag = false;
    }
  }

  /**
   * Validate number input
   *
   * @param float $input A number input
   * @param string $error_name Name of the error
   * @param int $min Minimum number to be validated
   * @param int $max Maximum number to be validated
   * @return string
   **/
  public function validate_number(string $input = null, string $error_name = null, int $min = 0, int $max = null)
  {
    $input = \filter_var($input, FILTER_SANITIZE_STRING);
    if (!empty($input)) {
      if (!is_numeric($input)) {
        $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " must be a numeric";
        $this->flag = false;
      } else if (isset($min) && isset($max)) {
        if ($input < $min) {
          $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " must not be lesser than {$min}";
          $this->flag = false;
        }
        if ($input > $max) {
          $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " must not be greater than {$max}";
          $this->flag = false;
        }
      }

      if ($this->flag == true) {
        return $input;
      }
    } else {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " is required";
      $this->flag = false;
    }
  }

  /**
   * Validate image input
   *
   * @param string $input Image name
   * @param string $error_name Error name
   * @return string
   **/
  public function validate_image(string $input = null, string $error_name = null)
  {
    $input = filter_var($input, FILTER_SANITIZE_STRING);
    if (!empty($input)) {
      // validate image extension type(s)
      $valid_img_ext = \pathinfo(basename($input), PATHINFO_EXTENSION);
      if (!\in_array($valid_img_ext, ['jpeg', 'jpg', 'png', 'PNG', 'JPEG', 'JPG'])) {
        $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " must be of type PNG, JPEG, JPG";
        $this->flag = false;
      } else {
        return $input;
      }
    } else {
      $this->error[str_replace(' ', '_', $error_name) . '_err'] = ucfirst($error_name) . " is required";
      $this->flag = false;
    }
  }
}
