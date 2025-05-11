<?php

namespace App\Traits;


trait ResponseTrait
{

    /**
     * @param string $key
     * @param string $msg
     * @param array  $data
     * @param array  $anotherKey
     * @param bool   $page
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function response($key, $msg, $data = [], $anotherKey = [], $page = false)
    {
        $allResponse = [
            'result' =>  $key == 'success' ? true : false,
            'errNum' => $this->getCode($key),
            'message' => (string) $msg,
        ];

        // unread notifications count if request ask
        if ('success' == $key && request()->has('count_notifications')) {
            $count = 0;
            if (auth()->check()) {
                $count = auth()->user()->notifications()->unread()->count();
            }

            $allResponse['count_notifications'] = $count;
        }

        // additional data
        if (!empty($anotherKey)) {
            foreach ($anotherKey as $otherKey => $value) {
                $allResponse[$otherKey] = $value;
            }
        }

        // res data, always return object instead of array
        if (in_array($key, ['success', 'needActive', 'exception'])) {
            // If data is not empty, return it as an object; else, return an empty object
            $allResponse['data'] = !empty($data) ? (object) $data : (object) [];
        } else {
            $allResponse['data'] = (object) [];
        }

        return response()->json($allResponse)->setStatusCode($this->getCode($key));
    }

    /**
     * Returns a response for unauthenticated users.
     *
     * @return \Illuminate\Http\JsonResponse The response with unauthenticated status and message.
     */

    public function unauthenticatedReturn()
    {
        return $this->response('unauthenticated', trans('auth.unauthenticated'));
    }

    /**
     * Returns a response for unauthorized users.
     *
     * @param array $otherData
     * @return \Illuminate\Http\JsonResponse The response with unauthorized status and message.
     */
    public function unauthorizedReturn($otherData)
    {
        return $this->response('unauthorized', trans('auth.not_authorized'), [], $otherData);
    }

    /**
     * Returns a response for blocked users after logging them out.
     *
     * @param \App\Models\User $user The user instance to be logged out.
     * @return \Illuminate\Http\JsonResponse The response with blocked status and message.
     */
    public function blockedReturn($user)
    {
        $user->logout();
        return $this->response('blocked', __('auth.blocked'));
    }

    /**
     * Returns a response for users who need to activate their phone number.
     *
     * @param \App\Models\User $user The user instance to be activated.
     * @return \Illuminate\Http\JsonResponse The response with needActive status and message, including the verification code.
     */
    public function phoneActivationReturn($user)
    {
        $data = $user->sendVerificationCode();
        return $this->response('needActive', __('auth.not_active'), $data);
    }


    /**
     * Returns a failure response with a specified message.
     *
     * @param string $msg The message to be included in the failure response.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the failure message.
     */
    public function failMsg($msg)
    {
        return $this->response('fail', $msg);
    }


    /**
     * Returns a success response with a specified message.
     *
     * @param string $msg The message to be included in the success response.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the success message.
     */
    public function successMsg($msg = 'done')
    {
        return $this->response('success', $msg);
    }

    /**
     * Returns a success response with data.
     *
     * @param mixed $data The data to be included in the success response.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the success message and data.
     */
    public function successData($data)
    {
        return $this->response('success', trans('t.success'), $data);
    }

    /**
     * Returns a success response with additional data as an array.
     *
     * @param array $dataArr The additional data to be included in the success response.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the success message and additional data.
     */
    public function successOtherData(array $dataArr)
    {
        return $this->response('success', trans('t.success'), [], $dataArr);
    }

    /**
     * Returns the HTTP status code associated with the given response key using the match expression.
     *
     * @param string $key The response key to find the associated HTTP status code.
     * @return int The HTTP status code associated with the given response key.
     */
    public function getCodeMatch($key)
    {

        // $code = match($key) {
        //   'success' => 200,
        //   'fail' => 400,
        //   'unauthorized' => 400,
        //   'needActive' => 203,
        //   'unauthenticated' => 401,
        //   'blocked' => 423,
        //   'exception' => 500,
        //   default => '200',
        // };

        // return $code;
    }

    /**
     * Returns the HTTP status code associated with the given response key.
     *
     * @param string $key The response key to find the associated HTTP status code.
     * @return int The HTTP status code associated with the given response key.
     */
    public function getCode($key)
    {
        switch ($key) {
            case 'success':
                $code = 200;
                break;
            case 'fail':
                $code = 400;
                break;
            case 'needActive':
                $code = 203;
                break;
            case 'unauthorized':
                $code = 400;
                break;
            case 'unauthenticated':
                $code = 401;
                break;
            case 'blocked':
                $code = 423;
                break;
            case 'exception':
                $code = 500;
                break;

            default:
                $code = 200;
                break;
        }

        return $code;
    }
}
