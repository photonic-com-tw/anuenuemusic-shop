<?php
namespace ReCaptcha;

class ReCaptcha{
    /**
     * Version of this client library.
     * @const string
     */
    const VERSION = 'php_1.2.4';

    /**
     * URL for reCAPTCHA siteverify API
     * @const string
     */
    const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Invalid JSON received
     * @const string
     */
    const E_INVALID_JSON = 'invalid-json';

    /**
     * Could not connect to service
     * @const string
     */
    const E_CONNECTION_FAILED = 'connection-failed';

    /**
     * Did not receive a 200 from the service
     * @const string
     */
    const E_BAD_RESPONSE = 'bad-response';

    /**
     * Not a success, but no error codes received!
     * @const string
     */
    const E_UNKNOWN_ERROR = 'unknown-error';

    /**
     * ReCAPTCHA response not provided
     * @const string
     */
    const E_MISSING_INPUT_RESPONSE = 'missing-input-response';

    /**
     * Expected hostname did not match
     * @const string
     */
    const E_HOSTNAME_MISMATCH = 'hostname-mismatch';

    /**
     * Expected APK package name did not match
     * @const string
     */
    const E_APK_PACKAGE_NAME_MISMATCH = 'apk_package_name-mismatch';

    /**
     * Expected action did not match
     * @const string
     */
    const E_ACTION_MISMATCH = 'action-mismatch';

    /**
     * Score threshold not met
     * @const string
     */
    const E_SCORE_THRESHOLD_NOT_MET = 'score-threshold-not-met';

    /**
     * Challenge timeout
     * @const string
     */
    const E_CHALLENGE_TIMEOUT = 'challenge-timeout';

    /**
     * Shared secret for the site.
     * @var string
     */
    private $secret;

    /**
     * Method used to communicate with service. Defaults to POST request.
     * @var RequestMethod
     */
    private $requestMethod;

    /**
     * Create a configured instance to use the reCAPTCHA service.
     *
     * @param string $secret The shared key between your site and reCAPTCHA.
     * @param RequestMethod $requestMethod method used to send the request. Defaults to POST.
     * @throws \RuntimeException if $secret is invalid
     */
    public function __construct($secret, RequestMethod $requestMethod = null)
    {
        if (empty($secret)) {
            throw new \RuntimeException('No secret provided');
        }

        if (!is_string($secret)) {
            throw new \RuntimeException('The provided secret must be a string');
        }

        $this->secret = $secret;
        $this->requestMethod = (is_null($requestMethod)) ? new Post() : $requestMethod;
    }

    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test and additionally runs any specified additional checks
     *
     * @param string $response The user response token provided by reCAPTCHA, verifying the user on your site.
     * @param string $remoteIp The end user's IP address.
     * @return Response Response from the service.
     */
    public function verify($response, $remoteIp = null)
    {
        // Discard empty solution submissions
        if (empty($response)) {
            $recaptchaResponse = new Response(false, array(self::E_MISSING_INPUT_RESPONSE));
            return $recaptchaResponse;
        }

        $params = new RequestParameters($this->secret, $response, $remoteIp, self::VERSION);
        $rawResponse = $this->requestMethod->submit($params);
        $initialResponse = Response::fromJson($rawResponse);
        $validationErrors = array();

        if (isset($this->hostname) && strcasecmp($this->hostname, $initialResponse->getHostname()) !== 0) {
            $validationErrors[] = self::E_HOSTNAME_MISMATCH;
        }

        if (isset($this->apkPackageName) && strcasecmp($this->apkPackageName, $initialResponse->getApkPackageName()) !== 0) {
            $validationErrors[] = self::E_APK_PACKAGE_NAME_MISMATCH;
        }

        if (isset($this->action) && strcasecmp($this->action, $initialResponse->getAction()) !== 0) {
            $validationErrors[] = self::E_ACTION_MISMATCH;
        }

        if (isset($this->threshold) && $this->threshold > $initialResponse->getScore()) {
            $validationErrors[] = self::E_SCORE_THRESHOLD_NOT_MET;
        }

        if (isset($this->timeoutSeconds)) {
            $challengeTs = strtotime($initialResponse->getChallengeTs());

            if ($challengeTs > 0 && time() - $challengeTs > $this->timeoutSeconds) {
                $validationErrors[] = self::E_CHALLENGE_TIMEOUT;
            }
        }

        if (empty($validationErrors)) {
            return $initialResponse;
        }

        return new Response(
            false,
            array_merge($initialResponse->getErrorCodes(), $validationErrors),
            $initialResponse->getHostname(),
            $initialResponse->getChallengeTs(),
            $initialResponse->getApkPackageName(),
            $initialResponse->getScore(),
            $initialResponse->getAction()
        );
    }

