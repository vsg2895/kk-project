<?php namespace Jakten\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Jakten\Models\School;

class RuleAPIService
{
    private $baseUrl;
    private $apiToken;

    /**
     * @param $baseUrl
     * @param $apiToken
     */
    public function __construct()
    {
        $this->baseUrl = config('services.rule.url');
        $this->apiToken = config('services.rule.token');
    }

    public function addSubscriber($data, $type = 'school', $payer = null)
    {
        switch ($type) {
            case 'student':
                $this->studentStore($data, $payer);
                break;
            case 'student_addon':
                $this->studentStore($data, $payer, 'student_addon');
                break;
            default:
                $this->schoolStore($data);
        }
    }

    public function deleteTag($email, $tag)
    {
        try {
            $client = new Client();
            $request = new Request('GET', $this->baseUrl . '/subscribers/' . $email,
                $this->getHeaders());

            $response = $client->send($request);
            $status = $response->getStatusCode();
            $response = $response->getBody()->getContents();
            $decoded = json_decode($response, true);

            if ($status >= 200 && $status < 300) {
                $subscriber = $decoded['subscriber'];
                $tagId = null;
                foreach ($subscriber['tags'] as $tagInfo) {
                    if (strtolower($tagInfo['name']) === strtolower($tag)) {
                        $tagId = $tagInfo['id'];
                    }
                }

                if ($tagId) {
                    //delete tag by id
                    $client = new Client();
                    $request = new Request('DELETE', $this->baseUrl . '/subscribers/' . $email . '/tags/' . $tagId,
                        $this->getHeaders());

                    $response = $client->send($request);
                    $status = $response->getStatusCode();
                    $response = $response->getBody()->getContents();
                    $decoded = json_decode($response, true);

                    if ($status >= 200 && $status < 300) {
                        Log::info('Handle deleting subscriber tag', [
                            'message' => $decoded['message'],
                        ]);
                    } else {
                        Log::info('rule:tag-delete failed', [
                            'message' => 'Something went wrong',
                        ]);
                    }
                } else {
                    Log::info('rule:tag-delete failed', [
                        'message' => 'Tag not found',
                    ]);
                }
            } else {
                Log::info('rule:tag-delete failed. API data: ', [
                    'message' => $decoded['message'],
                    'data' => $decoded['subscriber'],
                    'status' => $status,
                ]);
            }
        } catch (\Exception $exception) {
            Log::info('rule:tag-delete failed', [
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
            ]);
        }
    }

    private function studentStore($data, $payer, $type = 'student')
    {
        try {
            $client = new Client();
            $request = new Request('POST', $this->baseUrl . '/subscribers',
                $this->getHeaders(), $this->getParticipantBody($data, $payer, $type));

            $response = $client->send($request);
            $status = $response->getStatusCode();
            $response = $response->getBody()->getContents();
            $decoded = json_decode($response, true);

            if ($decoded['message'] === 'Success') {
                Log::info('Handle creating participant subscriber in Rule', [
                    'message' => $decoded['message'],
                    'data' => $decoded['subscriber'],
                ]);
            } else {
                Log::info('rule:participant failed. API data: ', [
                    'message' => $decoded['message'],
                    'data' => $decoded['subscriber'],
                    'status' => $status,
                ]);
            }
        } catch (\Exception $exception) {
            Log::info('rule:participant failed', [
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'data' => $data->toArray(),
            ]);
        }
    }

    private function schoolStore($data)//will be updated on duplicate, checking email by default
    {
        try {
            $client = new Client();
            $request = new Request('POST', $this->baseUrl . '/subscribers',
                $this->getHeaders(), $this->getSchoolBody($data));

            $response = $client->send($request);
            $status = $response->getStatusCode();
            $response = $response->getBody()->getContents();
            $decoded = json_decode($response, true);
            if ($decoded['message'] === 'Success') {
                Log::info('Handle creating school subscriber in Rule', [
                    'message' => $decoded['message'],
                    'data' => $decoded['subscriber'],
                ]);
            } else {
                Log::info('rule:school failed. API data: ', [
                    'message' => $decoded['message'],
                    'data' => $decoded['subscriber'],
                    'status' => $status,
                ]);
            }
        } catch (\Exception $exception) {
            Log::info('rule:school failed', [
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'data' => $data->toArray(),
            ]);
        }
    }

    private function getHeaders($headers = [])
    {
        return array_merge([
            'Authorization' => "Bearer $this->apiToken",
            'Content-Type' => "application/json"
        ], $headers);
    }

    private function getSchoolBody(School $school)
    {
        $tags = ['School'];
        if ($school['top_partner']) $tags[] = 'Top-partner';
        if ($school['connected_to']) $tags[] = 'Connected';

        return json_encode([
            "update_on_duplicate" => true,
            "tags" => $tags,
            "subscribers" => [
                "email" => $school['contact_email'],
                "phone_number" => $school['phone_number'],
                "language" => "sv",
                "fields" => [
                    [
                        "key" => "School.Name",
                        "value" => $school['name'],
                        "type" => "text"
                    ],
                    [
                        "key" => "School.City",
                        "value" => $school->city->name,
                        "type" => "text"
                    ],
                    [
                        "key" => "School.Level",
                        "value" => $school['loyalty_level'],
                        "type" => "text"
                    ],
                    [
                        "key" => "Connected.Value",
                        "value" => (bool)$school['connected_to'],
                        "type" => "text"
                    ],
                    [
                        "key" => "Connected.Date",
                        "value" => $school['connected_at'] . '',
                        "type" => "text"
                    ]
                ]
            ]
        ]);
    }

    private function getParticipantBody($participant, $payer, $type)
    {
        $tags = ['Student'];
        if ($payer && $payer['email'] && $participant['email'] == $payer['email']) $tags[] = 'Payer';

        $data = [
            "update_on_duplicate" => true,
            "tags" => $tags,
            "subscribers" => [
                "email" => $participant['email'],
                "language" => "sv",
                "fields" => [
                    [
                        "key" => "User.FirstName",
                        "value" => $participant['given_name'],
                        "type" => "text"
                    ],
                    [
                        "key" => "User.LastName",
                        "value" => $participant['family_name'],
                        "type" => "text"
                    ],
                    [
                        "key" => "User.SSN",
                        "value" => $participant['social_security_number'],
                        "type" => "text"
                    ]
                ]
            ]
        ];

        if ($participant['phone_number'] && $participant['phone_number'] != 0) {
            $data['subscribers']['phone_number'] = $participant['phone_number'];
        }

        if ($type === 'student') {
            $courseName = $participant->course->name;
            $data['subscribers']['fields'] = array_merge($data['subscribers']['fields'], [[
                "key" => "$courseName.LastAttended",
                "value" => $participant->course->start_time . '',
                "type" => "datetime"
            ],
                [
                    "key" => "$courseName.Name",
                    "value" => $courseName,
                    "type" => "text"
                ],
                [
                    "key" => "$courseName.SchoolName",
                    "value" => $participant->course->school->name,
                    "type" => "text"
                ],
                [
                    "key" => "$courseName.SchoolCity",
                    "value" => $participant->course->school->city->name,
                    "type" => "text"
                ]]);
        }

        return json_encode($data);
    }
}
