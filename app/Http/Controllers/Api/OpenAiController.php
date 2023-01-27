<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ExternalApiCallJob;
use App\Mail\WelcomeEmail;
use Aws\Exception\AwsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Queue\SqsQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JetBrains\PhpStorm\NoReturn;
use OpenAI\Laravel\Facades\OpenAI;
use Aws\Sqs\SqsClient;

class OpenAiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function testOpenAi(): JsonResponse
    {
        $response = OpenAI::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => 'What is difference between Sofort and Klarna?'
        ]);

//        $emailAddress = 'nikolavdev@gmail.com';
//
//        // Send Welcome email
//        $a = Mail::to($emailAddress)->queue(new WelcomeEmail());
//        dd($a);

        return response()->json([
           'response' => $response['choices'][0]['text']
        ],200);
    }

    /*
     * test SQS Client
     * @return array|string
     */
    public function testSQSClient(): array|string
    {
        $sqsEndpoint = config('queue.connections.sqs.prefix');
        $queueName = config('queue.connections.sqs.queue');

        $userEmail = 'vule93ca@gmail.com';

        $sqs = new SqsClient([
           "version" => "latest",
            "region" => "eu-central-1"
        ]);

//        $payload = new ExternalApiCallJob($userEmail);
        dispatch(new ExternalApiCallJob($userEmail));

        return response()->json([
           'message' => 'Success'
        ]);

//        try {
//            $response = $sqs->sendMessage(array(
//                'QueueUrl' => $sqsEndpoint, //your queue url goes here
//                'MessageGroupId' => 'chat1',
//                'MessageDeduplicationId' => 'test',
//                'MessageBody' => json_encode($payload),
//            ));
//
//            return response()->json([
//                'message' =>  json_decode($response),
//                'status' => 200
//            ]);
//        } catch (AwsException $awsException) {
//            return response()->json([
//               'message' => $awsException->getAwsErrorMessage(),
//               'line' => $awsException->getLine(),
//               'trace' => $awsException->getTrace(),
//               'code' => $awsException->getAwsErrorCode()
//            ]);
//        }
    }

    public function getMessagesFromAWSSqs()
    {
        $sqsEndpoint = config('queue.connections.sqs.prefix');
        $queueName = config('queue.connections.sqs.queue');

        $sqs = new SqsClient([
            "version" => "latest",
            "region" => "eu-central-1"
        ]);

        try {
            $response = $sqs->receiveMessage(array(
                'QueueUrl' => $sqsEndpoint, //your queue url goes here
            ));

            dd($response);
        } catch (AwsException $awsException) {
            return response()->json([
                'message' => $awsException->getAwsErrorMessage(),
                'code' => $awsException->getAwsErrorCode()
            ]);
        }

    }


    /**
     * @return JsonResponse
     */
    public function getAllUsers(): JsonResponse
    {
        try {
            $users = Cache::remember('users', now()->addMinutes(150), function () {
               $data = array();
               $dataFromDb = DB::table('users')->orderBy('created_at', 'DESC')->paginate(100);

               foreach ($dataFromDb as $d) {
                   $data[] = array(
                       'id' => $d->id,
                       'email' => $d->email,
                       'name' => $d->name
                   );
               }

               return $data;
            });

            if ($users) {
                return response()->json([
                    'message' => 'Succeed',
                    'content' => [
                        'users' => $users
                    ],
                    'code' => 200
                ],200);
            } else {
                return response()->json([
                    'message' => 'Failed: No data found',
                    'code' => 401
                ], 401);
            }

        } catch(\Throwable $th) {
            return response()->json([
                'message' => $th,
                'code' => 501
            ], 501);
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    #[NoReturn] public function storeUser(Request $request): void
    {
        $data = $request->get('name');
        dd($data);
    }
}
