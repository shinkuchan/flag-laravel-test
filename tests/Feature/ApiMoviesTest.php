<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Model\Movie;
use App\Model\Genre;

use Faker\Generator as Faker;
use Illuminate\Support\Str;

class ApiMoviesTest extends TestCase
{
    public function testList()
    {
        $response = $this->get('/api/movies');
        $response->assertStatus(200);
        
        $response = $this->put('/api/movies');
        $response->assertStatus(405);
        
        $response = $this->patch('/api/movies');
        $response->assertStatus(405);
        
        $response = $this->delete('/api/movies');
        $response->assertStatus(405);
    }
    
    public function testPagination()
    {
        $response = $this->getJson('/api/movies');
        $data = $response->decodeResponseJson();
        
        $response = $this->get('/api/movies?page='.($data['page_details']['max_page']));
        $response->assertStatus(200);
        
        $response = $this->get('/api/movies?page='.(0));
        $response->assertStatus(422);
        
        $response = $this->get('/api/movies?page='.($data['page_details']['max_page']+1));
        $response->assertStatus(422);
        
        $response = $this->get('/api/movies?page='.$this->generateRandomString());
        $response->assertStatus(422);
    }
    
    public function testFilter()
    {
        $genres = Genre::all();
        
        foreach ($genres as $genre) {
            $response = $this->getJson('/api/movies?filter[]='.$genre['id']);
            $response->assertStatus(200);
        }
        
        $max_id = $genres->pluck('id')->max();
        $response = $this->getJson('/api/movies?filter[]='.($max_id+1));
        $response->assertStatus(422);
        
        $response = $this->getJson('/api/movies?filter[]='.$this->generateRandomString());
        $response->assertStatus(422);
    }
    
    public function testSort()
    {
        $sort_keys = [
            'id', '-id',
            'name', '-name',
            'release_date', '-release_date',
        ];
        
        foreach ($sort_keys as $sort) {
            $response = $this->getJson('/api/movies?sort='.$sort);
            $response->assertStatus(200);
        }
        
        $response = $this->getJson('/api/movies?sort='.$this->generateRandomString());
        $response->assertStatus(422);
    }

    public function testAddEditDelete()
    {
        $genre = Genre::inRandomOrder()->limit(1)->get()->first();
        
        $send_data = factory(Movie::class)->make()->toArray();
        $send_data['genres'] = [$genre['id']];
        
        foreach ($send_data as $key => $val) {
            $fail_data = $send_data;
            unset($fail_data[$key]);
            
            $response = $this->post('/api/movies', $fail_data);
            $response->assertStatus(422);
        }
        
        $response = $this->post('/api/movies', $send_data);
        $response->assertStatus(201);
        
        $data = $response->decodeResponseJson();
        
        $response = $this->get('/api/movies/'.$data['data']['id']);
        $response->assertStatus(200);
        
        $response = $this->post('/api/movies/'.$data['data']['id'], $send_data);
        $response->assertStatus(405);
        
        $response = $this->put('/api/movies/'.$data['data']['id'], $send_data);
        $response->assertStatus(200);
        
        $response = $this->get('/api/movies/'.$data['data']['id'], $send_data);
        $response->assertStatus(200);
        
        $response = $this->delete('/api/movies/'.$data['data']['id']);
        $response->assertStatus(202);
    }
    
    protected function generateRandomString($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
}
