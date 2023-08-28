<?php
// this class intercepts all requests and handles them

namespace Benson\InforSharing\Helpers\Traits;

use Benson\InforSharing\Handlers\ErrorHandler;
use Benson\InforSharing\Handlers\JsonHandler;

trait CanHandleRequests
{
    public array $inputs;
    private array $validationInputs = [];




    /**
     * Intercept all requests. This method intercepts all requests and
     * sets the request properties.
     * 
     * @return void
     */
    private function interceptAll()
    {
        $request = [];
        // if content type is application/json
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            if (JsonHandler::isJsonContent())
                $request = JsonHandler::receiveAsArray(file_get_contents('php://input'));
        } else
            $request = $_REQUEST;


        foreach ($request as $key => $value) {
            // sanitize input
            $value = $this->sanitizeInput($value);
            $this->{$key} = $value;
        }

        $this->interceptHeaders();

        return $this;
    }

    public function payload()
    {
        $payload = JsonHandler::receiveAsArray(file_get_contents('php://input'));
        
        foreach ($payload as $key => $value) {
            // sanitize input
            $value = $this->sanitizeInput($value);
        }

        return $payload;
    }

    private function interceptHeaders()
    {
        $headers = getallheaders();
        foreach ($headers as $key => $value) {
            $this->{"header_$key"} = $value;
        }
    }

    public function requests()
    {
        // echo all incoming requests
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }


    /**
     * Magic method to get a property
     * 
     * @param string $name The name of the property to get.
     * @return string|void Returns the value of the property or an error message
     *                if the property does not exist.
     */
    public function __get($name)
    {
        if (isset($this->{$name})) {
            $this->{$name} = $this->sanitizeInput($this->{$name});
            return $this->{$name};
        }
    }

    private function sanitizeInput($input)
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
    }

    // get all incoming requests
    public function __getAll()
    {
        $this->interceptAll();

        return $this;
    }


    /**
     * Magic method to set a property
     * 
     * @param string $name The name of the property to set.
     * @param string $value The value of the property to set.
     * @return void
     */
    public function __set($name, $value): void
    {
        $this->{$name} = $value;
    }

    private function validateRule($ruleName, $ruleValue, $key)
    {
        switch ($ruleName) {
            case 'required':
                if (!isset($this->{$key}))
                    return $key . ' is required';
                break;
            case 'email':
                if (isset($this->{$key}))
                    if (!filter_var($this->{$key}, FILTER_VALIDATE_EMAIL))
                        return $key . ' is not a valid email';
                break;
            case 'min':
                if (strlen($this->{$key}) < $ruleValue)
                    return $key . ' must be at least ' . $ruleValue . ' characters';
                break;
            case 'max':
                if (strlen($this->{$key}) > $ruleValue)
                    return $key . ' must be at most ' . $ruleValue . ' characters';
                break;
            case 'confirm':
                if (isset($this->{$key}))
                    if ($this->{$key} !== $this->{$key . '_confirmation'})
                        return $key . ' does not match ' . $key . '_confirmation';
                break;
                // case 'unique':
                //     $this->user->select('users', ['email'])->where('email', '=', $this->{$key})->run();
                //     if ($this->user->executed && $this->user->count() > 0)
                //         return $key . ' already exists';
                //     break;
            default:
                break;
        }
    }

    protected function setInputs(array $inputs)
    {
        $this->inputs = $inputs;
    }

    public function validate(array $inputs = [])
    {
        if (count($inputs) > 0)
            $this->validationInputs = $inputs;
        $errors = [];

        foreach ($this->inputs as $input => $rules) {
            $rules = explode('|', $rules);
            foreach ($rules as $rule) {
                $rule = explode(':', $rule);
                $ruleName = $rule[0];
                $ruleValue = $rule[1] ?? null;
                $error = $this->validateRule($ruleName, $ruleValue, $input);
                if ($error)
                    $errors[] = $error;
            }
        }

        if (count($errors) > 0) {
            echo ErrorHandler::send($errors);
            exit;
        }

        return $this;
    }


    public function validateEither(array $inputs = [])
    {
        if (count($inputs) === 0)
            ErrorHandler::send('No input rules provided');


        $errors = [];
        $inputsToBeValidated = [];


        foreach ($inputs as $input => $rule) {
            if (isset($this->{$input}))
                $inputsToBeValidated[$input] = $rule;
        }


        if (count($inputsToBeValidated) === 0) {
            echo ErrorHandler::send('No input provided for validation or input does not exist');
            die();
        }

        $this->validationInputs = $inputsToBeValidated;

        foreach ($inputsToBeValidated as $input => $rules) {
            $rules = explode('|', $rules);
            foreach ($rules as $rule) {
                $rule = explode(':', $rule);
                $ruleName = $rule[0];
                $ruleValue = $rule[1] ?? null;
                $error = $this->validateRule($ruleName, $ruleValue, $input);
                if ($error)
                    $errors[] = $error;
            }
        }


        if (count($errors) > 0) {
            echo ErrorHandler::send($errors);
            die();
        }

        return $this;
    }
}
