

<?php

    require_once "../definitions.php";

    class Trakt{

        public static $instance;

        private $authheaders;

        public function __construct(){
            $this->authheaders = [
                'Content-Type: application/json',
                'trakt-api-version: 2',
                'trakt-api-key: '.TRAKT_ACCESSTOKEN
            ];
        }

        public static function getInstance(){

            if (!isset(self::$instance)) {
                self::$instance = new Trakt();
            }

            return self::$instance;
        }

        /**
         * Execute a cURL call.
         * Prefered to only do one cURL call which might result in a prefered httpcode per request.
         *
         * @param string $url
         * @param array $header An array of headers.
         * @param string $mode Mode for the execution (PUT/GET/POST).
         * @param string $variables Variables/body to send in execution. String or json.
         * @return mixed Returns decoded json of return from execution.
         */
        private function executeCURL($url, $header = array(), $mode = "GET", $variables = ""){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL,$url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($header, $this->authheaders));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $mode);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $variables);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $content = curl_exec($curl);
            $this->lastHTTPCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $array = json_decode($content, true);
            $this->response['variables']['http_response'] = $this->lastHTTPCode;
            if(isset($array['error']['status']) && $array['error']['status'] == 401){
                $this->response['successful'] = false;
                $this->response['errors'][] = "Missing permissions.";
                $this->response['status_message'] = "You do not have permissions to do this. (Wrong scope?)";
            }

            switch($this->lastHTTPCode){
                case 405:
                    $this->response['errors'][] = "The endpoint $url does not support $mode.";
                    break;
                case 429:
                    $this->response['status_message'] = "Too many damn requests. STAAAHP.";
                    break;
                default:
                    break;
            }
            
            curl_close($curl);
			var_dump($this->lastHTTPCode);
			
            return $array;

        }


        public function getWatched(){
            $allMovies = $this->executeCURL("https://api.trakt.tv/users/sebbejohansson/watched/movies");
            return $allMovies;
        }
        public function getRated(){
            $allMovies = $this->executeCURL("https://api.trakt.tv/users/sebbejohansson/ratings/movies/");
            return $allMovies;
        }

    }