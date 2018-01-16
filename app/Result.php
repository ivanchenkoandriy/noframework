<?php

namespace app;

/**
 * Result class
 *
 * @author Andriy Ivanchenko
 */
class Result {

    /**
     * Constants
     */
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_UNKNOWN = 'unknown';

    /**
     * Status
     * @var string
     */
    private $status = 'unknown';

    /**
     * Message
     * @var string
     */
    private $message = '';

    /**
     * Data
     * @var array
     */
    private $data = '';

    /**
     * Constructor
     *
     * @param string $status Status id
     * @param string $message Message text
     * @param array $data Advanced data
     */
    public function __construct(string $status, string $message, array $data = []) {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Create success result
     *
     * @param string $message Message text
     * @param array $data Advanced data
     * @return \app\Result Result
     */
    public static function createSuccess(string $message, array $data = []): Result {
        return new Result(Result::STATUS_SUCCESS, $message, $data);
    }

    /**
     * Create fail result
     *
     * @param string $message Message text
     * @param array $data Advanced data
     * @return \app\Result Result
     */
    public static function createFail(string $message, array $data = []): Result {
        return new Result(Result::STATUS_FAIL, $message, $data);
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * Get advanced data
     *
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Convert this object to array
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ];
    }

    /**
     * Is result success
     *
     * @return bool
     */
    public function isSuccess(): bool {
        return Result::STATUS_SUCCESS === $this->status;
    }

}
