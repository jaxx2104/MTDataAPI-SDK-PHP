<?php
/**
 *  MTDataApi-PHP
 *  @author iwashita
 *  @version 0.1
 */

namespace MT;

class DataApi
{
    const VERSION = "v2";

    private $apiUrl;
    private $accessToken;
    private $params;
    public  $response;

    public function __construct($apiUrl) {
        if (empty($apiUrl)) {
            var_dump("empty dataapi url");
            exit;
        }

        $this->apiUrl = $apiUrl;
    }


    /**
     *  Login method
     *  @param  String  Username
     *  @param  String  Password
     *  @return boolean true or false
     */
    public function login($params = array())
    {
        $url = sprintf("%s/%s/authentication", $this->apiUrl, self::VERSION);

        if (empty($params["username"]) || empty($params["password"])) {
            $this->response['error'] = 'empty user or pass';
            return false;
        }

        $defaultParams = array(
            'username'   => null,
            'password'   => null,
            'clientId'   => "MTAuth",
        );

        $params = array_merge($defaultParams, $params);

        $status = $this->userRequest(array(
            'method'        => 'post',
            'url'           => $url,
            'json_params'   => false,
            'login'         => false,
            'params'        => $params
        ));

        if ($status) {
            $this->accessToken = $this->response['response']['accessToken'];
            return true;
        } else {
            return false;
        }
    }

