<?php

/**
 * PHPStan stubs for Dolibarr API classes that are not always available in the
 * analysed core checkout (renamed/moved across versions).
 *
 * These declarations allow PHPStan to resolve FundingApi's parent class and
 * related API helpers without failing with "unknown class".
 */

// @codingStandardsIgnoreStart

namespace Luracast\Restler {
    class RestException extends \Exception
    {
        /**
         * @param int    $code    HTTP status code
         * @param string $message Message
         */
        public function __construct($code, $message = '')
        {
            parent::__construct($message, $code);
        }
    }
}

namespace {
    /** @property mixed $db */
    class DolibarrApi
    {
        /** @var mixed */
        public $db;

        /**
         * @param string $resource  Resource name
         * @param int    $id        Object id
         * @param string $resource2 Second resource
         * @return bool
         */
        public static function _checkAccessToResource($resource, $id, $resource2 = '')
        {
            return true;
        }

        /**
         * @param object $object Object to clean
         * @return array<string,mixed>
         */
        protected function _cleanObjectDatas($object)
        {
            return array();
        }

        /**
         * @param array<string,mixed> $object     Object data
         * @param string              $properties Properties to keep
         * @return array<string,mixed>
         */
        public function _filterObjectProperties($object, $properties)
        {
            return $object;
        }
    }

    class DolibarrApiAccess
    {
        /** @var mixed */
        public static $user;
    }
}

// @codingStandardsIgnoreEnd
