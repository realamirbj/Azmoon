<?php

namespace Tests\API\V1\Questions;

use App\Consts\QuestionStatus;
use Tests\TestCase;

class QuestionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
    }

    public function test_ensure_we_can_create_new_question()
    {
        $quiz = $this->createQuiz()[0];

        $questionData = [
            'title' => 'What is PHP?',
            'options' => json_encode([
                1 => ['text'=> 'PHP is Car.','is_correct'=>0],
                2 => ['text'=> 'PHP is programming language.','is_correct'=>1],
                3 => ['text'=> 'PHP is animal.','is_correct'=>0],
                4 => ['text'=> 'PHP is toy.','is_correct'=>0],
            ]),
            'is_active' => QuestionStatus::ACTIVE,
            'score' => 5,
            'quiz_id' => $quiz->getId()
        ];

        $response = $this->call('POST','api/v1/questions',$questionData);

        $responseData = json_decode($response->getContent(),true)['data'];
        $this->assertEquals(201,$response->getStatusCode());

        $this->assertEquals($questionData['title'],$responseData['title']);
        $this->assertEquals($questionData['options'],json_encode($responseData['options']));
        $this->assertEquals($questionData['is_active'],$responseData['is_active']);
        $this->assertEquals($questionData['score'],$responseData['score']);
        $this->assertEquals($questionData['quiz_id'],$responseData['quiz_id']);

        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'title',
                'options',
                'is_active',
                'score',
                'quiz_id'
            ]
        ]);
    }

    public function test_ensure_we_can_delete_a_question()
    {
        $question = $this->createQuestion()[0];

        $response = $this->call('DELETE','api/v1/questions',[
            'id' => $question->getId()
        ]);

        $this->assertEquals(200,$response->getStatusCode());
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }
}