    /**
     *  getCategory
     *  @param  String  BlogId
     *  @param  String  Category
     *  @return boolean true or false
     */
    public function listCategory($blogId = null, $category = null, $params = array())
    {
        $url = sprintf("%s/%s/sites/{$blogId}/categories", $this->apiUrl, self::VERSION);

        if (empty($blogId)) {
            $this->response['error'] = 'empty blogid';
            return false;
        }

        if (isset($category)) {
            $url .= "/".$category;
        }

        $defaultParams = array(
            'search'         => null,
            'searchFields'   => null,
            'limit'          => null,
            "offset"         => null,
            "sortBy"         => null,
            "sortOrder"      => null,
            "fields"         => null,
            "top"            => null,
            "includeIds"     => null,
            "excludeIds"     => null,
        );

        $params = array_merge($defaultParams, $params);

        $status = $this->userRequest(array(
            'method'        => 'get',
            "url"           => $url,
            'json_params'   => true,
            'login'         => false,
            'params'        => $params,
        ));

        if ($status) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ListEntries method
     * @param Int    BlogID
     * @param Array  Params
     * @return boolean true or false
     */
    public function listEntries($blogId = null, $params = array())
    {
        $url = sprintf("%s/%s/sites/{$blogId}/entries", $this->apiUrl, self::VERSION);

        if (empty($blogId)) {
            $this->response['error'] = 'empty blogid';
            return false;
        }

        $defaultParams = array(
            'search'         => null,
            'searchFields'   => null,
            'limit'          => null,
            "offset"         => null,
            "sortBy"         => null,
            "sortOrder"      => null,
            "fields"         => null,
            "includeIds"     => null,
            "excludeIds"     => null,
            "status"         => null,
            "maxComments"    => null,
            "maxTrackbacks"  => null,
            "no_text_filter" => null,

        );
        $params = array_merge($defaultParams, $params);

        $status = $this->userRequest(array(
            'method'        => 'get',
            'url'           => $url,
            'json_params'   => true,
            'login'         => true,
            'params'        => $params
        ));

        if ($status) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * InsertEntries method
     * @param Int    BlogID
     * @param Array  Params
     * @return boolean true or false
     */
    public function createEntry($blogId = null, $params = array())
    {
        $url = sprintf("%s/%s/sites/{$blogId}/entries", $this->apiUrl, self::VERSION);

        if (empty($blogId)) {
            $this->response['error'] = 'empty blogid';
            return false;
        }

        $defaultParams = array(
            'title'        => null,
            'body'         => null,
            'more'         => null,
            "basename"     => null,
            "customFields" => null,
            "updatable"    => true,
        );

        $params = array_merge($defaultParams, $params);
        $status = $this->userRequest(array(
            'method'        => 'post',
            'url'           => $url,
            'request'       => 'entry',
            'json_params'   => true,
            'login'         => true,
            'params'        => $params
        ));


        if($status) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * InsertEntries method
     * @param Int    BlogID
     * @param Array  Params
     * @return boolean true or false
     */
    public function updateEntry($blogId = null, $entryId = null, $params = null)
    {
        $url = sprintf("%s/%s/sites/{$blogId}/entries", $this->apiUrl, self::VERSION);

        if(empty($blogId) || empty($entryId)) {
            $this->response['error'] = 'empty blogid or entryId';
            return false;
        }

        if (isset($entryId)) {
            $url .= "/".$entryId;
        }

        $defaultParams = array(
            'title'        => null,
            'body'         => null,
            'more'         => null,
            "basename"     => null,
            "customFields" => null,
        );

        $params = array_merge($defaultParams, $params);

        $status = $this->userRequest(array(
            'method'        => 'put',
            'url'           => $url,
            'request'       => 'entry',
            'json_params'   => true,
            'login'         => true,
            'params'        => $params
        ));

        if ($status) {
            return true;
        } else {
            return false;
        }

    }

    /**
     *  Other Request
     *  @param  array   method url login otherParam
     *  @return boolean true or false
     */
    public function userRequest($params = array())
    {
        $this->params = array_merge($this->defaultParams(), $params);

        if (empty($this->params['url'])) {
            $this->response['error'] = 'empty url';
            return false;
        }

        $this->curlMt();

        if ($this->response['code'] === 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Default Params
     *  return array    method url json_params login params
     */
    private function defaultParams()
    {
        return array(
            'method'        => 'post',
            'url'           => null,
            'request'       => null,
            'json_params'   => true,
            'login'         => false,
            'params'        => array(),
        );
    }

    /**
     *  Replace Json
     *  @param String   Json
     *  @return array
     */
    private function decodeJson($response = null)
    {
        $temp = json_decode($response, true);
        if(!$temp) {
            return $response;
        } else {
            return $temp;
        }
    }

    /**
     *  Connect DataAPI
     *  @return void
     */
    private function curlMt()
    {

        // curl mt;
        if ($this->params['login']) {
            $header = array('X-MT-Authorization: MTAuth accessToken=' . $this->accessToken);
        } else {
            $header = array();
        }

        if (!empty($this->params['request'])) {
            $this->params['params'] = sprintf("%s=%s", $this->params['request'], json_encode($this->params['params']));
        }

        if ($this->params['method'] == "get") {
            if (count($this->params['params']) != 0) {
                $this->params['params'] = urldecode(http_build_query($this->params['params']));
            }
        }


        // curl core
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($this->params['method'] == 'delete') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params['params']);
        }
        if ($this->params['method'] == 'get') {
            if (count($this->params['params']) != 0) {
                $this->params['url'] .= '?' .  $this->params['params'];
            }
        }
        if ($this->params['method'] == 'post') {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params['params']);
        }
        if ($this->params['method'] == 'put') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params['params']);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $this->params['url']);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $api_response = curl_exec($ch);
        $api_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $api_response_info = curl_getinfo($ch);
        $api_response_error = curl_error($ch);
        $api_response_errno = curl_errno($ch);
        curl_close($ch);
        $api_response_header = trim(substr($api_response, 0, $api_response_info['header_size']));
        $api_response_body = substr($api_response, $api_response_info['header_size']);
        $this->response['code'] = $api_response_code;
        $this->response['response'] = $this->decodeJson($api_response_body);
        $this->response['info'] =  $api_response_info;
        $this->response['error'] = $api_response_error;
        $this->response['errno'] = $api_response_errno;
    }


}
