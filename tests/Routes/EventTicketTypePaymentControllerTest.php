<?php

namespace Tests\Routes;

use App\Enums\TicketTypes;
use App\Facades\EventInvitesFacade;
use App\Facades\RolesFacade;
use App\Models\Event;
use App\Models\EventTicketPurchase;
use App\Models\EventTicketType;
use App\Models\EventTicketTypeSection;
use App\Models\User;
use Database\Factories\EventInviteFactory;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EventTicketTypePaymentControllerTest extends TestCase
{


    public function testListPurchases() {

        $event = Event::factory()->create();

        $type = EventTicketType::factory()->create(['event_id' => $event->id]);

        $purchase = EventTicketPurchase::factory(30)->create(['ticket_type_id' => $type->id, 'show_entry' => 1]);

        $user = User::factory()->create();

        RolesFacade::eventMakeAdmin($event, $user);

        $url = $this->_getApiRoute() . 'events/' . $event->id . '/tickettypes/' . $type->id . '/purchases';

        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user),
        ])->get($url);

        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $typesData = $json['data'];

        $this->assertCount(25, $typesData);

    }


    public function testPurchaseFree(){

        $event = Event::factory()->create();

        $type = EventTicketType::factory()->create(['event_id' => $event->id, 'ticket_type' => TicketTypes::FREE]);

        $user = User::factory()->create();

        RolesFacade::eventMakeAdmin($event, $user);

        $url = $this->_getApiRoute() . 'events/' . $event->id . '/tickettypes/' . $type->id . '/purchases';

        /*$response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user),
        ])->post($url);

        $this->assertEquals(422, $response->status());*/

        $faker = \Faker\Factory::create();

        $data = [
            'quantity' => rand(1,10),
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken($user),
        ])->post($url, $data);

        $this->assertEquals(201, $response->status());

        $json = $response->json();

        $result = $json['data'];

        $this->assertEquals($result['ticket_type_id'], $type->id);
        $this->assertNotNull($result['id']);
        //$this->assertEquals($event['user']['id'], $user->id);

    }

    public function testView() {

        $event = Event::factory()->create();

        $user_admin = User::factory()->create();

        $user_purchaser = User::factory()->create();

        $user_denied = User::factory()->create();

        $type = EventTicketType::factory()->create(['event_id' => $event->id]);

        $purchase = EventTicketPurchase::factory()->create(['ticket_type_id' => $type->id, 'user_id' => $user_purchaser->id]);

        $url = $this->_getApiRoute() .  'events/' . $event->id . '/tickettypes/' . $type->id . '/purchases/' . $purchase->id;

        RolesFacade::eventMakeAdmin($event, $user_admin);

        //Test With Access Token
        $response = $this->withHeaders([
           
        ])->get($url . '?access_token=' . $purchase->access_token);

        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $data = $json['data'];

        $this->assertEquals($purchase->id, $data['id']);
        $this->assertEquals($type->id, $data['ticket_type_id']);

        //Test With Admin Token
        $response = $this->withHeaders([
           
        ])->get($url . '?admin_token=' . $purchase->admin_token);
    
        $this->assertEquals(200, $response->status());
    
        $json = $response->json();
    
        $data = $json['data'];
    
        $this->assertEquals($purchase->id, $data['id']);
        $this->assertEquals($type->id, $data['ticket_type_id']);

        //Test With Admin User
        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user_admin),
        ])->get($url);

        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $data = $json['data'];

        $this->assertEquals($purchase->id, $data['id']);
        $this->assertEquals($type->id, $data['ticket_type_id']);

        //Test With Purchase User
        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user_purchaser),
        ])->get($url);

        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $data = $json['data'];

        $this->assertEquals($purchase->id, $data['id']);
        $this->assertEquals($type->id, $data['ticket_type_id']);

        //Test With Denied User
        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user_denied),
        ])->get($url);

        $this->assertEquals(401, $response->status());

    }


}