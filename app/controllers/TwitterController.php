<?php
    use Phalcon\Mvc\Controller;

    class TwitterController extends Controller
    {
        public function indexAction()
        {
        }
        
        /**
         * This action searches for Tweets based on the params provided
         * The query paramter it will search are "from:XXX", "to:XXX, "#XXX", "@XXX"
         * 
         * @params
         *      $searchFor => the string to search for in twitter
         */
        public function searchAction($searchFor)
        {
            try
            {
                $basicOAuth = $this->_generateBasicOAuth();
                $baseURL = 'https://api.twitter.com/1.1/search/tweets.json';

                $searchCriteria = array(
                    array(
                        'title' => 'From '.$searchFor,
                        'query' => array('q' => rawurlencode('from:'.$searchFor))
                    ),
                    array(
                        'title' => 'To '.$searchFor,
                        'query' => array('q' => rawurlencode('to:'.$searchFor))
                    ),
                    array(
                        'title' => '#'.$searchFor,
                        'query' => array('q' => rawurlencode('#'.$searchFor))
                    ),
                    array(
                        'title' => '@'.$searchFor,
                        'query' => array('q' => rawurlencode('@'.$searchFor))
                    )
                );

                foreach($searchCriteria as $sc)
                {
                    $title = $sc['title'];
                    $query = $sc['query'];

                    $oauth = array_merge($query, $basicOAuth);
                    $header = $this->_generateHeader(array('base_url' => $baseURL, 'method' => 'GET', 'oauth' => $oauth));
                    
                    $ch = curl_init();
                    curl_setopt_array($ch, array(
                        CURLOPT_HTTPHEADER => $header,
                        CURLOPT_HEADER => false,
                        CURLOPT_URL => $baseURL."?".http_build_query($query),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => false
                    ));
        
                    $curlOutput = curl_exec($ch);
                    curl_close($ch);
        
                    $curlOutput = json_decode($curlOutput, true);
                    if($curlOutput !== false && is_array($curlOutput) && isset($curlOutput['statuses']) && is_array($curlOutput['statuses']))
                    {
                        if(count($curlOutput['statuses']) > 0)
                        {
                            echo "<h1>Title: ".$title."</h1>";
                            echo "<div style = 'background:#EEE;'>";
                            echo "<pre>"; var_dump($curlOutput['statuses']); echo "</pre>";
                            echo "</div>";
                        }
                        else
                        {
                            echo '<h1 style = "color:red;">Nothing found for '.$title.'</h1>';
                        }
                    }
                    else
                        throw new Exception('Unable to fetch Tweet');
                }
            }
            catch(Exception $ex)
            {
                var_dump($ex->getMessage());
            }
        }
        
        private function _generateHeader(Array $option)
        {
            try
            {
                $oauth = $this->_generateBasicOAuth();

                if(!isset($option['base_url']) || ($baseURL = trim($option['base_url'])) === '')
                    throw new Exception('A base URL must be provided to generate header');
                if(isset($option['oauth']) && is_array($option['oauth']) && count($option['oauth']) > 0)
                    $oauth = $option['oauth'];
                if(!isset($option['method']) || ($method = trim($option['method'])) === '' || !in_array($method, array('GET', 'POST')))
                    throw new Exception('A valid HTTP method must be provided');

                $baseString = $this->_buildBaseString($baseURL, $method, $oauth);
                $compositeKey = rawurlencode($this->config->twitter->api->api_secret_key)."&".rawurlencode($this->config->twitter->api->access_token_secret);
                $oauthSignature = base64_encode(hash_hmac('sha1', $baseString, $compositeKey, true)); 
                
                $oauth['oauth_signature'] = $oauthSignature;
                $header = array($this->_buildAuthorizationHeader($oauth), 'Expect:');

                return $header;
            }
            catch(Exception $ex)
            {
                throw $ex;
            }
        }

        /**
         * A helper function to generate the Basic OAuth for Twitter api
         */
        private function _generateBasicOAuth()
        {
            return array(
                'oauth_consumer_key' => $this->config->twitter->api->api_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $this->config->twitter->api->access_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0'
            );
        }

        /**
         * 
         */
        private function _buildBaseString($baseURI, $method, $params) {
            $r = array();
            ksort($params);
            return $method."&" . rawurlencode($baseURI) .'&'.rawurlencode(http_build_query($params));
        }
    
        /**
         * 
         */
        private function _buildAuthorizationHeader($oauth) {
            $r = 'Authorization: OAuth ';
            $values = array();
            
            foreach($oauth as $key => $value)
                $values[] = $key.'="'.rawurlencode($value).'"';
            
            $r .= implode(', ', $values);
            return $r;
        }
    }

?>