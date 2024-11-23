<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tour_list_by_travel_slug(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/tour/'.$travel->slug);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }

    public function test_tour_price(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(
            ['travel_id' => $travel->id,
                'price' => 123.45
            ]);

        $response = $this->get('/api/tour/'.$travel->slug);
       
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => 123.45]);
    }

    public function test_tour_list_paginated(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory(16)->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/tour/'.$travel->slug);
        $response->assertStatus(200);
        $response->assertJsonCount(15,'data');
        $response->assertJsonPath('meta.last_page',2);

    }
    public function test_tour_list_sorts_by_starting_date(): void
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'starting_date'=>now()->addDays(2),
            'ending_date'=>now()->addDays(3),
        ]);
        $earlierTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'starting_date'=>now(),
            'ending_date'=>now()->addDays(1),
        ]);


        $response = $this->get('/api/tour/'.$travel->slug);
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id',$earlierTour->id);
        $response->assertJsonPath('data.1.id',$laterTour->id);
    }

    public function test_tour_list_sorts_by_price(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'price'=>200,
        ]);

        $cheapLaterTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'price'=>100,
            'starting_date'=>now()->addDays(2),
            'ending_date'=>now()->addDays(3),
        ]);
        $cheapEarlierTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'price'=>100,
            'starting_date'=>now(),
            'ending_date'=>now()->addDays(1),
        ]);


        $response = $this->get('/api/tour/'.$travel->slug.'?sortBy=price&sortOrder=asc');
        $response->assertStatus(200);
        $response->assertJsonPath('data.1.id',$cheapEarlierTour->id);
        $response->assertJsonPath('data.2.id',$cheapLaterTour->id);
        $response->assertJsonPath('data.0.id',$expensiveTour->id);

    }
    public function test_tour_list_filtering_by_price(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'price'=>200,
        ]);

        $cheapTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'price'=>100,]);
            $endpoint='/api/tour/'.$travel->slug;

            $response = $this->get($endpoint.'?priceFrom=100');
            $response->assertJsonCount(2,'data');
            $response->assertJsonFragment(['id'=>$cheapTour->id]);
            $response->assertJsonFragment(['id'=>$expensiveTour->id]);

            $response = $this->get($endpoint.'?priceFrom=150');
            $response->assertJsonCount(1,'data');
            $response->assertJsonMissing(['id'=>$cheapTour->id]);
            $response->assertJsonFragment(['id'=>$expensiveTour->id]);
           
           
            $response = $this->get($endpoint.'?priceFrom=250');
            $response->assertJsonCount(0,'data');

             
            $response = $this->get($endpoint.'?priceTo=200');
            $response->assertJsonCount(2,'data');
            $response->assertJsonFragment(['id'=>$cheapTour->id]);
            $response->assertJsonFragment(['id'=>$expensiveTour->id]);


            $response = $this->get($endpoint.'?priceTo=150');
            $response->assertJsonCount(1,'data');
            $response->assertJsonMissing(['id'=>$expensiveTour->id]);
            $response->assertJsonFragment(['id'=>$cheapTour->id]);
          
            $response = $this->get($endpoint.'?priceTo=50');
            $response->assertJsonCount(0,'data');

            $response = $this->get($endpoint.'?priceFrom=150&priceTo=250');
            $response->assertJsonCount(1,'data');
            $response->assertJsonMissing(['id'=>$cheapTour->id]);
            $response->assertJsonFragment(['id'=>$expensiveTour->id]);

    }
    public function test_tour_list_filtering_by_starting_date(): void
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'starting_date'=>now()->addDays(2),
            'ending_date'=>now()->addDays(3),
        ]);
        $earlierTour = Tour::factory()->create(
            ['travel_id' => $travel->id,
            'starting_date'=>now(),
            'ending_date'=>now()->addDays(1),
        ]);


        $endpoint='/api/tour/'.$travel->slug;

        $response = $this->get($endpoint.'?dateFrom='.now());
        $response->assertJsonCount(2,'data');
        $response->assertJsonFragment(['id'=>$earlierTour->id]);
        $response->assertJsonFragment(['id'=>$laterTour->id]);

        $response = $this->get($endpoint.'?dateFrom='.now()->addDay());
        $response->assertJsonCount(1,'data');
        $response->assertJsonMissing(['id'=>$earlierTour->id]);
        $response->assertJsonFragment(['id'=>$laterTour->id]);

        $response = $this->get($endpoint.'?dateFrom='.now()->addDays(5));
        $response->assertJsonCount(0,'data');

        $response = $this->get($endpoint.'?dateTo='.now()->addDays(5));
        $response->assertJsonCount(2,'data');
        $response->assertJsonFragment(['id'=>$earlierTour->id]);
        $response->assertJsonFragment(['id'=>$laterTour->id]);

        $response = $this->get($endpoint.'?dateTo='.now()->addDay());
        $response->assertJsonCount(1,'data');
        $response->assertJsonMissing(['id'=>$laterTour->id]);
        $response->assertJsonFragment(['id'=>$earlierTour->id]);

        $response = $this->get($endpoint.'?dateTo='.now()->subDay());
        $response->assertJsonCount(0,'data');

        $response = $this->get($endpoint.'?dateFrom='.now()->addDay().'&dateTo='.now()->addDays(5));
        $response->assertJsonCount(1,'data');
        $response->assertJsonMissing(['id'=>$earlierTour->id]);
        $response->assertJsonFragment(['id'=>$laterTour->id]);
    }
    public function test_tour_list_validation_errors(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->getJson('/api/tour/'.$travel->slug.'?priceFrom=abcde');
        $response->assertStatus(422);

        $response = $this->getJson('/api/tour/'.$travel->slug.'?dateFrom=abcde');
        $response->assertStatus(422);
    }


}