    /**
     * Provide a hostname to match against in verify()
     * This should be without a protocol or trailing slash, e.g. www.google.com
     *
     * @param string $hostname Expected hostname
     * @return ReCaptcha Current instance for fluent interface
     */
    public function setExpectedHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * Provide an APK package name to match against in verify()
     *
     * @param string $apkPackageName Expected APK package name
     * @return ReCaptcha Current instance for fluent interface
     */
    public function setExpectedApkPackageName($apkPackageName)
    {
        $this->apkPackageName = $apkPackageName;
        return $this;
    }

    /**
     * Provide an action to match against in verify()
     * This should be set per page.
     *
     * @param string $action Expected action
     * @return ReCaptcha Current instance for fluent interface
     */
    public function setExpectedAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Provide a threshold to meet or exceed in verify()
     * Threshold should be a float between 0 and 1 which will be tested as response >= threshold.
     *
     * @param float $threshold Expected threshold
     * @return ReCaptcha Current instance for fluent interface
     */
    public function setScoreThreshold($threshold)
    {
        $this->threshold = floatval($threshold);
        return $this;
    }

    /**
     * Provide a timeout in seconds to test against the challenge timestamp in verify()
     *
     * @param int $timeoutSeconds Expected hostname
     * @return ReCaptcha Current instance for fluent interface
     */
    public function setChallengeTimeout($timeoutSeconds)
    {
        $this->timeoutSeconds = $timeoutSeconds;
        return $this;
    }
}


interface RequestMethod{
    /**
     * Submit the request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params);
}


class Post implements RequestMethod
{
    /**
     * URL for reCAPTCHA siteverify API
     * @var string
     */
    private $siteVerifyUrl;

    /**
     * Only needed if you want to override the defaults
     *
     * @param string $siteVerifyUrl URL for reCAPTCHA siteverify API
     */
    public function __construct($siteVerifyUrl = null)
    {
        $this->siteVerifyUrl = (is_null($siteVerifyUrl)) ? ReCaptcha::SITE_VERIFY_URL : $siteVerifyUrl;
    }

    /**
     * Submit the POST request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params)
    {
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $params->toQueryString(),
                // Force the peer to validate (not needed in 5.6.0+, but still works)
                'verify_peer' => true,
            ),
        );
        $context = stream_context_create($options);
        $response = file_get_contents($this->siteVerifyUrl, false, $context);

        if ($response !== false) {
            return $response;
        }

        return '{"success": false, "error-codes": ["'.ReCaptcha::E_CONNECTION_FAILED.'"]}';
    }
}

class Response
{
    /**
     * Success or failure.
     * @var boolean
     */
    private $success = false;

    /**
     * Error code strings.
     * @var array
     */
    private $errorCodes = array();

    /**
     * The hostname of the site where the reCAPTCHA was solved.
     * @var string
     */
    private $hostname;

    /**
     * Timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
     * @var string
     */
    private $challengeTs;

    /**
     * APK package name
     * @var string
     */
    private $apkPackageName;

    /**
     * Score assigned to the request
     * @var float
     */
    private $score;

    /**
     * Action as specified by the page
     * @var string
     */
    private $action;

