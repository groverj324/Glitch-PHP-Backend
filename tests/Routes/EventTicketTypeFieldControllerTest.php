<?php

namespace Tests\Routes;

use App\Facades\EventInvitesFacade;
use App\Facades\RolesFacade;
use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\EventTicketTypeField;
use App\Models\EventTicketTypeSection;
use App\Models\User;
use Database\Factories\EventInviteFactory;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EventTicketTypeFieldControllerTest extends TestCase
{


    public function testList(){

        $event = Event::factory()->create();

        $type = EventTicketType::factory()->create(['event_id' => $event->id]);

        $fields = EventTicketTypeField::factory(30)->create(['ticket_type_id' => $type->id]);

        $user = User::factory()->create();

        $url = $this->_getApiRoute() . 'events/' . $event->id . '/tickettypes/' . $type->id . '/fields';

        RolesFacade::eventMakeAdmin($event, $user);

        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user),
        ])->get($url);

        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $typesData = $json['data'];

        $this->assertCount(25, $typesData);

    }

    public function testCreation(){

        $event = Event::factory()->create();

        $type = EventTicketType::factory()->create(['event_id' => $event->id]);

        $user = User::factory()->create();

        RolesFacade::eventMakeAdmin($event, $user);

        $url = $this->_getApiRoute() . 'events/' . $event->id . '/tickettypes/' . $type->id . '/fields';

        /*$response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user),
        ])->post($url);

        $this->assertEquals(422, $response->status());*/

        $faker = \Faker\Factory::create();

        $data = [
            'label' => $faker->name(),
            'name' => $faker->name(),
            'field_type' => rand(1,4)
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken($user),
        ])->post($url, $data);

        $this->assertEquals(201, $response->status());

        $json = $response->json();

        $result = $json['data'];

        $this->assertEquals($result['ticket_type_id'], $type->id);
        $this->assertEquals($result['name'], $data['name']);
        $this->assertEquals($result['label'], $data['label']);
        $this->assertEquals($result['field_type'], $data['field_type']);
        $this->assertNotNull($result['id']);
        //$this->assertEquals($event['user']['id'], $user->id);

    }

    public function testView() {

        $event = Event::factory()->create();

        $type = EventTicketType::factory()->create(['event_id' => $event->id]);

        $field = EventTicketTypeField::factory()->create(['ticket_type_id' => $type->id]);

        $url = $this->_getApiRoute() .  'events/' . $event->id . '/tickettypes/' . $type->id . '/fields/' . $field->id;

        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken(),
        ])->get($url);

        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $data = $json['data'];

        $this->assertEquals($field->id, $data['id']);
        $this->assertEquals($type->id, $data['ticket_type_id']);

    }

    public function testUpdate() {

        $user = User::factory()->create();

        $event = Event::factory()->create();

        RolesFacade::eventMakeAdmin($event, $user);

        $type = EventTicketType::factory()->create(['event_id' => $event->id]);

        $field = EventTicketTypeField::factory()->create(['ticket_type_id' => $type->id]);

        $url = $this->_getApiRoute() .  'events/' . $event->id . '/tickettypes/' . $type->id . '/fields/' . $field->id;

        $faker = \Faker\Factory::create();

        $section = EventTicketTypeSection::factory()->create(['ticket_type_id' => $type->id]);

        $data = [
            'section_id' => $section->id,
            'field_type' => rand(1,4),
            
            'label' => $faker->name(),
            'name' => $faker->name(),
            'field_order' => rand(1,100),

            'is_required' => rand(0,1),
            'is_disabled' => rand(0,1),
             
        ];

        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user),
        ])->put($url, $data);
        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $jsonData = $json['data'];

        $this->assertEquals($field->id, $jsonData['id']);
        $this->assertEquals($jsonData['section_id'], $data['section_id']);
        $this->assertEquals($jsonData['field_type'], $data['field_type']);
        $this->assertEquals($jsonData['label'], $data['label']);
        $this->assertEquals($jsonData['name'], $data['name']);
        $this->assertEquals($jsonData['field_order'], $data['field_order']);
        $this->assertEquals($jsonData['is_required'], $data['is_required']);
        $this->assertEquals($jsonData['is_disabled'], $data['is_disabled']);

    }

    public function testDelete() {

        $user = User::factory()->create();

        $event = Event::factory()->create();

        RolesFacade::eventMakeAdmin($event, $user);

        $type = EventTicketType::factory()->create(['event_id' => $event->id]);

        $field = EventTicketTypeField::factory()->create(['ticket_type_id' => $type->id]);

        $url = $this->_getApiRoute() .  'events/' . $event->id . '/tickettypes/' . $type->id . '/fields/' . $field->id;;

        $response = $this->withHeaders([
            'Authorization Bearer' => $this->getAccessToken($user),
        ])->delete($url);

        $this->assertEquals(204, $response->status());

    }


}