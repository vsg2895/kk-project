<?php namespace Jakten\Helpers;

use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Log;

/**
 * Class KlarnaError
 * @package Jakten\Helpers
 */
class KlarnaError
{
    /**
     * @var \Exception $exception
     */
    public $exception;

    /**
     * @var null $data
     */
    public $data;

    /**
     * @var $statusCode
     */
    public $statusCode;

    /**
     * @var $reasonPhrase
     */
    public $reasonPhrase;

    /**
     * @var array $errors
     */
    public $errors;

    /**
     * KlarnaError constructor.
     * @param \Exception $e
     * @param null $data
     */
    function __construct(\Exception $e, $data = null)
    {
        $this->exception = $e;
        $this->data = $data;
        $this->errors = [];
        $this->parseException();
        $this->log();
    }

    /**
     * Write information to log.
     *
     * @return void
     **/
    protected function log()
    {
        Log::debug('Klarna request failed', array_merge($this->get(), [
            'exception' => $this->exception,
            'data' => $this->data
        ]));
    }

    /**
     * Extract information from the guzzle exception.
     *
     * @return void
     **/
    protected function parseException()
    {
        $response = $this->exception->getResponse();
        $this->statusCode = $response->getStatusCode();
        $this->reasonPhrase = $response->getReasonPhrase();

        $errors = [];
        $responseBody = $response->getBody();
        if ($responseBody instanceof Stream) {
            $errors = json_decode($responseBody->getContents());
            if (isset($errors->errors)) {
                $errors = $errors->errors;
            }
        }

        if (is_array($errors)) {
            foreach ($errors as $e) {
                $this->parseError($e);
            }
        } elseif ($errors) {
            $this->parseError($errors);
        }
    }

    /**
     * Extracts errors from klarna output.
     *
     * @return void
     **/
    protected function parseError(string $e)
    {
        $pos = stripos($e, " ");
        $this->errors[$this->translateKlarnaFieldToReadable(substr($e, 0, $pos))] = substr($e, $pos + 1);
    }

    /**
     * Translate klarna error field names to more readable ones.
     *
     * @return string
     **/
    protected function translateKlarnaFieldToReadable($field)
    {
        $field = str_replace(['stores[0].', 'company.'], '', $field);
        // todo: add more advanced translation

        return $field;
    }

    /**
     * Return extracted information.
     *
     * @return array
     **/
    public function get()
    {
        return [
            'status' => $this->statusCode,
            'reason' => $this->reasonPhrase,
            'errors' => $this->errors
        ];
    }
}