    /**
     * Build the response from the expected JSON returned by the service.
     *
     * @param string $json
     * @return \ReCaptcha\Response
     */
    public static function fromJson($json)
    {
        $responseData = json_decode($json, true);

        if (!$responseData) {
            return new Response(false, array(ReCaptcha::E_INVALID_JSON));
        }

        $hostname = isset($responseData['hostname']) ? $responseData['hostname'] : null;
        $challengeTs = isset($responseData['challenge_ts']) ? $responseData['challenge_ts'] : null;
        $apkPackageName = isset($responseData['apk_package_name']) ? $responseData['apk_package_name'] : null;
        $score = isset($responseData['score']) ? floatval($responseData['score']) : null;
        $action = isset($responseData['action']) ? $responseData['action'] : null;

        if (isset($responseData['success']) && $responseData['success'] == true) {
            return new Response(true, array(), $hostname, $challengeTs, $apkPackageName, $score, $action);
        }

        if (isset($responseData['error-codes']) && is_array($responseData['error-codes'])) {
            return new Response(false, $responseData['error-codes'], $hostname, $challengeTs, $apkPackageName, $score, $action);
        }

        return new Response(false, array(ReCaptcha::E_UNKNOWN_ERROR), $hostname, $challengeTs, $apkPackageName, $score, $action);
    }

    /**
     * Constructor.
     *
     * @param boolean $success
     * @param string $hostname
     * @param string $challengeTs
     * @param string $apkPackageName
     * @param float $score
     * @param string $action
     * @param array $errorCodes
     */
    public function __construct($success, array $errorCodes = array(), $hostname = null, $challengeTs = null, $apkPackageName = null, $score = null, $action = null)
    {
        $this->success = $success;
        $this->hostname = $hostname;
        $this->challengeTs = $challengeTs;
        $this->apkPackageName = $apkPackageName;
        $this->score = $score;
        $this->action = $action;
        $this->errorCodes = $errorCodes;
    }

    /**
     * Is success?
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Get error codes.
     *
     * @return array
     */
    public function getErrorCodes()
    {
        return $this->errorCodes;
    }

    /**
     * Get hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Get challenge timestamp
     *
     * @return string
     */
    public function getChallengeTs()
    {
        return $this->challengeTs;
    }

    /**
     * Get APK package name
     *
     * @return string
     */
    public function getApkPackageName()
    {
        return $this->apkPackageName;
    }
    /**
     * Get score
     *
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    public function toArray()
    {
        return array(
            'success' => $this->isSuccess(),
            'hostname' => $this->getHostname(),
            'challenge_ts' => $this->getChallengeTs(),
            'apk_package_name' => $this->getApkPackageName(),
            'score' => $this->getScore(),
            'action' => $this->getAction(),
            'error-codes' => $this->getErrorCodes(),
        );
    }
}

class RequestParameters
{
    /**
     * The shared key between your site and reCAPTCHA.
     * @var string
     */
    private $secret;

    /**
     * The user response token provided by reCAPTCHA, verifying the user on your site.
     * @var string
     */
    private $response;

    /**
     * Remote user's IP address.
     * @var string
     */
    private $remoteIp;

    /**
     * Client version.
     * @var string
     */
    private $version;

    /**
     * Initialise parameters.
     *
     * @param string $secret Site secret.
     * @param string $response Value from g-captcha-response form field.
     * @param string $remoteIp User's IP address.
     * @param string $version Version of this client library.
     */
    public function __construct($secret, $response, $remoteIp = null, $version = null)
    {
        $this->secret = $secret;
        $this->response = $response;
        $this->remoteIp = $remoteIp;
        $this->version = $version;
    }

    /**
     * Array representation.
     *
     * @return array Array formatted parameters.
     */
    public function toArray()
    {
        $params = array('secret' => $this->secret, 'response' => $this->response);

        if (!is_null($this->remoteIp)) {
            $params['remoteip'] = $this->remoteIp;
        }

        if (!is_null($this->version)) {
            $params['version'] = $this->version;
        }

        return $params;
    }

    /**
     * Query string representation for HTTP request.
     *
     * @return string Query string formatted parameters.
     */
    public function toQueryString()
    {
        return http_build_query($this->toArray(), '', '&');
    }
}