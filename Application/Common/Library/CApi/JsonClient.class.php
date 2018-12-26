<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/13
 * Time: 16:18
 */

vendor('jsonRPC.jsonRPCClient');
class JsonClient extends  \jsonRPCClient {

    public $debug;

    /**
     * The server URL
     *
     * @var string
     */
    public $url;
    /**
     * The request id
     *
     * @var integer
     */
    public $id;
    /**
     * If true, notifications are performed instead of requests
     *
     * @var boolean
     */
    public $notification = true;

    public $isOneline = true;

    public $api = true;

    public $timeout = 0;



    public function __construct($url,$debug = false) {
        // server URL
        $this->url = $url;
        // proxy
        empty($proxy) ? $this->proxy = '' : $this->proxy = $proxy;
        // debug state
        empty($debug) ? $this->debug = false : $this->debug = true;
        // message id
        $this->id = 1;
    }

    public function __call($method,$params) {
        set_time_limit(0);



        // check
        if (!is_scalar($method)) {
            throw new Exception('Method name has no scalar value');
        }

        // check
        if (is_array($params)) {
            // no keys
            $params = array_values($params);
        } else {
            throw new Exception('Params must be given as array');
        }

        if($this->isOneline){

            return R($this->url.'/'.$method,$params);
        }

        // sets notification or request task
        if ($this->notification) {
            $currentId = NULL;
        } else {
            $currentId = $this->id;
        }

        // prepares the request
        $request = array(
            'method' => $method,
            'params' => $params,
            'id' => $currentId
        );
        if($user = is_login()){
            $request['handle_uids'] =   $user;
        }

        $request = json_encode($request);

        $this->debug && $this->debug.='***** Request *****'."\n".$request."\n".'***** End Of request *****'."\n\n";

        // performs the HTTP POST
        $opts = array ('http' => array (
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => $request
        ));

        if($this->timeout){
            $opts['http']['timeout'] = $this->timeout;
        }
        $context  = stream_context_create($opts);
        if ($fp = fopen($this->url, 'r', false, $context)) {
            $log_data = array(
                'class' =>$this->url,
                'action' =>$method,
                'method' =>$request,
                'create_time' =>date('Y-m-d h:i:s'),
            );
           // $capi_log = M('capi_log');
           // $id = $capi_log->add($log_data);
            $response = '';
            while($row = fgets($fp)) {
                $response.= trim($row)."\n";
            }
            $this->debug && $this->debug.='***** Server response *****'."\n".$response.'***** End of server response *****'."\n";
           // $capi_log->where(array('capi_log_id'=>$id))->save(array('return'=>$response,'return_time'=>date('Y-m-d h:i:s')));
            $response = json_decode($response,true);

        } else {
           // throw new Exception('Unable to connect to '.$this->url);
        }

        // debug output
        if ($this->debug) {
            echo nl2br($debug);
        }

        // final checks and return
        if (!$this->notification) {
            // check
            if ($response['id'] != $currentId) {
              //  throw new Exception('Incorrec2t response id (request id: '.$currentId.', response id: '.$response['id'].')');
            }
            if (!is_null($response['error'])) {
             //   throw new Exception('Request error: '.$response['error']);
            }

            return $response['result'];

        } else {
            return true;
        }
    }

}